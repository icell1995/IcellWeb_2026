<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProsecutorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prosecutors', function (Blueprint $table) {
            $table->string('id');

            $table->string('name');
            $table->string('alias')->nullable();
            $table->string('top_agency')->nullable();
            $table->string('class')->comment('negeri, tinggi, agung')->nullable();

            $table->string('address')->nullable();
            $table->string('village')->nullable();
            $table->string('district')->nullable();
            $table->string('regency')->nullable();
            $table->string('province')->nullable();
            
            $table->boolean('is_active')->comment('true: active, false: inactive')->default(true);
            $table->integer('sort')->nullable();

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
        Schema::dropIfExists('prosecutors');
    }
}
