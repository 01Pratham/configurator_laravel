<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\ModifyDatesInFormat;

class UnitMap extends Model
{
    use HasFactory;

    protected $table = "tbl_unit_map";
    use ModifyDatesInFormat;


    public function units()
    {
        return $this->hasMany(Unit::class, 'id', 'unit_id');
    }

    public static function getProductUnit($id)
    {
        $unitMapRecords = self::where('prod_id', $id)->get();
        $unitIds = $unitMapRecords->pluck('unit_id');
        $units = Unit::whereIn('id', $unitIds)->get();

        if ($units->isEmpty()) {
            return [
                ['id' => 0, 'unit_name' => 'NO']
            ];
        }

        return $units;
    }
}
