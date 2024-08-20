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
        Schema::create('tbl_terms_conditions', function (Blueprint $table) {
            $table->id();
            $table->text('terms')->nullable(false);
            $table->boolean('is_active')->nullable(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_terms_conditions');
    }
};
