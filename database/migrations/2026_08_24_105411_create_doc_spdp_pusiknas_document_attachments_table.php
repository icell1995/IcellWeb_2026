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
            Schema::create('doc.spdp_pusiknas_document_attachments', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->uuid('spdp_pusiknas_document_id');

                $table->string('name');
                $table->string('original_name')->nullable();
                $table->string('extension')->nullable();
                $table->string('mimetype')->nullable();
                $table->string('size')->nullable();
                $table->string('path')->nullable();

                $table->enum('type', ['DOCUMENT', 'IMAGE', 'VIDEO', 'AUDIO'])
                    ->nullable()->default('DOCUMENT');

                $table->timestamps();

                $table->foreign('spdp_pusiknas_document_id', 'fk_spdp_pus_attachments_doc_id')
                    ->references('id')->on('doc.spdp_pusiknas_documents')
                    ->onDelete('cascade')->onUpdate('cascade');
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
            Schema::table('doc.spdp_pusiknas_document_attachments', function (Blueprint $table) {
                $table->dropForeign('fk_spdp_pus_attachments_doc_id');
            });
            Schema::dropIfExists('doc.spdp_pusiknas_document_attachments');

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
};
