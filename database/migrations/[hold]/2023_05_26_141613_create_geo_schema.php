<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateGeoSchema extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('CREATE SCHEMA geo');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tables = Schema::getAllTables('geo');

        if (count($tables) > 0) {
            throw new \Exception('Cannot drop schema. Tables exist within the schema.');
        }

        DB::statement('DROP SCHEMA IF EXISTS geo CASCADE');
    }
}
