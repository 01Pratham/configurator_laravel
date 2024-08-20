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
        Schema::create('tbl_rate_cards', function (Blueprint $table) {
            $table->id();
            $table->string('rate_card_name');
            $table->enum('card_type', ["Public", "Private"]);
            $table->integer('listing');
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            $table->boolean("is_active")->default(true);
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_rate_cards');
    }
};
