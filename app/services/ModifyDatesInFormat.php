<?php

namespace App\Services;

use Carbon\Carbon;

trait ModifyDatesInFormat
{
    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('d-m-Y H:i:s');
    }

    // Accessor for updated_at
    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('d-m-Y H:i:s');
    }
}
