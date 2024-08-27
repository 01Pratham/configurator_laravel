<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RateCard extends Model
{
    use HasFactory;

    protected $table = "tbl_rate_cards";

    protected $fillable = [
        "rate_card_name",
        "card_type",
        "listing",
        "created_by",
        "is_active",
    ];
}
