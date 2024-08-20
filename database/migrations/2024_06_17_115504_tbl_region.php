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
        Schema::create('tbl_region_master', function (Blueprint $table) {
            $table->id();
            $table->string('region_name', 50);
            $table->timestamps();

        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_region_master');
    }
};
