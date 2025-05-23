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
    public function up()
    {
        Schema::create('tbl_associative_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('prod_id');
            $table->unsignedBigInteger('associative_product_id');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'))->nullable();
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->nullable();
            $table->boolean('is_active');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_associative_products');
    }
};
