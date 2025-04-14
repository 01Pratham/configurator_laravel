<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models\ProductList;
use App\Models\ProjectMaster;
use Illuminate\Http\Request;
use App\Services\QuotationFunctionService;
use PhpParser\Error;
use PhpParser\Node\Expr\Print_;

class EstimateActionsController extends Controller
{
    use QuotationFunctionService;

    private ?int $quotation_id = null;

    private array $tc_array = [];

    private int $listID;

    public function index(Request $request, int $id = null)
    {
        $validatedData = $request->validate(rules: [
            'emp_id' => 'required|integer',
            'data' => 'required|string',
            'tc' => 'required|string',
        ]);

        // try {
        $user_id = $validatedData['emp_id'];
        $data = json_decode(json: base64_decode(string: $validatedData['data']), associative: true);
        $this->tc_array = json_decode(json: base64_decode($validatedData['tc']), associative: true);
        $this->quotation_id = $id;

        // Create Project
        $project = $this->createProject(data: $data);

        // Calculate Prices
        $total_mrc = $this->calculateRecursively(data: $data, key: "mrc");
        $total_otc = $this->calculateRecursively(data: $data, key: "otc");

        // Process Data
        $quotations = $this->processData(data: $data, project: $project, user_id: $user_id, total_mrc: $total_mrc, total_otc: $total_otc);

        // Set Session
        session(key: ["edit_id" => $quotations->id]);

        return response()->json(data: [
            "status" => 200,
            "message" => "Data has been stored successfully",
        ]);
        // } catch (\Exception $e) {
        //     $data = json_decode(json: base64_decode(string: $validatedData['data']), associative: true);
        //     return response()->json(data: [
        //         "status" => 500,
        //         "message" => "An error occurred: " . $e->getMessage(),
        //         "data" => $data
        //     ]);
        // }
    }


    private function createProject(array $data)
    {
        return ProjectMaster::firstOrCreate(
            ["project_pot_id" => $data["pot_id"]],
            [
                "project_name" => $data["project_name"],
                "project_pot_id" => $data["pot_id"],
            ]
        );
    }
    private function processData($data, $project, $user_id, $total_mrc, $total_otc)
    {
        $phaseIdsInRequest = [];
        foreach ($data as $val) {
            if (is_array($val)) {
                $quotations = $this->CreateOrUpdateQuotation($project, $data, $user_id, $total_mrc, $total_otc);
                foreach ($val as $phaseData) {
                    if (is_array($phaseData)) {
                        try {
                            $phase = $this->CreateOrUpdatePhase($quotations, $phaseData, $user_id);
                            $phaseIdsInRequest[] = $phase->id;
                        } catch (\Exception $e) {
                            throw new Error(json_encode([$e->getMessage(), $phaseData, $e->getLine()]));
                        }
                        if (isset($phaseData["groups"])) {
                            $groupIdsInRequest = [];
                            foreach ($phaseData["groups"] as $groupKey => $groupData) {
                                if (is_array($groupData)) {
                                    try {
                                        $group = $this->CreateOrUpdateGroup($phase, $groupKey, $groupData, $user_id);
                                        $groupIdsInRequest[] = $group->id;
                                    } catch (\Exception $e) {
                                        throw new Error(json_encode([$e->getMessage(), $groupData, $e->getLine()]));
                                    }
                                    if (isset($groupData["products"])) {
                                        $itemIdsInRequest = [];
                                        foreach ($groupData["products"] as $itemKey => $itemData) {
                                            if (is_array($itemData)) {
                                                try {
                                                    $item = $this->CreateOrUpdateItem($group, $itemKey, $itemData, $user_id);
                                                    $itemIdsInRequest[] = $item->id;
                                                } catch (\Exception $e) {
                                                    throw new Error(json_encode([$e->getMessage(), $itemData, $e->getLine()]));
                                                }
                                            }
                                        }
                                        $group->items()->whereNotIn('id', $itemIdsInRequest)->update(['is_deleted' => 1]);
                                    }
                                }
                            }
                            $phase->groups()->whereNotIn('id', $groupIdsInRequest)->update(['is_deleted' => 1]);
                        }
                    }
                }
            }
        }
        $quotations->phases()->whereNotIn('id', $phaseIdsInRequest)->update(['is_deleted' => 1]);
        return $quotations;
    }

    private function CreateOrUpdateQuotation($project, $data, $user_id, $total_mrc, $total_otc)
    {
        // echo $project->quotations()->where('id', $this->quotation_id)->first();
        if (!isset($data["price_list_id"])) {
            throw new \ErrorException(json_encode($data));
        }
        $this->listID = $data["price_list_id"];

        $quotation = $project->quotations()->where('id', $this->quotation_id)->first();
        $quotationData = [
            "user_id" => $user_id,
            "owner" => $user_id,
            "last_changed_by" => $user_id,
            "quotation_name" => $data["quotation_name"],
            "price_list_id" => $data["price_list_id"],
            "total_selling_price" => $total_mrc,
            "total_otc_price" => $total_otc ?? 0,
            "terms" => json_encode($this->tc_array["terms"]),
            "assumptions" => json_encode($this->tc_array["assumptions"]),
            "exculsions" => json_encode($this->tc_array["exculsions"]),
        ];

        if ($quotation) {
            $updated = false;
            foreach ($quotationData as $key => $value) {
                if ($quotation->{$key} !== $value) {
                    $quotation->{$key} = $value;
                    $updated = true;
                }
            }
            if ($updated) $quotation->save();

            return $quotation;
        }
        return $project->quotations()->create($quotationData);
    }

