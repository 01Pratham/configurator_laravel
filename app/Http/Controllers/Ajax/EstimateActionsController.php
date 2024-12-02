<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models\DiscountData;
use App\Models\LoginMaster;
use App\Models\SavedEstimate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class EstimateActionsController extends Controller
{
    public function Delete(Request $request, $_id)
    {
        $validator = Validator::make(['_id' => $_id], [
            '_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse("Invalid ID provided.");
        }

        $id = SavedEstimate::where("id", $_id)->update(["is_deleted" => 1]);
        $status = $id ? "success" : "danger";
        $msg = $id ? "Estimate Deleted Successfully" : "Error! While deleting the estimate";
        $redirect = route("SavedEstimates");

        return view("layouts.action-alert", compact("status", "msg", "redirect"));
    }

    public function Clone(Request $request, $_id)
    {
        $validator = Validator::make(['_id' => $_id], [
            '_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse("Invalid ID provided.");
        }

        $est_id = null;
        $est = SavedEstimate::where("id", $_id)
            ->where("is_deleted", false)
            ->get()
            ->toArray();
        if (isset($est[0])) {
            unset($est[0]["id"], $est[0]["created_at"], $est[0]["updated_at"]);
            $est_id = SavedEstimate::insertGetId([...$est[0]]);
        }

        $status = $est_id ? "success" : "danger";
        $msg = $est_id ? "Estimate Cloned Successfully" : "Error! While Cloning Estimate";
        $redirect = route("SavedEstimates");

        return view("layouts.action-alert", compact("status", "msg", "redirect"));
    }

    public function Share(Request $request, $_id)
    {
        $validator = Validator::make(['_id' => $_id], [
            '_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse("Invalid ID provided.");
        }

        $table_head = [
            "name" => 'NAME',
            "employee_code" => "EMPLOYEE CODE",
            "designation" => "DESIGNATION",
            "action" => 'ACTION',
        ];

        $table_body = [];

        $users = LoginMaster::select(["first_name", "last_name", "employee_code", "designation", "crm_user_id"])
            ->where("crm_user_id", "!=", session()->get('user')["crm_user_id"])
            ->get()
            ->toArray();
        $content_header = ["Saved Estimates" => route("SavedEstimates"), 'Users' => route('Users')];

        foreach ($users as $key => $user) {
            $table_body[$key] = arrange_keys($table_head, $user);
        }

        foreach ($table_body as $k => $arr) {
            $table_body[$k]["action"] = [
                [
                    "name" => "Share",
                    "path" => route("ShareEstimateToUser", ["user_id" => $arr["crm_user_id"], "_id" => $_id]),
                    "icon" => "fa fa-share-alt",
                ],
            ];
        }

        $exceptional_keys = ["crm_user_id"];

        $searchable = [
            "key" => "name",
            "class" => "name"
        ];
        return view("layouts.master-table-layoutes", compact("table_head", "table_body", "exceptional_keys", "searchable", "content_header"));
    }

    public function ShareToUser(Request $request, $user_id, $_id)
    {
        $validator = Validator::make(['_id' => $_id, 'user_id' => $user_id], [
            '_id' => 'required|integer',
            'user_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse("Invalid ID provided.");
        }

        $est_id = null;
        $est = SavedEstimate::where("id", $_id)
            ->where("is_deleted", false)
            ->get()
            ->toArray();
        if (isset($est[0])) {
            unset($est[0]["id"], $est[0]["created_at"], $est[0]["updated_at"], $est[0]["emp_code"]);
            $est_id  = SavedEstimate::insertGetId(["emp_code" => $user_id, ...$est[0]]);
        }

        $status = $est_id ? "success" : "danger";
        $msg = $est_id ? "Estimate Shared Successfully" : "Error! While Sharing Estimate";
        $redirect = route("SavedEstimates");

        return view("layouts.action-alert", compact("status", "msg", "redirect"));
    }

    public function CreateEstimate(Request $request)
    {
        try {
            $empCode = Session::get('user')["crm_user_id"];
            $data = base64_decode($request->input('data'));
            $prices = base64_decode($request->input('priceData'));
            $tc = json_decode(base64_decode($request->input('tc')), true);
            if (empty($tc)) {
                $tc = [
                    "terms" => [],
                    "exculsions" => [],
                    "assumptions" => [],
                ];
            }
            $est_id = SavedEstimate::insertGetId([
                'emp_code' => $empCode,
                'pot_id' => $request->input('pot_id'),
                'project_name' => $request->input('project_name'),
                'version' =>  $request->input('version', 1),
                'owner' => $empCode,
                'last_changed_by' => $empCode,
                'contract_period' => $request->input('period'),
                'total_upfront' => $request->input('total'),
                'discounted_upfront' => $request->input('discounted_upfront', '0'),
                'terms' => json_encode($tc['terms']),
                'exculsions' => json_encode($tc['exculsions']),
                'assumptions' => json_encode($tc['assumptions']),
                'data' => $data,
                'prices' => $prices,
            ]);
            Session::put('edit_id', $est_id);

            return response()->json([
                'Message' => 'Data Stored Successfully',
                'quotationID' => Session::get('edit_id')
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error while storing data: ' . $e->getMessage()], 500);
        }
    }
    public function UpdateEstimate(Request $request)
    {
        try {
            $empCode = Session::get('user')["crm_user_id"];
            $data = base64_decode($request->input('data'));
            $prices = base64_decode($request->input('priceData'));
            $tc = json_decode(base64_decode($request->input('tc')), true);

            if (empty($tc)) {
                $tc = [
                    "terms" => [],
                    "exculsions" => [],
                    "assumptions" => [],
                ];
            }

            SavedEstimate::where("id", Session::get("edit_id"))->update([
                'total_upfront' => $request->input('total', ''),
                'discounted_upfront' => $request->input('discounted_upfront', '0'),
                'data' => $data,
                'prices' => $prices,
                'pot_id' => $request->input('pot_id'),
                'project_name' => $request->input('project_name'),
                'contract_period' => $request->input('period', ''),
                'terms' => json_encode($tc['terms']),
                'exculsions' => json_encode($tc['exculsions']),
                'assumptions' => json_encode($tc['assumptions']),
                'last_changed_by' => $empCode,
            ]);
            return response()->json([
                'Message' => 'Data Stored Successfully',
                'quotationID' => Session::get('edit_id')
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error while storing data: ' . $e->getMessage()], 500);
        }
    }
    public function CreateDiscount(Request $request)
    {
        try {
            $data = $request->all();
            $est_id = DiscountData::updateOrInsert(
                ["quot_id" => $data['id'] ?? session("edit_id")],
                [
                    'quot_id' => $data['id'] ?? session("edit_id"),
                    'discounted_data' => $data['discountedData'],
                    'approved_status' => 'NA',
                    'discounted_mrc' => intval($data['discounted_upfront']),
                    'approved_by' => $data['approved_by'] ?? null,
                ]
            );

            return response()->json([
                'Message' => "Discount Update Successfully",
                'quotationID' => $data['id'] ?? session("edit_id"),
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error while storing data: ' . $e->getMessage()], 500);
        }
    }
    public function UpdateDiscountStatus(Request $request)
    {
        try {
            $data = $request->all();
            $est_id = DiscountData::where(["quot_id" => $data['id']])->update(
                [
                    'approved_status' => $data["status"],
                    'approved_by' => $data["approved_by"],
                ]
            );

            return response()->json([
                'Message' => "Discount Update Successfully",
                'quotationID' => $data['id'] ?? session("edit_id"),
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error while storing data: ' . $e->getMessage()], 500);
        }
    }

    private function errorResponse($message)
    {
        $status = "danger";
        $msg = $message;
        $redirect = route("SavedEstimates");

        return view("layouts.action-alert", compact("status", "msg", "redirect"));
    }
}
