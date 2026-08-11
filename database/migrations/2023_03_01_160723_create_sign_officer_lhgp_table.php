<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSignOfficerLhgpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sign_officer_lhgp', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('lhgp_id');
            $table->string('officer_id',16);
            // $table->string('name');
            // $table->string('rank_id');
            // $table->string('position');
            $table->timestamps();
            $table->foreign('lhgp_id')->on('lhgp')->references('id')->onUpdate('CASCADE')->onDelete('CASCADE');
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
        Schema::dropIfExists('sign_officer_lhgp');
    }
}
