<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePolresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('polres', function (Blueprint $table) {
            $table->string('id',4)->primary();
            $table->string('name');
            $table->smallInteger('sort');
            $table->string('polda_id',2);
            $table->string('provinsi_id',10)->nullable();
            $table->string('alamat')->nullable();
            $table->smallInteger('state');
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
        Schema::dropIfExists('polres');
    }
}
