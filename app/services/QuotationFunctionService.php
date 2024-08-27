<?php

namespace App\Services;

use App\Models\Calculation;
use App\Models\DiscountData;
use App\Models\OsCalculation;
use App\Models\ProductList;
use App\Models\RateCardPrice;
use App\Models\Unit;
use App\Models\UnitMap;
use Illuminate\Support\Facades\Route;


trait QuotationFunctionService
{
    private function processVmData(&$Result,  &$Total, $KEY, $Key, $Val)
    {
        $vmarr = [
            "cpu"       => $Val["vcpu"],
            "ram"       => $Val["ram"],
            "diskIops"  => $Val["vmDiskIOPS"],
            "disk"      => $Val["inst_disk"],
            "os"        => $Val["os"],
            "db"        => $Val["database"],
            "key"       => $KEY,
            "_K"        => $Key,
            "prod_ints" => [
                "vcore"   => "vcpu_static",
                "ram"     => "vram_static",
                "storage" => $Val['vmDiskIOPS'],
                "os"      => $Val["os"],
                "db"      => $Val["database"],
            ],
        ];

        $this->updateVmMetrics($KEY, $Val);
        $Result[$KEY][preg_replace("/_.*/", "", $Key)][$Key] = $this->getVmResult($Val, $vmarr);
        $Total["_prices"][$KEY][preg_replace("/_.*/", "", $Key)][$Key]  = $this->getVmPrice($vmarr, false);
        if (Route::is("FinalQuotation") && !is_null($this->edit_id)) {
            try {
                $DiscountedVMMrc =   intval($Val["vmqty"]) * $this->getVmPrice($vmarr, true, ["Type" => "Discount", "Data" => $this->DiscountDataObject(), "KeyString" => "$KEY.Data.$Key"]);
                $VMMrc = intval($Val["vmqty"]) * $this->getVmPrice($vmarr);
                $Result[$KEY][preg_replace("/_.*/", "", $Key)][$Key]["discount"]  = 100 - (100 * ($DiscountedVMMrc / $VMMrc));
            } catch (\Exception $e) {
            }
        }

        if (!isset($Result[$KEY]["software"])) {
            $Result[$KEY]["software"] = [];
        }

        if (!empty($Val["os"])) {
            $Result[$KEY]["software"] = array_merge($Result[$KEY]["software"], $this->getSoftwareLic("os", $KEY));
        }

        if (!empty($Val["database"]) && $Val["database"] != "NA") {
            $Result[$KEY]["software"] = array_merge($Result[$KEY]["software"], $this->getSoftwareLic("db", $KEY));
        }

        if ($Val["virus_type"] != "") {
            $this->processSecurityData($Result, $KEY, $Val);
        }

        if (intval($Val["ip_public"]) > 0) {
            $this->processNetworkData($Result, $KEY, $Val);
        }
    }

    private function processStorageData(&$Result, $KEY, $Key, $Val)
    {
        if (empty($Val["strg_capacity"]) || empty($Val["strg_iops"])) return;

        $types = ProductList::where("primary_category", "block_storage")
            ->where("sec_category", "storage_types")
            ->get();

        $maxIopsList = [];
        foreach ($types as $stg) {
            $maxIopsList[$stg->prod_int] = floatval(preg_replace("/[a-zA-Z]|-| /", "", $stg->product));
        }

        $prodInt = $this->getNearestStrgType(floatval($Val['strg_iops']), $maxIopsList);
        $Qty = ($this->getUnitName($Val['strg_unit'])['unit_name'] == "TB") ? floatval($Val['strg_capacity']) * 1024 : floatval($Val['strg_capacity']);
        $Mrc = (floatval($Val['strg_iops']) * $this->getProductPrice("block_strg_iops")) + ($Qty * $this->getProductPrice($prodInt));

        $Result[$KEY]["block_storage"][$Key] = [
            "service"    => "Storage",
            "product"    => "Block Storage - {$Val['strg_iops']} IOPS",
            "prod_unit"  => $this->getUnitName($Val['strg_unit'])['unit_name'],
            "qty"        => $Val['strg_capacity'],
            "prod_int"   => $prodInt,
            "unit_price" => $Mrc / floatval($Val['strg_capacity']),
            "mrc"        => $Mrc,
            "otc"        => 0,
            "discount"   => 0,
        ];
    }

