<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePengadilanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pengadilan', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('top_agency')->nullable();
            $table->string('class')->comment('negeri, tinggi, agung, umum')->nullable();
            
            $table->string('address')->nullable();
            $table->string('province')->nullable();
            $table->string('regency')->nullable();
            $table->string('district')->nullable();
            $table->string('village')->nullable();

            $table->smallInteger('state')->comment('1: active, 0: inactive')->default(1);
            $table->smallInteger('sort')->nullable();

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
        Schema::dropIfExists('pengadilan');
    }
}
