<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tbl_rate_card_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('prod_id');
            $table->unsignedBigInteger('rate_card_id');
            $table->unsignedBigInteger('region_id');
            $table->decimal('input_price', 8, 2)->default(0);
            $table->decimal('price', 8, 2)->default(0);
            $table->decimal('discountable_percentage', 8, 2)->default(0);
            $table->decimal('input_otc', 8, 2)->default(0);
            $table->decimal('otc', 8, 2)->default(0);
            $table->decimal('discountable_otc', 8, 2)->default(0);
            $table->timestamps();
            $table->enum('is_active', ['True', 'False']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_rate_card_prices');
    }
};
