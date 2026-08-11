<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOfficerInvestigativeDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('officer_investigative_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('officer_id');

            $table->boolean('is_skep_penyidik_exists');
            $table->string('skep_penyidik_number')->nullable();

            $table->timestamps();

            $table->foreign('officer_id', 'fk_officer_investigative_details_officer_id')
                ->references('id')
                ->on('public.officers')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop foreign key fk_officer_investigative_details_officer_id
        Schema::table('public.officer_investigative_details', function (Blueprint $table) {
            $table->dropForeign('fk_officer_investigative_details_officer_id');
        });
        
        Schema::dropIfExists('public.officer_investigative_details');
    }
}
