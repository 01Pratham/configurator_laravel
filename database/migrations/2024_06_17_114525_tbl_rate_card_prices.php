<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
            $table->double('input_price')->default(0);
            $table->double('price')->default(0);
            $table->double('discountable_percentage')->default(0);
            $table->double('input_otc')->default(0);
            $table->double('otc')->default(0);
            $table->double('discountable_otc')->default(0);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'))->nullable();
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->nullable();

            $table->boolean('is_active');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_rate_card_prices');
    }
};
