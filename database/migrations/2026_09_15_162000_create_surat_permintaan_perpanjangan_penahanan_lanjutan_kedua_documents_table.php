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
        // 1. Tabel Utama Dokumen S-22 (Kedua 30 Hari)
        Schema::create('doc.surat_perpanjangan_penahanan_kedua_documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('accident_id')->index();
            $table->string('document_category_id')->nullable();
            $table->string('status_id')->nullable();

            // Identitas Surat
            $table->string('nomor_surat')->nullable();
            $table->date('tanggal_surat')->nullable();
            $table->string('klasifikasi_surat_id')->nullable();
            $table->string('lampiran_surat')->nullable();
            $table->string('kejaksaan_id')->nullable();
            $table->string('nama_pengadilan_negeri')->nullable();

            // Poin 1.f - Sprint Sidik (Baru di dokumen Kedua 30 Hari)
            $table->string('nomor_surat_perintah_penyidikan')->nullable();
            $table->date('tanggal_surat_perintah_penyidikan')->nullable();

            // Poin 1.g - SPDP
            $table->string('nomor_spdp')->nullable();
            $table->date('tanggal_spdp')->nullable();
            $table->string('kode_satker_penerbit_spdp')->nullable();

            // Poin 1.h - S.Ket Penetapan Tersangka
            $table->string('nomor_sket_tersangka')->nullable();
            $table->date('tanggal_sket_tersangka')->nullable();

            // Poin 1.i - Sprint Penahanan Penyidik S-17
            $table->string('nomor_surat_perintah_penahanan')->nullable();
            $table->date('tanggal_surat_perintah_penahanan')->nullable();

            // Poin 1.j - Surat Perpanjangan Penahanan dari Kejaksaan
            $table->string('nama_kejaksaan_surat_perpanjangan')->nullable();
            $table->string('nomor_surat_perpanjangan_kejaksaan')->nullable();
            $table->date('tanggal_surat_perpanjangan_kejaksaan')->nullable();

            // Poin 1.k - Sprint Perpanjangan Penahanan (JPU)
            $table->string('nomor_surat_perintah_perpanjangan_penahanan')->nullable();
            $table->date('tanggal_surat_perintah_perpanjangan_penahanan')->nullable();

            // Poin 1.l - S.Ket Perpanjangan KPN Pertama (KPN1) (Baru di dokumen Kedua 30 Hari)
            $table->string('nomor_sket_perpanjangan_kpn_pertama')->nullable();
            $table->date('tanggal_sket_perpanjangan_kpn_pertama')->nullable();

            // Poin 1.m - Sprint Perpanjangan Penahanan Penyidik (KPN1) (Baru di dokumen Kedua 30 Hari)
            $table->string('nomor_surat_perintah_perpanjangan_penahanan_kpn_pertama')->nullable();
            $table->date('tanggal_surat_perintah_perpanjangan_penahanan_kpn_pertama')->nullable();

            // Poin 3 - Batas Masa Penahanan Pengadilan Negeri Berakhir
            $table->date('pengadilan_negeri_akhir_tanggal')->nullable();
            $table->date('kejaksaan_akhir_tanggal')->nullable();

            // Alasan & Dugaan Tindak Pidana
            $table->text('alasan_perpanjangan')->nullable();
            $table->text('pasal_diduga')->nullable();
            $table->text('dugaan_tindak_pidana')->nullable();

            // Durasi & Fasilitas Penahanan
            $table->integer('waktu_penahanan_hari')->default(30);
            $table->unsignedBigInteger('prison_id')->nullable()->index('s22_kedua_doc_prison_id_idx');
            $table->string('rutan_name')->nullable();
            $table->string('kode_satker_tempat_penahanan')->nullable();
            $table->date('tanggal_mulai_perpanjangan_penahanan')->nullable();
            $table->date('tanggal_akhir_perpanjangan_penahanan')->nullable();

            // Petugas Kontak & Tembusan
            $table->string('contact_officer_id')->nullable();
            $table->jsonb('tembusan')->nullable();

            // Audit Trail
            $table->unsignedBigInteger('created_by_user_id')->nullable();
            $table->unsignedBigInteger('updated_by_user_id')->nullable();
            $table->unsignedBigInteger('deleted_by_user_id')->nullable();
            $table->jsonb('ip_addresses')->nullable();
            $table->jsonb('timestamps')->nullable();
            $table->jsonb('messages')->nullable();

            $table->dateTime('released_at')->nullable();
            $table->dateTime('last_synced_at')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Tabel Petugas Penandatangan
        Schema::create('doc.surat_perpanjangan_penahanan_kedua_officers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('document_id')->index('s89_off_doc_id_idx');
            $table->string('officer_id')->nullable()->index('s89_off_off_id_idx');

            $table->string('register_number')->nullable();
            $table->string('first_title')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('last_title')->nullable();

            $table->string('rank_id')->nullable();
            $table->string('position_id')->nullable();
            $table->jsonb('rank')->nullable();
            $table->jsonb('position')->nullable();
            $table->jsonb('role')->nullable();

            $table->string('phone_number')->nullable();
            $table->string('email')->nullable();
            $table->string('police_id')->nullable();

            $table->string('type')->default('penandatangan');
            $table->jsonb('status')->nullable();
            $table->jsonb('class')->nullable();
            $table->jsonb('flag')->nullable();

            $table->unsignedBigInteger('created_by_user_id')->nullable();
            $table->unsignedBigInteger('updated_by_user_id')->nullable();
            $table->unsignedBigInteger('deleted_by_user_id')->nullable();
            $table->jsonb('ip_addresses')->nullable();
            $table->jsonb('timestamps')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        // 3. Tabel Pivot Tersangka
        Schema::create('doc.surat_perpanjangan_penahanan_kedua_suspects', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('document_id')->index('s89_susp_doc_id_idx');
            $table->uuid('suspect_id')->index('s89_susp_susp_id_idx');

            $table->timestamps();
        });

        // 4. Tabel Lampiran Dokumen
        Schema::create('doc.surat_perpanjangan_penahanan_kedua_attachments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('document_id')->index('s89_att_doc_id_idx');
            $table->string('file_id')->nullable()->index('s89_att_file_id_idx');
            $table->string('document_type')->nullable();
            $table->string('name')->nullable();
            $table->string('original_name')->nullable();
            $table->string('extension')->nullable();
            $table->string('mimetype')->nullable();
            $table->string('size')->nullable();
            $table->string('path')->nullable();
            $table->string('type')->nullable();
            $table->string('flag')->nullable();

            $table->unsignedBigInteger('created_by_user_id')->nullable();
            $table->unsignedBigInteger('updated_by_user_id')->nullable();
            $table->unsignedBigInteger('deleted_by_user_id')->nullable();
            $table->jsonb('ip_addresses')->nullable();
            $table->jsonb('timestamps')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        // 5. Tabel Undang-Undang Terkait Dokumen
        Schema::create('doc.surat_perpanjangan_penahanan_kedua_laws', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('document_id')->index('s89_law_doc_id_idx');
            $table->string('crime_type_id')->nullable();
            $table->string('crime_class_id')->nullable();
            $table->string('crime_constitution_id')->nullable();
            $table->string('constitution_chapter')->nullable();
            $table->text('constitution')->nullable();
            $table->string('flag')->default('MAIN'); // 'MAIN' atau 'ADDITIONAL'

            $table->unsignedBigInteger('created_by_user_id')->nullable();
            $table->unsignedBigInteger('updated_by_user_id')->nullable();
            $table->unsignedBigInteger('deleted_by_user_id')->nullable();
            $table->jsonb('ip_addresses')->nullable();
            $table->jsonb('timestamps')->nullable();

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
        Schema::dropIfExists('doc.surat_perpanjangan_penahanan_kedua_laws');
        Schema::dropIfExists('doc.surat_perpanjangan_penahanan_kedua_attachments');
        Schema::dropIfExists('doc.surat_perpanjangan_penahanan_kedua_suspects');
        Schema::dropIfExists('doc.surat_perpanjangan_penahanan_kedua_officers');
        Schema::dropIfExists('doc.surat_perpanjangan_penahanan_kedua_documents');
    }
};
