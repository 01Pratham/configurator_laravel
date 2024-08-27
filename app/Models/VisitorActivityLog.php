<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitorActivityLog extends Model
{
    use HasFactory;

    protected $table = "tbl_visitor_activity_logs";
    protected $fillable = [
        "user_ip_address",
        "emp_code",
        "session_id",
        "uname",
        "page_url",
    ];
}
