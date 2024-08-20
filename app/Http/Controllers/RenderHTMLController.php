<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\RegionMaster;
use App\Models\SavedEstimate;
use App\Services\GetFormDataService;
use Illuminate\Http\Request;

class RenderHTMLController extends Controller
{
    private $Data;
    private $request;

    public function __construct(Request $req)
    {
        $this->request = $req->all();
        $this->Data = new GetFormDataService();
    }
    public function ProductAjax()
    {
        $component =  ($this->request["request"] == "prod") ? "components.product-elem" : 'components.product-group';
        return view($component, [
            ...$this->request,
            "Data" => $this->Data
        ]);
    }

    public function VirtualMachine()
    {
        return view("layouts.virtual-machine", [
            ...$this->request,
            "Data" => $this->Data
        ]);
    }
    public function BlockStorage()
    {
        return view("layouts.block-storage", [
            ...$this->request,
            "Data" => $this->Data
        ]);
    }

    public function Estimate()
    {
        $prod_list = $this->request["list_id"];
        $regions = RegionMaster::all()->toArray();
        return view('layouts.estmt-tab', [
            'array' => [...$this->request, 'type' => 'ajax'],
            'prod_list' => $prod_list,
            'regions' => $regions,
            "Data" => $this->Data
        ]);
    }
}
