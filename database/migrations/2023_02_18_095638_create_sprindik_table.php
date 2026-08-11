<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSprindikTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sprindik', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('document_number');
            $table->dateTime('sprindik_date');
            $table->unsignedInteger('created_location_id');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->json('signing_officers');
            $table->json('personnel');
            $table->binary('attachment')->nullable();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();

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
        Schema::dropIfExists('sprindik');
    }
}
