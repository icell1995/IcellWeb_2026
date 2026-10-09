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
            Schema::create('doc.surat_laporan_persetujuan_penyitaan_document_laws', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('surat_laporan_persetujuan_penyitaan_document_id');
                
                $table->string('crime_type_id')->nullable();
                $table->string('crime_class_id')->nullable();
                $table->string('crime_constitution_id')->nullable();
                $table->string('constitution')->nullable();
                $table->string('constitution_chapter')->nullable();
                $table->text('description')->nullable();

                $table->enum('flag', ['MAIN', 'ADDITIONAL'])->default('MAIN')->nullable();

                $table->timestamps();
                $table->softDeletes();

                $table->foreign('surat_laporan_persetujuan_penyitaan_document_id', 'fk_s13_laws_doc_id')
                    ->references('id')->on('doc.surat_laporan_persetujuan_penyitaan_documents')
                    ->onDelete('cascade')->onUpdate('cascade');
                    
                $table->foreign('crime_type_id', 'fk_s13_laws_crime_type_id')
                    ->references('id')->on('lib.crime_types')
                    ->onDelete('restrict')->onUpdate('cascade');

                $table->foreign('crime_class_id', 'fk_s13_laws_crime_class_id')
                    ->references('id')->on('lib.crime_classes')
                    ->onDelete('restrict')->onUpdate('cascade');

                $table->foreign('crime_constitution_id', 'fk_s13_laws_crime_constitution_id')
                    ->references('id')->on('lib.crime_constitutions')
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
            Schema::table('doc.surat_laporan_persetujuan_penyitaan_document_laws', function (Blueprint $table) {
                $table->dropForeign('fk_s13_laws_doc_id');
                $table->dropForeign('fk_s13_laws_crime_type_id');
                $table->dropForeign('fk_s13_laws_crime_class_id');
                $table->dropForeign('fk_s13_laws_crime_constitution_id');
            });

            Schema::dropIfExists('doc.surat_laporan_persetujuan_penyitaan_document_laws');

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
};
