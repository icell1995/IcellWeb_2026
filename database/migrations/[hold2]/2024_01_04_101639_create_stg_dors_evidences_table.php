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
        Schema::create('public.stg_dors_evidences', function (Blueprint $table) {
            $table->uuid('id');

            $table->string('kode')->nullable();
            $table->string('kelompok')->nullable();
            $table->string('jenis')->nullable();
            $table->string('bentuk')->nullable();
            $table->string('satuan')->nullable();
            $table->string('jumlah')->nullable();
            $table->string('keterangan')->nullable();
            $table->string('ran_no_registrasi')->nullable();
            $table->string('ran_nama_pemilik')->nullable();
            $table->string('ran_alamat')->nullable();
            $table->string('ran_merk')->nullable();
            $table->string('ran_type')->nullable();
            $table->string('ran_jenis')->nullable();
            $table->string('ran_model')->nullable();
            $table->string('ran_thn_pembuatan')->nullable();
            $table->string('ran_isi_silinder')->nullable();
            $table->string('ran_no_rangka')->nullable();
            $table->string('ran_no_mesin')->nullable();
            $table->string('ran_warna')->nullable();
            $table->string('ran_bahan_bakar')->nullable();
            $table->string('ran_warna_tnkb')->nullable();
            $table->string('ran_thn_registrasi')->nullable();
            $table->string('ran_no_bpkb')->nullable();
            $table->string('dors_id'); //

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('public.stg_dors_evidences');
    }
};
