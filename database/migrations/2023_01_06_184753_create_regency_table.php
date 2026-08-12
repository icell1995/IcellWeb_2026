<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegencyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('regency', function (Blueprint $table) {
            $table->string('id', 4)->primary();
            $table->string('province_id', 2);
            $table->string('name');
            $table->smallInteger('sort')->nullable();
            $table->string('timezone',2)->nullable();
            $table->smallInteger('state')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('regency');
    }
}
