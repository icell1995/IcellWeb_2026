<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuthorizedSignatoryInvestigationWarrantTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('authorized_signatory_investigation_warrant', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->uuid('investigation_warrant_id');
            $table->uuid('authorized_signatory_id');

            $table->timestamps();

            $table->foreign('investigation_warrant_id')->references('id')->on('investigation_warrants')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('authorized_signatory_id')->references('id')->on('authorized_signatories')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::table('authorized_signatory_investigation_warrant', function (Blueprint $table) {
            $table->dropForeign('authorized_signatory_investigation_warrant_investigation_warrant_id_foreign');
            $table->dropForeign('authorized_signatory_investigation_warrant_authorized_signatory_id_foreign');
        });
        Schema::dropIfExists('authorized_signatory_investigation_warrant');
    }
}
