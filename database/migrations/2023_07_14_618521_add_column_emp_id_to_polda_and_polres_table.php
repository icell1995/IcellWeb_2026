<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnEmpIdToPoldaAndPolresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('public.polda', function (Blueprint $table) {
            $table->string('emp_id')->nullable()->after('puskarda_id');
        });
       
        Schema::table('public.polres', function (Blueprint $table) {
            $table->string('emp_id')->nullable()->after('puskarda_id');
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
            $table->dropColumn('emp_id');
        });
       
        Schema::table('public.polres', function (Blueprint $table) {
            $table->dropColumn('emp_id');
        });
    }
}
