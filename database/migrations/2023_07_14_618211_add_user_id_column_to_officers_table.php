<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIdColumnToOfficersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('officers', function (Blueprint $table) {
            $table->bigInteger('user_id')->nullable()->after('id');

            $table->foreign('user_id', 'fk_officers_user_id')->references('id')->on('users')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('officers', function (Blueprint $table) {
            //drop fk
            $table->dropForeign('fk_officers_user_id');
        });

        Schema::table('officers', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
    }
}
