<?php

namespace App\Models;

use App\Services\ModifyDatesInFormat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Calculation extends Model
{
    use HasFactory;
    protected $table = "tbl_calculation";
    use ModifyDatesInFormat;

}
