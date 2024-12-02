<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\LoginMaster;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function index()
    {
        $table_head = [
            "name" => 'NAME',
            "employee_code" => "EMPLOYEE CODE",
            "designation" => "DESIGNATION",
            "action" => 'ACTION',
        ];

        $table_body = [];

        $users = LoginMaster::select(["first_name", "last_name", "employee_code", "designation", "crm_user_id"])
            ->where("manager_code", session()->get('user')["crm_user_id"])
            ->get()
            ->toArray();
        $content_header = ['Users' => route('Users')];

        foreach ($users as $key => $user) {
            $table_body[$key] = arrange_keys($table_head, $user);
        }

        foreach ($table_body as $k => $arr) {
            $table_body[$k]["action"] = [
                [
                    "name" => "View Estimates",
                    "path" => route("SavedEstimates", $arr["crm_user_id"]),
                    "icon" => "",
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
}
