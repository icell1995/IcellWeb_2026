<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvestigationWarrantOfficerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('investigation_warrant_officer', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('investigation_warrant_id');
            $table->string('officer_id', 16);
            $table->timestamps();

            $table->foreign('investigation_warrant_id')->references('id')->on('investigation_warrants')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('officer_id')->references('id')->on('officer')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop foreign key
        Schema::table('investigation_warrant_officer', function (Blueprint $table) {
            $table->dropForeign('investigation_warrant_officer_investigation_warrant_id_foreign');
            $table->dropForeign('investigation_warrant_officer_officer_id_foreign');
        });
        
        Schema::dropIfExists('investigation_warrant_officer');
    }
}
