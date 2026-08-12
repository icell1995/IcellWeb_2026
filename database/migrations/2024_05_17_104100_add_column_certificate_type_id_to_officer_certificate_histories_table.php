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
        Schema::table('officer_certificate_histories', function (Blueprint $table) {
            $table->unsignedInteger('certificate_type_id')->nullable();

            $table->foreign('certificate_type_id', 'fk_officer_certificate_histories_certificate_type_id')->references('id')->on('lib.certificate_types')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('officer_certificate_histories', function (Blueprint $table) {
            $table->dropForeign('fk_officer_certificate_histories_certificate_type_id');
        });

        Schema::table('officer_certificate_histories', function (Blueprint $table) {
            $table->dropColumn('certificate_type_id');
        });
    }
};
