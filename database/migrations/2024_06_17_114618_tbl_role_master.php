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
        Schema::create('tbl_role_master', function (Blueprint $table) {
            $table->id();
            $table->string('role_name', 15);
            $table->string('prefix', 5);
            $table->timestamps();

            // $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'))->nullable();
            // $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_role_master');
    }
};
