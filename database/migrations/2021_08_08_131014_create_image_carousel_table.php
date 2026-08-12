<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImageCarouselTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('image_carousel', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->string('name_image');
            $table->string('description'); 
            $table->string('url'); 
            $table->string('state')->nullable();  
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
        Schema::dropIfExists('image_carousel');
    }
}
