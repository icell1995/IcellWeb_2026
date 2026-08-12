<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyRankColumnToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('rank_id')->nullable();

            $table->foreign('rank_id', 'fk_users_rank_id')->references('id')->on('lib.ranks')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //drop fk
            $table->dropForeign('fk_users_rank_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('rank_id');
        });
    }
}