    private function processSecurityData(&$Result, $KEY, $Val)
    {
        global $MatricsArray;
        $Result[$KEY]["security"][$Val["virus_type"]] = [
            "service"    => "Service",
            "product"    => $this->getProdName($Val["virus_type"]),
            "prod_unit" => "NO",
            "qty"        => array_sum($MatricsArray["AntiVirus"][$KEY][$Val["virus_type"]]),
            "unit_price" => $this->getProductPrice($Val["virus_type"]),
            "prod_int"   => $Val["virus_type"],
            "mrc"        => array_sum($MatricsArray["AntiVirus"][$KEY][$Val["virus_type"]]) * $this->getProductPrice($Val["virus_type"]),
            "otc"        => 0,
            "discount"   => 0,
        ];
    }

    private function processNetworkData(&$Result, $KEY, $Val)
    {
        global $MatricsArray;
        $Result[$KEY]["network"][$Val["ip_public_type"]] = [
            "service"    => "Service",
            "product"    => $this->getProdName($Val["ip_public_type"]),
            "prod_unit"  => "NO",
            "qty"        => array_sum($MatricsArray["IpAddress"][$KEY][$Val["ip_public_type"]]),
            "unit_price" => $this->getProductPrice($Val["ip_public_type"]),
            "prod_int"   => $Val["ip_public_type"],
            "mrc"        => array_sum($MatricsArray["IpAddress"][$KEY][$Val["ip_public_type"]]) * $this->getProductPrice($Val["ip_public_type"]),
            "otc"        => 0,
            "discount"   => 0,
        ];
    }

    private function updateVmMetrics($KEY, $Val)
    {
        global $MatricsArray;
        $MatricsArray["DISK"][$KEY][$Val["database"]][]            = intval($Val["inst_disk"]) * intval($Val["vmqty"]);
        $MatricsArray["DISK"][$KEY][$Val["os"]][]                  = intval($Val["inst_disk"]) * intval($Val["vmqty"]);
        $MatricsArray["VMQTY"][$KEY][$Val["database"]][]           = intval($Val["vmqty"]);
        $MatricsArray["VMQTY"][$KEY][$Val["os"]][]                 = intval($Val["vmqty"]);
        $MatricsArray["VMQTY"][$KEY]["sum"][]                      = intval($Val["vmqty"]);
        $MatricsArray["CPU"][$KEY][$Val["database"]][]             = intval($Val["vcpu"]);
        $MatricsArray["STATE"][$KEY][$Val["database"]][]           = $Val["state"];
        $MatricsArray["CPU"][$KEY][$Val["os"]][]                   = intval($Val["vcpu"]);
        $MatricsArray["AntiVirus"][$KEY][$Val["virus_type"]][]     = ($Val["virus_type"] == "") ? 0 : intval($Val["vmqty"]);
        $MatricsArray["IpAddress"][$KEY][$Val["ip_public_type"]][] = floatval($Val["ip_public"]) * floatval(intval($Val["vmqty"]));
    }

    private function getVmPrice(array $arr, bool $sum = true, $options = []): mixed
    {
        $cpu_price = $this->getProductPrice("vcpu_static");
        $ram_price = $this->getProductPrice("vram_static");
        $disk_price = $this->getProductPrice($arr['diskIops']);
        if ($sum) {
            if (empty($options)) {
                $result = array_sum([
                    $arr['cpu'] * $cpu_price,
                    $arr['ram'] * $ram_price,
                    $arr['disk'] * $disk_price,
                ]);
            } elseif (!empty($options) && !is_null($options["Data"])) {
                extract($options);
                $result = array_sum([
                    $arr['cpu'] * ($cpu_price - ($cpu_price * (floatval($Data->value($KeyString . ".vcore")) / 100))),
                    $arr['ram'] * ($ram_price - ($ram_price * (floatval($Data->value($KeyString . ".ram")) / 100))),
                    $arr['disk'] * ($disk_price - ($disk_price * (floatval($Data->value($KeyString . ".storage")) / 100))),
                ]);
            }
        } else {
            $result =  [
                "vcore"   => $cpu_price,
                "ram"     => $ram_price,
                "storage" => $disk_price,
            ];
        }

        return $result;
    }

