<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateLegacySchema extends Migration
{
    private $schema = 'legacy';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('CREATE SCHEMA ' . $this->schema);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tables = Schema::getAllTables($this->schema);

        if (count($tables) > 0) {
            throw new \Exception('Cannot drop schema ' . $this->schema . '. Tables exist within the schema.');
        }

        DB::statement('DROP SCHEMA IF EXISTS ' . $this->schema . ' CASCADE');
    }
}
