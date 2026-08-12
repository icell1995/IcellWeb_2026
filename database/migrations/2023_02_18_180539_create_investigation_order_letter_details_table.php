<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvestigationOrderLetterDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('investigation_order_letter_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('investigation_order_letter_id');

            $table->longText('description');

            $table->timestamps();

            $table->foreign('investigation_order_letter_id')->references('id')->on('investigation_order_letters')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::table('investigation_order_letter_details', function (Blueprint $table) {
            $table->dropForeign('investigation_order_letter_details_investigation_order_letter_id_foreign');
        });

        Schema::dropIfExists('investigation_order_letter_details');
    }
}
