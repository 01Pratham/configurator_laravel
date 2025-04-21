<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectQuotationMaster extends Model
{

    use \App\Services\ModifyDatesInFormat;

    protected $table = 'tbl_project_quotation_master';
    protected $fillable = [
        "user_id",
        'project_id',
        "quotation_name",
        'owner',
        'last_changed_by',
        'total_selling_price',
        'total_discounted_selling_price',
        "total_otc_price",
        "total_discounted_otc_price",
        'total_discount_percentage',
        'price_list_id',
        "discount_approval_status",
        "discount_approved_by",
        "discount_rejection_remark",
        'terms',
        'assumptions',
        'exculsions',
        'is_deleted'
    ];

    use HasFactory;
    public function phases()
    {
        return $this->hasMany(QuotationPhaseMaster::class, 'quotation_id')->where('is_deleted', 0);
    }

    public function project()
    {
        return $this->belongsTo(ProjectMaster::class, 'project_id');
    }
}
