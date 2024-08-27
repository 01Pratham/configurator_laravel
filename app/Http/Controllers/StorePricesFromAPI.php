<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ProductList;
use App\Models\RateCard;
use App\Models\RateCardPrice;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StorePricesFromAPI extends Controller
{
    private const API_TIMEOUT = 1200;

    public function index()
    {
        ini_set('max_execution_time', self::API_TIMEOUT);

        $result = [
            'RateCard' => $this->addRateCards(),
            'Products' => $this->addProducts(),
            'Prices' => $this->addPrices(),
        ];

        return response()->json($result);
    }

    private function fetchData(string $url): array
    {
        try {
            $response = Http::timeout(self::API_TIMEOUT)
                ->withHeaders([
                    'Authorization' => getenv("CRM_API_AUTH")
                ])
                ->post($url);

            $data = $response->json();

            if (!$data || empty($data['result'])) {
                throw new \Exception('Invalid API response or empty data');
            }

            return $data;
        } catch (\Exception $e) {
            Log::error("Error fetching data from $url: " . $e->getMessage());
            return [];
        }
    }

    private function addRateCards(): array
    {
        $data = $this->fetchData("https://crm.esdsdev.com/~crmesdsdev/uat/pricing_list_master.php");

        if (empty($data['result']['pricing_list'])) {
            return ['message' => 'Issues with the API or no pricing data available'];
        }

        $result = ['updated' => 0, 'created' => 0];

        foreach ($data['result']['pricing_list'] as $prod) {
            $this->upsertRateCard($prod, $result);
        }

        return $result;
    }

    private function upsertRateCard(array $prod, array &$result): void
    {
        $rateCard = RateCard::updateOrCreate(
            ['listing' => $prod['pricing_list_id']],
            [
                'rate_card_name' => $prod['pricing_list_name'],
                'card_type' => 'Public',
                'created_by' => 1,
                'is_active' => $prod['is_active'],
            ]
        );

        $result[$rateCard->wasRecentlyCreated ? 'created' : 'updated']++;
    }

    private function addProducts(): array
    {
        $data = $this->fetchData("https://crm.esdsdev.com/~crmesdsdev/uat/sku_api_rest.php");

        if (empty($data['result']['sku_details'])) {
            return ['message' => 'Issues with the API or no SKU data available'];
        }

        $result = ['updated' => 0, 'created' => 0];

        foreach ($data['result']['sku_details'] as $prod) {
            $this->upsertProduct($prod, $result);
        }

        return $result;
    }

    private function upsertProduct(array $prod, array &$result): void
    {
        $created = null;
        $product = ProductList::where(['crm_prod_id' => $prod['core_product_id'], 'prod_int' => strtolower(preg_replace("/00/", "", $prod['skucode']))])->first();

        if ($product) {
            $product->update(
                [
                    'sku_code' => $prod['skucode'],
                    'crm_group_id' => $prod['primary_category_id'],
                ]
            );
            $created = false;
        } else {
            try {
                ProductList::insert(
                    [
                        'sku_code' => $prod['skucode'],
                        'crm_group_id' => $prod['primary_category_id'],
                        'primary_category' => $prod['primary_category_name'],
                        'sec_category' => $prod['secondary_category_name'],
                        'default_int' => strtolower(preg_replace("/00/", "", $prod['skucode'])),
                        'default_name' => $prod['core_product_name'],
                        'prod_int' => strtolower(preg_replace("/00/", "", $prod['skucode'])),
                        'product' => $prod['core_product_name'],
                    ]
                );
            } catch (Exception) {
            }
            $created = true;
        }

        $result[$created ? 'created' : 'updated']++;
    }

    private function addPrices(): array
    {
        $data = $this->fetchData("http://115.124.98.60/~crmesdsdev/uat/product_align_check.php");

        if (empty($data['result']['pricing_data'])) {
            return ['message' => 'Issues with the API or no pricing data available'];
        }

        $result = ['updated' => 0, 'created' => 0, 'missing' => 0, 'missing_prods' => []];

        foreach ($data['result']['pricing_data'] as $prod) {
            $this->processPrice($prod, $result);
        }

        return $result;
    }

    private function processPrice(array $prod, array &$result): void
    {
        $product = ProductList::where('crm_prod_id', $prod['core_product_id'])->first();
        $rateCard = RateCard::where('id', $prod['pricing_list_id'])->first();

        if ($product && $rateCard) {
            $this->upsertRateCardPrice($product, $rateCard, $prod, $result);
        } else {
            Log::warning("Missing product or rate card for CRM product ID: " . $prod['core_product_id']);
            // $this->addProducts(); // Ensure this doesn't require parameters or adjust accordingly
        }
    }

    private function upsertRateCardPrice(ProductList $product, RateCard $rateCard, array $prod, array &$result): void
    {
        $rateCardPrice = RateCardPrice::updateOrCreate(
            ['prod_id' => $product->id, 'rate_card_id' => $rateCard->id],
            [
                'input_price' => round(floatval($prod['recurring_cost']), 2),
                "region_id" => 0,
                'price' => round(floatval($prod['recurring_selling_price']), 2),
                'discountable_percentage' => 30,
                'otc' => round(floatval($prod['selling_price']), 2),
                'input_otc' => round(floatval($prod['purchase_cost']), 2),
                'discountable_otc' => 30,
            ]
        );

        $result[$rateCardPrice->wasRecentlyCreated ? 'created' : 'updated']++;
    }
}
