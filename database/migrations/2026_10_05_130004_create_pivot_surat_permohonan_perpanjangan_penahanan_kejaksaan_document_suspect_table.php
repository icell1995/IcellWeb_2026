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
            if (!Schema::hasTable('public.pivot_surat_permohonan_perpanjangan_penahanan_kejaksaan_document_suspect')) {
                Schema::create('public.pivot_surat_permohonan_perpanjangan_penahanan_kejaksaan_document_suspect', function (Blueprint $table) {
                    $table->uuid('id')->default(DB::raw('gen_random_uuid()'))->primary();
                    $table->uuid('document_id')->nullable();
                    $table->uuid('suspect_id')->nullable();
                    $table->timestamps();

                    $table->foreign('document_id', 'fk_spp_kejaksaan_suspect_doc_id')
                        ->references('id')->on('doc.surat_permohonan_perpanjangan_penahanan_kejaksaan_documents')
                        ->onDelete('cascade')->onUpdate('cascade');

                    $table->foreign('suspect_id', 'fk_spp_kejaksaan_suspect_suspect_id')
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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('public.pivot_surat_permohonan_perpanjangan_penahanan_kejaksaan_document_suspect');
    }
};
