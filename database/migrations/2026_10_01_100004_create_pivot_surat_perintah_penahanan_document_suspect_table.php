<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Pivot Tersangka S-17 (Gambar 2: tersangka[])
     */
    public function up(): void
    {
        DB::beginTransaction();
        try {
            DB::statement('CREATE SCHEMA IF NOT EXISTS pivot;');

            if (!Schema::hasTable('pivot.surat_perintah_penahanan_document_suspect')) {
                Schema::create('pivot.surat_perintah_penahanan_document_suspect', function (Blueprint $table) {
                    $table->id();
                    $table->uuid('surat_perintah_penahanan_document_id');
                    $table->uuid('suspect_id');
                    $table->timestamps();

                    $table->foreign('surat_perintah_penahanan_document_id', 'fk_piv_spp_doc_id')
                        ->references('id')->on('doc.surat_perintah_penahanan_documents')
                        ->onDelete('cascade')->onUpdate('cascade');

                    $table->foreign('suspect_id', 'fk_piv_spp_suspect_id')
                        ->references('id')->on('public.suspects')
                        ->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('pivot.surat_perintah_penahanan_document_suspect');
    }
};
