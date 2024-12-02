<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\IndexTables;
use App\Services\AdminTableService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TablesDataController extends Controller
{
    use AdminTableService;
    private string $table_name;
    private int $limit = 10;

    public function index($table_name, Request $request)
    {
        $this->table_name = $table_name;

        $content_header = [
            "Admin" => route("AdminDashboard"),
            ucwords($this->tbl($this->table_name)) => route("TableData", ["table_name" => $this->table_name])
        ];

        $table_body = $this->selectDataFromModel($this->tbl($this->table_name));

        $searchable = [
            "key" => "*",   
            "class" => "*"
        ];
        $table_head = $this->get_table_headers($table_body);

        $foreignKeys = $this->getForeignKeys($this->table_name);

        return view("admin.pages.master-table-layoutes", compact(
            "content_header",
            "searchable",
            "table_body",
            "table_head",
        ));
    }


    private function selectDataFromModel(string $tbl)
    {
        $foreignKeys = $this->getForeignKeys($this->table_name);
        $data = $this->getDataModel($tbl)::paginate($this->limit);
        foreach ($data as $item) {

            foreach ($foreignKeys as $foreignKey) {
                $column = $foreignKey['column'];
                if (isset($item->$column)) {
                    try {
                        $referencedTable = $foreignKey['referenced_table'];
                        $referencedColumn = $foreignKey['referenced_column'];
                        $referencedData = DB::table($referencedTable)
                            ->where($referencedColumn, $item->$column)
                            ->first();

                        if ($referencedData) {
                            $indexColumn = $this->getIndexColumn($referencedTable);
                            $newValue = $referencedData->$indexColumn;
                            $this->replace_attribute($item, $column, $indexColumn, $newValue);
                        }
                    } catch (Exception) {
                    }
                }
            }
            $item->action = [
                [
                    "name" => "Edit",
                    "path" => "/Admin/Action/{$this->table_name}/{$item->id}/Render",
                    "icon" => "fa fa-edit",
                ],
                [
                    "name" => "Delete",
                    "path" => "/Admin/Action/{$this->table_name}/{$item->id}/Delete",
                    "icon" => "fa fa-trash",
                ],
            ];
        }
        return $data;
    }

    protected function getForeignKeys($table_name)
    {
        $foreignKeys = [];
        $results = DB::select("
            SELECT
                COLUMN_NAME,
                REFERENCED_TABLE_NAME,
                REFERENCED_COLUMN_NAME AS referenced_column
            FROM
                INFORMATION_SCHEMA.KEY_COLUMN_USAGE
            WHERE
                TABLE_NAME = :table_name
                AND REFERENCED_TABLE_NAME IS NOT NULL
        ", ['table_name' => $table_name]);

        foreach ($results as $result) {
            $foreignKeys[] = [
                'column' => $result->COLUMN_NAME,
                'referenced_table' => $result->REFERENCED_TABLE_NAME,
                'referenced_column' => $result->referenced_column,
            ];
        }
        return $foreignKeys;
    }

    private function getIndexColumn(string $table)
    {
        try {
            $col = IndexTables::where("table_name", $table)->first();
            return $col->index_column;
        } catch (Exception) {
            return "";
        }
    }
    private function get_table_headers($data)
    {
        $arr = $data->toArray();
        $head = [];
        if (!empty($arr)) {
            $head = array_keys($arr["data"][0]);
        }
        return $head;
    }
    private function replace_attribute($item, string $oldKey, string $newKey, $newValue)
    {
        $attributes = $item->getAttributes();
        $newAttributes = [];
        foreach ($attributes as $key => $value) {
            if ($key === $oldKey) {
                $newAttributes[$newKey] = $newValue;
            } else {
                $newAttributes[$key] = $value;
            }
        }
        $item->setRawAttributes($newAttributes);
    }
}