    private function getVm($VAL)
    {
        if (is_array($VAL)) {
            return sprintf(
                "vCores : %d | RAM %d GB | Disk - %d IOPS - %d GB | OS : %s | DB : %s",
                $VAL['cpu'],
                $VAL['ram'],
                preg_replace("/[a-zA-Z]| /", '', $this->getProdName($VAL['diskIops'])),
                $VAL['disk'],
                $this->getProdName($VAL["prod_ints"]['os'] ?? ""),
                $this->getProdName($VAL["prod_ints"]['db'] ?? "")
            );
        } elseif (is_string($VAL)) {
            $pattern = "/vCores : (\d+) \| RAM (\d+) GB \| Disk - (\d+) IOPS - (\d+) GB \| OS : ([^|]+) \| DB : (.+)/";
            if (preg_match($pattern, $VAL, $matches)) {
                return [
                    'vcore' => (int)$matches[1],
                    'ram' => (int)$matches[2],
                    'diskIops' => $matches[3],
                    'storage' => (int)$matches[4],
                    'os' => $matches[5],
                    'db' => $matches[6]
                ];
            }
        }
        return null;
    }

    private function getProductSku($int)
    {
        try {
            $product = ProductList::where('prod_int', $int)->first();
            return $product ? $product->sku_code : 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getNearestStrgType($iops, $maxIopsList)
    {
        foreach ($maxIopsList as $prodint => $number) {
            if ($number > $iops) {
                return $prodint;
            }
        }
        return array_search(max($maxIopsList), $maxIopsList);
    }

    private function getUnitName($unit_id, $type = "id")
    {
        try {
            if ($type == 'id') {
                return Unit::find($unit_id) ?? ['id' => 1, 'unit_name' => 'NO', 'is_active' => 1];
            } else {
                $product = ProductList::where('prod_int', $unit_id)->first();
                if ($product) {
                    $unitMap = UnitMap::where('prod_id', $product->id)->first();
                    if ($unitMap) {
                        return Unit::find($unitMap->unit_id) ?? ['id' => 1, 'unit_name' => 'NO', 'is_active' => 1];
                    }
                }
            }
        } catch (\Exception $e) {
            return ['id' => 1, 'unit_name' => 'NO', 'is_active' => 1];
        }
    }


    private function getProductPrice($int, $col = "price")
    {
        try {
            $prod = RateCardPrice::getProductPrice($int, $this->listID, $col);
            return $prod;
        } catch (\Error $e) {
            return 0;
        }
    }
    private function getCrmGroupId($group)
    {
        try {
            $product = ProductList::where('primary_category', $group)->first();
            return $product ? $product->crm_group_id : "";
        } catch (\Exception $e) {
            return "";
        }
    }

    private function getProdName(string $int): string
    {
        try {
            $Prod = ProductList::where("prod_int", $int)->first();
            return $Prod ? $Prod->product : "";
        } catch (\Exception $e) {
            return "";
        }
    }
    private function getSoftwareLic(string $type, $KEY): array
    {
        global $MatricsArray;
        $result = [];
        $products = ProductList::where('sec_category', $type)->distinct()->pluck('prod_int')->toArray();
        $cpuKeys = array_keys($MatricsArray["CPU"][$KEY]);

        foreach ($cpuKeys as $val) {
            if (in_array($val, $products)) {
                $lic = 0;
                $osCalc = OsCalculation::where('product_int', $val)->first();
                if ($osCalc) {
                    $totalCores = [];
                    list($variableName, $value) = explode(' = ', $osCalc->calculation);
                    $$variableName = $value;
                    foreach ($MatricsArray["CPU"][$KEY][$val] as $i => $c) {
                        $coreCount = intval($MatricsArray["CPU"][$KEY][$val][$i]) * intval($MatricsArray["VMQTY"][$KEY][$val][$i]);
                        if (preg_match("/ms/", $val) && preg_match("/Passive/", $MatricsArray["STATE"][$KEY][$val][$i])) {
                            $coreCount /= 2;
                        }
                        $totalCores[$i] = $coreCount;
                    }
                    $lic += round(array_sum($totalCores) / $core_devide, 0);
                } else {
                    $lic = array_sum($MatricsArray["VMQTY"][$KEY][$val]);
                }

                $unitPrice = $updatedPrices[$KEY]["software"][$val] ?? $this->getProductPrice($val);
                $discount = $discountedData[$KEY]["Data"]["software"][$val] ?? 0;

                $result[$val] = [
                    "service"    => "Service",
                    "product"    => $this->getProdName($val),
                    "prod_unit" => ($lic > 1) ? "Lics" : "Lic",
                    "qty"        => $lic,
                    "unit_price" => $unitPrice,
                    "prod_int"   => $val,
                    "mrc"        => $lic * $unitPrice,
                    "otc"        => 0,
                    "discount"   => $discount,
                ];
            }
        }

        return $result;
    }
    private function processOtherData(&$Result, &$Sku_Data, $KEY, $Key, $Val)
    {
        $Sku_Data[$KEY]["groups"][$Key]["group_name"] = $Key;
        $Sku_Data[$KEY]["groups"][$Key]["group_id"] = $this->getCrmGroupId($Key);
        $Sku_Data[$KEY]["groups"][$Key]["group_quantity"] = 1;

        foreach ($Val as $key => $val) {
            $name = preg_replace("/_select|_qty|_unit/", "", $key);
            if (preg_match("/_mgmt/", $key) && preg_match("/os|db/", $key)) {
                $this->getMngServicesQty($Result, $Sku_Data, $KEY, $Key, $name, $Val);
            } else {
                $this->processGeneralServices($Result, $Sku_Data, $KEY, $Key, $name, $Val);
            }
        }
    }
    private function processGeneralServices(&$Result, &$Sku_Data, $KEY, $Key, $name, $Val)
    {
        global $MatricsArray;

        foreach ($Val as $key => $val) {
            $name = preg_replace("/_select|_qty|_unit/", "", $key);
            if (preg_match("/_mgmt/", $key) && preg_match("/os|db/", $key)) continue;
            try {
                $Unit = isset($Val["{$name}_unit"])
                    ? $this->getUnitName($Val["{$name}_unit"])['unit_name']
                    : $this->getUnitName($Val["{$name}_select"], "prod_int")["unit_name"];
            } catch (\Exception $e) {
                $Unit = "NO";
            }
            $calQuery = Calculation::where('sec_cat_name', $name)->first();
            if (!empty($calQuery) && floatval($Val["{$name}_qty"]) < 1) {
                $itemsArr = explode(",", $calQuery->calculation);
                foreach ($itemsArr as $item) {
                    if (preg_match("/vm/", $item)) {
                        $calculation[$KEY][$Key][$Val["{$name}_select"]][$item] = !empty($MatricsArray["VMQTY"][$KEY]["sum"]) ? array_sum($MatricsArray["VMQTY"][$KEY]["sum"]) : 0;
                    } else {
                        $calculation[$KEY][$Key][$Val["{$name}_select"]][$item] = !empty($Val[$item]) ? floatval($Val[$item]) : 0;
                    }
                }
            }
            $Qty = floatval($Val["{$name}_qty"]);
            $UnitPrice = floatval($this->getProductPrice($Val["{$name}_select"]));
            $Result[$KEY][$Key][$Val["{$name}_select"]] = [
                "service"    => "Service",
                "product"    => $this->getProdName($Val["{$name}_select"]),
                "prod_unit" => $Unit,
                "qty"        => $Qty,
                "unit_price" => ($this->getProductPrice($Val["{$name}_select"], "otc") > 0) ? $this->getProductPrice($Val["{$name}_select"], "otc") : $UnitPrice,
                "prod_int"   => $Val["{$name}_select"],
                "mrc"        => $UnitPrice * floatval((isset($Val["{$name}_unit"]) && $Unit == "TB") ? $Val["{$name}_qty"] * 1024 : $Val["{$name}_qty"]),
                "otc"        => floatval($Val["{$name}_qty"]) * $this->getProductPrice($Val["{$name}_select"], "otc"),
                "discount"   => 0,
            ];
            $Sku_Data[$KEY]["groups"][$Key]["products"][$name] = [
                "qty"        => floatval($Val["{$name}_qty"]),
                "sku_code"   => $this->getProductSku($Val["{$name}_select"]),
                "unit_price" => $UnitPrice,
                "discount"   => 0,
                "otc"        => $this->getProductPrice($Val["{$name}_select"], "otc"),
                "is_billable" => 1
            ];

            if (preg_match("/otc/", $Key)) {
                $Sku_Data[$KEY]["groups"][$Key]["group_name"] = $Key;
                $Sku_Data[$KEY]["groups"][$Key]["group_id"] = $this->getCrmGroupId($key);
                $Sku_Data[$KEY]["groups"][$Key]["group_quantity"] = 1;
                $Result[$KEY][$Key]["otc"] = [
                    "service"    => "Service",
                    "product"    => $this->getProdName($Val["{$name}_select"]),
                    "prod_unit" => "NO",
                    "qty"        => floatval($Val["{$name}_qty"]),
                    "prod_int"   => $Val["{$name}_select"],
                    "unit_price" => 0,
                    "mrc"        => 0,
                    "otc"        => 0,
                    "discount"   => 0,
                ];
                $Sku_Data[$KEY]["groups"][$Key]["products"][$Key] = [
                    "qty"         => 1,
                    "sku_code"    => $this->getProductSku($Val["{$name}_select"]),
                    "unit_price"  => 0,
                    "discount"    => 0,
                    "otc"         => 0,
                    "is_billable" => 1,
                ];
            }
        }
    }
    private function getMngServicesQty(&$Result, &$Sku_Data, $KEY, $Key, $name, $Val)
    {
        global $MatricsArray;
        try {
            $func = "process" . ucwords(explode("_", $name)[0]) . "Management";
            $products = ProductList::select('prod_int')
                ->where('sec_category', $name)
                ->distinct()
                ->get();
            $prod = $products->pluck('prod_int')->toArray();
            $this->{$func}($Result, $Sku_Data, $KEY, $Key, $name, $Val, $prod);
        } catch (\Exception $e) {
            return [];
        }
    }
    private function processDbManagement(&$Result, &$Sku_Data, $KEY, $Key, $prodType, $Val, $CompareableArray): void
    {
        global $MatricsArray;

        $prodTypeKeys = array_keys($MatricsArray["DISK"][$KEY]);
        foreach ($prodTypeKeys as $val) {
            $str = explode("_", $val);
            foreach ($CompareableArray as $k => $int) {
                if ($str[0] . "_{$prodType}" == $int) {
                    $product_name = $this->getProdName($str[0] . "_{$prodType}");
                    $UnitPrice = floatval($this->getProductPrice($str[0] . "_{$prodType}"));
                    $DISK_SUM = 0;
                    foreach ($MatricsArray["DISK"][$KEY][$val] as $i => $v) {
                        $DISK_SUM += floatval($v) * $MatricsArray["VMQTY"][$KEY][$val][$i];
                    }
                    // echo "HI";
                    // $DISK_SUM = 100;
                    // $REM = $DISK_SUM - 100;
                    // $upto100 = floor($REM / 100);
                    // $upto50 = floor(($REM - ($upto100 * 100)) / 50);

                    // $mgmt_unit_cost = ["base" => $UnitPrice];
                    // $mgmt_mrc = ["base" => $UnitPrice * $mgmt_qty];

                    $Result[$KEY][$Key][$str[0] . "_{$prodType}"] = [
                        "service"    => "Service",
                        "product"    => $product_name,
                        "prod_unit" => "NO",
                        "qty"        => $DISK_SUM / 100,
                        "prod_int"   => $str[0] . "_{$prodType}",
                        "unit_price" => $UnitPrice,
                        "mrc"        => $UnitPrice * ($DISK_SUM / 100),
                        "otc"        => 0,
                        "discount"   => 0,
                    ];
                }
            }
        }
    }

    // private function processOsManagement(array &$MGMT, $KEY, string $int, array $str, float $UnitPrice, string $product_name): void
    private function processOsManagement(&$Result, &$Sku_Data, $KEY, $Key, $prodType, $Val, $CompareableArray): void
    {
        global $MatricsArray;

        $prodTypeKeys = array_keys($MatricsArray["DISK"][$KEY]);
        foreach ($prodTypeKeys as $val) {
            $str = explode("_", $val);
            foreach ($CompareableArray as $k => $int) {
                if ($str[0] . "_{$prodType}" == $int) {
                    $product_name = $this->getProdName($str[0] . "_{$prodType}");
                    $VMQTY_SUM = array_sum($MatricsArray["VMQTY"][$KEY][$val]);
                    $UnitPrice = floatval($this->getProductPrice($str[0] . "_{$prodType}"));

                    $Result[$KEY][$Key][$str[0] . "_{$prodType}"] = [
                        "service"    => strtoupper(explode("_", $prodType)[0]) . " Management",
                        "product"    => $product_name,
                        "prod_unit" => "NO",
                        "qty"        => $VMQTY_SUM,
                        "prod_int"   => $str[0] . "_{$prodType}",
                        "unit_price" => $UnitPrice,
                        "mrc"        => $VMQTY_SUM * $UnitPrice,
                        "otc"        => 0,
                        "discount"   => 0,
                    ];
                }
            }
        }
    }

    private function getTotalArray(&$Result, &$Total, &$Other = [], &$Sku_Data = null)
    {
        foreach ($Result as $KEY => $VAL) {
            $Total[$KEY]["MONTHLY_TOTAL"] = 0;
            $Total[$KEY]["DISCOUNTED_MONTHLY_TOTAL"] = 0;
            $Total[$KEY]["OTC"] = 0;
            $Total[$KEY]["TENURE"] = $VAL["period"];
            $Total[$KEY]["TITLE"] = $VAL["estmtname"];
            $Total[$KEY]["TENURE_TOTAL"] = 0;
            $Total[$KEY]["DISCOUNTED_TENURE_TOTAL"] = 0;


            if (is_array($VAL)) {
                foreach ($VAL as $Key => $Val) {
                    if (is_array($Val)) {
                        foreach ($Val as $key => $val) {
                            if (!preg_match("/vm_/", $key)) {
                                $Total["_prices"][$KEY][$Key][$key] = $val["unit_price"];
                            }
                            if (isset($val["mrc"])) {
                                $Total[$KEY]["MONTHLY_TOTAL"] += floatval($val["mrc"]);
                                $Total[$KEY]["DISCOUNTED_MONTHLY_TOTAL"] += floatval($val["mrc"] - ($val["mrc"] * (floatval($val["discount"]) / 100)));
                                if (!is_null($Sku_Data)) $Sku_Data[$KEY]["phase_total_recurring"] += floatval($val["mrc"] - ($val["mrc"] * (floatval($val["discount"]) / 100)));
                                if (floatval($val["otc"]) > 0) {
                                    $Total[$KEY]["OTC"] += floatval($val["otc"]);
                                }
                            }
                            if ($Key == "managed") {
                                $Total[$KEY]["MANAGED"][] = floatval($val["mrc"]);
                                // $Total[$KEY]["DISCOUNTED_MANAGED"][] = floatval($val["discount"]) / 100;
                                $Total[$KEY]["DISCOUNTED_MANAGED"][] = $val["mrc"] - ($val["mrc"] * (floatval($val["discount"]) / 100));
                                continue;
                            }
                            if (isset($val["mrc"])) {
                                $Total[$KEY]["INFRASTRUCTURE"][] = floatval($val["mrc"]);
                                $Total[$KEY]["DISCOUNTED_INFRASTRUCTURE"][] = floatval($val["mrc"] - ($val["mrc"] * (floatval($val["discount"]) / 100)));
                            } else {
                                foreach ($val as $_K => $_V) {
                                    $Total[$KEY]["INFRASTRUCTURE"][] = floatval($_V["mrc"]);
                                    $Total[$KEY]["DISCOUNTED_INFRASTRUCTURE"][] = floatval($_V["mrc"] - ($_V["mrc"] * (floatval($_V["discount"]) / 100)));
                                    $Total[$KEY]["MONTHLY_TOTAL"] += floatval($_V["mrc"]);
                                    $Total[$KEY]["DISCOUNTED_MONTHLY_TOTAL"] += floatval($_V["mrc"] - ($_V["mrc"] * (floatval($_V["discount"]) / 100)));
                                }
                            }
                        }
                    }
                }
            }
            if ($Total[$KEY]["MONTHLY_TOTAL"] > 0) {
                $Total[$KEY]["TENURE_TOTAL"] = $Total[$KEY]["MONTHLY_TOTAL"] * $VAL["period"];
                $Total[$KEY]["DISCOUNTED_TENURE_TOTAL"] = $Total[$KEY]["DISCOUNTED_MONTHLY_TOTAL"] * $VAL["period"];

                if (isset($Other["TENURE_TOTAL"])) {
                    $Other["TENURE_TOTAL"] +=  $Total[$KEY]["MONTHLY_TOTAL"] * $VAL["period"];
                }
            }
            if (isset($VAL["otc"]["otc"])) {
                $percentage = $VAL["otc"]["otc"]["qty"];
                if ($percentage > 0) {
                    $otc = ($Total[$KEY]["MONTHLY_TOTAL"] * 12) *    ($percentage / 100);
                    $Result[$KEY]["otc"]["otc"]["otc"] = $otc;
                    $Result[$KEY]["otc"]["otc"]["unit_price"] = $otc;
                    $Result[$KEY]["otc"]["otc"]["qty"] = 1;
                    $Total[$KEY]["OTC"] +=  $otc;
                }
            }
        }
    }

    public function UpdateResultDiscount(&$Result)
    {

        if (!is_null($this->edit_id)) {
            $Data = $this->DiscountDataObject();
            if (!is_null($Data)) {
                foreach ($Result as $KEY => $VAL) {
                    $Result[$KEY]["percentage"] = floatval($Data->value("$KEY.percentage")) * 100;
                    if (is_array($VAL)) {
                        foreach ($VAL as $Key => $Val) {
                            if (is_array($Val)) {
                                foreach ($Val as $key => $val) {
                                    if (isset($val['mrc'])) {
                                        if (preg_match("/vm_/", $key)) continue;
                                        $Result[$KEY][$Key][$key]["discount"] = $Data->value("$KEY.Data.$Key.$key") ?? 0;
                                    } else {
                                        foreach ($val as $_K => $_V) {
                                            $Discount = $Data->value("$KEY.Data.$key.$_K");
                                            $Result[$KEY][$Key][$key][$_K]["discount"] = $Discount ?? 0;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    private function DiscountDataObject()
    {
        try {
            $jsonData = DiscountData::where("quot_id", $this->edit_id)->get()->toArray();
            return new GetFormDataService($jsonData[0]["discounted_data"]);
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

    private function JSON_TEMPLATE($Sku_Data)
    {
        $template = [
            "quotation_name" =>  $this->Request['project_name'],
            "opportunity_id" => (strlen($this->Request['pot_id']) < 5) ? "0" .  $this->Request['pot_id'] :  $this->Request['pot_id'],
            "quotation_id"   => '',
            "price_list"     =>  $this->Request['product_list'],
            "rate_card_id"   =>  $this->Request['product_list'],
            "user_id"        => intval(session("user")["crm_user_id"]),
            "phase_name"     => []
        ];

        $date = new \DateTime();
        $date->setTimezone(new \DateTimeZone("Asia/Kolkata"));
        $date = $date->format('Y-m-d');
        $p = 1;
        $pCount = 0;
        foreach ($Sku_Data as $KEY => $VAL) {
            if (is_array($VAL)) {
                $template['phase_name'][$pCount]["phase"]                 = $VAL["phase_name"];
                $template['phase_name'][$pCount]["phase_start_date"]      = $date;
                $template['phase_name'][$pCount]["phase_tenure_month"]    = floatval($VAL["phase_tenure"]);
                $template['phase_name'][$pCount]["phase_total_recurring"] = floatval($VAL["phase_total_recurring"]) * floatval($VAL["phase_tenure"]);
                $template['phase_name'][$pCount]["phase_total_otp"]       = 0;
                if (is_array($VAL["groups"])) {
                    foreach ($VAL as $Key => $Val) {
                        if (is_array($Val)) {
                            $gCount = 0;
                            foreach ($Val as $key => $val) {
                                $template['phase_name'][$pCount]["group_name"][$gCount]['quotation_group_name']  = empty($val["group_name"]) ? "group" : $val["group_name"];
                                $template['phase_name'][$pCount]["group_name"][$gCount]['group_otp_price']       = 0;
                                $template['phase_name'][$pCount]["group_name"][$gCount]['group_recurring_price'] = 1;
                                $template['phase_name'][$pCount]["group_name"][$gCount]['group_quantity']        = (floatval($val["group_quantity"]) > 0) ? $val["group_quantity"]  : 1;
                                $template['phase_name'][$pCount]["group_name"][$gCount]['group_id']              = floatval($val["group_id"]);
                                if (is_array($val["products"])) {
                                    $iCount = 0;
                                    foreach ($val as $_K => $_V) {
                                        if (is_array($_V)) {
                                            foreach ($_V as $_k => $_v) {
                                                if ($_v["qty"] > 0) {
                                                    $QTY = $_v["qty"];
                                                    $IS_BILLABLE = true;
                                                } else {
                                                    $QTY = 1;
                                                    $IS_BILLABLE = false;
                                                }
                                                $SKU = $_v["sku_code"];
                                                $UNIT_PRICE = floatval($_v["unit_price"]);
                                                $OTC = $_v["otc"] ?? 0;
                                                $DISCOUNT = ($OTC == 0) ? ($_v["discount"] ?? 0) : 0;
                                                $OTC_DISCOUNT = ($OTC != 0) ? ($_v["discount"] ?? 0) : 0;
                                                $template['phase_name'][$pCount]["group_name"][$gCount]['products'][$iCount]['product_sku']      = $SKU;
                                                $template['phase_name'][$pCount]["group_name"][$gCount]['products'][$iCount]['product_quantity'] = $QTY;
                                                $template['phase_name'][$pCount]["group_name"][$gCount]['products'][$iCount]['product_price']    = $UNIT_PRICE;
                                                $template['phase_name'][$pCount]["group_name"][$gCount]['products'][$iCount]['product_discount'] = $DISCOUNT;
                                                $template['phase_name'][$pCount]["group_name"][$gCount]['products'][$iCount]['otp_price']        = $OTC;
                                                $template['phase_name'][$pCount]["group_name"][$gCount]['products'][$iCount]['discount_otp']     = $OTC_DISCOUNT;
                                                $template['phase_name'][$pCount]["group_name"][$gCount]['products'][$iCount]['is_billable']      = intval($IS_BILLABLE);
                                                $iCount += 1;
                                            }
                                        }
                                    }
                                }
                                $gCount += 1;
                            }
                        }
                    }
                }
                $pCount += 1;
            }
        }
        return $template;
    }
}
