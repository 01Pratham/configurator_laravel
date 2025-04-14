<?php

namespace App\Http\Controllers\FinalQuotation;

use App\Http\Controllers\Controller;
use App\Models\OsCalculation;
use App\Models\ProjectQuotationMaster;
use App\Models\TermsCondition;
use App\Services\QuotationFunctionService;
use Illuminate\Http\Request;
use PhpParser\Error;

class FinalQuotationController extends Controller
{

    use QuotationFunctionService;
    private $listID;
    private $Request;
    private $edit_id;
    const VM_PATTERN = "/vm_/";
    const STRG_PATTERN = "/strg_[1-9]/";
    const ESTMT_PATTERN = "/estmtname|period|region|phase_id/";

    public function __construct(Request $req)
    {
        $this->listID = $req->product_list;
        $this->Request = $req->all();
    }

    public function index()
    {
        $this->edit_id = session("edit_id");
        $Result = [];
        $Sku_Data = [];
        $Total = ["_prices" => []];
        $Other = [
            "PROJECT" => $this->Request["project_name"],
            "POT" => $this->Request["pot_id"],
            "QUOTATION_NAME" => $this->Request["quotation_name"],
            "PRICE_LIST" => $this->Request["product_list"],
            "TENURE_TOTAL" => 0,
            "terms" => [],
            "assumptions" => [],
            "exculsions" => [],
        ];


        $this->ArrManipulate($this->Request, $Result, $Total, $Sku_Data);
        return response()->json($Result);

        $this->UpdateResultDiscount($Result);
        // try {
        $this->updateSKU_Data($Sku_Data, $Result);
        // } catch (\Exception $e) {
        //     throw new Error("SKU Data cannot be generated : {$e->getMessage()}");
        // }
        $this->getTotalArray($Result, $Total, $Other, $Sku_Data);
        $this->getOtherData($Other, $this->edit_id);

        $JSON =  $this->JSON_TEMPLATE($Sku_Data);

        $disc_data = [];

        $Other["sku_data"] = ['pot_id' => $Other['POT'], 'project_name' => $Other['PROJECT'], 'quotation_name' => $Other['QUOTATION_NAME'], 'price_list_id' => $Other['PRICE_LIST'], "phases" => $Sku_Data];
        // return response()->json($Other["sku_data"]);
        // return response()->json(base64_encode(json_encode($Other["sku_data"])));


        return view("layouts.final-quotation", [
            "Array" => $Result,
            "Total" => $Total,
            "Other" => $Other,
            "edit_id" => $this->edit_id,
            "JSON" => json_encode($JSON, JSON_PRETTY_PRINT),
            "_request" => base64_encode(json_encode($this->Request)),
            "_discount_status" => $disc_data[0]["approved_status"] ?? null,
        ]);
    }

