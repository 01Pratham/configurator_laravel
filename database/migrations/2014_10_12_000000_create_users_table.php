<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tbl_login_master', function (Blueprint $table) {
            $table->id();
            $table->integer('employee_code')->nullable();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('username', 20)->unique();
            $table->string('email', 100)->unique();
            $table->string('password', 100);
            $table->string('department', 10);
            $table->string('designation', 50);
            $table->unsignedBigInteger('manager_code');
            $table->unsignedBigInteger('user_role');
            $table->unsignedBigInteger('crm_user_id')->unique();
            $table->integer('applicable_discounting_percentage');
            // $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'))->nullable();
            // $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_login_master');
    }
};
