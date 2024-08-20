<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tbl_permission_master', function (Blueprint $table) {
            $table->id();
            $table->string('permission', 50);
            // $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'))->nullable();
            // $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_permission_master');
    }
};