    private function ArrManipulate(array $Array, &$Result,  &$Total, &$Sku_Data): void
    {
        foreach ($Array as $KEY => $VAL) {
            if (!is_array($VAL)) continue;
            $Sku_Data[$KEY] = [
                "quotation_name"        => $Array["project_name"],
                "price_list_id"         => $this->Request["product_list"],
                "phase_name"            => $VAL["estmtname"],
                "phase_tenure"          => $VAL["period"],
                "region"                => $VAL["region"],
                "phase_total_recurring" => 0,
                "conf_phase_id"         => $VAL["phase_id"],
                "groups"                => []
            ];

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
                    $this->processVmData($Result, $Total, $KEY, $Key, $Val["products"]);
                } elseif (preg_match(self::STRG_PATTERN, $Key)) {
                    $this->processStorageData($Result, $KEY, $Key, $Val["products"]);
                } else {
                    $this->processOtherData($Result, $Sku_Data, $KEY, $Key, $Val["products"]);
                }
            }
        }
    }

    private function getVmResult(array $Data): array
    {

        return [
            "group_id"   => $Data["group_id"],
            "service"    => $Data["Val"]["vmname"],
            "product"    => $this->getVm($Data["vmarr"]),
            "prod_unit"  => "NO",
            "qty"        => (int) $Data["Val"]["vmqty"],
            "unit_price" => $this->getVmPrice($Data["vmarr"]),
            "prod_int"   => $Data["vmarr"]["prod_ints"],
            "mrc"        => $this->getVmPrice($Data["vmarr"]) * intval($Data["Val"]["vmqty"]),
            "otc"        => 0,
            "discount"   => 0
        ];
    }

    private function getOtherData(&$Array, $edit_id)
    {
        if (is_null($edit_id)) {
            foreach (TermsCondition::all()->toArray() as $arr) {
                if (!empty($arr)) {
                    $Array["terms"][] = $arr["terms"];
                }
            }
            return;
        }

        $Data = ProjectQuotationMaster::select("terms", "assumptions", "exculsions")->where("id", $edit_id)->get()->toArray();
        if (isset($Data[0])) {
            foreach ($Data[0] as $key => $arr) {
                $Array[$key] = json_decode($arr, true);
            }
            return;
        }
    }

    private function updateSKU_Data(&$Sku_Data, $Result)
    {
        $Data = $this->DiscountDataObject();
        foreach ($Result as $KEY => $VAL) {
            if (is_array($VAL)) {
                $DoneProds = [];
                foreach ($VAL as $Key => $Val) {
                    if (is_array($Val)) {
                        foreach ($Val as $key => $val) {
                            if (isset($val["prod_int"]) && $val["prod_int"] != "") {
                                if (is_array($val["prod_int"])) {
                                    foreach ($val["prod_int"] as $name => $int) {
                                        $keyString = preg_match("/os|db/", $name) ? "$KEY.Data.software.$int" : "$KEY.Data.$key.$name";
                                        $values = $this->getVm($val["product"]);
                                        $Sku_Data[$KEY]["groups"][$key]["group_name"] = $val["service"];
                                        $Sku_Data[$KEY]["groups"][$key]["group_id"] = $this->getCrmGroupId("virtual_machine");
                                        $Sku_Data[$KEY]["groups"][$key]["group_quantity"] = $val["qty"];
                                        $Sku_Data[$KEY]["groups"][$key]["conf_group_id"] = $val["group_id"];
                                        $Sku_Data[$KEY]["groups"][$key]["products"][$int] = [
                                            "qty"        => preg_match(
                                                "/[A-Za-z]/",
                                                isset($values[$name]) ? $values[$name] : ""
                                            ) ?
                                                $this->getLics(
                                                    $int,
                                                    $values["vcore"],
                                                    [
                                                        "KEY" => $KEY,
                                                        "i" => substr($key, 4)
                                                    ]
                                                ) : (isset($values[$name]) ? $values[$name] : ""),
                                            "sku_code"   => $this->getProductSku($int),
                                            "unit_price" => $this->getProductPrice($int),
                                            "discount"   => $Data->value($keyString),
                                            "is_billable" => 1,
                                            "prod_int"    => $int
                                        ];
                                        $DoneProds[] = $int;
                                    }
                                } else {
                                    if (!in_array($val["prod_int"], $DoneProds)) {
                                        $Sku_Data[$KEY]["groups"][$Key]["group_name"] = $Key;
                                        $Sku_Data[$KEY]["groups"][$Key]["group_id"] = $this->getCrmGroupId($Key);
                                        $Sku_Data[$KEY]["groups"][$Key]["group_quantity"] = 1;
                                        if (isset($val["group_id"])) $Sku_Data[$KEY]["groups"][$Key]["conf_group_id"] = $val["group_id"];
                                        $Sku_Data[$KEY]["groups"][$Key]["products"][$val["prod_int"]] = [
                                            "qty"        => $val["qty"],
                                            "sku_code"   => $this->getProductSku($val["prod_int"]),
                                            "unit_price" => $val["unit_price"],
                                            "discount"   => $val["discount"],
                                            "is_billable" => 1,
                                            "prod_int"    => $val["prod_int"]

                                        ];
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    private function getLics($int, $core, $options = [])
    {
        global $MatricsArray;
        $osCalc = OsCalculation::where('product_int', $int)->first();
        if ($osCalc) {

            list($variableName, $value) = explode(' = ', $osCalc->calculation);
            $$variableName = $value;
            $coreCount = $core;
            try {
                if (preg_match("/ms/", $int) && preg_match("/Passive/", $MatricsArray["STATE"][$options["KEY"]][$int][$options["i"]])) {
                    $coreCount /= 2;
                }
            } catch (\Exception $e) {
            }

            $lic = round($coreCount / $core_devide, 0);
        } else {
            $lic = 1;
        }

        return $lic;
    }
}
