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
        Schema::create('tbl_quot_type', function (Blueprint $table) {
            $table->id();
            $table->string('template_name', 30);
            $table->enum('is_active', ['True', 'False']);
            $table->timestamps();

        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_quot_type');
    }
};
