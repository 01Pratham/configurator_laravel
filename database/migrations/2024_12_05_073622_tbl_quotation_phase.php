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
        Schema::create('tbl_quotation_phase_master', function (Blueprint $table) {
            $table->id();
            $table->string("phase_name", 20);
            $table->unsignedBigInteger("quotation_id");
            $table->integer("phase_duration");
            $table->unsignedBigInteger("region_id");
            $table->unsignedBigInteger("created_by");
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'))->nullable();
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->nullable();
            $table->boolean("is_deleted");
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_quotation_phase_master');
    }
};
