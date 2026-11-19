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
        Schema::create('doc.surat_perintah_penangkapan_documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('accident_id')->index();
            $table->string('document_category_id')->nullable()->default('0301');
            $table->string('status_id')->nullable()->default('2');

            // Header & Identitas Surat
            $table->string('kopstuk_1')->nullable();
            $table->string('kopstuk_2')->nullable();
            $table->string('kopstuk_3')->nullable();
            $table->string('nomor_surat')->nullable();
            $table->date('tanggal_surat')->nullable();
            $table->string('dikeluarkan_di')->nullable();

            // Pertimbangan
            $table->text('pertimbangan')->nullable();

            // 7 Dasar Hukum (Spesimen S-10.1.1)
            $table->text('dasar_kuhap')->nullable();
            $table->text('dasar_polri')->nullable();
            $table->text('dasar_uu_pidana')->nullable();
            $table->string('dasar_lp_nomor')->nullable();
            $table->date('dasar_lp_tanggal')->nullable();
            $table->string('dasar_sp_sidik_nomor')->nullable();
            $table->date('dasar_sp_sidik_tanggal')->nullable();
            $table->string('dasar_sp_gas_nomor')->nullable();
            $table->date('dasar_sp_gas_tanggal')->nullable();
            $table->string('dasar_sp_tap_nomor')->nullable();
            $table->date('dasar_sp_tap_tanggal')->nullable();
            $table->string('dasar_sp_tap_nama')->nullable();

            // Petugas yang Diperintahkan (Kepada) - Repeater JSONB
            $table->jsonb('officers')->nullable();

            // Identitas Tersangka (Untuk Poin 1)
            $table->uuid('tersangka_id')->nullable();
            $table->string('tersangka_nama')->nullable();
            $table->string('tersangka_nik')->nullable();
            $table->string('tersangka_kewarganegaraan')->nullable()->default('Indonesia');
            $table->string('tersangka_jenis_kelamin')->nullable();
            $table->string('tersangka_tempat_lahir')->nullable();
            $table->date('tersangka_tanggal_lahir')->nullable();
            $table->string('tersangka_pekerjaan')->nullable();
            $table->string('tersangka_agama')->nullable();
            $table->text('tersangka_alamat')->nullable();

            // Uraian Perkara & Pasal Pidana
            $table->text('uraian_tindak_pidana')->nullable();
            $table->string('pasal_pidana')->nullable();
            $table->string('uu_pidana_nomor')->nullable();
            $table->string('uu_pidana_tahun')->nullable();
            $table->string('uu_pidana_tentang')->nullable();
            $table->text('tkp')->nullable();
            $table->text('waktu_kejadian')->nullable();

            // Ketentuan Perintah (Untuk Poin 2 s/d 4)
            $table->text('perintah_penggeledahan')->nullable();
            $table->date('berlaku_mulai')->nullable();
            $table->string('berlaku_sampai')->nullable();
            $table->text('perintah_laporan')->nullable();

            // Penandatangan Perintah
            // Kiri: Yang Menerima Perintah
            $table->string('penerima_nama')->nullable();
            $table->string('penerima_pangkat')->nullable();
            $table->string('penerima_nrp')->nullable();
            $table->string('penerima_jabatan')->nullable();

            // Kanan: ATASAN PENYIDIK
            $table->string('atasan_nama')->nullable();
            $table->string('atasan_pangkat')->nullable();
            $table->string('atasan_nrp')->nullable();
            $table->string('atasan_jabatan')->nullable()->default('ATASAN PENYIDIK');

            // Lembar Tanda Terima / Penyerahan (Bawah)
            $table->string('penyerahan_hari')->nullable();
            $table->date('penyerahan_tanggal')->nullable();
            $table->string('yang_menerima_nama')->nullable();
            $table->string('yang_menyerahkan_nama')->nullable();
            $table->string('yang_menyerahkan_pangkat')->nullable();
            $table->string('yang_menyerahkan_nrp')->nullable();

            // Audit Trail
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
        Schema::dropIfExists('doc.surat_perintah_penangkapan_documents');
    }
};
