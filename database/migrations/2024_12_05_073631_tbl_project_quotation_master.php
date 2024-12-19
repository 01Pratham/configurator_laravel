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
        Schema::create('tbl_project_quotation_master', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->string("quotation_name", 50);
            $table->unsignedBigInteger('owner')->nullable(false);
            $table->unsignedBigInteger('last_changed_by')->nullable(false);
            $table->double('total_selling_price')->nullable(false);
            $table->double('total_discounted_selling_price')->nullable(false);
            $table->double('total_otc_price')->nullable(false);
            $table->double('total_discounted_otc_price')->nullable(false);
            $table->double('total_discount_percentage')->nullable(false);
            $table->unsignedBigInteger('price_list_id');
            $table->enum("discount_approval_status", ["NA", "Pending", "Approved", "Rejected"]);
            $table->unsignedBigInteger('discount_approved_by');
            $table->text('discount_rejection_remark');
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
        Schema::dropIfExists('tbl_project_quotation_master');
    }
};
