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
            Schema::create('doc.surat_laporan_persetujuan_penyitaan_document_persons', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('surat_laporan_persetujuan_penyitaan_document_id');
                
                $table->uuid('suspect_id')->nullable();
                $table->uuid('witness_id')->nullable();
                $table->uuid('reported_person_id')->nullable();
                
                $table->string('bap_number');
                $table->date('bap_date');

                $table->timestamps();
                $table->softDeletes();

                $table->foreign('surat_laporan_persetujuan_penyitaan_document_id', 'fk_s13_persons_doc_id')
                    ->references('id')->on('doc.surat_laporan_persetujuan_penyitaan_documents')
                    ->onDelete('cascade')->onUpdate('cascade');
                    
                $table->foreign('suspect_id', 'fk_s13_persons_suspect_id')
                    ->references('id')->on('public.suspects')
                    ->onDelete('restrict')->onUpdate('cascade');

                $table->foreign('witness_id', 'fk_s13_persons_witness_id')
                    ->references('id')->on('public.witnesses')
                    ->onDelete('restrict')->onUpdate('cascade');
                    
                $table->foreign('reported_person_id', 'fk_s13_persons_reported_id')
                    ->references('id')->on('public.reported_persons')
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
            Schema::table('doc.surat_laporan_persetujuan_penyitaan_document_persons', function (Blueprint $table) {
                $table->dropForeign('fk_s13_persons_doc_id');
                $table->dropForeign('fk_s13_persons_suspect_id');
                $table->dropForeign('fk_s13_persons_witness_id');
                $table->dropForeign('fk_s13_persons_reported_id');
            });

            Schema::dropIfExists('doc.surat_laporan_persetujuan_penyitaan_document_persons');

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
};
