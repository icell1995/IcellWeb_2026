<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropDocInvestigationOrderLetterDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Drop table
        Schema::dropIfExists('doc.investigation_order_letter_details');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         // Create table
         Schema::create('doc.investigation_order_letter_details', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('investigation_order_letter_id')->nullable();
            $table->text('description')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }
}
