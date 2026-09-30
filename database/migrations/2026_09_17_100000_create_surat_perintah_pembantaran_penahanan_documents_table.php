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
        // 1. Tabel Utama Dokumen S-23 (Surat Perintah Pembantaran Penahanan)
        Schema::create('doc.surat_perintah_pembantaran_penahanan_documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('accident_id')->index();
            $table->string('document_category_id')->nullable();
            $table->string('status_id')->nullable();

            // Identitas Dokumen
            $table->string('nomor_surat')->nullable();
            $table->date('tanggal_surat')->nullable();
            $table->string('dikeluarkan_di')->nullable();
            $table->text('pertimbangan')->nullable();

            // SPDP & Satker (JSON SPPT-TI)
            $table->string('nomor_spdp')->nullable();
            $table->date('tanggal_spdp')->nullable();
            $table->string('kode_satker_penerbit_spdp')->nullable();

            // Rujukan Dasar Perkara
            $table->string('nomor_surat_perintah_penyidikan')->nullable();
            $table->date('tanggal_surat_perintah_penyidikan')->nullable();
            $table->string('nomor_sket_tersangka')->nullable();
            $table->date('tanggal_sket_tersangka')->nullable();
            $table->string('nomor_surat_perintah_penahanan')->nullable();
            $table->date('tanggal_surat_perintah_penahanan')->nullable();

            // Data Dokter & Rumah Sakit (JSON SPPT-TI)
            $table->string('nama_dokter')->nullable();
            $table->string('nomor_surat_dokter')->nullable();
            $table->date('tanggal_surat_dokter')->nullable();
            $table->string('tempat_rawat_inap')->nullable();
            $table->string('kota_rumah_sakit')->nullable();
            $table->date('tanggal_mulai_rawat_inap')->nullable();

            // Lembar Tanda Terima Penyerahan
            $table->string('hari_penyerahan')->nullable();
            $table->date('tanggal_penyerahan')->nullable();
            $table->string('nama_penerima_keluarga')->nullable();
            $table->string('hubungan_penerima')->nullable();

            $table->jsonb('tembusan')->nullable();

            // Audit & Tracking
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

        // 2. Tabel Petugas S-23 (Penyidik, Penerima Perintah, Penyerah)
        Schema::create('doc.surat_perintah_pembantaran_penahanan_document_officers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('document_id')->index('s23_doc_off_doc_id_idx');
            $table->string('officer_id')->nullable()->index('s23_doc_off_off_id_idx');

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

        // 3. Tabel Tersangka S-23 (Pivot)
        Schema::create('doc.surat_perintah_pembantaran_penahanan_document_suspects', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('document_id')->index('s23_doc_susp_doc_id_idx');
            $table->uuid('suspect_id')->index('s23_doc_susp_susp_id_idx');

            $table->timestamps();
        });

        // 4. Tabel Lampiran S-23
        Schema::create('doc.surat_perintah_pembantaran_penahanan_document_attachments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('document_id')->index('s23_doc_att_doc_id_idx');
            $table->string('file_id')->nullable()->index('s23_doc_att_file_id_idx');
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

        // 5. Tabel Dasar Hukum S-23 (Pasal Pidana)
        Schema::create('doc.surat_perintah_pembantaran_penahanan_document_laws', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('document_id')->index('s23_doc_law_doc_id_idx');
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
        Schema::dropIfExists('doc.surat_perintah_pembantaran_penahanan_document_laws');
        Schema::dropIfExists('doc.surat_perintah_pembantaran_penahanan_document_attachments');
        Schema::dropIfExists('doc.surat_perintah_pembantaran_penahanan_document_suspects');
        Schema::dropIfExists('doc.surat_perintah_pembantaran_penahanan_document_officers');
        Schema::dropIfExists('doc.surat_perintah_pembantaran_penahanan_documents');
    }
};
