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
            Schema::create('pivot.surat_permintaan_izin_penyitaan_document_suspect', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->uuid('surat_permintaan_izin_penyitaan_document_id');
                $table->uuid('suspect_id');

                $table->timestamps();

                $table->foreign('surat_permintaan_izin_penyitaan_document_id', 'fk_spizinsita_suspect_doc_id')
                    ->references('id')->on('doc.surat_permintaan_izin_penyitaan_documents')
                    ->onDelete('cascade')->onUpdate('cascade');

                $table->foreign('suspect_id', 'fk_spizinsita_suspect_suspect_id')
                    ->references('id')->on('public.suspects')
                    ->onDelete('restrict')->onUpdate('cascade');
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
            Schema::table('pivot.surat_permintaan_izin_penyitaan_document_suspect', function (Blueprint $table) {
                $table->dropForeign('fk_spizinsita_suspect_doc_id');
                $table->dropForeign('fk_spizinsita_suspect_suspect_id');
            });

            Schema::dropIfExists('pivot.surat_permintaan_izin_penyitaan_document_suspect');

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
};
