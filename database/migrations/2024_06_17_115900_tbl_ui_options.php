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
        Schema::create('tbl_ui_options', function (Blueprint $table) {
            $table->id();
            $table->string('sec_category_name', 512)->nullable(false);
            $table->enum('input_num', ['True', 'False'])->nullable(false);
            $table->enum('select_box', ['True', 'False'])->nullable(false);
            $table->text('input_placeholder')->nullable(false);
            $table->timestamps();

        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_ui_options');
    }
};
