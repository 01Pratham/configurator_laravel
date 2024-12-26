<?php

namespace App\Http\Controllers\Estimate;

use App\Http\Controllers\Controller;
use App\Models\LoginMaster;
use App\Models\ProductList;
use App\Models\ProjectMaster;
use App\Models\ProjectQuotationMaster;
use App\Models\QuotationPhaseMaster;
use App\Models\RegionMaster;
use App\Services\GetFromJson;
use Illuminate\Http\Request;
use App\Services\GetFromJsonAbstractClass;
use Illuminate\Support\Facades\Log;

class EstimateController extends Controller
{
    private $edit_id;
    public function __construct(Request $request) {}
    public function index(Request $request)
    {
        $this->edit_id = session("edit_id");
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

        return view("layouts.estimates", compact("Categories", "Products", "prod_list", "post_array", "Data", "regions",  'edit_id'));
    }

    private function getFormData($id)
    {
        try {

            $quotation_data = QuotationPhaseMaster::with([
                'groups.items'
            ])
                ->where("tbl_quotation_phase_master.quotation_id", $id)
                ->get();

            // dd($quotation_data->toArray());
            return new GetFromJson(json_string: json_encode(value: $quotation_data->toArray()));
        } catch (\Exception $e) {
            // print_r($e->getMessage());
            return new class extends GetFromJsonAbstractClass {};
        }
    }
}
