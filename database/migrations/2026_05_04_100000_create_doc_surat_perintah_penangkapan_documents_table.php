<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Surat Perintah Penangkapan (DCT-0301) — pola kolom mengikuti dokumen tahap serupa (payload JSON + status).
     */
    public function up(): void
    {
        Schema::create('doc.surat_perintah_penangkapan_documents', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('accident_id')->nullable()->comment('Perkara');
            $table->uuid('suspect_id')->nullable()->comment('Tersangka');

            $table->string('document_number')->nullable()->comment('Nomor surat');
            $table->date('document_date')->nullable()->comment('Tanggal surat');

            $table->uuid('sprindik_document_id')->nullable()->comment('Surat Perintah Penyidikan');
            $table->uuid('sket_document_id')->nullable()->comment('Surat Ketetapan Penetapan Tersangka');
            $table->uuid('surat_perintah_tugas_document_id')->nullable()->comment('Surat Perintah Tugas');

            $table->date('valid_until_date')->nullable()->comment('Berlaku s.d.');

            $table->boolean('is_active')->default(true);
            $table->boolean('is_legacy')->default(false)->nullable();

            $table->string('status_id')->nullable();
            $table->string('document_category_id')->nullable();

            $table->unsignedBigInteger('created_by_user_id')->nullable();
            $table->unsignedBigInteger('updated_by_user_id')->nullable();
            $table->unsignedBigInteger('deleted_by_user_id')->nullable();

            $table->json('messages')->nullable();
            $table->json('timestamps')->nullable();
            $table->json('ip_addresses')->nullable();
            $table->json('payload')->nullable()->comment('Pertimbangan, dasar tambahan, identitas form, serah terima, dll.');

            $table->timestamps();
            $table->softDeletes();
            $table->dateTime('released_at')->nullable();
            $table->dateTime('last_synced_at')->nullable()->comment('Waktu terakhir ditarik dengan EMP');
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();

            $table->foreign('accident_id', 'fk_spp_docs_accident_id')
                ->references('id')
                ->on('public.accidents')
                ->onDelete('set null')
                ->onUpdate('cascade');

            $table->foreign('suspect_id', 'fk_spp_docs_suspect_id')
                ->references('id')
                ->on('public.suspects')
                ->onDelete('set null')
                ->onUpdate('cascade');

            $table->foreign('sprindik_document_id', 'fk_spp_docs_sprindik_document_id')
                ->references('id')
                ->on('doc.surat_perintah_penyidikan_documents')
                ->onDelete('set null')
                ->onUpdate('cascade');

            $table->foreign('sket_document_id', 'fk_spp_docs_sket_document_id')
                ->references('id')
                ->on('doc.surat_ketetapan_tentang_penetapan_tersangka_documents')
                ->onDelete('set null')
                ->onUpdate('cascade');

            $table->foreign('surat_perintah_tugas_document_id', 'fk_spp_docs_spt_document_id')
                ->references('id')
                ->on('doc.surat_perintah_tugas_documents')
                ->onDelete('set null')
                ->onUpdate('cascade');

            $table->foreign('status_id', 'fk_spp_docs_status_id')
                ->references('id')
                ->on('opt.statuses')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->foreign('document_category_id', 'fk_spp_docs_document_category_id')
                ->references('id')
                ->on('lib.document_categories')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreign('created_by_user_id', 'fk_spp_docs_created_by_user_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreign('updated_by_user_id', 'fk_spp_docs_updated_by_user_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreign('deleted_by_user_id', 'fk_spp_docs_deleted_by_user_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('doc.surat_perintah_penangkapan_documents', function (Blueprint $table) {
            $table->dropForeign('fk_spp_docs_accident_id');
            $table->dropForeign('fk_spp_docs_suspect_id');
            $table->dropForeign('fk_spp_docs_sprindik_document_id');
            $table->dropForeign('fk_spp_docs_sket_document_id');
            $table->dropForeign('fk_spp_docs_spt_document_id');
            $table->dropForeign('fk_spp_docs_status_id');
            $table->dropForeign('fk_spp_docs_document_category_id');
            $table->dropForeign('fk_spp_docs_created_by_user_id');
            $table->dropForeign('fk_spp_docs_updated_by_user_id');
            $table->dropForeign('fk_spp_docs_deleted_by_user_id');
        });

        Schema::dropIfExists('doc.surat_perintah_penangkapan_documents');
    }
};
