<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuotationGroupMaster extends Model
{
    use HasFactory;
    use \App\Services\ModifyDatesInFormat;

    protected $table = 'tbl_quotation_group_master';
    protected $fillable = [
        'group_name',
        'crm_group_id',
        'group_quantity',
        'created_by',
        'phase_id',
        'is_special',
        'is_deleted',
    ];

    protected $appends = ["products"];

    /**
     * Relationship with QuotationProductMaster
     */
    public function items()
    {
        return $this->hasMany(QuotationProductMaster::class, 'group_id');
    }

    /**
     * Relationship with QuotationPhaseMaster
     */
    public function phase()
    {
        return $this->belongsTo(QuotationPhaseMaster::class, 'phase_id');
    }

    protected $hidden = ["items"];
    /**
     * Accessor for transforming the 'items' relationship into a grouped array
     */
    public function getProductsAttribute()
    {
        // Fetch the items relationship (products associated with this quotation group)
        $items = $this->items()->get();

        // Transform the items array using secondary_category as the key
        $transformedItems = [];
        foreach ($items as $item) {
            $transformedItems[$item->secondary_category] = $item;
        }

        return $transformedItems;
    }
}
