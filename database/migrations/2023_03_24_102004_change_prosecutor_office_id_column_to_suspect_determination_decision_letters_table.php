<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeProsecutorOfficeIdColumnToSuspectDeterminationDecisionLettersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('suspect_determination_decision_letters', function (Blueprint $table) {
            $table->string('prosecutor_office_id')->change(); //kejaksaanId

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('suspect_determination_decision_letters', function (Blueprint $table) {
            $table->integer('prosecutor_office_id')->change(); //kejaksaanId
        });
    }
}
