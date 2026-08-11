<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvestigationWarrantDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('investigation_warrant_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('investigation_warrant_id');

            $table->longText('description');

            $table->timestamps();

            $table->foreign('investigation_warrant_id')->references('id')->on('investigation_warrants')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::table('investigation_warrant_details', function (Blueprint $table) {
            $table->dropForeign('investigation_warrant_details_investigation_warrant_id_foreign');
        });

        Schema::dropIfExists('investigation_warrant_details');
    }
}
