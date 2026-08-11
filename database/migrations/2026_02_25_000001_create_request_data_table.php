<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('request_data', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Catatan permintaan data
            $table->text('catatan_permintaan')->nullable();

            // Detail Pemohon
            $table->string('nama_lengkap_pemohon');
            $table->string('no_telp_pemohon')->nullable(); // tidak wajib saat korlantas

            // Institusi pemohon
            $table->enum('jenis_institusi', ['korlantas', 'polda', 'polres', 'lainnya']);
            $table->string('polda_id', 10)->nullable(); // jika jenis = polda atau polres
            $table->string('polres_id', 10)->nullable(); // jika jenis = polres
            $table->string('instansi_lain')->nullable(); // jika jenis = lainnya

            // Bukti permintaan (file: PDF, Gambar, Word)
            $table->string('evidence_path')->nullable();
            $table->string('evidence_name')->nullable();

            // Detail Penyedia
            $table->date('tanggal_permintaan');
            $table->date('tanggal_penyajian')->nullable();
            $table->unsignedBigInteger('penyedia_data_id')->nullable(); // user login saat input

            // File data yang disediakan (Excel, PPT, Word, dll.)
            $table->string('file_data_path')->nullable();
            $table->string('file_data_name')->nullable();

            // Status: true = aktif, false = soft-deleted
            $table->boolean('status')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('request_data');
    }
};
