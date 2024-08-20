<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\RateCard;
use Illuminate\Http\Request;

class RateCardController extends Controller
{
    public function RateCards()
    {
        $table_head = [
            "rate_card_name" => 'RATE CARD NAME',
            "created_by" => "CREATED BY",
            "card_type" => "VISIBILITY",
            "created_date" => "CREATED DATE",
            "is_active" => "IS ACTIVE",
            "action" => 'ACTION',
        ];

        $searchable = [
            "class" => "rate_card_name",
            "key"   => "rate_card_name"
        ];

        $exceptional_keys = ["listing"];

        $cards = RateCard::where("card_type", "Public")->orderBy("listing", "asc")->get()->toArray();


        $content_header = ['Rate Cards' => route('AllRateCards')];


        foreach ($cards as $key => $card) {
            $table_body[$key] = arrange_keys($table_head, $card);
        }

        foreach ($table_body as $k => $arr) {
            $table_body[$k]["action"] = [
                [
                    "name" => "View Estimates",
                    "path" => "SavedEstimates",
                    "icon" => "",
                ],
            ];
        }

        return view("layouts.master-table-layoutes", compact("table_head", "table_body", "searchable", "content_header", "exceptional_keys"));
    }
    public function RateCard($_id)
    {
        return route("RateCard", $_id);
    }
}
