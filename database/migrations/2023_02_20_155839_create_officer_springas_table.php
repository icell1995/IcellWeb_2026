<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOfficerSpringasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('officer_springas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('sprint_gas_id');
            $table->string('officer_id',16);
            // $table->string('name');
            // $table->string('rank_id');
            // $table->string('position');
            $table->timestamps();
            $table->foreign('sprint_gas_id')->on('springas')->references('id')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('officer_id')->on('officer')->references('id')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('officer_springas');
    }
}
