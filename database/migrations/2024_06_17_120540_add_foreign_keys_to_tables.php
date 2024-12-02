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
    public function up(): void
    {
        Schema::table('tbl_discount_data', function (Blueprint $table) {
            $table->foreign('quot_id')
                ->references('id')->on('tbl_saved_estimates')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });

        Schema::table('tbl_associative_products', function (Blueprint $table) {
            $table->foreign('prod_id')
                ->references('id')->on('tbl_product_list')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
        Schema::table('tbl_os_calculation', function (Blueprint $table) {
            $table->foreign('product_int')
                ->references('prod_int')->on('tbl_product_list')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });

        Schema::table('tbl_unit_map', function (Blueprint $table) {
            $table->foreign('prod_id')
                ->references('id')->on('tbl_product_list')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('unit_id')
                ->references('id')->on('tbl_unit')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });

        Schema::table('tbl_rate_card_prices', function (Blueprint $table) {
            $table->foreign('prod_id')
                ->references('id')->on('tbl_product_list')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('rate_card_id')
                ->references('id')->on('tbl_rate_cards')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('region_id')
                ->references('id')->on('tbl_region_master')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });

        Schema::table('tbl_saved_estimates', function (Blueprint $table) {
            $table->foreign('emp_code')
                ->references('crm_user_id')->on('tbl_login_master')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('owner')
                ->references('crm_user_id')->on('tbl_login_master')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('last_changed_by')
                ->references('crm_user_id')->on('tbl_login_master')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
        Schema::table('tbl_visitor_activity_logs', function (Blueprint $table) {
            $table->foreign('emp_code')
                ->references('crm_user_id')->on('tbl_login_master')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('uname')
                ->references('username')->on('tbl_login_master')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });

        Schema::table('tbl_role_permissions', function (Blueprint $table) {
            $table->foreign('role_id')
                ->references('id')->on('tbl_role_master')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_role_permissions', function (Blueprint $table) {
            $table->dropForeign('role_id_fkc');
        });

        Schema::table('tbl_saved_estimates', function (Blueprint $table) {
            $table->dropForeign('emp_code_fkc');
            $table->dropForeign('owner_fkc');
            $table->dropForeign('last_changed_by_fkc');
            $table->dropForeign('uname_fkc');
        });

        Schema::table('tbl_rate_card_prices', function (Blueprint $table) {
            $table->dropForeign('prod_id_rate_card_prices_fkc');
            $table->dropForeign('rate_card_id_fkc');
            $table->dropForeign('region_id_fkc');
        });

        Schema::table('tbl_unit_map', function (Blueprint $table) {
            $table->dropForeign('prod_id_unit_map_fkc');
            $table->dropForeign('unit_id_fkc');
        });
        Schema::table('tbl_visitor_activity_logs', function (Blueprint $table) {
            $table->dropForeign('emp_code_fkc');
            $table->dropForeign('uname_fkc');
        });

        Schema::table('tbl_os_calculation', function (Blueprint $table) {
            $table->dropForeign('prod_int_fkc');
        });
        Schema::table('tbl_associative_products', function (Blueprint $table) {
            $table->dropForeign('prod_id_fkc');
        });

        Schema::table('tbl_discount_data', function (Blueprint $table) {
            $table->dropForeign('estmt_fkc');
        });
    }
};
