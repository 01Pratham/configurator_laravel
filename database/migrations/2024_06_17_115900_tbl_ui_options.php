<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
            $table->boolean('input_num')->default(1)->nullable(false);
            $table->boolean('select_box')->default(1)->nullable(false);
            $table->text('input_placeholder')->nullable(false);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'))->nullable();
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_ui_options');
    }
};
