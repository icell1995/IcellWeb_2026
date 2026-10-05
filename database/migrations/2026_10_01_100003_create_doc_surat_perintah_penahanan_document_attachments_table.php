<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel lampiran/upload dokumen fisik S-17
     */
    public function up(): void
    {
        DB::beginTransaction();
        try {
            DB::statement('CREATE SCHEMA IF NOT EXISTS doc;');

            if (!Schema::hasTable('doc.surat_perintah_penahanan_document_attachments')) {
                Schema::create('doc.surat_perintah_penahanan_document_attachments', function (Blueprint $table) {
                    $table->id();
                    $table->uuid('surat_perintah_penahanan_document_id');
                    $table->string('name');
                    $table->string('original_name')->nullable();
                    $table->string('extension')->nullable();
                    $table->string('mimetype')->nullable();
                    $table->string('size')->nullable();
                    $table->string('path')->nullable();
                    $table->string('type')->nullable()->default('DOCUMENT');
                    $table->string('flag')->nullable();
                    $table->timestamps();

                    $table->foreign('surat_perintah_penahanan_document_id', 'fk_spp_doc_attachments_doc_id')
                        ->references('id')->on('doc.surat_perintah_penahanan_documents')
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
        Schema::dropIfExists('doc.surat_perintah_penahanan_document_attachments');
    }
};
