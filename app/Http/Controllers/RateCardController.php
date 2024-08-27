<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\RateCard;
use App\Models\RateCardPrice;
use App\Services\table_interface;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;

class RateCardController extends Controller
{
    public function RateCards()
    {
        $table_head = [
            "rate_card_name" => 'RATE CARD NAME',
            "created_by" => "CREATED BY",
            "card_type" => "VISIBILITY",
            "created_at" => "CREATED DATE",
            "is_active" => "IS ACTIVE",
        ];
        $table_body = [];

        $cards = RateCard::select(["id", ...array_keys($table_head)])
            ->where("card_type", "Public")
            ->orderBy("listing", "asc")
            ->get()
            ->toArray();
        foreach ($cards as $key => $card) {
            $table_body[$key] = arrange_keys($table_head, $card);
        }
        foreach ($table_body as $k => $arr) {
            $table_body[$k]["action"] = [
                [
                    "name" => "View Estimates",
                    "path" => route("RateCard", $arr["id"]),
                    "icon" => "",
                ],
            ];
        }
        $table_head["action"] = 'ACTION';

        $searchable = [
            "class" => "rate_card_name",
            "key"   => "rate_card_name"
        ];
        $exceptional_keys = ["listing"];
        $content_header = ['Rate Cards' => route('AllRateCards')];
        return view("layouts.master-table-layoutes", compact("table_head", "table_body", "searchable", "content_header", "exceptional_keys"));
    }
    public function RateCard($_id)
    {
        $table_head = [
            "product" => 'PRODUCT',
            "region_name" => "REGION",
            "price" => "MRC",
            "otc" => "OTC",
            "discountable_percentage" => "DISCOUNTABLE %",
            "is_active" => "IS ACTIVE",
        ];
        $table_body = [];

        $cards = RateCardPrice::select([
            "tbl_rate_card_prices.id",
            "tbl_product_list.product",
            "tbl_region_master.region_name",
            "tbl_rate_card_prices.price",
            "tbl_rate_card_prices.otc",
            "tbl_rate_cards.rate_card_name",
            "tbl_rate_card_prices.discountable_percentage",
            "tbl_rate_card_prices.is_active",
        ])
            ->join("tbl_product_list", "tbl_product_list.id", "=", "tbl_rate_card_prices.prod_id")
            ->join("tbl_region_master", "tbl_region_master.id", "=", "tbl_rate_card_prices.region_id")
            ->join("tbl_rate_cards", "tbl_rate_cards.id", "=", "tbl_rate_card_prices.rate_card_id")
            ->where("tbl_rate_card_prices.rate_card_id", $_id)
            ->get()
            ->toArray();

        foreach ($cards as $key => $card) {
            foreach ($card as $k => $val) {
                if ($k == "price" || $k == "otc") $card[$k] = INR($val);
            }
            $table_body[$key] = arrange_keys($table_head, $card);
        }

        // return $cards;
        $content_header = ['Rate Cards' => route("AllRateCards")];
        foreach ($table_body as $k => $arr) {
            $content_header[$arr["rate_card_name"]] = route("RateCard", $_id);
        }
        $searchable = [
            "class" => "prd",
            "key"   => "product"
        ];
        $exceptional_keys = ["rate_card_name"];
        return view("layouts.master-table-layoutes", compact("table_head", "table_body", "searchable", "content_header", "exceptional_keys"));
    }
}
