<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropDocInvestigationWarrantDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Drop table
        Schema::dropIfExists('doc.investigation_warrant_details');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Create table
        Schema::create('doc.investigation_warrant_details', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('investigation_warrant_id')->nullable();
            $table->text('description')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }
}
