<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePivotPoliceProsecutorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pivot.police_prosecutor', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('police_id');
            $table->string('prosecutor_id');

            $table->timestamps();

            $table->foreign('police_id', 'fk_police_prosecutor_police_id')->references('id')->on('lib.polices')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('prosecutor_id', 'fk_police_prosecutor_prosecutor_id')->references('id')->on('lib.prosecutors')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //drop fk
        Schema::table('pivot.police_prosecutor', function (Blueprint $table) {
            $table->dropForeign('fk_police_prosecutor_police_id');
            $table->dropForeign('fk_police_prosecutor_prosecutor_id');
        });

        Schema::dropIfExists('pivot.police_prosecutor');
    }
}
