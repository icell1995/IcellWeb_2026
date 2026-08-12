<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddSomeColumnsToOfficersTable extends Migration
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
            Schema::table('officers', function (Blueprint $table) {
                $table->string('identity_type_id')->nullable();
                $table->string('employment_type_id')->nullable();
                $table->date('birth_date')->nullable();
                $table->string('gender_id')->nullable();
                $table->string('religion_id')->nullable();
                $table->string('education_id')->nullable();
                $table->string('education_institution_name')->nullable();
                $table->string('police_diktuk_education_id')->nullable();
                $table->string('police_diktuk_education_graduate_year')->nullable();

                $table->foreign('identity_type_id', 'fk_officers_identity_type_id')
                    ->references('id')
                    ->on('lib.identity_types')
                    ->onDelete('restrict')
                    ->onUpdate('cascade');

                $table->foreign('employment_type_id', 'fk_officers_employment_type_id')
                    ->references('id')
                    ->on('lib.employment_types')
                    ->onDelete('restrict')
                    ->onUpdate('cascade');

                $table->foreign('gender_id', 'fk_officers_gender_id')
                    ->references('id')
                    ->on('lib.genders')
                    ->onDelete('restrict')
                    ->onUpdate('cascade');

                $table->foreign('religion_id', 'fk_officers_religion_id')
                    ->references('id')
                    ->on('lib.religions')
                    ->onDelete('restrict')
                    ->onUpdate('cascade');

                $table->foreign('education_id', 'fk_officers_education_id')
                    ->references('id')
                    ->on('lib.educations')
                    ->onDelete('restrict')
                    ->onUpdate('cascade');

                $table->foreign('police_diktuk_education_id', 'fk_officers_police_diktuk_education_id')
                    ->references('id')
                    ->on('lib.police_diktuk_educations')
                    ->onDelete('restrict')
                    ->onUpdate('cascade');
            });

            DB::commit();
        }catch (\Exception $e){
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
            Schema::table('officers', function (Blueprint $table) {
                $table->dropForeign('fk_officers_identity_type_id');
                $table->dropForeign('fk_officers_employment_type_id');
                $table->dropForeign('fk_officers_gender_id');
                $table->dropForeign('fk_officers_religion_id');
                $table->dropForeign('fk_officers_education_id');
                $table->dropForeign('fk_officers_police_diktuk_education_id');
            });

            Schema::table('officers', function (Blueprint $table) {
                $table->dropColumn('identity_type_id');
                $table->dropColumn('employment_type_id');
                $table->dropColumn('birth_date');
                $table->dropColumn('gender_id');
                $table->dropColumn('religion_id');
                $table->dropColumn('education_id');
                $table->dropColumn('education_institution_name');
                $table->dropColumn('police_diktuk_education_id');
            });

            DB::commit();
        }catch (\Exception $e){
            DB::rollback();
            throw $e;
        }
    }
}
