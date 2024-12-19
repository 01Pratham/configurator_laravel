<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuotationPhaseMaster extends Model
{

    use \App\Services\ModifyDatesInFormat;

    protected $table = 'tbl_quotation_phase_master';
    protected $fillable = [
        'phase_name',
        'quotation_id',
        'phase_duration',
        'region_id',
        "created_by",
        'is_deleted'
    ];

    use HasFactory;

    public function groups()
    {
        return $this->hasMany(QuotationGroupMaster::class, 'phase_id');
    }

    public function quotation()
    {
        return $this->belongsTo(ProjectQuotationMaster::class, 'quotation_id');
    }
}
