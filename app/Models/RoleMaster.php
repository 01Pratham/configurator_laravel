<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\ModifyDatesInFormat;

class RoleMaster extends Model
{
    use HasFactory;
    protected $table = "tbl_role_master";
    use ModifyDatesInFormat;
}
