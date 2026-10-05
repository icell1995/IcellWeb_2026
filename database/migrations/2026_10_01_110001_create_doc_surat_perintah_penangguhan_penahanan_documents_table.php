<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Sesuai syarat format S-18 (Surat Perintah Penangguhan Penahanan / SPRIN GUHAN):
     * Kode Dokumen: s18 | Kode Proses SPP-TI: DAT-3
     * 
     * a. identitas_dokumen:
     * - nomor (WAJIB. Nomor S-18)
     * - tanggal (WAJIB. Tanggal S-18)
     * - nomor_spdp (WAJIB. Nomor SPDP - readonly)
     * - tanggal_spdp (WAJIB. Tanggal SPDP - readonly)
     * - kode_satker_penerbit_spdp (WAJIB. Kode Satker Penerbit SPDP - readonly)
     * - nomor_surat_perintah_penahanan (WAJIB. Nomor S-17 - readonly)
     * - tanggal_surat_permohonan (WAJIB. Tanggal Surat Permohonan Penangguhan)
     * 
     * b. konten_dokumen:
     * - jenis_jaminan: Opsional (nullable). Integer (master_jenisjaminan: 1: Uang, 2: Orang)
     * - besaran_uang_jaminan: Opsional (nullable). Number
     * - nomor_identitas_penjamin: Opsional (nullable). String
     * - nama_penjamin: Opsional (nullable). String
     * - alamat_penjamin: Opsional (nullable). String
     * - lokasi_penyimpanan_jaminan: Opsional (nullable). String, untuk jaminan uang
     * 
     * Kondisi poin 9 & 10 (bila ada) pada Dasar:
     * - has_perpanjangan_penahanan (boolean)
     */
    public function up(): void
    {
        DB::beginTransaction();
        try {
            DB::statement('CREATE SCHEMA IF NOT EXISTS doc;');

            if (!Schema::hasTable('doc.surat_perintah_penangguhan_penahanan_documents')) {
                Schema::create('doc.surat_perintah_penangguhan_penahanan_documents', function (Blueprint $table) {
                    $table->uuid('id')->primary();
                    $table->uuid('accident_id')->nullable();
                    $table->uuid('surat_perintah_penyidikan_document_id')->nullable();
                    $table->uuid('surat_ketetapan_penetapan_tersangka_id')->nullable();
                    $table->uuid('surat_perintah_penahanan_document_id')->nullable();

                    // Identitas Dokumen
                    $table->string('nomor')->nullable()->comment('WAJIB. Nomor S-18');
                    $table->date('tanggal')->nullable()->comment('WAJIB. Tanggal S-18');
                    $table->string('nomor_spdp')->nullable()->comment('WAJIB. Nomor SPDP');
                    $table->date('tanggal_spdp')->nullable()->comment('WAJIB. Tanggal SPDP');
                    $table->string('kode_satker_penerbit_spdp')->nullable()->comment('WAJIB. Kode Satker Penerbit SPDP');
                    $table->string('nomor_surat_perintah_penahanan')->nullable()->comment('WAJIB. Nomor S-17');
                    $table->date('tanggal_surat_permohonan')->nullable()->comment('WAJIB. Tanggal Surat Permohonan Penangguhan');

                    // Poin 9 & 10 (Perpanjangan Penahanan bila ada)
                    $table->boolean('has_perpanjangan_penahanan')->default(false)->comment('Penanda umum ada/tidaknya perpanjangan penahanan');
                    $table->boolean('has_surat_perpanjangan_penahanan')->default(false)->comment('Penanda ada/tidaknya poin 9');
                    $table->string('nomor_surat_perpanjangan_penahanan')->nullable()->comment('Nomor Surat Perpanjangan Penahanan (poin 9)');
                    $table->date('tanggal_surat_perpanjangan_penahanan')->nullable()->comment('Tanggal Surat Perpanjangan Penahanan (poin 9)');
                    $table->boolean('has_sprin_perpanjangan_penahanan')->default(false)->comment('Penanda ada/tidaknya poin 10');
                    $table->string('nomor_sprin_perpanjangan_penahanan')->nullable()->comment('Nomor Surat Perintah Perpanjangan Penahanan (poin 10)');
                    $table->date('tanggal_sprin_perpanjangan_penahanan')->nullable()->comment('Tanggal Surat Perintah Perpanjangan Penahanan (poin 10)');

                    // Konten Dokumen (Jaminan)
                    $table->integer('jenis_jaminan')->nullable()->comment('Opsional. 1=Jaminan Uang, 2=Jaminan Orang');
                    $table->decimal('besaran_uang_jaminan', 18, 2)->nullable()->comment('Opsional. Nilai uang jaminan bila jenis_jaminan=1');
                    $table->string('nomor_identitas_penjamin')->nullable()->comment('Opsional. Nomor identitas penjamin bila jenis_jaminan=2');
                    $table->string('nama_penjamin')->nullable()->comment('Opsional. Nama penjamin bila jenis_jaminan=2');
                    $table->text('alamat_penjamin')->nullable()->comment('Opsional. Alamat penjamin bila jenis_jaminan=2');
                    $table->string('lokasi_penyimpanan_jaminan')->nullable()->comment('Opsional. Lokasi penyimpanan jaminan bila jenis_jaminan=1');

                    // Kompatibilitas Sistem Dokumen Internal
                    $table->string('document_number')->nullable();
                    $table->date('document_date')->nullable();
                    $table->string('status_id')->nullable()->default('2');
                    $table->string('document_category_id')->nullable()->default('0603');
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

                    $table->foreign('accident_id', 'fk_spp_guhan_docs_accident_id')
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

    public function down(): void
    {
        Schema::dropIfExists('doc.surat_perintah_penangguhan_penahanan_documents');
    }
};
