<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class RenameEmpAnyIdColumnToLibTables extends Migration
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
            Schema::table('lib.case_classifications', function (Blueprint $table) {
                $table->renameColumn('emp_case_classification_id', 'emp_id');
            });
        
            Schema::table('lib.crime_classes', function (Blueprint $table) {
                $table->renameColumn('emp_crime_class_id', 'emp_id');
            });
          
            Schema::table('lib.crime_constitutions', function (Blueprint $table) {
                $table->renameColumn('emp_crime_constitution_id', 'emp_id');
           
            });

            Schema::table('lib.crime_types', function (Blueprint $table) {
                $table->renameColumn('emp_crime_type_id', 'emp_id');
            });
           
            Schema::table('lib.ranks', function (Blueprint $table) {
                $table->renameColumn('emp_rank_id', 'emp_id');
            });

            DB::commit();
        }catch(\Exception $e){
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
            Schema::table('lib.case_classifications', function (Blueprint $table) {
                $table->renameColumn('emp_id', 'emp_case_classification_id');
            });

            Schema::table('lib.crime_classes', function (Blueprint $table) {
                $table->renameColumn('emp_id', 'emp_crime_class_id');
            });

            Schema::table('lib.crime_constitutions', function (Blueprint $table) {
                $table->renameColumn('emp_id', 'emp_crime_constitution_id');
            });

            Schema::table('lib.crime_types', function (Blueprint $table) {
                $table->renameColumn('emp_id', 'emp_crime_type_id');
            });

            Schema::table('lib.ranks', function (Blueprint $table) {
                $table->renameColumn('emp_id', 'emp_rank_id');
            });

            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
            throw $e;
        }
    }
}
