<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateOptSchema extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('CREATE SCHEMA opt');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tables = Schema::getAllTables('opt');

        if (count($tables) > 0) {
            throw new \Exception('Cannot drop schema opt. Tables exist within the schema.');
        }

        DB::statement('DROP SCHEMA IF EXISTS opt CASCADE');
    }
}
