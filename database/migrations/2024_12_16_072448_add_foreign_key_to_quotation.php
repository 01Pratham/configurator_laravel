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

        // tbl_project_quotation_master
        Schema::table('tbl_project_quotation_master', function (Blueprint $table) {
            $table->foreign('user_id')->references('crm_user_id')->on('tbl_login_master')->onDelete('cascade');
            $table->foreign('project_id')->references('id')->on('tbl_project_master')->onDelete('cascade');
            $table->foreign('owner')->references('crm_user_id')->on('tbl_login_master')->onDelete('cascade');
            $table->foreign('price_list_id')->references('id')->on('tbl_rate_cards')->onDelete('cascade');
            $table->foreign('last_changed_by')->references('crm_user_id')->on('tbl_login_master')->onDelete('cascade');
        });

        // tbl_quotation_group_master
        Schema::table('tbl_quotation_group_master', function (Blueprint $table) {
            $table->foreign('created_by')->references('crm_user_id')->on('tbl_login_master')->onDelete('cascade');
            $table->foreign('phase_id')->references('id')->on('tbl_quotation_phase_master')->onDelete('cascade');
        });

        // tbl_quotation_phase_master
        Schema::table('tbl_quotation_phase_master', function (Blueprint $table) {
            $table->foreign('created_by')->references('crm_user_id')->on('tbl_login_master')->onDelete('cascade');
            $table->foreign('quotation_id')->references('id')->on('tbl_project_quotation_master')->onDelete('cascade');
            $table->foreign('region_id')->references('id')->on('tbl_region_master')->onDelete('cascade');
        });

        // tbl_quotation_product_master
        Schema::table('tbl_quotation_product_master', function (Blueprint $table) {
            $table->foreign('added_by')->references('crm_user_id')->on('tbl_login_master')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('tbl_product_list')->onDelete('cascade');
            $table->foreign('group_id')->references('id')->on('tbl_quotation_group_master')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_project_quotation_master', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['project_id']);
            $table->dropForeign(['owner']);
            $table->dropForeign(['price_list_id']);
            $table->dropForeign(['last_changed_by']);
        });

        // tbl_quotation_group_master
        Schema::table('tbl_quotation_group_master', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['phase_id']);
        });

        // tbl_quotation_phase_master
        Schema::table('tbl_quotation_phase_master', function (Blueprint $table) {
            $table->dropForeign(['quotation_id']);
            $table->dropForeign(['user_id']);
            $table->dropForeign(['region_id']);
        });

        // tbl_quotation_product_master
        Schema::table('tbl_quotation_product_master', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropForeign(['user_id']);
            $table->dropForeign(['group_id']);
        });
    }
};
