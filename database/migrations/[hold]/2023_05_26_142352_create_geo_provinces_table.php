<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGeoProvincesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('geo.provinces', function (Blueprint $table) {
            $table->string('id', 32)->primary();
            $table->string('country_id', 32)->nullable();
            $table->string('name');
            $table->bigInteger('sort')->nullable(true);
            $table->string('timezone', 8)->nullable(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('country_id')->references('id')->on('geo.countries')->onDelete('set null')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('geo.provinces', function (Blueprint $table) {
            $table->dropForeign(['country_id']);
        });

        Schema::dropIfExists('geo.provinces');
    }
}
