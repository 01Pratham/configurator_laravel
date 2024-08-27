<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\LoginMaster;
use App\Models\SavedEstimate;
use Illuminate\Http\Request;
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

    private function errorResponse($message)
    {
        $status = "danger";
        $msg = $message;
        $redirect = route("SavedEstimates");

        return view("layouts.action-alert", compact("status", "msg", "redirect"));
    }
}
