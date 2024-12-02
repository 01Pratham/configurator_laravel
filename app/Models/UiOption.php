<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\ModifyDatesInFormat;

class UiOption extends Model
{
    use HasFactory;
    protected $table = "tbl_ui_options";
    use ModifyDatesInFormat;
}
