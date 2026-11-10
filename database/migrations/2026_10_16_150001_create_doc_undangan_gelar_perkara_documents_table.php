<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Undangan Gelar Perkara
     * Kode Kategori Dokumen: 0701 (DCT-0701)
     */
    public function up(): void
    {
        DB::beginTransaction();
        try {
            DB::statement('CREATE SCHEMA IF NOT EXISTS doc;');

            if (!Schema::hasTable('doc.undangan_gelar_perkara_documents')) {
                Schema::create('doc.undangan_gelar_perkara_documents', function (Blueprint $table) {
                    $table->uuid('id')->primary();
                    $table->uuid('accident_id')->nullable();
                    $table->uuid('surat_perintah_penyelidikan_document_id')->nullable();
                    $table->uuid('surat_perintah_penyidikan_document_id')->nullable();

                    // Identitas Dokumen
                    $table->string('nomor')->nullable()->comment('Nomor Surat Undangan Gelar Perkara');
                    $table->string('sifat')->nullable()->default('Biasa')->comment('Sifat: Biasa / Segera / Rahasia');
                    $table->string('lampiran')->nullable()->default('-')->comment('Lampiran');
                    $table->string('perihal')->nullable()->default('Undangan Pelaksanaan Gelar Perkara')->comment('Perihal Surat');
                    $table->date('tanggal')->nullable()->comment('Tanggal Surat');
                    $table->string('tempat_surat')->nullable()->comment('Tempat penerbitan surat');

                    // Rujukan / Dasar Surat
                    $table->text('dasar_hukum')->nullable()->comment('Dasar rujukan pelaksanaan gelar');
                    $table->string('nomor_lp')->nullable();
                    $table->date('tanggal_lp')->nullable();
                    $table->string('nomor_sprindik')->nullable();
                    $table->date('tanggal_sprindik')->nullable();

                    // Pelaksanaan Gelar Perkara
                    $table->string('hari_pelaksanaan')->nullable()->comment('Hari pelaksanaan (misal: Senin)');
                    $table->date('tanggal_pelaksanaan')->nullable()->comment('Tanggal pelaksanaan');
                    $table->string('jam_pelaksanaan')->nullable()->comment('Pukul / Jam pelaksanaan (misal: 09:00 WIB)');
                    $table->string('tempat_pelaksanaan')->nullable()->comment('Tempat / Ruangan pelaksanaan');
                    $table->text('agenda')->nullable()->comment('Agenda / Masalah yang digelarkan');
                    $table->text('tujuan_undangan')->nullable()->comment('Daftar pihak/pejabat yang diundang (Kepada Yth)');

                    // Pejabat Penandatangan (Signatory)
                    $table->string('signatory_id')->nullable();
                    $table->string('signatory_head_text')->nullable();
                    $table->string('signatory_position')->nullable();
                    $table->string('signatory_name')->nullable();
                    $table->string('signatory_rank')->nullable();
                    $table->string('signatory_nrp')->nullable();

                    // Tembusan
                    $table->json('tembusan')->nullable()->comment('Daftar Tembusan');
                    $table->text('catatan')->nullable();

                    // Kompatibilitas Sistem Dokumen Internal
                    $table->string('document_number')->nullable();
                    $table->date('document_date')->nullable();
                    $table->string('status_id')->nullable()->default('2');
                    $table->string('document_category_id')->nullable()->default('0701');
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

                    $table->foreign('accident_id', 'fk_undangan_gelar_perkara_accident_id')
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
        Schema::dropIfExists('doc.undangan_gelar_perkara_documents');
    }
};
