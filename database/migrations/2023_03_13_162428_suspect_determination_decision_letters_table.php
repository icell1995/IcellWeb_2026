<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SuspectDeterminationDecisionLettersTable extends Migration
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
            $table->string('letter_number'); //NomorSuratKetetapan
            $table->date('letter_date'); //TanggalSuratKetetapan
            $table->date('tgl_gelar'); //TanggalGelarPerkara
            $table->date('tgl_resume')->nullable(); //TanggalResume
            $table->uuid('investigation_report_id'); //sprindikId
            $table->string('prosecutor_office_id'); //kejaksaanId
            $table->string('suspect_source'); //sumbertersangka
            $table->uuid('signing_officials'); //pejabatpenandatangan
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
        Schema::dropIfExists('suspect_determination_decision_letters');
    }
}
