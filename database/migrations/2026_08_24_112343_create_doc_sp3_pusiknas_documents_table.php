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
            Schema::create('doc.sp3_pusiknas_documents', function (Blueprint $table) {
                $table->uuid('id')->primary();

                $table->uuid('accident_id')->nullable();
                $table->uuid('surat_pemberitahuan_dimulainya_penyidikan_document_id')->nullable();

                // identitas_dokumen (SPPT-TI)
                $table->string('document_number');
                $table->date('document_date');
                $table->string('no_spdp')->nullable();

                // konten_dokumen
                $table->json('kode_alasan')->nullable()
                    ->comment('Array integer alasan penghentian (SPPT-TI)');
                
                $table->json('messages')->nullable()
                    ->comment('Additional data (sumber form, dll)');

                // status & workflow
                $table->string('status_id')->nullable();
                $table->boolean('is_active')->default(true);
                $table->boolean('is_legacy')->default(false);

                // audit & sync
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

                // Foreign Keys
                $table->foreign('accident_id', 'fk_sp3_pus_docs_accident_id')
                    ->references('id')->on('public.accidents')
                    ->onDelete('set null')->onUpdate('cascade');

                $table->foreign('surat_pemberitahuan_dimulainya_penyidikan_document_id', 'fk_sp3_pus_docs_spdp_id')
                    ->references('id')->on('doc.surat_pemberitahuan_dimulainya_penyidikan_documents')
                    ->onDelete('restrict')->onUpdate('cascade');

                $table->foreign('created_by_user_id', 'fk_sp3_pus_docs_created_by')
                    ->references('id')->on('users')
                    ->onDelete('restrict')->onUpdate('cascade');

                $table->foreign('updated_by_user_id', 'fk_sp3_pus_docs_updated_by')
                    ->references('id')->on('users')
                    ->onDelete('restrict')->onUpdate('cascade');

                $table->foreign('deleted_by_user_id', 'fk_sp3_pus_docs_deleted_by')
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
            Schema::table('doc.sp3_pusiknas_documents', function (Blueprint $table) {
                $table->dropForeign('fk_sp3_pus_docs_accident_id');
                $table->dropForeign('fk_sp3_pus_docs_spdp_id');
                $table->dropForeign('fk_sp3_pus_docs_created_by');
                $table->dropForeign('fk_sp3_pus_docs_updated_by');
                $table->dropForeign('fk_sp3_pus_docs_deleted_by');
            });
            Schema::dropIfExists('doc.sp3_pusiknas_documents');

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
};
