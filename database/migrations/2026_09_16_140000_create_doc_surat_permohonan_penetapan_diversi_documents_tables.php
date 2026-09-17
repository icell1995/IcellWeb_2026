<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocSuratPermohonanPenetapanDiversiDocumentsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('doc.surat_permohonan_penetapan_diversi_documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('accident_id')->nullable();
            $table->uuid('suspect_id')->nullable();
            $table->uuid('surat_perintah_penyidikan_document_id')->nullable();
            $table->uuid('surat_kesepakatan_diversi_document_id')->nullable();

            $table->string('court_id')->nullable();
            $table->string('court_type')->nullable();
            $table->string('court_name')->nullable();
            $table->string('court_place')->nullable();

            $table->string('document_number')->nullable();
            $table->date('document_date')->nullable();

            $table->string('status_id')->default('2');
            $table->string('document_category_id')->default('0212');

            $table->json('payload')->nullable();
            $table->json('messages')->nullable();
            $table->json('timestamps')->nullable();
            $table->json('ip_addresses')->nullable();

            $table->bigInteger('created_by_user_id')->nullable();
            $table->bigInteger('updated_by_user_id')->nullable();
            $table->bigInteger('deleted_by_user_id')->nullable();

            $table->boolean('is_active')->default(true);
            $table->boolean('is_legacy')->default(false);
            $table->timestamp('released_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('last_synced_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('accident_id', 'fk_sppd_docs_accident_id')
                ->references('id')->on('public.accidents')
                ->onDelete('set null')->onUpdate('cascade');

            $table->foreign('suspect_id', 'fk_sppd_docs_suspect_id')
                ->references('id')->on('public.suspects')
                ->onDelete('set null')->onUpdate('cascade');
        });

        Schema::create('doc.surat_permohonan_penetapan_diversi_document_officers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('surat_permohonan_penetapan_diversi_document_id');
            $table->string('register_number')->nullable();

            $table->string('first_title')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('last_title')->nullable();

            $table->string('rank_id')->nullable();
            $table->string('position_id')->nullable();
            $table->string('police_id')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('email')->nullable();

            $table->string('status')->nullable();
            $table->string('class')->nullable();
            $table->string('flag')->nullable();
            $table->string('insert_method')->nullable();

            $table->timestamps();

            $table->foreign('surat_permohonan_penetapan_diversi_document_id', 'fk_sppd_doc_officers_sppd_document_id')
                ->references('id')->on('doc.surat_permohonan_penetapan_diversi_documents')
                ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('doc.surat_permohonan_penetapan_diversi_document_officers');
        Schema::dropIfExists('doc.surat_permohonan_penetapan_diversi_documents');
    }
}
