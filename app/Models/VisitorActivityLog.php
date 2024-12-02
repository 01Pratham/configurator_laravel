<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\ModifyDatesInFormat;

class VisitorActivityLog extends Model
{
    use HasFactory;

    protected $table = "tbl_visitor_activity_logs";
    use ModifyDatesInFormat;
    protected $fillable = [
        "user_ip_address",
        "emp_code",
        "session_id",
        "uname",
        "page_url",
    ];
}
