<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    // This is the schema name we're creating
    private $schema = 'history';
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::beginTransaction();
        try {
            DB::statement('CREATE SCHEMA ' . $this->schema);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = Schema::getAllTables($this->schema);

        if (count($tables) > 0) {
            throw new \Exception('Cannot drop schema ' . $this->schema . '. Tables exist within the schema.');
        }

        DB::beginTransaction();
        try {
            DB::statement('DROP SCHEMA IF EXISTS ' . $this->schema . ' CASCADE');
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
};