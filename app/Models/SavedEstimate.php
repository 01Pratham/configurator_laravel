<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use App\Models\LoginMaster;

class SavedEstimate extends Model
{
    use HasFactory;
    protected $table = "tbl_saved_estimates";

    public function getTotalUpfrontAttribute()
    {
        return INR($this->attributes['total_upfront']);
    }
}
