<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ProductList;
use App\Models\RateCard;
use App\Models\RateCardPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Str;

class test_SavePricesController extends Controller
{

    private $urls = [
        "RateCards" => "https://crm.esdsdev.com/~crmesdsdev/uat/pricing_list_master.php",
        "Products" => "https://crm.esdsdev.com/~crmesdsdev/uat/sku_api_rest.php",
        "Prices" => "http://115.124.98.60/~crmesdsdev/uat/product_align_check.php"
    ];
    public function index()
    {
        $result = [
            "RateCards" =>  [],
            "Products" => [],
            "Prices" => [],
        ];

        $result['Prices'] =  $this->save_(
            $this->urls["RateCards"],
            function ($prod, $array) {
                return RateCardPrice::where([
                    "prod_id" => $array["product"]->id,
                    "rate_card_id" => $array["rateCard"]->id
                ])->first();
            },
            function ($prod, $ty = "") {
                $product = ProductList::where("crm_prod_id", $prod["core_product_id"])->first();
                $rateCard = RateCard::where("id", $prod["pricing_list_id"])->first();

                if ($ty == "bool") return $product && $rateCard;

                return compact(
                    "product",
                    "rateCard"
                );
            }
        );

        return response()->json($result);
    }

    private function save_(string $url, callable $orm_obj, callable $func = null)
    {
        $API_Data = API($url);

        if (empty($res) || empty($res["result"]["pricing_data"])) {
            return response()->json([
                "message" => "Issues with the API or no pricing data available"
            ], 500);
        }
        $result = ['updated' => 0, 'created' => 0, "missing" => 0];

        foreach ($API_Data as $prod) {
            $func_res = $func($prod, "bool");
            if ($func == null || $func_res == true) {
                $existing = $orm_obj($prod, is_null($func($prod)) ? $prod : $func($prod));

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
                    try {
                        RateCardPrice::create([
                            // "prod_id" => $product->id,
                            // "rate_card_id" => $rateCard->id,
                            "input_price" => round(floatval($prod["recurring_cost"]), 2),
                            "region_id" => 0,
                            "price" => round(floatval($prod["recurring_selling_price"]), 2),
                            "discountable_percentage" => 30,
                            "otc" => round(floatval($prod["selling_price"]), 2),
                            "input_otc" => round(floatval($prod["purchase_cost"]), 2),
                            "discountable_otc" => 30,
                        ]);
                        $result['created']++;
                    } catch (\Exception) {
                        print_r($prod);
                        exit();
                    }
                }
            }
        }

        return $result;
    }

    private function save_array($array, $prod) {}
}
