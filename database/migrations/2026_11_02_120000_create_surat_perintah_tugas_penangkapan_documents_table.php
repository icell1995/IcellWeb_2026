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
        Schema::create('doc.surat_perintah_tugas_penangkapan_documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('accident_id');
            $table->foreign('accident_id')->references('id')->on('accidents')->onDelete('cascade');
            $table->string('document_category_id', 10)->default('0303');
            $table->string('status_id', 5)->default('2');

            // Kopstuk (Kiri Atas)
            $table->string('kopstuk_1')->nullable();
            $table->string('kopstuk_2')->nullable();
            $table->string('kopstuk_3')->nullable();
            $table->string('kopstuk_4')->nullable();

            // Header Surat
            $table->string('nomor_surat')->nullable();
            $table->date('tanggal_surat')->nullable();
            $table->string('dikeluarkan_di')->nullable();

            // Pertimbangan
            $table->text('pertimbangan')->nullable();

            // Dasar Hukum
            $table->text('dasar_kuhap')->nullable();
            $table->text('dasar_polri')->nullable();
            $table->text('dasar_uu_pidana')->nullable();
            $table->string('dasar_lp_nomor')->nullable();
            $table->date('dasar_lp_tanggal')->nullable();
            $table->string('dasar_sp_sidik_nomor')->nullable();
            $table->date('dasar_sp_sidik_tanggal')->nullable();
            $table->string('dasar_sp_kap_nomor')->nullable();
            $table->date('dasar_sp_kap_tanggal')->nullable();

            // Data Tersangka (Untuk Poin 1 "Untuk")
            $table->uuid('tersangka_id')->nullable();
            $table->string('tersangka_nama')->nullable();

            // Daftar Petugas Pelaksana (Kepada)
            $table->jsonb('officers')->nullable();

            // Ketentuan Untuk (Poin 1 s/d 3)
            $table->string('untuk_sp_kap_nomor')->nullable();
            $table->date('untuk_sp_kap_tanggal')->nullable();
            $table->date('berlaku_mulai')->nullable();
            $table->string('berlaku_sampai')->nullable();

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
        Schema::dropIfExists('doc.surat_perintah_tugas_penangkapan_documents');
    }
};

