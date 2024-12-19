<?php

namespace App\Http\Controllers\Estimate;

use App\Http\Controllers\Controller;
use App\Models\DiscountData;
use App\Models\LoginMaster;
use App\Models\ProjectQuotationMaster;
use App\Models\QuotType;
use App\Models\RateCard;
use App\Models\SavedEstimate;
use App\Services\GetFormDataService;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

class CreateNewController extends Controller
{
    public function index($id = null)
    {
        $priceLists = RateCard::where("is_active", true)->orderBy("listing", "asc")->get()->toArray();
        $quoteType = QuotType::where("is_active", true)->get()->toArray();
        $Data = $this->getFormData($id);
        session(["edit_id" => $id]);

        // return response()->json($Data);
        // print_r(session()->all());
        return view("layouts.create-new", compact("priceLists", "quoteType", "id", "Data"));
    }

    private function getFormData($id)
    {
        // Query the project and quotation data
        return ProjectQuotationMaster::select([
            "tbl_project_master.project_pot_id as pot_id",
            "tbl_project_master.project_name as project_name",
            "tbl_project_quotation_master.quotation_name",
            "tbl_project_quotation_master.price_list_id as price_list"
        ])
            ->join("tbl_project_master", "tbl_project_master.id", "=", "tbl_project_quotation_master.project_id") // Correct join
            ->where("tbl_project_quotation_master.id", $id) // Filter by quotation ID
            ->first(); // Use first() for a single record instead of get()
    }
}
