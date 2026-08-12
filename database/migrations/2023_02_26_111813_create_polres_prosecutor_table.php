<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePolresProsecutorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('polres_prosecutor', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('polres_id',4)->nullable();
            $table->string('prosecutor_id')->nullable();

            $table->timestamps();

            $table->foreign('polres_id')->references('id')->on('polres')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('prosecutor_id')->references('id')->on('prosecutors')->onDelete('set null')->onUpdate('cascade');
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
        Schema::table('polres_prosecutor', function (Blueprint $table) {
            // $table->dropForeign('polres_prosecutor_polres_id_foreign');
            $table->dropForeign('polres_prosecutor_prosecutor_id_foreign');
        });
        Schema::dropIfExists('polres_prosecutor');
    }
}
