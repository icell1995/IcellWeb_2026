<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Sesuai syarat format S-17 (Surat Perintah Penahanan):
     * Gambar 1 (identitas_dokumen): nomor, tanggal, nomor_spdp, tanggal_spdp, kode_satker_penerbit_spdp, nomor_surat_perintah_penangkapan
     * Gambar 2 (konten_dokumen): kode_jenis_penahanan, kode_satker_tempat_penahanan, tanggal_mulai, tanggal_akhir
     */
    public function up(): void
    {
        DB::beginTransaction();
        try {
            DB::statement('CREATE SCHEMA IF NOT EXISTS doc;');

            if (!Schema::hasTable('doc.surat_perintah_penahanan_documents')) {
                Schema::create('doc.surat_perintah_penahanan_documents', function (Blueprint $table) {
                    $table->uuid('id')->primary();
                    $table->uuid('accident_id')->nullable();
                    $table->uuid('surat_perintah_penyidikan_document_id')->nullable();
                    $table->uuid('surat_ketetapan_penetapan_tersangka_id')->nullable();

                    // Identitas Dokumen (Gambar 1)
                    $table->string('nomor')->nullable()->comment('WAJIB. Nomor Surat Perintah Penahanan (S-17)');
                    $table->date('tanggal')->nullable()->comment('WAJIB. Tanggal Surat Perintah Penahanan');
                    $table->string('nomor_spdp')->nullable()->comment('WAJIB. Nomor SPDP');
                    $table->date('tanggal_spdp')->nullable()->comment('WAJIB. Tanggal SPDP');
                    $table->string('kode_satker_penerbit_spdp')->nullable()->comment('WAJIB. Kode Satker Penerbit SPDP');
                    $table->string('nomor_surat_perintah_penangkapan')->nullable()->comment('Opsional (nullable). Nomor S-16');

                    // Kompatibilitas Sistem Dokumen Internal
                    $table->string('document_number')->nullable();
                    $table->date('document_date')->nullable();

                    // Konten Dokumen (Gambar 2)
                    $table->integer('kode_jenis_penahanan')->nullable()->comment('WAJIB. Integer (master_jenispenahanan: 1=Rutan, 2=Rumah, 3=Kota)');
                    $table->string('kode_satker_tempat_penahanan')->nullable()->comment('Opsional (nullable). Kode Satker Tempat Penahanan');
                    $table->date('tanggal_mulai')->nullable()->comment('WAJIB. Tanggal mulai penahanan');
                    $table->date('tanggal_akhir')->nullable()->comment('WAJIB. Tanggal akhir penahanan');

                    // Field deskriptif tambahan
                    $table->string('jenis_penahanan')->nullable();
                    $table->string('lokasi_penahanan')->nullable();
                    $table->string('cabang_penahanan')->nullable();
                    $table->dateTime('start_date')->nullable();
                    $table->dateTime('end_date')->nullable();

                    // Workflow Status & Audit
                    $table->string('status_id')->nullable()->default('2');
                    $table->string('document_category_id')->nullable()->default('0601');
                    $table->boolean('is_active')->default(true);
                    $table->boolean('is_legacy')->default(false);
                    $table->dateTime('last_synced_at')->nullable();
                    $table->dateTime('released_at')->nullable();
                    $table->dateTime('approved_at')->nullable();
                    $table->dateTime('rejected_at')->nullable();
                    $table->json('messages')->nullable();
                    $table->json('ip_addresses')->nullable();

                    $table->unsignedBigInteger('created_by_user_id')->nullable();
                    $table->unsignedBigInteger('updated_by_user_id')->nullable();
                    $table->unsignedBigInteger('deleted_by_user_id')->nullable();

                    $table->timestamps();
                    $table->softDeletes();

                    $table->foreign('accident_id', 'fk_spp_docs_accident_id')
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
        Schema::dropIfExists('doc.surat_perintah_penahanan_documents');
    }
};
