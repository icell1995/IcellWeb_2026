<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Lampiran berkas scan fisik / file upload S-18
     */
    public function up(): void
    {
        DB::beginTransaction();
        try {
            DB::statement('CREATE SCHEMA IF NOT EXISTS doc;');

            if (!Schema::hasTable('doc.surat_perintah_penangguhan_penahanan_document_attachments')) {
                Schema::create('doc.surat_perintah_penangguhan_penahanan_document_attachments', function (Blueprint $table) {
                    $table->id();
                    $table->uuid('surat_perintah_penangguhan_penahanan_document_id');
                    $table->string('name')->nullable();
                    $table->string('type')->nullable();
                    $table->string('size')->nullable();
                    $table->string('path')->nullable();
                    $table->text('description')->nullable();
                    $table->timestamps();

                    $table->foreign('surat_perintah_penangguhan_penahanan_document_id', 'fk_spp_guhan_attach_doc_id')
                        ->references('id')->on('doc.surat_perintah_penangguhan_penahanan_documents')
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
        Schema::dropIfExists('doc.surat_perintah_penangguhan_penahanan_document_attachments');
    }
};
