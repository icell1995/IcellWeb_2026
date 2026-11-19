<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::beginTransaction();
        try {
            Schema::create('pivot.surat_permintaan_izin_penyitaan_document_witnesses', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->uuid('surat_permintaan_izin_penyitaan_document_id');
                $table->uuid('witness_id');

                $table->timestamps();

                $table->foreign('surat_permintaan_izin_penyitaan_document_id', 'fk_s12_witnesses_doc_id')
                    ->references('id')->on('doc.surat_permintaan_izin_penyitaan_documents')
                    ->onDelete('cascade')->onUpdate('cascade');

                $table->foreign('witness_id', 'fk_s12_witnesses_witness_id')
                    ->references('id')->on('public.witnesses')
                    ->onDelete('restrict')->onUpdate('cascade');
                    
                $table->unique(['surat_permintaan_izin_penyitaan_document_id', 'witness_id'], 'uk_s12_witnesses_doc_witness');
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
            Schema::table('pivot.surat_permintaan_izin_penyitaan_document_witnesses', function (Blueprint $table) {
                $table->dropForeign('fk_s12_witnesses_doc_id');
                $table->dropForeign('fk_s12_witnesses_witness_id');
            });

            Schema::dropIfExists('pivot.surat_permintaan_izin_penyitaan_document_witnesses');

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
};
