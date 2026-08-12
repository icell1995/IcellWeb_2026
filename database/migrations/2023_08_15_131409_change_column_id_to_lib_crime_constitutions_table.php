<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnIdToLibCrimeConstitutionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lib.crime_constitutions', function (Blueprint $table) {
            $table->string('id')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lib.crime_constitutions', function (Blueprint $table) {
            $table->dropColumn('id');
        });
        Schema::table('lib.crime_constitutions', function (Blueprint $table) {
            $table->bigIncrements('id');
        });
    }
}
