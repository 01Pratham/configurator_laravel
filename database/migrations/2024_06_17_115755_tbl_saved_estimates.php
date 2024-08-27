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
        Schema::create('tbl_saved_estimates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('emp_code')->nullable(false);
            $table->integer('pot_id')->nullable(false);
            $table->string('project_name', 50)->charset('utf8mb4')->collation('utf8mb4_general_ci')->nullable(false);
            $table->string('version', 5)->nullable(false);
            $table->unsignedBigInteger('owner')->nullable(false);
            $table->unsignedBigInteger('last_changed_by')->nullable(false);
            $table->integer('contract_period')->nullable(false);
            $table->integer('total_upfront')->nullable(false);
            $table->bigInteger('discounted_upfront')->nullable(false);
            $table->text('data')->charset('utf8mb4')->collation('utf8mb4_general_ci')->nullable(false);
            $table->text('prices')->nullable(false);
            $table->text('terms')->nullable(false);
            $table->text('assumptions')->nullable(false);
            $table->text('exculsions')->nullable(false);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'))->nullable();
$table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->nullable();

            $table->boolean('is_deleted')->default(false);
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_saved_estimates');
    }
};
