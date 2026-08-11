<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreatePivotSchema extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('CREATE SCHEMA pivot');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tables = Schema::getAllTables('pivot');

        if (count($tables) > 0) {
            throw new \Exception('Cannot drop schema pivot. Tables exist within the schema.');
        }

        DB::statement('DROP SCHEMA IF EXISTS pivot CASCADE');
    }
}
