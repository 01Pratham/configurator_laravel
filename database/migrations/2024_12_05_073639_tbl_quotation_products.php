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
        Schema::create('tbl_quotation_product_master', function (Blueprint $table) {
            $table->id();
            $table->string("product_name", 20);
            $table->unsignedBigInteger("product_id");
            $table->unsignedBigInteger("group_id");
            $table->double("quantity");
            $table->double("unit_price");
            $table->double("mrc_price");
            $table->double("otc_price");
            $table->double("dicount_percentage");
            $table->unsignedBigInteger("added_by");
            $table->boolean("is_billable");
            $table->boolean("is_deleted");
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'))->nullable();
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_quotation_product_master');
    }
};
