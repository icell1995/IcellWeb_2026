<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccidentRecentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accident_recent', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->string('description');
            $table->string('user_id')->nullable();
            $table->string('polres_id',4)->nullable();
            $table->string('road_name')->nullable();
            $table->date('accident_date');
            $table->float('latitude')->nullable();
            $table->float('longtitude')->nullable();
            $table->string('photo')->nullable();   
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
        Schema::dropIfExists('accident_recent');
    }
}
