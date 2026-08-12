<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Form S-21 menyimpan UUID Surat Perintah Penahanan dari doc.surat_perintah_penahanan_documents,
 * sedangkan FK lama mengarah ke tabel surat_perintah_penahanan → insert gagal (generic error).
 */
return new class extends Migration
{
    private string $table = 'doc.permintaan_perpanjangan_penahanan_documents';

    private string $fkName = 'fk_ppp_docs_surat_perintah_penahanan_id';

    private string $docSphTable = 'doc.surat_perintah_penahanan_documents';

    public function up(): void
    {
        if (! Schema::hasTable($this->table) || ! Schema::hasTable($this->docSphTable)) {
            return;
        }

        if (! Schema::hasColumn($this->table, 'surat_perintah_penahanan_id')) {
            return;
        }

        Schema::table($this->table, function (Blueprint $table) {
            $table->dropForeign($this->fkName);
        });

        $validIds = DB::table($this->docSphTable)->pluck('id');
        if ($validIds->isEmpty()) {
            DB::table($this->table)->update(['surat_perintah_penahanan_id' => null]);
        } else {
            DB::table($this->table)
                ->whereNotNull('surat_perintah_penahanan_id')
                ->whereNotIn('surat_perintah_penahanan_id', $validIds->all())
                ->update(['surat_perintah_penahanan_id' => null]);
        }

        Schema::table($this->table, function (Blueprint $table) {
            $table->foreign('surat_perintah_penahanan_id', $this->fkName)
                ->references('id')
                ->on($this->docSphTable)
                ->onDelete('set null')
                ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable($this->table)) {
            return;
        }

        if (! Schema::hasColumn($this->table, 'surat_perintah_penahanan_id')) {
            return;
        }

        Schema::table($this->table, function (Blueprint $table) {
            $table->dropForeign($this->fkName);
        });

        if (! Schema::hasTable('surat_perintah_penahanan')) {
            return;
        }

        $validIds = DB::table('surat_perintah_penahanan')->pluck('id');
        if ($validIds->isEmpty()) {
            DB::table($this->table)->update(['surat_perintah_penahanan_id' => null]);
        } else {
            DB::table($this->table)
                ->whereNotNull('surat_perintah_penahanan_id')
                ->whereNotIn('surat_perintah_penahanan_id', $validIds->all())
                ->update(['surat_perintah_penahanan_id' => null]);
        }

        Schema::table($this->table, function (Blueprint $table) {
            $table->foreign('surat_perintah_penahanan_id', $this->fkName)
                ->references('id')
                ->on('surat_perintah_penahanan')
                ->onDelete('set null')
                ->onUpdate('cascade');
        });
    }
};
