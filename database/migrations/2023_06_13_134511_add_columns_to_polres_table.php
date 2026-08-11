<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToPolresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('polres', function (Blueprint $table) {
            $table->string('spptti_id', 32)->nullable();
            $table->string('work_unit_code', 32)->nullable();
            $table->string('puskarda_code', 128)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('polres', function (Blueprint $table) {
            $table->dropColumn('spptti_id');
            $table->dropColumn('work_unit_code');
            $table->dropColumn('puskarda_code');
        });
    }
}
