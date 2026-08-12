<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvolvedPeoplesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('involved_peoples', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->uuid('accident_id');
            $table->string('dors_id')->nullable();
            
            $table->string('identity_number')->nullable();
            $table->string('name');
            $table->string('name_alias')->nullable();

            $table->string('nationality')->nullable();
            $table->text('statement')->nullable();
            
            $table->string('birth_place')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('age')->nullable();

            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            
            $table->string('phone_number')->nullable();
            $table->string('email')->nullable();
            
            $table->string('identity_type_id')->nullable();
            $table->string('education_id')->nullable();
            $table->string('job_id')->nullable();
            $table->string('ethnic_id')->nullable();
            $table->string('gender_id')->nullable();
            $table->string('religion_id')->nullable();
            $table->string('marital_status_id')->nullable();
            
            $table->string('address')->nullable();
            $table->string('country_id')->nullable();
            $table->string('province_id')->nullable();
            $table->string('regency_id')->nullable();
            $table->string('district_id')->nullable();
            $table->string('village_id')->nullable();

            $table->string('status')->nullable();
            $table->string('flag')->nullable();
            $table->string('class')->nullable();
            $table->string('group')->nullable();
            $table->string('insert_method')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();
            
            $table->foreign('accident_id', 'fk_involved_peoples_accident_id')->references('id')->on('public.accidents')->onDelete('cascade')->onUpdate('cascade');
            
            $table->foreign('identity_type_id', 'fk_involved_peoples_identity_type_id')->references('id')->on('lib.identity_types')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('education_id', 'fk_involved_peoples_education_id')->references('id')->on('lib.educations')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('job_id', 'fk_involved_peoples_job_id')->references('id')->on('lib.jobs')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('ethnic_id', 'fk_involved_peoples_ethnic_id')->references('id')->on('lib.ethnics')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('gender_id', 'fk_involved_peoples_gender_id')->references('id')->on('lib.genders')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('religion_id', 'fk_involved_peoples_religion_id')->references('id')->on('lib.religions')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('marital_status_id', 'fk_involved_peoples_marital_status_id')->references('id')->on('lib.marital_statuses')->onDelete('restrict')->onUpdate('cascade');
            
            $table->foreign('country_id', 'fk_involved_peoples_country_id')->references('id')->on('lib.locations')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('province_id', 'fk_involved_peoples_province_id')->references('id')->on('lib.locations')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('regency_id', 'fk_involved_peoples_regency_id')->references('id')->on('lib.locations')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('district_id', 'fk_involved_peoples_district_id')->references('id')->on('lib.locations')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('village_id', 'fk_involved_peoples_village_id')->references('id')->on('lib.locations')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // drop foreign key
        Schema::table('public.involved_peoples', function (Blueprint $table) {
            // drop foreign key
            $table->dropForeign('fk_involved_peoples_accident_id');

            $table->dropForeign('fk_involved_peoples_identity_type_id');
            $table->dropForeign('fk_involved_peoples_gender_id');
            $table->dropForeign('fk_involved_peoples_ethnic_id');
            $table->dropForeign('fk_involved_peoples_job_id');
            $table->dropForeign('fk_involved_peoples_religion_id');
            $table->dropForeign('fk_involved_peoples_education_id');
            $table->dropForeign('fk_involved_peoples_marital_status_id');

            $table->dropForeign('fk_involved_peoples_country_id');
            $table->dropForeign('fk_involved_peoples_province_id');
            $table->dropForeign('fk_involved_peoples_regency_id');
            $table->dropForeign('fk_involved_peoples_district_id');
            $table->dropForeign('fk_involved_peoples_village_id');
        });

        Schema::dropIfExists('public.involved_peoples');
    }
}
