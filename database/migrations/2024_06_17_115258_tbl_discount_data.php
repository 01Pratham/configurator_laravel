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
        Schema::create('tbl_discount_data', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('quot_id');
            $table->text('discounted_data');
            $table->bigInteger('discounted_mrc');
            $table->enum('approved_status', ['Approved', 'Rejected', 'Remaining']);
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('approved_by');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_discount_data');
    }
};
