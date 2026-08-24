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
            Schema::create('pivot.sp3_pusiknas_document_suspect', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->uuid('sp3_pusiknas_document_id');
                $table->uuid('suspect_id');
                $table->timestamps();

                $table->foreign('sp3_pusiknas_document_id', 'fk_sp3_pus_suspect_doc_id')
                    ->references('id')->on('doc.sp3_pusiknas_documents')
                    ->onDelete('cascade')->onUpdate('cascade');

                $table->foreign('suspect_id', 'fk_sp3_pus_suspect_suspect_id')
                    ->references('id')->on('public.suspects')
                    ->onDelete('restrict')->onUpdate('cascade');
            });

            // Pivot tabel untuk terlapor (reported_persons)
            Schema::create('pivot.sp3_pusiknas_document_reported_person', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->uuid('sp3_pusiknas_document_id');
                $table->uuid('reported_person_id');
                $table->timestamps();

                $table->foreign('sp3_pusiknas_document_id', 'fk_sp3_pus_rp_doc_id')
                    ->references('id')->on('doc.sp3_pusiknas_documents')
                    ->onDelete('cascade')->onUpdate('cascade');

                $table->foreign('reported_person_id', 'fk_sp3_pus_rp_reported_person_id')
                    ->references('id')->on('public.reported_persons')
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
            Schema::table('pivot.sp3_pusiknas_document_suspect', function (Blueprint $table) {
                $table->dropForeign('fk_sp3_pus_suspect_doc_id');
                $table->dropForeign('fk_sp3_pus_suspect_suspect_id');
            });
            Schema::dropIfExists('pivot.sp3_pusiknas_document_suspect');

            Schema::table('pivot.sp3_pusiknas_document_reported_person', function (Blueprint $table) {
                $table->dropForeign('fk_sp3_pus_rp_doc_id');
                $table->dropForeign('fk_sp3_pus_rp_reported_person_id');
            });
            Schema::dropIfExists('pivot.sp3_pusiknas_document_reported_person');

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
};
