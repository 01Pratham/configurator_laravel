<?php

namespace App\Models;

use App\Services\ModifyDatesInFormat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssociativeProducts extends Model
{
    use HasFactory;
    protected $table = "tbl_associative_products";

    public function productList()
    {
        return $this->belongsTo(ProductList::class, 'prod_id', 'id');
    }
    use ModifyDatesInFormat;

    public static function getAssciatedProducts($prod_id)
    {
        $products = self::where("prod_id", $prod_id)->get();
        $productsArray = $products->map(function ($item) {
            $prd = explode(',', $item->associative_products);
            foreach ($prd as $prd_id) {
                return ProductList::where("id", $prd_id)->get()->toArray();
            }
        });
        return $productsArray;
    }
}
