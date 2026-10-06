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
            DB::statement('CREATE SCHEMA IF NOT EXISTS doc;');

            if (!Schema::hasTable('doc.surat_permohonan_perpanjangan_penahanan_kejaksaan_document_attachments')) {
                Schema::create('doc.surat_permohonan_perpanjangan_penahanan_kejaksaan_document_attachments', function (Blueprint $table) {
                    $table->uuid('id')->primary();
                    $table->uuid('doc_id')->nullable();

                    $table->string('name')->nullable();
                    $table->string('original_name')->nullable();
                    $table->string('mime_type')->nullable();
                    $table->unsignedBigInteger('size')->nullable();
                    $table->string('path')->nullable();
                    $table->string('category')->nullable();

                    $table->unsignedBigInteger('created_by_user_id')->nullable();
                    $table->unsignedBigInteger('updated_by_user_id')->nullable();
                    $table->unsignedBigInteger('deleted_by_user_id')->nullable();

                    $table->timestamps();
                    $table->softDeletes();

                    $table->foreign('doc_id', 'fk_spp_kejaksaan_attachments_doc_id')
                        ->references('id')->on('doc.surat_permohonan_perpanjangan_penahanan_kejaksaan_documents')
                        ->onDelete('cascade')->onUpdate('cascade');
                });
            }

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
        Schema::dropIfExists('doc.surat_permohonan_perpanjangan_penahanan_kejaksaan_document_attachments');
    }
};
