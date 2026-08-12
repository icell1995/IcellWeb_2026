<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ModifyPublicRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try{
            //move schema table roles to lib.roles
            DB::statement('ALTER TABLE public.roles SET SCHEMA lib;');

            Schema::table('lib.roles', function (Blueprint $table) {
                $table->integer('id')->change();
    
                $table->string('code')->nullable();
                $table->string('full_name')->nullable();
    
                $table->bigInteger('sort')->default(0)->nullable();
                $table->boolean('is_active')->default(true);
                
                $table->softDeletes();
            });

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
        try{
            //move schema table roles to lib.roles
            DB::statement('ALTER TABLE lib.roles SET SCHEMA public;');

            Schema::table('public.roles', function (Blueprint $table) {
                $table->bigInteger('id')->change();
    
                $table->dropColumn('code');
                $table->dropColumn('full_name');
    
                $table->dropColumn('sort');
                $table->dropColumn('is_active');
                
                $table->dropSoftDeletes();
            });

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
