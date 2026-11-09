<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('doc.surat_permintaan_visum_documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('accident_id')->index();
            $table->string('document_category_id')->nullable()->default('0714');
            $table->string('status_id')->nullable()->default('2');

            // Header & Identitas Surat
            $table->string('nomor_surat')->nullable();
            $table->date('tanggal_surat')->nullable();
            $table->string('dikeluarkan_di')->nullable();
            $table->string('lampiran_surat')->nullable();

            // Tujuan Rumah Sakit
            $table->string('nama_rumah_sakit')->nullable();
            $table->string('kota_rumah_sakit')->nullable();

            // Rujukan LP & Satker
            $table->string('nomor_lp')->nullable();
            $table->date('tanggal_lp')->nullable();
            $table->string('satker_penyidik')->nullable();

            // Identitas Korban
            $table->string('nama_korban')->nullable();
            $table->string('nik_korban')->nullable();
            $table->string('jenis_kelamin')->nullable();
            $table->string('kewarganegaraan')->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('pekerjaan')->nullable();
            $table->string('agama')->nullable();
            $table->text('alamat')->nullable();

            // Riwayat / Peristiwa Sebelum ke RS
            $table->string('hari_lapor')->nullable();
            $table->date('tanggal_lapor')->nullable();
            $table->string('jam_lapor')->nullable();
            $table->string('penemu')->nullable();
            $table->string('hari_temu')->nullable();
            $table->date('tanggal_temu')->nullable();
            $table->string('jam_temu')->nullable();
            $table->text('lokasi_temu')->nullable();
            $table->string('jenis_luka')->nullable();

            // Pejabat Penandatangan
            $table->string('nama_penandatangan')->nullable();
            $table->string('pangkat_penandatangan')->nullable();
            $table->string('nrp_penandatangan')->nullable();

            // Penyidik Penghubung
            $table->string('nama_kontak')->nullable();
            $table->string('hp_kontak')->nullable();

            // Serah Terima RS
            $table->string('petugas_penyerah')->nullable();
            $table->string('petugas_rs')->nullable();

            // Tracking & Audit
            $table->unsignedBigInteger('created_by_user_id')->nullable();
            $table->unsignedBigInteger('updated_by_user_id')->nullable();
            $table->unsignedBigInteger('deleted_by_user_id')->nullable();
            $table->jsonb('ip_addresses')->nullable();
            $table->jsonb('timestamps')->nullable();
            $table->jsonb('messages')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('doc.surat_permintaan_visum_documents');
    }
};
