<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Menyamakan nama kolom dengan FK di beberapa DB (surat_perintah_penahanan_document_id).
 * Jika DB sudah pakai nama baru, migrasi ini tidak mengubah apa pun.
 */
return new class extends Migration
{
    private string $table = 'doc.permintaan_perpanjangan_penahanan_documents';

    private string $docSph = 'doc.surat_perintah_penahanan_documents';

    private string $fkOld = 'fk_ppp_docs_surat_perintah_penahanan_id';

    private string $fkNew = 'fk_ppp_docs_surat_perintah_penahanan_document_id';

    public function up(): void
    {
        if (! Schema::hasTable($this->table) || ! Schema::hasTable($this->docSph)) {
            return;
        }

        if (! Schema::hasColumn($this->table, 'surat_perintah_penahanan_id')) {
            return;
        }

        if (Schema::hasColumn($this->table, 'surat_perintah_penahanan_document_id')) {
            return;
        }

        Schema::table($this->table, function (Blueprint $table) {
            $table->dropForeign($this->fkOld);
        });

        Schema::table($this->table, function (Blueprint $table) {
            $table->renameColumn('surat_perintah_penahanan_id', 'surat_perintah_penahanan_document_id');
        });

        Schema::table($this->table, function (Blueprint $table) {
            $table->foreign('surat_perintah_penahanan_document_id', $this->fkNew)
                ->references('id')
                ->on($this->docSph)
                ->onDelete('set null')
                ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable($this->table)) {
            return;
        }

        if (! Schema::hasColumn($this->table, 'surat_perintah_penahanan_document_id')) {
            return;
        }

        if (Schema::hasColumn($this->table, 'surat_perintah_penahanan_id')) {
            return;
        }

        Schema::table($this->table, function (Blueprint $table) {
            $table->dropForeign($this->fkNew);
        });

        Schema::table($this->table, function (Blueprint $table) {
            $table->renameColumn('surat_perintah_penahanan_document_id', 'surat_perintah_penahanan_id');
        });

        Schema::table($this->table, function (Blueprint $table) {
            $table->foreign('surat_perintah_penahanan_id', $this->fkOld)
                ->references('id')
                ->on($this->docSph)
                ->onDelete('set null')
                ->onUpdate('cascade');
        });
    }
};
