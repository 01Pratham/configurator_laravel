<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
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
    const ESTMT_PATTERN = "/estmtname|period|region/";

    // public function __construct(Request $req)
    // {
    //     $this->edit_id = $req->edit_id;
    //     $data = SavedEstimate::where("id", $this->edit_id)->first()->toArray();
    //     $this->Request = json_decode($data["data"], true);
    //     $this->listID = $this->Request["product_list"];
    // }

    public function __construct(Request $req)
    {
        $this->edit_id = $req->edit_id;
        $data = SavedEstimate::where("id", $this->edit_id)->first();
        if ($data) {
            $data = $data->toArray();
            $this->Request = json_decode($data["data"], true);
            $this->listID = $this->Request["product_list"];
        } else {
            // Handle the case where data is not found
        }
    }

    public function index(Request $req)
    {
        $Result = [];
        $Products = [];
        $Total = ["_prices" => []];

        $this->ArrManipulate($this->Request, $Result, $Total);
        $this->UpdateResultDiscount($Result);

        $this->getTotalArray($Result, $Total, $Other);
        $this->UpdateProductArray($Result, $Products);
        return view("layouts.discounting", [
            "Array" =>  $Result,
            "Total" => $Total,
            "Products" => $Products,
            "edit_id" =>  $this->edit_id,
            "_request" => base64_encode(json_encode($this->Request)),

        ]);
    }

    private function ArrManipulate(array $Array, &$Result,  &$Total): void
    {
        foreach ($Array as $KEY => $VAL) {
            if (!is_array($VAL)) continue;

            foreach ($VAL as $Key => $Val) {
                if (preg_match(self::ESTMT_PATTERN, $Key)) {
                    $Result[$KEY][$Key] = $Val;
                    $Result[$KEY]["percentage"] = 0;
                }
                if (!is_array($Val)) continue;

                if (!isset($Result[$KEY][$Key]) && !preg_match(self::VM_PATTERN, $Key) && !preg_match(self::STRG_PATTERN, $Key)) {
                    $Result[$KEY][$Key] = [];
                }

                if (preg_match(self::VM_PATTERN, $Key)) {
                    $this->processVmData($Result, $Total, $KEY, $Key, $Val);
                } elseif (preg_match(self::STRG_PATTERN, $Key)) {
                    $this->processStorageData($Result, $KEY, $Key, $Val);
                } else {
                    $this->processOtherData($Result, $Sku_Data, $KEY, $Key, $Val);
                }
            }
        }
    }

    private function getVmResult($Val, $vmarr): array
    {

        return [
            "vcore" => [
                "service"    => $Val["vmname"],
                "product"    => "vCores : {$vmarr["cpu"]}",
                "prod_unit"  => "NO",
                "qty"        => intval($Val["vmqty"]),
                "prod_int"   => $vmarr["prod_ints"]["vcore"],
                "unit_price" => $this->getProductPrice($vmarr["prod_ints"]["vcore"]),
                "mrc"        => ($vmarr["cpu"] * $this->getProductPrice($vmarr["prod_ints"]["vcore"])) * intval($Val["vmqty"]),
                "otc"        => 0,
                "discount"   => 0
            ],
            "ram" => [
                "service"    => $Val["vmname"],
                "product"    => "RAM : {$vmarr["ram"]}",
                "prod_unit" => "NO",
                "qty"        => intval($Val["vmqty"]),
                "prod_int"   => $vmarr["prod_ints"]["ram"],
                "unit_price" => $this->getProductPrice($vmarr["prod_ints"]["ram"]),
                "mrc"        => ($vmarr["ram"] * $this->getProductPrice($vmarr["prod_ints"]["ram"])) * intval($Val["vmqty"]),
                "otc"        => 0,
                "discount"   => 0
            ],
            "storage" => [
                "service"    => $Val["vmname"],
                "product"    => "Disk - " . (preg_replace("/[a-zA-Z]| /", '', $this->getProdName($vmarr["prod_ints"]["storage"]))) . " IOPS : {$vmarr["disk"]} GB",
                "prod_unit" => "NO",
                "qty"        => intval($Val["vmqty"]),
                "prod_int"   => $vmarr["prod_ints"]["storage"],
                "unit_price" => $this->getProductPrice($vmarr["prod_ints"]["storage"]),
                "mrc"        => ($vmarr["disk"] * $this->getProductPrice($vmarr["prod_ints"]["storage"])) * intval($Val["vmqty"]),
                "otc"        => 0,
                "discount"   => 0
            ]
        ];
    }

    private function UpdateProductArray(&$Result, &$Products)
    {
        foreach ($Result as $KEY => $VAL) {
            if (is_array($VAL)) {
                foreach ($VAL as $Key => $Val) {
                    if (is_array($Val)) {
                        foreach ($Val as $key => $val) {
                            if ($Key == "vm") {
                                foreach ($val as $_k => $_v) {
                                    if (is_array($_v)) {
                                        $QTY = preg_replace("/[A-Za-z]| /", "", explode(":", $_v["product"])[1]);
                                        $Products[$KEY][$key][$_k] = [
                                            "product"      => strtoupper($_k),
                                            "SKU"          => $this->getProductSku($_v["prod_int"]),
                                            "Quantity"     => $QTY,
                                            "MRC"          => $_v["mrc"],
                                        ];
                                    }
                                }
                                $Products[$KEY][$key]["QTY"] = $_v["qty"];
                                continue;
                            }

                            $Products[$KEY][$Key][$key] = [
                                "product"      => $val["product"],
                                "SKU"          => $this->getProductSku($val["prod_int"]),
                                "Quantity"     => $val["qty"],
                                "MRC"          => $val["mrc"],
                            ];
                        }
                    }
                }
            }
        }
    }
}
