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
        Schema::create('tbl_product_list', function (Blueprint $table) {
            $table->id();
            $table->string('sku_code', 512)->nullable();
            $table->string('crm_prod_id', 512)->nullable();
            $table->integer('crm_group_id')->nullable();
            $table->string('primary_category', 512);
            $table->string('sec_category', 512);
            $table->string('default_int', 512);
            $table->string('default_name', 512);
            $table->string('prod_int', 512)->unique("prod_int");
            $table->string('product', 512);
            $table->boolean('is_active')->default(true);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'))->nullable();
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_product_list');
    }
};
