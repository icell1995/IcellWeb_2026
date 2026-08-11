<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGeoDistrictsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('geo.districts', function (Blueprint $table) {
            $table->string('id', 32)->primary();
            $table->string('regency_id', 32)->nullable(true);
            $table->string('name');
            $table->bigInteger('sort')->nullable(true);
            $table->string('timezone', 8)->nullable(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('regency_id')->references('id')->on('geo.regencies')->onDelete('set null')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('geo.districts', function (Blueprint $table) {
            $table->dropForeign(['regency_id']);
        });
        Schema::dropIfExists('geo.districts');
    }
}
