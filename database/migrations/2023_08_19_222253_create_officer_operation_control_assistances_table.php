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
        Schema::create('officer_operation_control_assistances', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('officer_id')->nullable();

            $table->boolean('is_operation_control_assistance')->default(false);
            $table->string('letter_number')->nullable();
            $table->date('date')->nullable();

            $table->string('origin_police_id')->nullable();
            $table->timestamps();

            $table->foreign('officer_id', 'fk_officer_operation_control_assistances_officer_id')->references('id')->on('public.officers')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('origin_police_id', 'fk_officer_operation_control_assistances_origin_police_id')->references('id')->on('lib.polices')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //drop foreign key constraints first
        Schema::table('officer_operation_control_assistances', function (Blueprint $table) {
            $table->dropForeign('fk_officer_operation_control_assistances_officer_id');
            $table->dropForeign('fk_officer_operation_control_assistances_origin_police_id');
        });

        Schema::dropIfExists('officer_operation_control_assistances');
    }
};
