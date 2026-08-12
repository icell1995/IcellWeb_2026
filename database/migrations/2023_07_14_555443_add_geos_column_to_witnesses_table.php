<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGeosColumnToWitnessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('witnesses', function (Blueprint $table) {
            $table->string('country_id')->nullable();
            $table->string('province_id')->nullable();
            $table->string('regency_id')->nullable();
            $table->string('district_id')->nullable();
            $table->string('village_id')->nullable();

            $table->foreign('country_id', 'fk_witnesses_country_id')->references('id')->on('lib.locations')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('province_id', 'fk_witnesses_province_id')->references('id')->on('lib.locations')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('regency_id', 'fk_witnesses_regency_id')->references('id')->on('lib.locations')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('district_id', 'fk_witnesses_district_id')->references('id')->on('lib.locations')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('village_id', 'fk_witnesses_village_id')->references('id')->on('lib.locations')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         //Drop fk
         Schema::table('witnesses', function (Blueprint $table) {
            $table->dropForeign('fk_witnesses_country_id');
            $table->dropForeign('fk_witnesses_province_id');
            $table->dropForeign('fk_witnesses_regency_id');
            $table->dropForeign('fk_witnesses_district_id');
            $table->dropForeign('fk_witnesses_village_id');
        });
        Schema::table('witnesses', function (Blueprint $table) {
            $table->dropColumn('country_id');
            $table->dropColumn('province_id');
            $table->dropColumn('regency_id');
            $table->dropColumn('district_id');
            $table->dropColumn('village_id');
        });
    }
}
