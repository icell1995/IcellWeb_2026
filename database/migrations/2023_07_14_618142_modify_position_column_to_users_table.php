<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyPositionColumnToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('position_id')->nullable();

            $table->foreign('position_id', 'fk_users_position_id')->references('id')->on('lib.positions')->onDelete('restrict')->onUpdate('cascade');
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
            $table->dropForeign('fk_users_position_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('position_id');
        });
    }
}
