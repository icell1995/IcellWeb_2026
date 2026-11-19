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
            Schema::create('doc.surat_laporan_persetujuan_penyitaan_document_seized_items', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('surat_laporan_persetujuan_penyitaan_document_person_id');
                
                $table->string('name');
                $table->integer('quantity');
                $table->string('unit')->nullable();
                $table->text('description')->nullable();

                $table->timestamps();
                $table->softDeletes();

                $table->foreign('surat_laporan_persetujuan_penyitaan_document_person_id', 'fk_s13_items_person_id')
                    ->references('id')->on('doc.surat_laporan_persetujuan_penyitaan_document_persons')
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
            Schema::table('doc.surat_laporan_persetujuan_penyitaan_document_seized_items', function (Blueprint $table) {
                $table->dropForeign('fk_s13_items_person_id');
            });

            Schema::dropIfExists('doc.surat_laporan_persetujuan_penyitaan_document_seized_items');

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
};
