<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGeoRegenciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('geo.regencies', function (Blueprint $table) {
            $table->string('id', 32)->primary();
            $table->string('province_id', 32)->nullable(true);
            $table->string('name');
            $table->bigInteger('sort')->nullable(true);
            $table->string('timezone', 8)->nullable(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('province_id')->references('id')->on('geo.provinces')->onDelete('set null')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('geo.regencies', function (Blueprint $table) {
            $table->dropForeign(['province_id']);
        });
        Schema::dropIfExists('geo.regencies');
    }
}
