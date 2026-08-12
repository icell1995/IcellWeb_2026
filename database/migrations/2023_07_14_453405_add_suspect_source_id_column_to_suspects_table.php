<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSuspectSourceIdColumnToSuspectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('suspects', function (Blueprint $table) {
            $table->string('suspect_source_id')->nullable();

            $table->foreign('suspect_source_id', 'fk_suspects_suspect_source_id')->references('id')->on('lib.suspect_sources')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('suspects', function (Blueprint $table) {
            $table->dropForeign('fk_suspects_suspect_source_id');
        });
        Schema::table('suspects', function (Blueprint $table) {
            $table->dropColumn('suspect_source_id');
        });
    }
}
