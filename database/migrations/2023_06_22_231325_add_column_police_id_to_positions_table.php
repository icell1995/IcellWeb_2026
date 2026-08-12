<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnPoliceIdToPositionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lib.positions', function (Blueprint $table) {
            $table->string('police_id')->nullable();

            $table->foreign('police_id', 'fk_positions_police_id')->references('id')->on('lib.polices')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lib.positions', function (Blueprint $table) {
            $table->dropForeign('fk_positions_police_id');
        });

        Schema::table('lib.positions', function (Blueprint $table) {
            $table->dropColumn('police_id');
        });
    }
}
