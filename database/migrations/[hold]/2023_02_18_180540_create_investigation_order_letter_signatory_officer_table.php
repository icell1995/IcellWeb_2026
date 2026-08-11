<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvestigationOrderLetterSignatoryOfficerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('investigation_order_letter_signatory_officer', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('investigation_order_letter_id');
            $table->string('officer_id', 16);
            $table->timestamps();

            $table->foreign('investigation_order_letter_id')->references('id')->on('investigation_order_letters')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::table('investigation_order_letter_signatory_officer', function (Blueprint $table) {
            $table->dropForeign('investigation_order_letter_signatory_officer_investigation_order_letter_id_foreign');
            $table->dropForeign('investigation_order_letter_signatory_officer_officer_id_foreign');
        });
        Schema::dropIfExists('investigation_order_letter_signatory_officer');
    }
}
