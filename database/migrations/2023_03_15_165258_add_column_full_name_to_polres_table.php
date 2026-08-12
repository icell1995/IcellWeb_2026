<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnFullNameToPolresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('polres', function (Blueprint $table) {
            Schema::table('polres', function (Blueprint $table) {
                $table->string('full_name')->after('name')->nullable();
            });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::table('polres', function (Blueprint $table) {
            $table->dropColumn('full_name');
        });
    }
}
