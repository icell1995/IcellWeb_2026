<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Surat Permintaan Perpanjangan Penahanan — dokumen utama.
     * Pola kolom mengikuti doc schema + dokumen serupa (status, user action JSON, approval).
     */
    public function up(): void
    {
        Schema::create('doc.permintaan_perpanjangan_penahanan_documents', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('accident_id')->nullable()->comment('Perkara');
            $table->uuid('suspect_id')->nullable()->comment('Tersangka');

            $table->string('document_number')->comment('Nomor surat');
            $table->date('document_date')->comment('Tanggal surat');
            $table->unsignedSmallInteger('requested_extension_days')->nullable()->comment('Masa perpanjangan diminta (hari)');

            $table->string('court_id')->nullable()->comment('lib.courts — Nama Pengadilan');
            $table->date('detention_end_date')->nullable()->comment('Akhir masa penahanan');
            $table->uuid('sprindik_document_id')->nullable()->comment('FK Surat Perintah Penyidikan');
            $table->uuid('sket_document_id')->nullable()->comment('FK SKET Penetapan Tersangka');
            $table->uuid('surat_perintah_penahanan_id')->nullable()->comment('FK surat_perintah_penahanan');

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
            $table->json('payload')->nullable()->comment('Data form tambahan (pengadilan, tembusan, dasar hukum, dll.)');

            $table->timestamps();
            $table->softDeletes();
            $table->dateTime('released_at')->nullable();
            $table->dateTime('last_synced_at')->nullable()->comment('Waktu terakhir ditarik dengan EMP');
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();

            $table->foreign('accident_id', 'fk_ppp_docs_accident_id')
                ->references('id')
                ->on('public.accidents')
                ->onDelete('set null')
                ->onUpdate('cascade');

            $table->foreign('suspect_id', 'fk_ppp_docs_suspect_id')
                ->references('id')
                ->on('public.suspects')
                ->onDelete('set null')
                ->onUpdate('cascade');

            $table->foreign('court_id', 'fk_ppp_docs_court_id')
                ->references('id')
                ->on('lib.courts')
                ->onDelete('set null')
                ->onUpdate('cascade');

            $table->foreign('sprindik_document_id', 'fk_ppp_docs_sprindik_document_id')
                ->references('id')
                ->on('doc.surat_perintah_penyidikan_documents')
                ->onDelete('set null')
                ->onUpdate('cascade');

            $table->foreign('sket_document_id', 'fk_ppp_docs_sket_document_id')
                ->references('id')
                ->on('doc.surat_ketetapan_tentang_penetapan_tersangka_documents')
                ->onDelete('set null')
                ->onUpdate('cascade');

            $table->foreign('surat_perintah_penahanan_id', 'fk_ppp_docs_surat_perintah_penahanan_id')
                ->references('id')
                ->on('surat_perintah_penahanan')
                ->onDelete('set null')
                ->onUpdate('cascade');

            $table->foreign('status_id', 'fk_ppp_docs_status_id')
                ->references('id')
                ->on('opt.statuses')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->foreign('document_category_id', 'fk_ppp_docs_document_category_id')
                ->references('id')
                ->on('lib.document_categories')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreign('created_by_user_id', 'fk_ppp_docs_created_by_user_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreign('updated_by_user_id', 'fk_ppp_docs_updated_by_user_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreign('deleted_by_user_id', 'fk_ppp_docs_deleted_by_user_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('doc.permintaan_perpanjangan_penahanan_documents', function (Blueprint $table) {
            $table->dropForeign('fk_ppp_docs_accident_id');
            $table->dropForeign('fk_ppp_docs_suspect_id');
            $table->dropForeign('fk_ppp_docs_court_id');
            $table->dropForeign('fk_ppp_docs_sprindik_document_id');
            $table->dropForeign('fk_ppp_docs_sket_document_id');
            $table->dropForeign('fk_ppp_docs_surat_perintah_penahanan_id');
            $table->dropForeign('fk_ppp_docs_status_id');
            $table->dropForeign('fk_ppp_docs_document_category_id');
            $table->dropForeign('fk_ppp_docs_created_by_user_id');
            $table->dropForeign('fk_ppp_docs_updated_by_user_id');
            $table->dropForeign('fk_ppp_docs_deleted_by_user_id');
        });

        Schema::dropIfExists('doc.permintaan_perpanjangan_penahanan_documents');
    }
};
