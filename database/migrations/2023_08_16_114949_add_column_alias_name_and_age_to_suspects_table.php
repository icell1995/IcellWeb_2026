<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnAliasNameAndAgeToSuspectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('public.suspects', function (Blueprint $table) {
            $table->string('alias_name')->nullable();
            $table->unsignedInteger('age')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('public.suspects', function (Blueprint $table) {
            $table->dropColumn('alias_name');
            $table->dropColumn('age');
        });
    }
}
