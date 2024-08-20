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
        Schema::create('tbl_calculation', function (Blueprint $table) {
            $table->id();
            $table->string('sec_cat_name', 512);
            $table->text('calculation');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_calculation');
    }
};
