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
        Schema::create('tbl_visitor_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->string('user_ip_address', 50)->nullable(false);
            $table->unsignedBigInteger('emp_code')->nullable(false);
            $table->string('uname', 20)->nullable(false);
            $table->string('page_url', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_visitor_activity_logs');
    }
};
