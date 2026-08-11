<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuthorizedSignatoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('authorized_signatories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('polres_id',4)->nullable();

            $table->string('register_number')->comment('NRP')->nullable();
            $table->string('identity_number')->comment('NIK')->nullable();
            $table->string('email')->comment('Email')->nullable();
            $table->string('phone')->comment('No. HP')->nullable();
            $table->string('name')->comment('Nama')->nullable();
            $table->string('position_id')->comment('Jabatan ID')->nullable();
            $table->string('position')->comment('Jabatan')->nullable();
            $table->string('rank_id')->comment('Pangkat ID')->nullable();
            $table->string('rank')->comment('Pangkat')->nullable();

            $table->timestamps();

            $table->foreign('polres_id')->references('id')->on('polres')->onDelete('set null')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop foreign key
        Schema::table('authorized_signatories', function (Blueprint $table) {
            $table->dropForeign('authorized_signatories_polres_id_foreign');
        });
        Schema::dropIfExists('authorized_signatories');
    }
}
