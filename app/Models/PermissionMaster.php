<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\ModifyDatesInFormat;

class PermissionMaster extends Model
{
    use HasFactory;
    protected $table = "tbl_permission_master";
    use ModifyDatesInFormat;
}
