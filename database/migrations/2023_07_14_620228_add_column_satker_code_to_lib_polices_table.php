<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnSatkerCodeToLibPolicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lib.polices', function (Blueprint $table) {
            $table->string('satker_code')->nullable()->after('spptti_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lib.polices', function (Blueprint $table) {
            $table->dropColumn('satker_code');
        });
    }
}
