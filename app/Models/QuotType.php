<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\ModifyDatesInFormat;

class QuotType extends Model
{
    use HasFactory;
    protected $table = "tbl_quot_type";
    use ModifyDatesInFormat;
}
