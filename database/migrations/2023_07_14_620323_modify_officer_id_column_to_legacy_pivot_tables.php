<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyOfficerIdColumnToLegacyPivotTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('legacy.investigation_order_letter_leader_officer', function (Blueprint $table) {
            $table->string('officer_id', 255)->nullable()->change();
        });
       
        Schema::table('legacy.investigation_order_letter_officer', function (Blueprint $table) {
            $table->string('officer_id', 255)->nullable()->change();
        });
        
        Schema::table('legacy.investigation_order_letter_signatory_officer', function (Blueprint $table) {
            $table->string('officer_id', 255)->nullable()->change();
        });
       
        Schema::table('legacy.investigation_warrant_leader_officer', function (Blueprint $table) {
            $table->string('officer_id', 255)->nullable()->change();
        });
        
        Schema::table('legacy.investigation_warrant_officer', function (Blueprint $table) {
            $table->string('officer_id', 255)->nullable()->change();
        });
        
        Schema::table('legacy.investigation_warrant_signatory_officer', function (Blueprint $table) {
            $table->string('officer_id', 255)->nullable()->change();
        });
       
        Schema::table('legacy.officer_springas', function (Blueprint $table) {
            $table->string('officer_id', 255)->nullable()->change();
        });
     
        Schema::table('public.sign_officer_lhgp', function (Blueprint $table) {
            $table->string('officer_id', 255)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('legacy.investigation_order_letter_leader_officer', function (Blueprint $table) {
            $table->string('officer_id', 16)->nullable()->change();
        });
       
        Schema::table('legacy.investigation_order_letter_officer', function (Blueprint $table) {
            $table->string('officer_id', 16)->nullable()->change();
        });
        
        Schema::table('legacy.investigation_order_letter_signatory_officer', function (Blueprint $table) {
            $table->string('officer_id', 16)->nullable()->change();
        });
       
        Schema::table('legacy.investigation_warrant_leader_officer', function (Blueprint $table) {
            $table->string('officer_id', 16)->nullable()->change();
        });
        
        Schema::table('legacy.investigation_warrant_officer', function (Blueprint $table) {
            $table->string('officer_id', 16)->nullable()->change();
        });
        
        Schema::table('legacy.investigation_warrant_signatory_officer', function (Blueprint $table) {
            $table->string('officer_id', 16)->nullable()->change();
        });
       
        Schema::table('legacy.officer_springas', function (Blueprint $table) {
            $table->string('officer_id', 16)->nullable()->change();
        });

        Schema::table('public.sign_officer_lhgp', function (Blueprint $table) {
            $table->string('officer_id', 16)->nullable()->change();
        });
    }
}
