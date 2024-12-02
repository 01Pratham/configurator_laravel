<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\ModifyDatesInFormat;

class DiscountData extends Model
{
    use HasFactory;
    protected $table = "tbl_discount_data";
    use ModifyDatesInFormat;
}
