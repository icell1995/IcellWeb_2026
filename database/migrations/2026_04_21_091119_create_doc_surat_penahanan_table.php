<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('doc.surat_perintah_penahanan_documents', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('accident_id');
            $table->uuid('surat_perintah_penyidikan_document_id')->nullable();
            $table->uuid('surat_ketetapan_penetapan_tersangka_id')->nullable();

            $table->string('document_number')->nullable();
            $table->date('document_date')->nullable();

            $table->string('jenis_penahanan')->nullable();
            $table->string('lokasi_penahanan')->nullable();
            $table->string('cabang_penahanan')->nullable();

            $table->string('status_id')->nullable();
            $table->string('document_category_id')->nullable();

            $table->boolean('is_active')->default(true)->nullable();

            $table->timestamp('last_synced_at')->nullable();

            $table->dateTime('released_at')->nullable();
            $table->boolean('is_legacy')->default(false)->nullable();

            $table->string('created_by_id')->nullable();
            $table->string('updated_by_id')->nullable();
            $table->string('deleted_by_id')->nullable();

            $table->timestamps();
            $table->softDeletes()->after('updated_at');
            $table->json('ip_addresses')->nullable();

            $table->json('messages')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();

            $table->foreign('accident_id', 'fk_sprin_penahanan_docs_accident_id')
                ->references('id')
                ->on('accidents')
                ->onDelete('set null')
                ->onUpdate('cascade');

            $table->foreign('surat_perintah_penyidikan_document_id', 'fk_sprin_penahanan_docs_sp_penyidikan_document_id')
                ->references('id')
                ->on('doc.surat_perintah_penyidikan_documents')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->foreign('surat_ketetapan_penetapan_tersangka_id', 'fk_sprin_penahanan_docs_stap_tersangka_id')
                ->references('id')
                ->on('doc.surat_ketetapan_tentang_penetapan_tersangka_documents')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->foreign('status_id', 'fk_sprin_penahahan_docs_status_id')
                ->references('id')
                ->on('opt.statuses')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->foreign('document_category_id', 'fk_sprin_penahahan_docs_document_category_id')
                ->references('id')
                ->on('lib.document_categories')
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('doc.surat_perintah_penahanan_documents', function (Blueprint $table) {
            $table->dropForeign('fk_sprin_penahanan_docs_accident_id');
            $table->dropForeign('fk_sprin_penahanan_docs_sp_penyidikan_document_id');
            $table->dropForeign('fk_sprin_penahanan_docs_stap_tersangka_id');
            $table->dropForeign('fk_sprin_penahahan_docs_status_id');
            $table->dropForeign('fk_sprin_penahahan_docs_document_category_id');
        });

        Schema::dropIfExists('doc.surat_perintah_penahanan_documents');
    }
};
