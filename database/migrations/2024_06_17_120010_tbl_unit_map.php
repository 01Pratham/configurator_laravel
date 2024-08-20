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
        Schema::create('tbl_unit_map', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('prod_id')->nullable();
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->timestamps();

        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_unit_map');
    }
};
