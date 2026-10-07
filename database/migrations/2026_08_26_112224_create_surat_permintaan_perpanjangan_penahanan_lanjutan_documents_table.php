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
        // 1. Tabel Utama Dokumen S-22
        Schema::create('doc.surat_permintaan_perpanjangan_penahanan_lanjutan_documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('accident_id')->index();
            $table->uuid('surat_perintah_penahanan_document_id')->nullable()->index('s22_s17_doc_id_idx');
            $table->uuid('surat_permohonan_perpanjangan_penahanan_kejaksaan_document_id')->nullable()->index('s22_s21_doc_id_idx');
            $table->string('document_category_id')->nullable()->default('0606');
            $table->string('status_id')->nullable();

            $table->string('nomor_surat')->nullable();
            $table->date('tanggal_surat')->nullable();
            $table->string('klasifikasi_surat_id')->nullable();
            $table->string('lampiran_surat')->nullable();
            $table->string('kejaksaan_id')->nullable();
            $table->string('nama_pengadilan_negeri')->nullable();

            $table->string('nomor_spdp')->nullable();
            $table->date('tanggal_spdp')->nullable();
            $table->string('kode_satker_penerbit_spdp')->nullable();

            $table->string('nomor_sket_tersangka')->nullable();
            $table->date('tanggal_sket_tersangka')->nullable();

            $table->string('nomor_surat_perintah_penahanan')->nullable();
            $table->date('tanggal_surat_perintah_penahanan')->nullable();

            $table->string('nama_kejaksaan_surat_perpanjangan')->nullable();
            $table->string('nomor_surat_perpanjangan_kejaksaan')->nullable();
            $table->date('tanggal_surat_perpanjangan_kejaksaan')->nullable();

            $table->string('nomor_surat_perintah_perpanjangan_penahanan')->nullable();
            $table->date('tanggal_surat_perintah_perpanjangan_penahanan')->nullable();

            $table->date('kejaksaan_akhir_tanggal')->nullable();
            $table->text('alasan_perpanjangan')->nullable();
            $table->text('pasal_diduga')->nullable();
            $table->text('dugaan_tindak_pidana')->nullable();

            $table->integer('waktu_penahanan_hari')->default(30);
            $table->unsignedBigInteger('prison_id')->nullable()->index('s22_doc_prison_id_idx');
            $table->string('rutan_name')->nullable();
            $table->string('kode_satker_tempat_penahanan')->nullable();

            $table->date('tanggal_mulai_perpanjangan_penahanan')->nullable();
            $table->date('tanggal_akhir_perpanjangan_penahanan')->nullable();

            $table->string('contact_officer_id')->nullable();

            $table->jsonb('tembusan')->nullable();

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

        // 2. Tabel Petugas (Penandatangan SPPT-TI & Word)
        Schema::create('doc.surat_permintaan_perpanjangan_penahanan_lanjutan_document_officers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('document_id')->index('s22_doc_off_doc_id_idx');
            $table->string('officer_id')->nullable()->index('s22_doc_off_off_id_idx');

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

        // 3. Tabel Tersangka (Pivot)
        Schema::create('doc.surat_permintaan_perpanjangan_penahanan_lanjutan_document_suspects', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('document_id')->index('s22_doc_susp_doc_id_idx');
            $table->uuid('suspect_id')->index('s22_doc_susp_susp_id_idx');

            $table->timestamps();
        });

        // 4. Tabel Lampiran (Sesuai kebutuhan Pusiknas "daftar_dokumen_digital")
        Schema::create('doc.surat_permintaan_perpanjangan_penahanan_lanjutan_document_attachments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('document_id')->index('s22_doc_att_doc_id_idx');
            $table->string('file_id')->nullable()->index('s22_doc_att_file_id_idx');
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

        // 5. Tabel Undang-Undang Dokumen S-22
        Schema::create('doc.surat_permintaan_perpanjangan_penahanan_lanjutan_document_laws', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('document_id')->index('s22_doc_law_doc_id_idx');
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
        Schema::dropIfExists('doc.surat_permintaan_perpanjangan_penahanan_lanjutan_document_laws');
        Schema::dropIfExists('doc.surat_permintaan_perpanjangan_penahanan_lanjutan_document_attachments');
        Schema::dropIfExists('doc.surat_permintaan_perpanjangan_penahanan_lanjutan_document_suspects');
        Schema::dropIfExists('doc.surat_permintaan_perpanjangan_penahanan_lanjutan_document_officers');
        Schema::dropIfExists('doc.surat_permintaan_perpanjangan_penahanan_lanjutan_documents');
    }
};
