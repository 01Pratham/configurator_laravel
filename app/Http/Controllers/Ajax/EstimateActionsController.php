<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models\ProductList;
use App\Models\ProjectMaster;
use Illuminate\Http\Request;
use App\Services\QuotationFunctionService;

class EstimateActionsController extends Controller
{
    use QuotationFunctionService;

    public array $tc_array = [];

    public int $listID;

    public function CreateEstimate(Request $request)
    {

        $validatedData = $request->validate([
            'emp_id' => 'required|integer',
            'data' => 'required|string',
            'tc' => 'required|string',
        ]);

        try {
            $user_id = $validatedData['emp_id'];
            $data = json_decode(base64_decode($validatedData['data']), true);
            $this->tc_array = json_decode(base64_decode($validatedData['tc']), true);

            // Create Project
            $project = $this->createProject($data);

            // Calculate Prices
            $total_mrc = $this->calculateRecursively($data, "mrc");
            $total_otc = $this->calculateRecursively($data, "otc");

            // Process Data
            $quotations = $this->processData($data, $project, $user_id, $total_mrc, $total_otc);

            // Set Session
            session(["edit_id" => $quotations->id]);

            return response()->json([
                "status" => 200,
                "message" => "Data has been stored successfully",
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "status" => 500,
                "message" => "An error occurred: " . $e->getMessage(),
            ]);
        }
    }


    private function createProject(array $data)
    {
        return ProjectMaster::create([
            "project_pot_id" => $data["pot_id"],
            "project_name" => $data["project_name"],
        ]);
    }

    private function processData($data, $project, $user_id, $total_mrc, $total_otc)
    {
        foreach ($data as $key => $val) {
            if (is_array($val)) {
                $quotations = $this->createQuotation($project, $data, $user_id, $total_mrc, $total_otc);

                foreach ($val as $phaseData) {
                    $phase = $this->createPhase($quotations, $phaseData, $user_id);

                    foreach ($phaseData as $groupKey => $groupData) {
                        if (is_array($groupData)) {
                            if ($groupKey == "vm") {
                                foreach ($groupData as $VmKey => $VmArr) {
                                    $group = $this->createGroup($phase, $groupKey, $VmArr, $user_id);
                                }
                            } else {
                                $group = $this->createGroup($phase, $groupKey, $groupData, $user_id);
                            }
                            $this->createItems($group, $groupData, $user_id);
                        }
                    }
                }
            }
        }
        return $quotations;
    }

    private function createQuotation($project, $data, $user_id, $total_mrc, $total_otc)
    {
        $this->listID = $data["price_list_id"];
        return $project->quotations()->create([
            "owner" => $user_id,
            "last_changed_by" => $user_id,
            "quotation_name" => $data["quotation_name"],
            "price_list_id" => $data["price_list_id"],
            "total_selling_price" => $total_mrc,
            "total_discounted_selling_price" => null,
            "total_otc_price" => $total_otc ?? 0,
            "total_discounted_otc_price" => null,
            "total_discount_percentage" => null,
            "discount_approval_status" => "NA",
            "terms" => json_encode($this->tc_array["terms"]),
            "assumptions" =>  json_encode($this->tc_array["assumptions"]),
            "exculsions" =>  json_encode($this->tc_array["exculsions"]),
        ]);
    }

    private function createPhase($quotation, $phaseData, $user_id)
    {
        return $quotation->phases()->create([
            "phase_name" => $phaseData["estmtname"],
            "phase_duration" => $phaseData["period"],
            "region_id" => $phaseData["region"],
            "created_by" => $user_id,
        ]);
    }

    private function createGroup($phase, $key, $groupData, $user_id)
    {
        $primaryCategory = ($key == "vm") ? "virtual_machine" : $key;
        $crm_group_id = ProductList::where("primary_category", $primaryCategory)
            ->value("crm_group_id");

        return $phase->groups()->create([
            "group_name" => ($key == "vm") ? $groupData["service"] : ucfirst($key),
            "crm_group_id" => $crm_group_id,
            "group_quantity" => ($key == "vm") ? $groupData["qty"] : 1,
            "created_by" => $user_id,
        ]);
    }

    private function createItems($group, $groupData, $user_id)
    {
        foreach ($groupData as $itemData) {
            if (isset($itemData["prod_int"])) {
                if (is_array($itemData["prod_int"])) {
                    $this->handleVmItems($group, $itemData, $user_id);
                } else {
                    $this->createItemEntry($group, $itemData, $user_id);
                }
            }
        }
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
        $product = ProductList::where("prod_int", $itemData["prod_int"])->first();

        $group->items()->create([
            "product_name" => $product?->product,
            "product_id" => $product?->id,
            "quantity" => $itemData["qty"] ?? 1,
            "unit_price" => $itemData["unit_price"] ?? 0,
            "mrc_price" => $itemData["mrc"] ?? 0,
            "otc_price" => $itemData["otc"] ?? 0,
            "dicount_percentage" => $itemData["discount"] ?? null,
            "added_by" => $user_id,
            "is_billable" => 1,
        ]);
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
