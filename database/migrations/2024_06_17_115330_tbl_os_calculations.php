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
        Schema::create('tbl_os_calculation', function (Blueprint $table) {
            $table->id();
            $table->string('product_int', 512);
            $table->text('calculation');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_os_calculation');
    }
};
