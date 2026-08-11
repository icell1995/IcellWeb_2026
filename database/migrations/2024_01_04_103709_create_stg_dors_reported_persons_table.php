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
        Schema::create('public.stg_dors_reported_persons', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('dors_accident_id')->nullable();
            $table->string('jenis_identitas')->nullable();
            $table->string('nik')->nullable();
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
            $table->dateTime('tgl_lahir')->nullable();
            $table->string('agama')->nullable();
            $table->string('status_terlapor')->nullable();
            $table->string('dors_id')->nullable();

            $table->timestamps();

            $table->foreign('dors_accident_id', 'fk_stg_dors_reported_persons_dors_accident_id')->references('id')->on('public.stg_dors_accidents')->onUpdate('CASCADE')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::table('public.stg_dors_reported_persons', function (Blueprint $table) {
        //     $table->dropForeign('fk_stg_dors_reported_persons_dors_accident_id');
        // });
        Schema::dropIfExists('public.stg_dors_reported_persons');
    }
};
