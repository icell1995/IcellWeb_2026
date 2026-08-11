<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameColumnClusterIdAndAddForeignKeyToPositionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lib.positions', function (Blueprint $table) {
            $table->string('position_cluster_id')->nullable();

            $table->foreign('position_cluster_id', 'fk_lib_positions_position_cluster_id')->references('id')->on('opt.position_clusters')->onDelete('restrict')->onUpdate('cascade');
        });

        Schema::table('lib.positions', function (Blueprint $table) {
            $table->dropColumn('cluster_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //drop foreign key
        Schema::table('lib.positions', function (Blueprint $table) {
            $table->dropForeign('fk_lib_positions_position_cluster_id');
        });

        //drop column
        Schema::table('lib.positions', function (Blueprint $table) {
            $table->dropColumn('position_cluster_id');
        });

        //add column
        Schema::table('lib.positions', function (Blueprint $table) {
            $table->string('cluster_id')->nullable();
        });
    }
}
