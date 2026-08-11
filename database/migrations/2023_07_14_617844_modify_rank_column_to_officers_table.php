<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MOdifyRankColumnToOfficersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('officers', function (Blueprint $table) {
            $table->renameColumn('rank_id', 'rank_short_name');
        });

        Schema::table('officers', function (Blueprint $table) {
            $table->string('rank_id')->nullable();

            $table->foreign('rank_id', 'fk_officers_rank_id')->references('id')->on('lib.ranks')->onDelete('restrict')->onUpdate('cascade');
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
        Schema::table('officers', function (Blueprint $table) {
            $table->dropForeign('fk_officers_rank_id');
        });

        Schema::table('officers', function (Blueprint $table) {
            $table->dropColumn('rank_id');
        });

        Schema::table('officers', function (Blueprint $table) {
            $table->renameColumn('rank_short_name', 'rank_id');
        });
    }
}
