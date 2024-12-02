<?php

namespace App\Http\Controllers\Pages;

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
            "created_at" => 'DATE CREATED',
            "updated_at" => 'DATE UPDATED',
            "contract_period" => 'CONTRACT PERIOD',
            "total_upfront" => 'TOTAL UPFRONT COST',
            "id" => 'ACTION',
        ];

        $table_body = SavedEstimate::select([
            "tbl_saved_estimates.id",
            "tbl_saved_estimates.pot_id",
            "tbl_saved_estimates.project_name",
            "tbl_saved_estimates.version",
            "owner.first_name as owner",
            "last_changed_by.first_name as last_changed_by",
            "tbl_saved_estimates.created_at",
            "tbl_saved_estimates.updated_at",
            "tbl_saved_estimates.contract_period",
            "tbl_saved_estimates.total_upfront",
        ])
            ->join("tbl_login_master as owner", "owner.crm_user_id", "=", "tbl_saved_estimates.owner")
            ->join("tbl_login_master as last_changed_by", "last_changed_by.crm_user_id", "=", "tbl_saved_estimates.last_changed_by")
            ->where("tbl_saved_estimates.emp_code", ($_id ?? session()->get('user')["crm_user_id"]))
            ->where("tbl_saved_estimates.is_deleted", 0)
            ->get()
            ->toArray();


        $content_header = ['Saved Estimates' => route('SavedEstimates')];
        if (!is_null($_id)) {
            $user = LoginMaster::select(['first_name', 'last_name'])
                ->where('crm_user_id', $_id)
                ->get()
                ->toArray();
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
