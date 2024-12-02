<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use App\Models\LoginMaster;
use App\Services\ModifyDatesInFormat;

class SavedEstimate extends Model
{
    use HasFactory;
    protected $table = "tbl_saved_estimates";
    use ModifyDatesInFormat;

    public function getTotalUpfrontAttribute()
    {
        return INR($this->attributes['total_upfront']);
    }
}
