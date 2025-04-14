<?php

namespace App\Http\Controllers\Discounting;

use App\Http\Controllers\Controller;
use App\Models\ProjectQuotationMaster;
use App\Models\QuotationPhaseMaster;
use App\Models\SavedEstimate;
use App\Services\QuotationFunctionService;
use Illuminate\Http\Request;

class DiscountingController extends Controller
{
    use QuotationFunctionService;
    private $listID;
    private $Request;
    private $edit_id;
    const VM_PATTERN = "/vm_/";
    const STRG_PATTERN = "/strg_[1-9]/";
    const ESTMT_PATTERN = "/phase_name|phase_duration|region_id/";

    public function __construct(Request $req)
    {
        $this->edit_id = $req->edit_id;
        $this->Request = QuotationPhaseMaster::with([
            'groups.items'
        ])
            ->where("tbl_quotation_phase_master.quotation_id", $this->edit_id)
            ->get()
            ->toArray();
    }

    public function index(Request $req)
    {
        $Result = [];
        $Products = [];
        $Total = ["_prices" => []];

        return response()->json($this->Request);

        // $this->ArrManipulate($this->Request, $Result, $Total);

        return view("layouts.discounting", [
            "Array" =>  $Result,
            "Total" => $Total,
            "Products" => $Products,
            "edit_id" =>  $this->edit_id,
            "_request" => base64_encode(json_encode($this->Request)),
        ]);
    }
}
