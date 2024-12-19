<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\DiscountData;
use App\Models\LoginMaster;
use App\Models\ProjectMaster;
use App\Models\SavedEstimate;
use Auth;
use Illuminate\Http\Request;

class SavedQuotationController extends Controller
{
    public function index($_id = null)
    {
        $table_head = $this->table_head();

        $table_body = $this->table_body();


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

        $this->update_action_table_body($table_body);

        $searchable = [
            "key" => "project_name",
            "class" => "project_name"
        ];

        $other = [
            "colapssible" => true
        ];

        return view(view: "layouts.master-table-layoutes",
            data: compact("table_head", "table_body", "content_header", "searchable", "other")
        );
    }
    private function table_head(): array
    {
        return [
            "project_name" => 'PROJECT NAME',
            "pot_id" => 'POT ID',
            "created_at" => 'CREATED AT',
            "updated_at" => 'UPDATED AT',
            "child_body" => [
                "quotation_name" => 'QUOTATION NAME',
                "owner" => 'OWNER',
                "last_changed_by" => 'LAST CHANGED BY',
                "total_selling_price" => 'TOTAL SELLING PRICE',
                "total_discounted_selling_price" => 'TOTAL DISCOUNTED SELLING PRICE',
                "total_otc_price" => 'TOTAL OTC PRICE',
                "total_discounted_otc_price" => 'TOTAL DISCOUNTED OTC PRICE',
                "total_discount_percentage" => 'TOTAL DISCOUNT PERCENTAGE',
                "price_list_id" => 'PRICE LIST ID',
                "discount_approval_status" => 'DISCOUNT APPROVAL STATUS',
                "discount_approved_by" => 'DISCOUNT APPROVED BY',
                "created_at" => 'CREATED AT',
                "updated_at" => 'UPDATED AT',
                "id" => "ACTION"
            ]
        ];
    }

    private function table_body(): array
    {
        return ProjectMaster::select([
            "tbl_project_master.id as project_id", // Changed alias to project_id for clarity
            "tbl_project_master.project_name as project_name",
            "tbl_project_master.project_pot_id as pot_id",
            "tbl_project_master.created_at as created_at",
            "tbl_project_master.updated_at as updated_at",
            "tbl_project_quotation_master.id as quotation_id", // Quotation ID
            "tbl_project_quotation_master.quotation_name",
            "owner.first_name as owner",
            "last_changed_by.first_name as last_changed_by",
            "tbl_project_quotation_master.total_selling_price",
            "tbl_project_quotation_master.total_discounted_selling_price",
            "tbl_project_quotation_master.total_otc_price",
            "tbl_project_quotation_master.total_discounted_otc_price",
            "tbl_project_quotation_master.total_discount_percentage",
            "tbl_project_quotation_master.price_list_id",
            "tbl_project_quotation_master.discount_approval_status",
            "tbl_project_quotation_master.discount_approved_by",
            "tbl_project_quotation_master.created_at as quotation_created_at",
            "tbl_project_quotation_master.updated_at as quotation_updated_at",
        ])
            ->from("tbl_project_master") // Specify the main table
            ->join("tbl_project_quotation_master", "tbl_project_quotation_master.project_id", "=", "tbl_project_master.id") // Join the related table
            ->join("tbl_login_master as owner", "owner.crm_user_id", "=", "tbl_project_quotation_master.owner")
            ->join("tbl_login_master as last_changed_by", "last_changed_by.crm_user_id", "=", "tbl_project_quotation_master.last_changed_by")
            ->where([
                "tbl_project_quotation_master.user_id" => ($_id ?? session()->get('user')["crm_user_id"]),
            ])
            ->get()
            ->groupBy('project_id') // Group results by project_id
            ->map(function ($rows, $project_id) {
                $firstRow = $rows->first();
                return [
                    "id" => $project_id,
                    "project_name" => $firstRow['project_name'],
                    "pot_id" => $firstRow['pot_id'],
                    "created_at" => $firstRow['created_at'],
                    "updated_at" => $firstRow['updated_at'],
                    "child_body" => $rows->map(function ($row) {
                        return [
                            "id" => $row['quotation_id'],
                            "quotation_name" => $row['quotation_name'],
                            "owner" => $row['owner'],
                            "last_changed_by" => $row['last_changed_by'],
                            "total_selling_price" => $row['total_selling_price'],
                            "total_discounted_selling_price" => $row['total_discounted_selling_price'] ?? "NA",
                            "total_otc_price" => $row['total_otc_price'] ?? "NA",
                            "total_discounted_otc_price" => $row['total_discounted_otc_price'] ?? "NA",
                            "total_discount_percentage" => $row['total_discount_percentage'] ?? "NA",
                            "price_list_id" => $row['price_list_id'],
                            "discount_approval_status" => $row['discount_approval_status'],
                            "discount_approved_by" => $row['discount_approved_by'] ?? "NA",
                            "created_at" => $row['quotation_created_at'],
                            "updated_at" => $row['quotation_updated_at'],
                        ];
                    })->toArray(),
                ];
            })
            ->values() // Reset the array keys
            ->toArray();
    }

    public function update_action_table_body(&$table_body): void
    {

        foreach ($table_body as $K => $Arr) {
            foreach ($Arr["child_body"] as $k => $arr) {
                $table_body[$K]["child_body"][$k]["action"] = [
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
        }
    }
}
