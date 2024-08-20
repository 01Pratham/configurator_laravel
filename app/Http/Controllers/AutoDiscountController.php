<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ProductList;
use Illuminate\Http\Request;

class AutoDiscountController extends Controller
{
    private $Request;

    public function __construct(Request $request)
    {
        $this->Request = $request->all();
    }

    public function index()
    {
        $Result = $this->ManipulateRes();
        return response()->json($Result, 200);
    }

    private function ManipulateRes()
    {
        $discountPercentage = $this->Request['discountVal'];
        $Total = $this->Request['Total'];
        $Data = json_decode(base64_decode($this->Request['data']), true);
        $maxDiscountTotal = [];
        $newTotal = [];
        foreach ($Data as $Index => $Arr) {
            if (is_array($Arr)) {
                foreach ($Arr as $Key => $Val) {
                    if (is_array($Val)) {
                        $Discountable_percentage = $this->Product($Val['SKU'])["discountable_percentage"];
                        $groupQty = floatval($Arr["QTY"] ?? 1);
                        $maxDiscountTotal[] = ($Discountable_percentage / 100) * floatval($Val['MRC']);
                        $newTotal[] = floatval($Val['MRC']);
                        $inputPrices[] = $this->Product($Val['SKU'])["input_price"];
                        $Prices[] = $this->Product($Val['SKU'])["price"];
                        $Quantity[] = intval($Val["Quantity"]) * $groupQty;
                    }
                }
            }
        }
        $discountToBeGiven = ($Total * floatval($discountPercentage));
        $avgDiscPerc = (floatval($discountToBeGiven) / array_sum($maxDiscountTotal));
        $DiscountedMrcArr = [];
        foreach ($Data as $Index => $Arr) {
            if (is_array($Arr)) {
                foreach ($Arr as $Key => $Val) {
                    if (is_array($Val)) {
                        $Discountable_percentage = $this->Product($Val['SKU'])["discountable_percentage"];
                        $discountable_price = ($Discountable_percentage / 100) * floatval($Val['MRC']);
                        $MRC = floatval($Val["MRC"]);
                        $DMRC = $MRC - ($discountable_price * floatval($avgDiscPerc));
                        $DiscountedMrcArr[$Index][preg_replace("/ /", "", $Key)] = ($MRC <= 0) ? 0 : 100 - (100 * ($DMRC / $MRC));
                    }
                }
            }
        }
        foreach ($DiscountedMrcArr as $KEY => $VAL) {
            if (is_array($VAL)) {
                foreach ($VAL as $Key => $Val) {
                    if ($Val < 0) {
                        $DiscountedMrcArr[$KEY][$Key] = 0;
                    }
                }
            }
        }

        return $DiscountedMrcArr;
    }

    private function Product($SKU)
    {
        $prices = ProductList::join('tbl_rate_card_prices', 'tbl_product_list.id', '=', 'tbl_rate_card_prices.prod_id')
            ->where('tbl_product_list.sku_code', $SKU)
            ->select('tbl_rate_card_prices.*')
            ->first();
        return $prices;
    }
}
