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
            Schema::create('doc.surat_permintaan_izin_penyitaan_documents', function (Blueprint $table) {
                $table->uuid('id')->primary();

                $table->uuid('accident_id')->nullable();
                $table->uuid('surat_perintah_penyidikan_document_id')->nullable();

                // Dokumen S-12
                $table->string('document_number');
                $table->date('document_date');


                // Sprindik
                $table->string('sprindik_number');
                $table->date('sprindik_date');

                // Surat Perintah Penyitaan (Optional)
                $table->string('surat_perintah_penyitaan_number')->nullable();
                $table->date('surat_perintah_penyitaan_date')->nullable();

                // SPDP (Optional)
                $table->string('spdp_number')->nullable();
                $table->date('spdp_date')->nullable();

                // Pengadilan Negeri Tujuan
                $table->string('court_id')->nullable();

                // Status, Kategori & Audit
                $table->string('status_id')->nullable();
                $table->string('document_category_id')->nullable();
                $table->boolean('is_active')->default(true);
                $table->boolean('is_legacy')->default(false);
                $table->json('messages')->nullable();
                $table->json('timestamps_log')->nullable();
                $table->json('ip_addresses')->nullable();
                $table->dateTime('released_at')->nullable();
                $table->dateTime('approved_at')->nullable();
                $table->dateTime('rejected_at')->nullable();
                $table->dateTime('last_synced_at')->nullable()->comment('Waktu terakhir disinkronkan');

                $table->unsignedBigInteger('created_by_user_id')->nullable();
                $table->unsignedBigInteger('updated_by_user_id')->nullable();
                $table->unsignedBigInteger('deleted_by_user_id')->nullable();

                $table->timestamps();
                $table->softDeletes();

                // Foreign Keys
                $table->foreign('accident_id', 'fk_spizinsita_docs_accident_id')
                    ->references('id')->on('public.accidents')
                    ->onDelete('set null')->onUpdate('cascade');

                $table->foreign('surat_perintah_penyidikan_document_id', 'fk_spizinsita_docs_sprindik_id')
                    ->references('id')->on('doc.surat_perintah_penyidikan_documents')
                    ->onDelete('restrict')->onUpdate('cascade');

                $table->foreign('court_id', 'fk_spizinsita_docs_court_id')
                    ->references('id')->on('lib.courts')
                    ->onDelete('restrict')->onUpdate('cascade');

                $table->foreign('document_category_id', 'fk_spizinsita_docs_category_id')
                    ->references('id')->on('lib.document_categories')
                    ->onDelete('restrict')->onUpdate('cascade');

                $table->foreign('created_by_user_id', 'fk_spizinsita_docs_created_by')
                    ->references('id')->on('public.users')
                    ->onDelete('restrict')->onUpdate('cascade');

                $table->foreign('updated_by_user_id', 'fk_spizinsita_docs_updated_by')
                    ->references('id')->on('public.users')
                    ->onDelete('restrict')->onUpdate('cascade');

                $table->foreign('deleted_by_user_id', 'fk_spizinsita_docs_deleted_by')
                    ->references('id')->on('public.users')
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
            Schema::table('doc.surat_permintaan_izin_penyitaan_documents', function (Blueprint $table) {
                $table->dropForeign('fk_spizinsita_docs_accident_id');
                $table->dropForeign('fk_spizinsita_docs_sprindik_id');
                $table->dropForeign('fk_spizinsita_docs_court_id');
                $table->dropForeign('fk_spizinsita_docs_category_id');
                $table->dropForeign('fk_spizinsita_docs_created_by');
                $table->dropForeign('fk_spizinsita_docs_updated_by');
                $table->dropForeign('fk_spizinsita_docs_deleted_by');
            });

            Schema::dropIfExists('doc.surat_permintaan_izin_penyitaan_documents');

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
};
