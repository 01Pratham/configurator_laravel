<?php

namespace App\Services;

use App\Models\IndexTables;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

trait AdminTableService
{
    private function tbl(string $table_name)
    {
        $tbl = preg_replace('/^tbl_/', '', $table_name);
        $tbl = str_replace('_', ' ', $tbl);
        return $tbl ?? "";
    }
    private function getDataModel(string $tbl)
    {
        $n_tbl = Str::studly($tbl);

        $modelClass = 'App\\Models\\' . $n_tbl;
        $class_exist = class_exists($modelClass) ?
            $modelClass : (
                class_exists(substr_replace($modelClass, "", -1)) ?
                substr_replace($modelClass, "", -1) :
                false
            );

        if ($class_exist) {
            $model = app($class_exist);
            if ($model instanceof \Illuminate\Database\Eloquent\Model) {
                return $model;
            } else {
                abort(404, "Model class does not extend Eloquent: $modelClass");
            }
        } else {
            abort(404, "Model not found for table: $this->table_name");
        }
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
