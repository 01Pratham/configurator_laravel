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
        Schema::create('tbl_quot_type', function (Blueprint $table) {
            $table->id();
            $table->string('template_name', 30);
            $table->boolean('is_active');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'))->nullable();
$table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->nullable();


        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_quot_type');
    }
};
