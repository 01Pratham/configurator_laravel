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
        'added_by',
        'is_billable',
        'is_deleted',
    ];

    use HasFactory;

    // Appending additional attributes for ease of use in your application
    protected $appends = ["prod_int", "secondary_category", "default_int"];

    /**
     * Relationship with QuotationGroupMaster
     */
    public function group()
    {
        return $this->belongsTo(QuotationGroupMaster::class, 'group_id');
    }

    /**
     * Relationship with ProductList
     */
    public function product()
    {
        return $this->belongsTo(ProductList::class, 'product_id', 'id')->where('is_deleted', 0);
    }

    /**
     * Accessor for 'prod_int' attribute
     */

    private function _Product()
    {
        try {
            return ProductList::where("id", $this->attributes["product_id"])->first();
        } catch (\Exception $e) {
            return null;
        }
    }
    public function getProdIntAttribute()
    {
        return $this->_Product()?->prod_int ?? "";
    }

    /**
     * Accessor for 'secondary_category' attribute
     */
    public function getSecondaryCategoryAttribute()
    {
        return $this->_Product()?->sec_category ?? "";
    }

    public function getDefaultIntAttribute()
    {
        return $this->_Product()?->default_int ?? "";
    }
}
