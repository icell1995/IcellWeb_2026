<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ModifyPublicSuspectsTable extends Migration
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
            Schema::table('public.suspects', function (Blueprint $table) {
                $table->string('identity_type_id')->nullable();
                $table->string('gender_id')->nullable();
                $table->string('ethnic_id')->nullable();
                $table->string('job_id')->nullable();
                $table->string('religion_id')->nullable();
                $table->string('education_id')->nullable();
                $table->string('marital_status_id')->nullable();
                $table->string('location_id')->nullable();

                $table->string('status')->nullable();
                $table->string('flag')->nullable();
                $table->string('class')->nullable();
                $table->string('model')->nullable();
                $table->string('insert_method')->nullable();

                $table->boolean('is_active')->default(true);
                
                $table->renameColumn('place_of_birth', 'birth_place');
                $table->renameColumn('date_of_birth', 'birth_date');

                $table->foreign('gender_id', 'fk_suspects_gender_id')->references('id')->on('lib.genders')->onDelete('restrict')->onUpdate('cascade');
                $table->foreign('religion_id', 'fk_suspects_religion_id')->references('id')->on('lib.religions')->onDelete('restrict')->onUpdate('cascade');
                $table->foreign('identity_type_id', 'fk_suspects_identity_type_id')->references('id')->on('lib.identity_types')->onDelete('restrict')->onUpdate('cascade');
                $table->foreign('education_id', 'fk_suspects_education_id')->references('id')->on('lib.educations')->onDelete('restrict')->onUpdate('cascade');
                $table->foreign('job_id', 'fk_suspects_job_id')->references('id')->on('lib.jobs')->onDelete('restrict')->onUpdate('cascade');
                $table->foreign('marital_status_id', 'fk_suspects_marital_status_id')->references('id')->on('lib.marital_statuses')->onDelete('restrict')->onUpdate('cascade');
                $table->foreign('ethnic_id', 'fk_suspects_ethnic_id')->references('id')->on('lib.ethnics')->onDelete('restrict')->onUpdate('cascade');
                $table->foreign('location_id', 'fk_suspects_location_id')->references('id')->on('lib.locations')->onDelete('restrict')->onUpdate('cascade');
            });

            DB::statement('UPDATE public.suspects SET is_active = true');

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
            Schema::table('public.suspects', function (Blueprint $table) {
                // drop foreign key
                $table->dropForeign('fk_suspects_identity_type_id');
                $table->dropForeign('fk_suspects_gender_id');
                $table->dropForeign('fk_suspects_ethnic_id');
                $table->dropForeign('fk_suspects_job_id');
                $table->dropForeign('fk_suspects_religion_id');
                $table->dropForeign('fk_suspects_education_id');
                $table->dropForeign('fk_suspects_marital_status_id');
                $table->dropForeign('fk_suspects_location_id');

                // drop column
                $table->dropColumn('identity_type_id');
                $table->dropColumn('gender_id');
                $table->dropColumn('ethnic_id');
                $table->dropColumn('job_id');
                $table->dropColumn('religion_id');
                $table->dropColumn('education_id');
                $table->dropColumn('marital_status_id');
                $table->dropColumn('location_id');

                $table->dropColumn('status');
                $table->dropColumn('flag');
                $table->dropColumn('class');
                $table->dropColumn('model');
                $table->dropColumn('insert_method');
                
                $table->dropColumn('is_active');
                
                // rename column
                $table->renameColumn('birth_place', 'place_of_birth');
                $table->renameColumn('birth_date', 'date_of_birth');
            });
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
