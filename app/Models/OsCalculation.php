<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\ModifyDatesInFormat;

class OsCalculation extends Model
{
    use HasFactory;

    protected $table = "tbl_os_calculation";
    use ModifyDatesInFormat;
}
