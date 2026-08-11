<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ModifyOfficersTable extends Migration
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

            Schema::table('public.officers', function (Blueprint $table) {
                // change id length to 255
                $table->string('id', 255)->change();
            });
            
            Schema::table('public.officers', function (Blueprint $table) {
                //create column register_number after id
                $table->string('register_number', 255)->nullable()->unique()->after('id');
                //create column police_id after register_number
                $table->string('police_id')->nullable()->after('register_number');

                //add foreign key to police_id
                $table->foreign('police_id', 'fk_officers_police_id')->references('id')->on('lib.polices')->onDelete('set null')->onUpdate('cascade');
            });
            
            // Mengisi kolom register_number dengan nilai duplikat dari kolom id
            DB::statement('UPDATE officers SET register_number = CAST(id AS varchar)');

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
            // Menghapus foreign key
            Schema::table('public.officers', function (Blueprint $table) {
                $table->dropForeign('fk_officers_police_id');
            });

            Schema::table('public.officers', function (Blueprint $table) {
                // drop column register_number
                $table->dropColumn('register_number');
                $table->dropColumn('police_id');
            });

            Schema::table('public.officers', function (Blueprint $table) {
                // change id length to 255
                $table->string('id', 255)->change();
            });

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
