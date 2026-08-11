<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameTypeColumnToLibRanksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lib.ranks', function (Blueprint $table) {
            $table->renameColumn('type', 'employment_type_id');
        });

        Schema::table('lib.ranks', function (Blueprint $table) {
            $table->foreign('employment_type_id', 'fk_lib_ranks_employment_type_id')
                ->references('id')
                ->on('lib.employment_types')
                ->onDelete('restrict')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lib.ranks', function (Blueprint $table) {
            $table->dropForeign('fk_lib_ranks_employment_type_id');
        });

        Schema::table('lib.ranks', function (Blueprint $table) {
            $table->renameColumn('employment_type_id', 'type');
        });
    }
}
