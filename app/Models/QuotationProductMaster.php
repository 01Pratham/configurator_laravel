<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuotationProductMaster extends Model
{

    use \App\Services\ModifyDatesInFormat;

    protected $table = 'tbl_quotation_product_master';
    protected $fillable = [
        'product_name',
        'product_id',
        'group_id',
        'quantity',
        'unit_price',
        'mrc_price',
        'otc_price',
        'dicount_percentage',
        "added_by",
        'is_billable',
        'is_deleted'
    ];

    use HasFactory;

    public function group()
    {
        return $this->belongsTo(QuotationGroupMaster::class, 'group_id');
    }
}
