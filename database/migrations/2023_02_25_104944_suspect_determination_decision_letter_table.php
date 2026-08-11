<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SuspectDeterminationDecisionLetterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suspect_determination_decision_letters', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('accident_id');
            $table->string('no_lp');
            $table->string('no_sprindik');
            $table->string('letter_number'); //NomorSurat
            $table->dateTime('letter_date'); //TanggalSurat
            $table->uuid('investigation_report_id'); //sprindikId
            $table->integer('prosecutor_office_id'); //kejaksaanId
            $table->string('suspect_source'); //sumbertersangka
            $table->json('signing_officials'); //pejabatpenandatangan
            $table->uuid('investigation_warrant_id'); //lhgpId
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
