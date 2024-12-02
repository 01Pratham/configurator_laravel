<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models\ProductList;
use App\Models\RateCard;
use App\Models\RateCardPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Str;

class SavePricesController extends Controller
{
    public function index()
    {
        ini_set('max_execution_time', '1200');
        $result = ["RateCard" => [], "Products" => [], "Prices" => [],];

        $result["RateCard"] = $this->addRateCards();
        $result["Products"] = $this->addProducts();
        $result["Prices"] = $this->addPrices();

        return response()->json($result);
    }

    private function addRateCards()
    {
        $Data = API("https://crm.esdsdev.com/~crmesdsdev/uat/pricing_list_master.php");

        if (empty($Data) || empty($Data["result"]["pricing_list"])) {
            return response()->json([
                "message" => "Issues with the API or no pricing data available"
            ], 500);
        }
        $result = ['updated' => 0, 'created' => 0];
        foreach ($Data["result"]["pricing_list"] as $prod) {
            try {
                $existing = RateCard::where("id", $prod["pricing_list_id"])->first();
                if ($existing) {
                    $existing->update([
                        "rate_card_name" => $prod["pricing_list_name"],
                        "card_type" => "Public",
                        "created_by" => 1,
                        "listing" => $prod["pricing_list_id"],
                        "is_active" => $prod["is_active"],
                    ]);
                    $result['updated']++;
                } else {

                    RateCard::create([
                        "rate_card_name" => $prod["pricing_list_name"],
                        "card_type" => "Public",
                        "created_by" => 1,
                        "listing" => $prod["pricing_list_id"],
                        "is_active" => $prod["is_active"],
                    ]);
                    $result['created']++;
                }
            } catch (\Exception $e) {
                print_r($prod);
                echo $e->getMessage();
                exit();
            }
        }

        return $result;
    }

    private function addProducts(array $missing = [])
    {
        $Data = API("https://crm.esdsdev.com/~crmesdsdev/uat/sku_api_rest.php");

        if (empty($Data) || empty($Data["result"]["sku_details"])) {
            return response()->json([
                "message" => "Issues with the API or no pricing data available"
            ], 500);
        }
        $result = ['updated' => 0, 'created' => 0];

        foreach ($Data["result"]["sku_details"] as $prod) {
            try {
                $existing = ProductList::where("crm_prod_id", $prod["core_product_id"])->first();
                if ($existing) {
                    $existing->update([
                        "sku_code" => $prod["skucode"],
                        "crm_group_id" => $prod["primary_category_id"],
                        "primary_category" => $prod["primary_category_name"],
                        "sec_category" => $prod["secondary_category_name"],
                        "default_int" => strtolower(preg_replace("/00/", "", $prod["skucode"])),
                        "default_name" => $prod["core_product_name"],
                        "prod_int" => strtolower(preg_replace("/00/", "", $prod["skucode"])),
                        "product" => $prod["core_product_name"],
                        // "is_active" => $prod["core_product_id"],
                    ]);
                    $result['updated']++;
                } else {

                    $id = ProductList::insertGetId([
                        "sku_code" => $prod["skucode"],
                        "crm_group_id" => $prod["primary_category_id"],
                        "primary_category" => $prod["primary_category_name"],
                        "sec_category" => $prod["secondary_category_name"],
                        "default_int" => strtolower(preg_replace("/00/", "", $prod["skucode"])),
                        "default_name" => $prod["core_product_name"],
                        "prod_int" => strtolower(preg_replace("/00/", "", $prod["skucode"])),
                        "product" => $prod["core_product_name"],
                    ]);
                    $result['created']++;
                }
            } catch (\Exception $e) {
                print_r($prod);
                echo "products";
                echo $e->getMessage();
                exit();
            }
        }
        if (!empty($missing)) return ProductList::where("id", $id)->first();
        return $result;
    }

    private function addPrices()
    {

        $Data = API("http://115.124.98.60/~crmesdsdev/uat/product_align_check.php");

        if (empty($Data) || empty($Data["result"]["pricing_data"])) {
            return response()->json([
                "message" => "Issues with the API or no pricing data available"
            ], 500);
        }
        $result = ['updated' => 0, 'created' => 0, "missing" => 0, "missing_prods" => []];

        foreach ($Data["result"]["pricing_data"] as $prod) {
            try {
                $product = ProductList::where("crm_prod_id", $prod["core_product_id"])->first();
                $rateCard = RateCard::where("id", $prod["pricing_list_id"])->first();

                if ($product && $rateCard) {

                    $existing = RateCardPrice::where([
                        "prod_id" => $product->id,
                        "rate_card_id" => $rateCard->id
                    ])->first();

                    $this->_save_price($existing, $prod, $result);
                } else {
                    Log::warning("Missing product or rate card for CRM product ID: " . $prod["core_product_id"]);
                    $existing = $this->addProducts($prod);
                    $this->_save_price($existing, $prod, $result);
                }
            } catch (\Exception $e) {
                print_r($prod);
                echo $e->getMessage();
                exit();
            }
        }

        return $result;
    }

    private function _save_price($existing, $prod, &$result)
    {
        if ($existing) {
            $existing->update([
                "input_price" => round(floatval($prod["recurring_cost"]), 2),
                "price" => round(floatval($prod["recurring_selling_price"]), 2),
                "discountable_percentage" => 30,
                "otc" => round(floatval($prod["selling_price"]), 2),
                "input_otc" => round(floatval($prod["purchase_cost"]), 2),
                "discountable_otc" => 30,
            ]);
            $result['updated']++;
        } else {
            RateCardPrice::create([
                "prod_id" => $existing->prod_id,
                "rate_card_id" =>  $existing->rate_card_id,
                "input_price" => round(floatval($prod["recurring_cost"]), 2),
                "region_id" => 0,
                "price" => round(floatval($prod["recurring_selling_price"]), 2),
                "discountable_percentage" => 30,
                "otc" => round(floatval($prod["selling_price"]), 2),
                "input_otc" => round(floatval($prod["purchase_cost"]), 2),
                "discountable_otc" => 30,
            ]);
            $result['created']++;
        }
    }
}
