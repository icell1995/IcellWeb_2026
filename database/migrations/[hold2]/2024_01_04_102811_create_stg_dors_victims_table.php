<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('public.stg_dors_victims', function (Blueprint $table) {
            $table->uuid('id');

            $table->string('jenis_korban')->nullable();
            $table->string('jenis_identitas')->nullable();
            $table->string('nama')->nullable();
            $table->string('pendidikan_terakhir')->nullable();
            $table->string('pekerjaan')->nullable();
            $table->string('suku')->nullable();
            $table->string('kewarganegaraan')->nullable();
            $table->string('alamat')->nullable();
            $table->string('alamat_non_nkri')->nullable();
            $table->string('no_hp')->nullable();
            $table->string('gender')->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->string('tgl_lahir')->nullable();
            $table->string('agama')->nullable();
            $table->string('status_korban')->nullable();
            $table->string('no_visum')->nullable();
            $table->string('dors_id'); //
            $table->string('nik')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('public.stg_dors_victims');
    }
};
