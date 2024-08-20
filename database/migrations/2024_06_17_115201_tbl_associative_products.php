<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
            $table->text('associative_products');
            $table->timestamp('date_updated')->useCurrent();
            $table->timestamps();

            $table->boolean('is_active');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_associative_products');
    }
};
