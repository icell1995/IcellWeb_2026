<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPoliceIdColumnToPublicAccidentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('public.accidents', function (Blueprint $table) {
            $table->string('police_id')->nullable();

            $table->foreign('police_id', 'fk_accidents_police_id')->references('id')->on('lib.polices')->onDelete('set null')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('public.accidents', function (Blueprint $table) {
            $table->dropForeign('fk_accidents_police_id');
            
            $table->dropColumn('police_id');
        });
    }
}
