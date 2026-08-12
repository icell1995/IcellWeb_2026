<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuthorizedSignatoryInvestigationOrderLetterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('authorized_signatory_investigation_order_letter', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->uuid('investigation_order_letter_id');
            $table->uuid('authorized_signatory_id');

            $table->timestamps();

            $table->foreign('investigation_order_letter_id')->references('id')->on('investigation_order_letters')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::table('authorized_signatory_investigation_order_letter', function (Blueprint $table) {
            $table->dropForeign('authorized_signatory_investigation_order_letter_investigation_order_letter_id_foreign');
            $table->dropForeign('authorized_signatory_investigation_order_letter_authorized_signatory_id_foreign');
        });
        Schema::dropIfExists('authorized_signatory_investigation_order_letter');
    }
}
