<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\DiscountData;
use App\Models\LoginMaster;
use App\Models\SavedEstimate;
use Auth;
use Illuminate\Http\Request;

class SavedQuotationController extends Controller
{
    public static function index($_id = null)
    {
        $table_head = [
            "pot_id" => 'POT ID',
            "project_name" => 'PROJECT NAME',
            "version" => 'VERSION',
            "owner" => 'OWNER',
            "last_changed_by" => 'LAST UPDATED BY',
            "date_created" => 'DATE CREATED',
            "date_updated" => 'DATE UPDATED',
            "contract_period" => 'CONTRACT PERIOD',
            "total_upfront" => 'TOTAL UPFRONT COST',
            "id" => 'ACTION',
        ];

        $table_body = SavedEstimate::select(array_keys($table_head))->where("emp_code", ($_id ?? session()->get('user')["crm_user_id"]))->get()->toArray();

        $content_header = ['Saved Estimates' => route('SavedEstimates')];
        if (!is_null($_id)) {
            $user = LoginMaster::select(['first_name', 'last_name'])
                ->where('crm_user_id', $_id)
                ->get()
                ->toArray();
            // print_r($user);
            if (isset($user[0]['name'])) {
                $content_header[$user[0]['name']] = route("SavedEstimates", $_id);
            }
        }

        foreach ($table_body as $k => $arr) {
            $table_body[$k]["action"] = [
                [
                    "name" => "Edit",
                    "path" => route("CreateNew", $arr["id"]),
                    "icon" => "fa fa-edit",
                ],
                [
                    "name" => "Clone",
                    "path" => "Estimate/Clone/{$arr['id']}",
                    "icon" => "fa fa-clone",
                ],
                [
                    "name" => "Share",
                    "path" => "Estimate/Share/{$arr['id']}",
                    "icon" => "fa fa-share",
                ],
                [
                    "name" => "Delete",
                    "path" => "Estimate/Delete/{$arr['id']}",
                    "icon" => "fa fa-trash",
                ],
            ];
        }

        $searchable = [
            "key" => "project_name",
            "class" => "project_name"
        ];

        // return view("layouts.saved-quotation", compact("table_head", "table_body", "_id"));
        return view("layouts.master-table-layoutes", compact("table_head", "table_body", "content_header", "searchable"));
    }
}
