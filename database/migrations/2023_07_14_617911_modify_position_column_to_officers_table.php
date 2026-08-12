<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyPositionColumnToOfficersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('officers', function (Blueprint $table) {
            $table->string('position_id')->nullable()->after('id');

            $table->foreign('position_id', 'fk_officers_position_id')->references('id')->on('lib.positions')->onDelete('restrict')->onUpdate('cascade');
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
            $table->dropForeign('fk_officers_position_id');
        });

        Schema::table('officers', function (Blueprint $table) {
            $table->dropColumn('position_id');
        });
    }
}
