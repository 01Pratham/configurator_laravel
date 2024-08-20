<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssociativeProduct extends Model
{
    use HasFactory;

    protected $table = "tbl_associative_products";

    public function productList()
    {
        return $this->belongsTo(ProductList::class, 'prod_id', 'id');
    }

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
