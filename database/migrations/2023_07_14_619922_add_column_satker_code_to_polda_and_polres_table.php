<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnSatkerCodeToPoldaAndPolresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('public.polda', function (Blueprint $table) {
            $table->string('satker_code')->nullable()->after('puskarda_id');
        });
       
        Schema::table('public.polres', function (Blueprint $table) {
            $table->string('satker_code')->nullable()->after('puskarda_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('public.polda', function (Blueprint $table) {
            $table->dropColumn('satker_code');
        });
       
        Schema::table('public.polres', function (Blueprint $table) {
            $table->dropColumn('satker_code');
        });
    }
}
