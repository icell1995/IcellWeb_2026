<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ModifyIdColumnToSomeLibTables extends Migration
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
                $table->string('id')->change();
            });

            Schema::table('lib.case_degree_types', function (Blueprint $table) {
                $table->string('id')->change();
            });

            Schema::table('lib.case_keywords', function (Blueprint $table) {
                $table->string('id')->change();
            });
           
            Schema::table('lib.crime_classes', function (Blueprint $table) {
                $table->string('id')->change();
            });
          
            Schema::table('lib.crime_constitutions', function (Blueprint $table) {
                $table->string('id')->change();
            });
        
            Schema::table('lib.crime_types', function (Blueprint $table) {
                $table->string('id')->change();
            });
           
            Schema::table('lib.document_classifications', function (Blueprint $table) {
                $table->string('id')->change();
            });
            
            Schema::table('lib.educations', function (Blueprint $table) {
                $table->string('id')->change();
            });
          
            Schema::table('lib.ethnics', function (Blueprint $table) {
                $table->string('id')->change();
            });
            
            Schema::table('lib.identity_types', function (Blueprint $table) {
                $table->string('id')->change();
            });
            
            Schema::table('lib.jobs', function (Blueprint $table) {
                $table->string('id')->change();
            });
           
            Schema::table('lib.positions', function (Blueprint $table) {
                $table->string('id')->change();
            });
            
            Schema::table('lib.religions', function (Blueprint $table) {
                $table->string('id')->change();
            });
            
            Schema::table('lib.suspect_sources', function (Blueprint $table) {
                $table->string('id')->change();
            });
          
            Schema::table('lib.timezones', function (Blueprint $table) {
                $table->string('id')->change();
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
            //lib.case_classifications
            Schema::table('lib.case_classifications', function (Blueprint $table) {
                $table->dropColumn('id');
            });
            Schema::table('lib.case_classifications', function (Blueprint $table) {
                $table->bigIncrements('id');
            });
        
            //lib.case_degree_types
            Schema::table('lib.case_degree_types', function (Blueprint $table) {
                $table->dropColumn('id');
            });
            Schema::table('lib.case_degree_types', function (Blueprint $table) {
                $table->bigIncrements('id');
            });

            //lib.case_keywords
            Schema::table('lib.case_keywords', function (Blueprint $table) {
                $table->dropColumn('id');
            });
            Schema::table('lib.case_keywords', function (Blueprint $table) {
                $table->bigIncrements('id');
            });

            //lib.crime_classes
            Schema::table('lib.crime_classes', function (Blueprint $table) {
                $table->dropColumn('id');
            });
            Schema::table('lib.crime_classes', function (Blueprint $table) {
                $table->bigIncrements('id');
            });

            //lib.crime_constitutions
            Schema::table('lib.crime_constitutions', function (Blueprint $table) {
                $table->dropColumn('id');
            });
            Schema::table('lib.crime_constitutions', function (Blueprint $table) {
                $table->bigIncrements('id');
            });

            //lib.crime_types
            Schema::table('lib.crime_types', function (Blueprint $table) {
                $table->dropColumn('id');
            });
            Schema::table('lib.crime_types', function (Blueprint $table) {
                $table->bigIncrements('id');
            });

            //lib.document_classifications
            Schema::table('lib.document_classifications', function (Blueprint $table) {
                $table->dropColumn('id');
            });
            Schema::table('lib.document_classifications', function (Blueprint $table) {
                $table->bigIncrements('id');
            });

            //lib.educations
            Schema::table('lib.educations', function (Blueprint $table) {
                $table->dropColumn('id');
            });
            Schema::table('lib.educations', function (Blueprint $table) {
                $table->bigIncrements('id');
            });

            //lib.ethnics
            Schema::table('lib.ethnics', function (Blueprint $table) {
                $table->dropColumn('id');
            });
            Schema::table('lib.ethnics', function (Blueprint $table) {
                $table->bigIncrements('id');
            });

            //lib.identity_types
            Schema::table('lib.identity_types', function (Blueprint $table) {
                $table->dropColumn('id');
            });
            Schema::table('lib.identity_types', function (Blueprint $table) {
                $table->bigIncrements('id');
            });

            //lib.jobs
            Schema::table('lib.jobs', function (Blueprint $table) {
                $table->dropColumn('id');
            });
            Schema::table('lib.jobs', function (Blueprint $table) {
                $table->bigIncrements('id');
            });

            //lib.positions
            Schema::table('lib.positions', function (Blueprint $table) {
                $table->dropColumn('id');
            });
            Schema::table('lib.positions', function (Blueprint $table) {
                $table->bigIncrements('id');
            });

            //lib.religions
            Schema::table('lib.religions', function (Blueprint $table) {
                $table->dropColumn('id');
            });
            Schema::table('lib.religions', function (Blueprint $table) {
                $table->bigIncrements('id');
            });

            //lib.suspect_sources
            Schema::table('lib.suspect_sources', function (Blueprint $table) {
                $table->dropColumn('id');
            });
            Schema::table('lib.suspect_sources', function (Blueprint $table) {
                $table->bigIncrements('id');
            });

            //lib.timezones
            Schema::table('lib.timezones', function (Blueprint $table) {
                $table->dropColumn('id');
            });
            Schema::table('lib.timezones', function (Blueprint $table) {
                $table->bigIncrements('id');
            });

            DB::commit();

        }catch(\Exception $e){
            DB::rollback();
            throw $e;
        }
    }
}
