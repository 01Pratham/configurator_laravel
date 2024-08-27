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
        Schema::create('tbl_visitor_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->string('user_ip_address', 50)->nullable(false);
            $table->string('session_id', 512)->nullable();
            $table->unsignedBigInteger('emp_code')->nullable();
            $table->string('uname', 20)->nullable();
            $table->string('page_url', 255)->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'))->nullable();
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_visitor_activity_logs');
    }
};
