<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::beginTransaction();
        try {
            Schema::create('pivot.surat_memperoleh_persetujuan_penggeledahan_document_suspect', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->uuid('surat_memperoleh_persetujuan_penggeledahan_document_id');
                $table->uuid('suspect_id');
                $table->timestamps();

                $table->foreign('surat_memperoleh_persetujuan_penggeledahan_document_id', 'fk_memperoleh_persetujuan_suspect_doc_id')
                    ->references('id')->on('doc.surat_memperoleh_persetujuan_penggeledahan_documents')
                    ->onDelete('cascade')->onUpdate('cascade');

                $table->foreign('suspect_id', 'fk_memperoleh_persetujuan_suspect_suspect_id')
                    ->references('id')->on('public.suspects')
                    ->onDelete('restrict')->onUpdate('cascade');
            });

            Schema::create('pivot.surat_memperoleh_persetujuan_penggeledahan_document_reported_person', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->uuid('surat_memperoleh_persetujuan_penggeledahan_document_id');
                $table->uuid('reported_person_id');
                $table->timestamps();

                $table->foreign('surat_memperoleh_persetujuan_penggeledahan_document_id', 'fk_memperoleh_persetujuan_rp_doc_id')
                    ->references('id')->on('doc.surat_memperoleh_persetujuan_penggeledahan_documents')
                    ->onDelete('cascade')->onUpdate('cascade');

                $table->foreign('reported_person_id', 'fk_memperoleh_persetujuan_rp_reported_person_id')
                    ->references('id')->on('public.reported_persons')
                    ->onDelete('restrict')->onUpdate('cascade');
            });

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::beginTransaction();
        try {
            Schema::dropIfExists('pivot.surat_memperoleh_persetujuan_penggeledahan_document_suspect');
            Schema::dropIfExists('pivot.surat_memperoleh_persetujuan_penggeledahan_document_reported_person');
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
};
