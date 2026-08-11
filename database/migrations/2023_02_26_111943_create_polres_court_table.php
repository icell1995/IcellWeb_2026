<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePolresCourtTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('polres_court', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('polres_id',4)->nullable();
            $table->string('court_id')->nullable();
            $table->timestamps();

            $table->foreign('polres_id')->references('id')->on('polres')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('court_id')->references('id')->on('courts')->onDelete('set null')->onUpdate('cascade');
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
        Schema::table('polres_court', function (Blueprint $table) {
            $table->dropForeign(['polres_id']);
            $table->dropForeign(['court_id']);
        });
        Schema::dropIfExists('polres_court');
    }
}
