<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class StoreInTableController extends Controller
{
    public function handleRequest(Request $request)
    {
        switch ($request->input('action')) {
            case 'Delete':
                return $this->deleteEstimate($request->input('id'));
            case preg_match("/Discount/", $request->input('action')) ? true : false:
                return $this->updateDiscountTbl($request->all());
            default:
                return $this->saveEstmt($request);
        }
    }

    private function deleteEstimate($id)
    {
        try {
            DB::transaction(function () use ($id) {
                DB::table('tbl_discount_data')->where('quot_id', $id)->delete();
                DB::table('tbl_saved_estimates')->where('id', $id)->delete();
            });

            return response()->json(['message' => 'Deleted'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    private function saveEstmt(Request $request)
    {
        $empCode = Session::get('user')["crm_user_id"];
        $projectId = $request->input('pot_id');
        $projectName = $request->input('project_name');
        $version = $request->input('version', 1);
        $date = now();
        $data = base64_decode($request->input('data'));
        $prices = base64_decode($request->input('priceData'));
        $tc = json_decode(base64_decode($request->input('tc')), true);

        if ($request->input('action') == 'save_as') {
            $version++;
        }

        try {
            if ($request->input('action') == 'Update') {
                $updatedData = [
                    'total_upfront' => $request->input('total', ''),
                    'discounted_upfront' => $request->input('discounted_upfront', '0'),
                    'data' => $data,
                    'prices' => $prices,
                    'pot_id' => $projectId,
                    'project_name' => $projectName,
                    'contract_period' => $request->input('period', ''),
                    'terms' => json_encode($tc['terms']),
                    'exculsions' => json_encode($tc['exculsions']),
                    'assumptions' => json_encode($tc['assumptions']),
                    'last_changed_by' => $empCode,

                ];

                DB::table('tbl_saved_estimates')
                    ->where('id', Session::get('edit_id'))
                    ->update($updatedData);
            } else {
                $newEstimate = [
                    'emp_code' => $empCode,
                    'pot_id' => $projectId,
                    'project_name' => $projectName,
                    'version' => $version,
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
                ];

                $insertId = DB::table('tbl_saved_estimates')->insertGetId($newEstimate);
                Session::put('edit_id', $insertId);
            }

            return response()->json([
                'Message' => 'Data Stored Successfully',
                'quotationID' => Session::get('edit_id')
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error while storing data: ' . $e->getMessage()], 500);
        }
    }

    private function updateDiscountTbl($data)
    {
        try {
            $existingEntry = DB::table('tbl_discount_data')
                ->where('quot_id', $data['id'] ?? session("edit_id"))
                ->first();

            if ($existingEntry) {
                if ($data['action'] == "UpdateDiscountingStatus") {
                    DB::table('tbl_discount_data')
                        ->where('quot_id', $data['id'] ?? session("edit_id"))
                        ->update([
                            'approved_status' => $data['status'],
                            'approved_by' => $data['approved_by'],
                            'remarks' => $data['remarks'] ?? null,
                        ]);
                } else {
                    DB::table('tbl_discount_data')
                        ->where('quot_id', $data['id'] ?? session("edit_id"))
                        ->update([
                            'discounted_data' => $data['discountedData'],
                            'discounted_mrc' => intval($data['discounted_upfront']),
                        ]);
                    DB::table('tbl_saved_estimates')
                        ->where('id', $data['id'] ?? session("edit_id"))
                        ->update([
                            'prices' => $data['prices'],
                        ]);
                }
            } else {
                DB::table('tbl_discount_data')->insert([
                    'quot_id' => $data['id'] ?? session("edit_id"),
                    'discounted_data' => $data['discountedData'],
                    'approved_status' => 'NA',
                    'discounted_mrc' => intval($data['discounted_upfront']),
                    'approved_by' => $data['approved_by'] ?? null,
                ]);
            }
            $status = $data['status'] ?? 'Updated';
            return response()->json([
                'Message' => "Discounting {$status} Successfully",
                'quotationID' => $data['id'] ?? session("edit_id"),
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error while storing data: ' . $e->getMessage()], 500);
        }
    }
}
