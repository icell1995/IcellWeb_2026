<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::beginTransaction();
        try {
            Schema::create('doc.surat_permintaan_izin_penyitaan_document_seized_items', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->uuid('surat_permintaan_izin_penyitaan_document_id');

                $table->string('nama');
                $table->string('jenis')->nullable();
                $table->decimal('jumlah', 12, 2)->default(1.00);
                $table->string('satuan')->nullable();
                $table->text('keterangan')->nullable();

                $table->timestamps();

                $table->foreign('surat_permintaan_izin_penyitaan_document_id', 'fk_spizinsita_items_doc_id')
                    ->references('id')->on('doc.surat_permintaan_izin_penyitaan_documents')
                    ->onDelete('cascade')->onUpdate('cascade');
            });

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function down(): void
    {
        DB::beginTransaction();
        try {
            Schema::table('doc.surat_permintaan_izin_penyitaan_document_seized_items', function (Blueprint $table) {
                $table->dropForeign('fk_spizinsita_items_doc_id');
            });

            Schema::dropIfExists('doc.surat_permintaan_izin_penyitaan_document_seized_items');

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
};
