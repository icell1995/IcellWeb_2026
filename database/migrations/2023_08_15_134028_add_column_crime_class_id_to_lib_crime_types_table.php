<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnCrimeClassIdToLibCrimeTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lib.crime_types', function (Blueprint $table) {
            $table->string('crime_class_id')->nullable();

            $table->foreign('crime_class_id', 'fk_crime_type_crime_class_id')->references('id')->on('lib.crime_classes')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // //drop fk
        Schema::table('lib.crime_types', function (Blueprint $table) {
            $table->dropForeign('fk_crime_type_crime_class_id');
        });

        Schema::table('lib.crime_types', function (Blueprint $table) {
            $table->dropColumn('crime_class_id');
        });
    }
}
