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
        Schema::create('doc.surat_permintaan_penggeledahan_document_attachments', function (Blueprint $table) {
               $table->bigIncrements('id');
                $table->uuid('surat_permintaan_penggeledahan_document_id');

                $table->string('name');
                $table->string('original_name')->nullable();
                $table->string('extension')->nullable();
                $table->string('mimetype')->nullable();
                $table->string('size')->nullable();
                $table->string('path')->nullable();

                $table->enum('type', ['DOCUMENT', 'IMAGE', 'VIDEO', 'AUDIO'])
                    ->nullable()->default('DOCUMENT');

                $table->timestamps();

                $table->foreign('surat_permintaan_penggeledahan_document_id', 'fk_surat_permintaan_penggeledahan_attachments_doc_id')
                    ->references('id')->on('doc.surat_permintaan_penggeledahan_documents')
                    ->onDelete('cascade')->onUpdate('cascade');
        });
         DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::beginTransaction();
        try {
        Schema::dropIfExists('doc.surat_permintaan_penggeledahan_document_attachments');
         DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
};
