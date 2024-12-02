<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\ModifyDatesInFormat;

class ProductList extends Model
{
    use HasFactory;

    protected $table = "tbl_product_list";

    use ModifyDatesInFormat;
    protected $fillable = [
        "sku_code",
        "crm_group_id",
        "primary_category",
        "sec_category",
        "default_int",
        "default_name",
        "prod_int",
        "product",
    ];

    public function rateCardPrices()
    {
        return $this->hasMany(RateCardPrice::class, 'prod_id', 'id');
    }

    public static function getProdData($col, $list_id)
    {
        $Array = ProductList::select($col)
            ->distinct()
            ->join('tbl_rate_card_prices', 'tbl_rate_card_prices.prod_id', '=', 'tbl_product_list.id')
            ->where('tbl_rate_card_prices.rate_card_id', $list_id)
            ->where('tbl_rate_card_prices.is_active', 1)
            ->where('tbl_product_list.is_active', 1)
            ->orderBy('tbl_product_list.id', 'asc')
            ->get()
            ->toArray();

        return $Array;
    }

    public function getProdName($int)
    {
        $Data = self::select("product")->where("prod_int", $int)->get();
        try {
            return $Data->product;
        } catch (Exception $e) {
            return "";
        }
    }
}
