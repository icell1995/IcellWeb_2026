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
            Schema::create('doc.spdp_pusiknas_documents', function (Blueprint $table) {
                $table->uuid('id')->primary();

                $table->uuid('accident_id')->nullable();
                $table->uuid('surat_perintah_penyidikan_document_id')->nullable();
                $table->uuid('surat_perintah_tugas_document_id')->nullable();

                $table->string('document_number');
                $table->date('document_date');
                $table->string('document_classification_id')->nullable();

                $table->string('prosecutor_id')->nullable();
                $table->string('court_id')->nullable();
                $table->boolean('is_suspect_exists')->nullable()->default(false);
                $table->text('description')->nullable()
                    ->comment('uraian_singkat_perkara (SPPT-TI: konten_dokumen.uraian_singkat_perkara)');

                $table->json('messages')->nullable();

                $table->bigInteger('appendix')->default(1);
                $table->json('carbon_copies')->nullable()
                    ->comment('Daftar penerima tembusan (array of string)');

                $table->string('status_id')->nullable();
                $table->string('document_category_id')->nullable();
                $table->boolean('is_active')->default(true);
                $table->boolean('is_legacy')->default(false);
                $table->json('timestamps_log')->nullable();
                $table->json('ip_addresses')->nullable();
                $table->dateTime('released_at')->nullable();
                $table->dateTime('approved_at')->nullable();
                $table->dateTime('rejected_at')->nullable();
                $table->dateTime('last_synced_at')->nullable()
                    ->comment('Waktu terakhir disinkronkan ke Pusiknas');

                $table->unsignedBigInteger('created_by_user_id')->nullable();
                $table->unsignedBigInteger('updated_by_user_id')->nullable();
                $table->unsignedBigInteger('deleted_by_user_id')->nullable();

                $table->timestamps();
                $table->softDeletes();

                $table->foreign('accident_id', 'fk_spdp_pus_docs_accident_id')
                    ->references('id')->on('public.accidents')
                    ->onDelete('set null')->onUpdate('cascade');

                $table->foreign('surat_perintah_penyidikan_document_id', 'fk_spdp_pus_docs_sprindik_id')
                    ->references('id')->on('doc.surat_perintah_penyidikan_documents')
                    ->onDelete('restrict')->onUpdate('cascade');

                $table->foreign('surat_perintah_tugas_document_id', 'fk_spdp_pus_docs_spt_id')
                    ->references('id')->on('doc.surat_perintah_tugas_documents')
                    ->onDelete('restrict')->onUpdate('cascade');

                $table->foreign('prosecutor_id', 'fk_spdp_pus_docs_prosecutor_id')
                    ->references('id')->on('lib.prosecutors')
                    ->onDelete('restrict')->onUpdate('cascade');

                $table->foreign('court_id', 'fk_spdp_pus_docs_court_id')
                    ->references('id')->on('lib.courts')
                    ->onDelete('restrict')->onUpdate('cascade');

                $table->foreign('document_classification_id', 'fk_spdp_pus_docs_doc_class_id')
                    ->references('id')->on('lib.document_classifications')
                    ->onDelete('restrict')->onUpdate('cascade');

                $table->foreign('created_by_user_id', 'fk_spdp_pus_docs_created_by')
                    ->references('id')->on('users')
                    ->onDelete('restrict')->onUpdate('cascade');

                $table->foreign('updated_by_user_id', 'fk_spdp_pus_docs_updated_by')
                    ->references('id')->on('users')
                    ->onDelete('restrict')->onUpdate('cascade');

                $table->foreign('deleted_by_user_id', 'fk_spdp_pus_docs_deleted_by')
                    ->references('id')->on('users')
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
            Schema::table('doc.spdp_pusiknas_documents', function (Blueprint $table) {
                $table->dropForeign('fk_spdp_pus_docs_accident_id');
                $table->dropForeign('fk_spdp_pus_docs_sprindik_id');
                $table->dropForeign('fk_spdp_pus_docs_spt_id');
                $table->dropForeign('fk_spdp_pus_docs_prosecutor_id');
                $table->dropForeign('fk_spdp_pus_docs_court_id');
                $table->dropForeign('fk_spdp_pus_docs_doc_class_id');
                $table->dropForeign('fk_spdp_pus_docs_created_by');
                $table->dropForeign('fk_spdp_pus_docs_updated_by');
                $table->dropForeign('fk_spdp_pus_docs_deleted_by');
            });
            Schema::dropIfExists('doc.spdp_pusiknas_documents');

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
};