    private function CreateOrUpdatePhase($quotation, $phaseData, $user_id)
    {
        $Data = [
            "phase_name" => $phaseData["phase_name"],
            "phase_duration" => $phaseData["phase_tenure"],
            "region_id" => $phaseData["region"],
            "created_by" => $user_id,
        ];
        // print_r($phaseData);
        $phase = $quotation->phases()->where('id', $phaseData["conf_phase_id"])->first();
        if ($phase) {
            $updated = false;
            foreach ($Data as $key => $value) {
                if ($phase->{$key} !== $value) {
                    $phase->{$key} = $value;
                    $updated = true;
                }
            }
            if ($updated) $phase->save();

            return $phase;
        }
        return $quotation->phases()->create($Data);
    }

    private function CreateOrUpdateGroup($phase, $key, $groupData, $user_id)
    {
        // print_r($groupData);
        $group_id = null;
        if (isset($groupData["conf_group_id"]) && $groupData["conf_group_id"] != null) {
            $group_id = $groupData["conf_group_id"];
        }
        $group = $phase->groups()->where('id', $group_id)->first();
        $Data = [
            "group_name" => preg_match("/vm_.*/", $key) ? $groupData["group_name"] : ucfirst($key),
            "crm_group_id" => $groupData["group_id"],
            "group_quantity" => $groupData["group_quantity"],
            "is_special" => preg_match("/vm|block_storage/", $key) ? preg_replace("/vm_.*/", "vm", $key) : "",
            "created_by" => $user_id,
        ];
        if ($group) {
            $updated = false;
            foreach ($Data as $key => $value) {
                if ($group->{$key} !== $value) {
                    $group->{$key} = $value;
                    $updated = true;
                }
            }
            if ($updated) $group->save();

            return $group;
        }
        return $phase->groups()->create($Data);
    }

    private function CreateOrUpdateItem($group, $itemKey, $itemData, $user_id)
    {
        $exitsted_product = $group->items()
            ->select("tbl_quotation_product_master.*")
            ->join("tbl_product_list", "tbl_product_list.id", "=", "tbl_quotation_product_master.product_id")
            ->where("tbl_product_list.prod_int", $itemData["prod_int"])
            ->first();

        $product = ProductList::where("prod_int", $itemData["prod_int"])->first();
        $productData = [
            "product_name" => $exitsted_product?->product_name ?? $product?->product,
            "product_id" => $exitsted_product?->product_id ?? $product?->id,
            "quantity" => $itemData["qty"] ?? 1,
            "unit_price" => $itemData["unit_price"] ?? 0,
            "mrc_price" => $itemData["mrc"] ?? 0,
            "otc_price" => $itemData["otc"] ?? 0,
            "discount_percentage" => $itemData["discount"] ?? null,
            "added_by" => $user_id,
            "is_billable" => 1,
        ];

        if ($exitsted_product) {
            foreach ($productData as $key => $value) {
                if ($product->{$key} !== $value) {
                    $product->{$key} = $value;
                }
            }
            $exitsted_product->save();
            return $exitsted_product;
        }

        return $group->items()->create($productData);
    }

    private function handleVmItems($group, $itemData, $user_id)
    {
        $vmarr = $this->getVm($itemData["product"]);
        foreach ($itemData["prod_int"] as $name => $int) {
            if (!preg_match("/os|db/", $int)) {
                try {
                    $this->createItemEntry($group, [
                        "prod_int" => $int,
                        "qty" => $vmarr[$name],
                        "unit_price" => $this->getVmPrice($vmarr, false)[$name],
                        "mrc" => $this->getVmPrice($vmarr, false)[$name] * $vmarr[$name],
                        "otc" => 0,
                        "discount" => 0,
                    ], $user_id);
                } catch (\Exception) {
                }
            }
        }
    }

    private function createItemEntry($group, $itemData, $user_id)
    {
        $exitsted_product = $group->items()
            ->select("tbl_quotation_product_master.*")
            ->join("tbl_product_list", "tbl_product_list.id", "=", "tbl_quotation_product_master.product_id")
            ->where("tbl_product_list.prod_int", $itemData["prod_int"])
            ->first();

        $product = ProductList::where("prod_int", $itemData["prod_int"])->first();
        $productData = [
            "product_name" => $exitsted_product?->product_name ?? $product?->product,
            "product_id" => $exitsted_product?->product_id ?? $product?->id,
            "quantity" => $itemData["qty"] ?? 1,
            "unit_price" => $itemData["unit_price"] ?? 0,
            "mrc_price" => $itemData["mrc"] ?? 0,
            "otc_price" => $itemData["otc"] ?? 0,
            "discount_percentage" => $itemData["discount"] ?? null,
            "added_by" => $user_id,
            "is_billable" => 1,
        ];

        if ($exitsted_product) {
            foreach ($productData as $key => $value) {
                if ($product->{$key} !== $value) {
                    $product->{$key} = $value;
                }
            }
            $exitsted_product->save();
            return $exitsted_product;
        }

        return $group->items()->create($productData);
    }

    private function calculateRecursively($data, $key)
    {
        $total = 0;
        if (is_array($data)) {
            foreach ($data as $item) {
                if (isset($item[$key]) && is_numeric($item[$key])) {
                    $total += $item[$key];
                } else {
                    $total += $this->calculateRecursively($item, $key);
                }
            }
        }

        return $total;
    }

    private function errorResponse($message)
    {
        return view("layouts.action-alert", [
            "status" => "danger",
            "msg" => $message,
            "redirect" => route("SavedEstimates")
        ]);
    }
}
