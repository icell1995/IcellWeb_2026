<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Surat Permohonan Perpanjangan Penahanan Kejaksaan
     * Kode Dokumen SPP-TI / Internal: DCT-0605 / DCT-0903
     */
    public function up(): void
    {
        DB::beginTransaction();
        try {
            DB::statement('CREATE SCHEMA IF NOT EXISTS doc;');

            if (!Schema::hasTable('doc.surat_permohonan_perpanjangan_penahanan_kejaksaan_documents')) {
                Schema::create('doc.surat_permohonan_perpanjangan_penahanan_kejaksaan_documents', function (Blueprint $table) {
                    $table->uuid('id')->primary();
                    $table->uuid('accident_id')->nullable();
                    $table->uuid('surat_perintah_penyidikan_document_id')->nullable();
                    $table->uuid('surat_ketetapan_penetapan_tersangka_id')->nullable();
                    $table->uuid('surat_perintah_penahanan_document_id')->nullable();
                    $table->string('prosecutor_id')->nullable()->comment('ID Kejaksaan Tujuan');

                    // Identitas Dokumen
                    $table->string('nomor')->nullable()->comment('Nomor surat permohonan');
                    $table->date('tanggal')->nullable()->comment('Tanggal surat permohonan');
                    $table->string('klasifikasi')->nullable()->default('BIASA')->comment('Klasifikasi surat (BIASA / RAHASIA)');
                    $table->string('lampiran')->nullable()->default('1 (satu) Berkas')->comment('Lampiran surat');
                    $table->string('tempat_surat')->nullable()->comment('Kota / Tempat terbitnya surat');

                    // Kejaksaan Tujuan
                    $table->string('nama_kejaksaan')->nullable()->comment('Nama Kejaksaan Penerima');
                    $table->string('lokasi_kejaksaan')->nullable()->comment('Lokasi/Kota Kejaksaan Penerima');

                    // Poin 1 (Rujukan)
                    $table->string('nomor_spdp')->nullable()->comment('Nomor SPDP');
                    $table->date('tanggal_spdp')->nullable()->comment('Tanggal SPDP');
                    $table->string('kode_satker_penerbit_spdp')->nullable()->comment('Satker penerbit SPDP');
                    $table->string('nomor_sprindik')->nullable()->comment('Nomor Sprindik');
                    $table->date('tanggal_sprindik')->nullable()->comment('Tanggal Sprindik');
                    $table->string('nomor_penetapan_tersangka')->nullable()->comment('Nomor SKET Penetapan Tersangka');
                    $table->date('tanggal_penetapan_tersangka')->nullable()->comment('Tanggal SKET Penetapan Tersangka');
                    $table->string('nomor_surat_perintah_penahanan')->nullable()->comment('Nomor Surat Perintah Penahanan');
                    $table->date('tanggal_surat_perintah_penahanan')->nullable()->comment('Tanggal Surat Perintah Penahanan');

                    // Poin 2 (Perkara)
                    $table->string('satker_penyidik')->nullable()->comment('Satker Penyidik');
                    $table->text('dugaan_tindak_pidana')->nullable()->comment('Uraian dugaan tindak pidana');
                    $table->string('pasal_diduga')->nullable()->comment('Pasal yang disangkakan');
                    $table->text('tempat_kejadian')->nullable()->comment('TKP');
                    $table->string('kurun_waktu')->nullable()->comment('Waktu kejadian perkara');

                    // Poin 3 (Masa Penahanan & Perpanjangan)
                    $table->date('tanggal_akhir_penahanan_lama')->nullable()->comment('Akhir masa penahanan penyidik 20 hari');
                    $table->string('nama_rutan')->nullable()->comment('Nama rutan tempat penahanan');
                    $table->integer('jumlah_hari')->nullable()->default(40)->comment('Masa perpanjangan hari');
                    $table->date('tanggal_mulai_perpanjangan')->nullable()->comment('Tanggal mulai perpanjangan penahanan');
                    $table->date('tanggal_akhir_perpanjangan')->nullable()->comment('Tanggal akhir perpanjangan penahanan');
                    $table->string('contact_officer_id')->nullable()->comment('Officer ID Penyidik/Penyidik Pembantu Penghubung');
                    $table->string('contact_officer_name')->nullable()->comment('Nama Penyidik Penghubung');
                    $table->string('contact_officer_phone')->nullable()->comment('No HP Penyidik Penghubung');

                    // Tembusan & Signatory
                    $table->json('carbon_copies')->nullable()->comment('Daftar Tembusan');
                    $table->string('signatory_id')->nullable()->comment('Officer ID Pejabat Penandatangan');
                    $table->text('signatory_head_text')->nullable()->comment('Teks a.n. Kepala Kepolisian Resor ...');
                    $table->string('signatory_position')->nullable()->comment('Jabatan penandatangan');
                    $table->string('signatory_name')->nullable()->comment('Nama penandatangan');
                    $table->string('signatory_rank')->nullable()->comment('Pangkat penandatangan');
                    $table->string('signatory_nrp')->nullable()->comment('NRP penandatangan');

                    // Kompatibilitas Sistem Dokumen Internal
                    $table->string('document_number')->nullable();
                    $table->date('document_date')->nullable();
                    $table->string('status_id')->nullable()->default('2');
                    $table->string('document_category_id')->nullable()->default('0605');
                    $table->boolean('is_active')->default(true);
                    $table->boolean('is_legacy')->default(false);
                    $table->dateTime('last_synced_at')->nullable();
                    $table->dateTime('released_at')->nullable();
                    $table->dateTime('approved_at')->nullable();
                    $table->dateTime('rejected_at')->nullable();
                    $table->json('messages')->nullable();
                    $table->json('ip_addresses')->nullable();
                    $table->json('payload')->nullable();

                    $table->unsignedBigInteger('created_by_user_id')->nullable();
                    $table->unsignedBigInteger('updated_by_user_id')->nullable();
                    $table->unsignedBigInteger('deleted_by_user_id')->nullable();

                    $table->timestamps();
                    $table->softDeletes();

                    $table->foreign('accident_id', 'fk_spp_kejaksaan_docs_accident_id')
                        ->references('id')->on('public.accidents')
                        ->onDelete('set null')->onUpdate('cascade');
                });
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doc.surat_permohonan_perpanjangan_penahanan_kejaksaan_documents');
    }
};
