<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddRegisterNumberColumnToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::beginTransaction();

        try{
            Schema::table('users', function (Blueprint $table) {
                $table->string('register_number')->nullable();
            });

            // Mengisi kolom register_number dengan nilai duplikat dari kolom id
            DB::statement('UPDATE users SET register_number = CAST(officer_id AS varchar)');

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::beginTransaction();

        try{
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('register_number');
            });

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
