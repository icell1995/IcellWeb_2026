<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDaftarTersangkaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daftar_tersangka', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('accident_id');
            $table->string('name');
            $table->string('gender');
            $table->string('city');
            $table->date('birth_date');
            $table->string('religion');
            $table->string('job');
            $table->string('education');
            $table->string('phone');
            $table->string('citizen');
            $table->text('address');
            $table->string('identity_type');
            $table->string('identity_no');
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
        Schema::dropIfExists('daftar_tersangka');
    }
}
