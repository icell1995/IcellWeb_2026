<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvestigationOrderLetterLawTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('investigation_order_letter_law', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('investigation_order_letter_id');
            $table->unsignedBigInteger('law_id');
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
        // drop the foreign key first
        Schema::table('investigation_order_letter_law', function (Blueprint $table) {
            $table->dropForeign('investigation_order_letter_law_investigation_order_letter_id_foreign');
        });
        
        Schema::dropIfExists('investigation_order_letter_law');
    }
}
