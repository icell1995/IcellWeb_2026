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
        Schema::table('lib.courts', function (Blueprint $table) {
            $table->string('country_id')->nullable();
            $table->string('province_id')->nullable();
            $table->string('regency_id')->nullable();
            $table->string('district_id')->nullable();
            $table->string('village_id')->nullable();

            $table->foreign('country_id', 'fk_courts_country_id')->references('id')->on('lib.locations')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('province_id', 'fk_courts_province_id')->references('id')->on('lib.locations')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('regency_id', 'fk_courts_regency_id')->references('id')->on('lib.locations')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('district_id', 'fk_courts_district_id')->references('id')->on('lib.locations')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('village_id', 'fk_courts_village_id')->references('id')->on('lib.locations')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lib.courts', function (Blueprint $table) {
            $table->dropForeign('fk_courts_country_id');
            $table->dropForeign('fk_courts_province_id');
            $table->dropForeign('fk_courts_regency_id');
            $table->dropForeign('fk_courts_district_id');
            $table->dropForeign('fk_courts_village_id');
        });
        
        Schema::table('lib.courts', function (Blueprint $table) {
            $table->dropColumn('country_id');
            $table->dropColumn('province_id');
            $table->dropColumn('regency_id');
            $table->dropColumn('district_id');
            $table->dropColumn('village_id');
        });
    }
};
