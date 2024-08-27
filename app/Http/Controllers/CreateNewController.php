<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\DiscountData;
use App\Models\LoginMaster;
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
        // print_r(session()->all());
        return view("layouts.create-new", compact("priceLists", "quoteType", "id", "Data"));
    }

    private function getFormData($id)
    {
        try {
            $jsonData = SavedEstimate::where("is_deleted", 0)->findOrFail($id)->data;
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
