<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOfficerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('officer', function (Blueprint $table) {
            $table->string('id',16)->primary();
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('polda_id',2);
            $table->string('polres_id',4);
            $table->string('rank_id');
            $table->string('position');
            $table->string('sebagai_kepala')->nullable();
            $table->string('state');
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
        Schema::dropIfExists('officer');
    }
}
