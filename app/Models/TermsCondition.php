<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\ModifyDatesInFormat;

class TermsCondition extends Model
{
    use HasFactory;
    protected $table = "tbl_terms_conditions";
    use ModifyDatesInFormat;
}
