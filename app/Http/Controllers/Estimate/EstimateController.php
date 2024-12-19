<?php

namespace App\Http\Controllers\Estimate;

use App\Http\Controllers\Controller;
use App\Models\LoginMaster;
use App\Models\ProductList;
use App\Models\ProjectMaster;
use App\Models\ProjectQuotationMaster;
use App\Models\QuotationPhaseMaster;
use App\Models\RegionMaster;
use App\Services\GetFormDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EstimateController extends Controller
{
    private $edit_id;
    public function __construct(Request $request) {}
    public function index(Request $request)
    {
        $this->edit_id = session("edit_id");


        $quotation_data = QuotationPhaseMaster::with([
            'groups.items'
        ])
            ->where("tbl_quotation_phase_master.quotation_id", $this->edit_id)
            ->get();

        // return response()->json($quotation_data);

        // // exit;
        $user = LoginMaster::getUser();
        $prod_list = $request->product_list;
        $Categories = ProductList::getProdData("primary_category", $prod_list);
        $Products = ProductList::getProdData([
            "default_int",
            "default_name",
            "primary_category",
            "sec_category"
        ], $prod_list);

        $regions = RegionMaster::all()->toArray();

        $edit_id = $this->edit_id;
        $post_array = $request->all();
        $Data = $this->getFormData($this->edit_id);
        unset($post_array["_token"]);

        return view("layouts.estimates", compact("Categories", "Products", "prod_list", "post_array", "Data", "regions",  'edit_id'));
        // print_r($primaryCategories);
    }

    private function getFormData($id)
    {
        try {
            $jsonData = ProjectQuotationMaster::findOrFail($id)->data;
            return new GetFormDataService($jsonData);
        } catch (\Exception $e) {
            return new class
            {
                public function value($str)
                {
                    return 0;
                }
            };
        }
    }
}
