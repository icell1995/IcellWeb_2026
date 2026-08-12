<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Kolom tambahan agar sesuai field yang disimpan saat Simpan (S-21 / S-22).
 * Untuk DB yang sudah menjalankan migrasi create sebelum kolom ini ada.
 */
return new class extends Migration
{
    private string $s21 = 'doc.permintaan_perpanjangan_penahanan_documents';

    private string $s22 = 'doc.perpanjangan_lanjutan_documents';

    public function up(): void
    {
        if (! Schema::hasColumn($this->s21, 'court_id')) {
            Schema::table($this->s21, function (Blueprint $table) {
                $table->string('court_id')->nullable()->comment('lib.courts — Nama Pengadilan');
                $table->date('detention_end_date')->nullable()->comment('Akhir masa penahanan');
                $table->uuid('sprindik_document_id')->nullable()->comment('FK Surat Perintah Penyidikan');
                $table->uuid('sket_document_id')->nullable()->comment('FK SKET Penetapan Tersangka');
                $table->uuid('surat_perintah_penahanan_id')->nullable()->comment('FK surat_perintah_penahanan');
            });

            Schema::table($this->s21, function (Blueprint $table) {
                $table->foreign('court_id', 'fk_ppp_docs_court_id')
                    ->references('id')
                    ->on('lib.courts')
                    ->onDelete('set null')
                    ->onUpdate('cascade');

                $table->foreign('sprindik_document_id', 'fk_ppp_docs_sprindik_document_id')
                    ->references('id')
                    ->on('doc.surat_perintah_penyidikan_documents')
                    ->onDelete('set null')
                    ->onUpdate('cascade');

                $table->foreign('sket_document_id', 'fk_ppp_docs_sket_document_id')
                    ->references('id')
                    ->on('doc.surat_ketetapan_tentang_penetapan_tersangka_documents')
                    ->onDelete('set null')
                    ->onUpdate('cascade');

                $table->foreign('surat_perintah_penahanan_id', 'fk_ppp_docs_surat_perintah_penahanan_id')
                    ->references('id')
                    ->on('surat_perintah_penahanan')
                    ->onDelete('set null')
                    ->onUpdate('cascade');
            });
        }

        if (! Schema::hasColumn($this->s22, 'extension_start_date')) {
            Schema::table($this->s22, function (Blueprint $table) {
                $table->date('extension_start_date')->nullable()->comment('Dari tanggal');
                $table->date('extension_end_date')->nullable()->comment('Sampai tanggal');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn($this->s22, 'extension_start_date')) {
            Schema::table($this->s22, function (Blueprint $table) {
                $table->dropColumn(['extension_start_date', 'extension_end_date']);
            });
        }

        if (Schema::hasColumn($this->s21, 'court_id')) {
            Schema::table($this->s21, function (Blueprint $table) {
                $table->dropForeign('fk_ppp_docs_court_id');
                $table->dropForeign('fk_ppp_docs_sprindik_document_id');
                $table->dropForeign('fk_ppp_docs_sket_document_id');
                $table->dropForeign('fk_ppp_docs_surat_perintah_penahanan_id');
            });

            Schema::table($this->s21, function (Blueprint $table) {
                $table->dropColumn([
                    'court_id',
                    'detention_end_date',
                    'sprindik_document_id',
                    'sket_document_id',
                    'surat_perintah_penahanan_id',
                ]);
            });
        }
    }
};
