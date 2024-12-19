<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuotationGroupMaster extends Model
{

    use \App\Services\ModifyDatesInFormat;

    protected $table = 'tbl_quotation_group_master';
    protected $fillable = [
        'group_name',
        'crm_group_id',
        'group_quantity',
        'created_by',
        'phase_id',
        'is_deleted'
    ];

    use HasFactory;

    public function items()
    {
        return $this->hasMany(QuotationProductMaster::class, 'group_id');
    }

    public function phase()
    {
        return $this->belongsTo(QuotationPhaseMaster::class, 'phase_id');
    }
}
