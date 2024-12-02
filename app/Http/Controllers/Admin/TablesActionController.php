<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Services\AdminTableService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class TablesActionController extends Controller
{
    use AdminTableService;
    public string $table_name;

    public function Create()
    {
        return "he";
    }
    public function Update(string $table_name, int $id, Request $request)
    {
        try {
            $data = DB::table($table_name)->where('id', $id)->first();

            if ($data) {
                $entry = DB::table($table_name)->where('id', $id)->update($request->except(['act']));
                if ($entry) {
                    return response()->json(["message" => "Table updated successfully"], 201);
                } else {
                    return response()->json(['error' => 'Update unsuccessful'], 404);
                }
            } else {
                return response()->json(['error' => 'Data not found'], 404);
            }
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['error' => 'Table not found or Query Exception: ' . $e->getMessage()], 500);
        } catch (Exception $e) {
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
    public function  Delete()
    {
        return "he";
    }

    public function Render(string $table_name, int $id)
    {
        $action = "Render";
        $this->table_name = $table_name;
        $tbl = $this->tbl($this->table_name);

        try {
            $model = $this->getDataModel($tbl);

            if (!$model) {
                return response()->json(['error' => 'Model not found'], 404);
            }

            $structure = DB::select("DESCRIBE {$this->table_name}");
            $data = DB::table($this->table_name)->where('id', $id)->first();

            if ($data) {
                return view("admin.components.render_html", [
                    "structure" => $structure,
                    "data" => (array)$data,
                    "action" => $action,
                    "url" => "/Admin/Action/{$this->table_name}/{$id}/"
                ]);
            } else {
                return response()->json(['error' => 'Data not found'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred'], 500);
        }
    }
}
