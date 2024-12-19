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
        Schema::create('tbl_quotation_group_master', function (Blueprint $table) {
            $table->id();
            $table->string("group_name", 20);
            $table->unsignedBigInteger("crm_group_id");
            $table->unsignedBigInteger("group_quantity");
            $table->unsignedBigInteger("created_by");
            $table->unsignedBigInteger("phase_id");
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'))->nullable();
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->nullable();
            $table->boolean("is_deleted");
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_quotation_group_master');
    }
};
