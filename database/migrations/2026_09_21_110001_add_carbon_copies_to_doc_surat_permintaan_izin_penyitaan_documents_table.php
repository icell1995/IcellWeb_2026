<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        DB::beginTransaction();
        try {
            Schema::table('doc.surat_permintaan_izin_penyitaan_documents', function (Blueprint $table) {
                if (!Schema::hasColumn('doc.surat_permintaan_izin_penyitaan_documents', 'document_classification_id')) {
                    $table->string('document_classification_id')->nullable()->after('court_id');
                    $table->foreign('document_classification_id', 'fk_spizinsita_docs_doc_classification_id')
                        ->references('id')->on('lib.document_classifications')
                        ->onDelete('restrict')->onUpdate('cascade');
                }
                if (!Schema::hasColumn('doc.surat_permintaan_izin_penyitaan_documents', 'carbon_copies')) {
                    $table->json('carbon_copies')->nullable()->after('court_id');
                }
            });

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        DB::beginTransaction();
        try {
            Schema::table('doc.surat_permintaan_izin_penyitaan_documents', function (Blueprint $table) {
                if (Schema::hasColumn('doc.surat_permintaan_izin_penyitaan_documents', 'document_classification_id')) {
                    $table->dropForeign('fk_spizinsita_docs_doc_classification_id');
                    $table->dropColumn('document_classification_id');
                }
                if (Schema::hasColumn('doc.surat_permintaan_izin_penyitaan_documents', 'carbon_copies')) {
                    $table->dropColumn('carbon_copies');
                }
            });

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
};
