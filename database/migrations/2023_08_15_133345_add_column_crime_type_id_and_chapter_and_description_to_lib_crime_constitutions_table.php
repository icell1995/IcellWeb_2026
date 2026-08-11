<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnCrimeTypeIdAndChapterAndDescriptionToLibCrimeConstitutionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lib.crime_constitutions', function (Blueprint $table) {
            $table->string('crime_type_id')->nullable();
            $table->string('chapter')->nullable();
            $table->longText('description')->nullable();

            $table->foreign('crime_type_id', 'fk_crime_constitution_crime_type_id')->references('id')->on('lib.crime_types')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //drop fk
        Schema::table('lib.crime_constitutions', function (Blueprint $table) {
            $table->dropForeign('fk_crime_constitution_crime_type_id');
        });

        Schema::table('lib.crime_constitutions', function (Blueprint $table) {
            $table->dropColumn('crime_type_id');
            $table->dropColumn('chapter');
            $table->dropColumn('description');
        });
    }
}
