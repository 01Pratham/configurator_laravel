<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Exception;
use App\Services\ModifyDatesInFormat;

class RateCardPrice extends Model
{
    use HasFactory;

    protected $table = "tbl_rate_card_prices";
    private static $tbl = "tbl_rate_card_prices";
    use ModifyDatesInFormat;

    protected $fillable = [
        "prod_id",
        "rate_card_id",
        "input_price",
        "region_id",
        "price",
        "discountable_percentage",
        "otc",
        "input_otc",
        "discountable_otc",
    ];
    /**
     * Define the relationship with the ProductList model.
     */
    public function productList()
    {
        return $this->belongsTo(ProductList::class, 'prod_id', 'id');
    }

    /**
     * Get the product price based on product internal code, price list, and column name.
     *
     * @param string $prod_int
     * @param int $price_list
     * @param string $col
     * @return float
     */
    public static function getProductPrice($prod_int, $price_list, $col)
    {
        try {
            // Fetch the price using first() if expecting a single result
            $price = self::select($col)
                ->join("tbl_product_list", "tbl_product_list.id", "=", self::$tbl . ".prod_id")
                ->where("tbl_product_list.prod_int", $prod_int)
                ->where('tbl_product_list.is_active', 1)
                ->where(self::$tbl . ".rate_card_id", $price_list)
                ->first(); // Use first() for a single result

            // Return the price value or 0 if not found
            return $price ? $price->{$col} : 0;
        } catch (Exception $e) {
            // Log the exception for debugging
            Log::error("Error fetching product price: " . $e->getMessage());
            return 0; // Return 0 in case of an exception
        }
    }
}
