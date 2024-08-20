<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use App\Models\AssociativeProduct;
use App\Models\ProductList;
use App\Models\UnitMap;
use Exception;
use Illuminate\Http\Request;

class GetDataFromModelController extends Controller
{

    private $Request;

    public function __construct(Request $req)
    {
        $this->Request = $req;
    }

    public function AssociativeProduct()
    {
        try {
            $prodId = ProductList::select("id")->where("prod_int", $this->Request->prod)->get()->toArray()[0];
            $associateProds = AssociativeProduct::getAssciatedProducts($prodId["id"]);
            // return response()->json($prodId);
            return response()->json($associateProds);
        } catch (Exception $e) {
            return [];
        }
    }

    public function getUnit()
    {

        try {
            $prod_id = ProductList::where("prod_int", $this->Request->prod)->first();
            $unit = UnitMap::getProductUnit($prod_id->id);
            return $unit[0]["unit_name"];
        } catch (Exception $e) {
            return "NO";
        }
    }
}
