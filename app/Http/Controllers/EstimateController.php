<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\DiscountData;
use App\Models\LoginMaster;
use App\Models\ProductList;
use App\Models\RegionMaster;
use App\Models\SavedEstimate;
use App\Services\GetFormDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EstimateController extends Controller
{
    private $edit_id;
    public function __construct(Request $request)
    {
    }
    public function index(Request $request)
    {
        $this->edit_id = session("edit_id");
        // exit;
        $user = LoginMaster::getUser();
        $discountArray = DiscountData::where("approved_status", "Remaining");
        $prod_list = $request->product_list;
        $Categories = ProductList::getProdData("primary_category", $prod_list);
        $Products = ProductList::getProdData([
            "default_int",
            "default_name",
            "primary_category",
            "sec_category"
        ], $prod_list);

        $regions = RegionMaster::all()->toArray();

        $Data = $this->getFormData($this->edit_id);

        $post_array = $request->all();
        unset($post_array["_token"]);
        return view("layouts.estimates", compact("Categories", "Products", "prod_list", "post_array", "regions", "Data"));
        // print_r($primaryCategories);
    }

    private function getFormData($id)
    {
        try {
            $jsonData = SavedEstimate::findOrFail($id)->data;
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
