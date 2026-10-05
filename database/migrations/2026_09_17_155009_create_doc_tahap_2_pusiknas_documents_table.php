<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doc.tahap_2_documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('accident_id')->nullable();

            // Identitas Dokumen (sesuai SPPT-TI bpt2)
            $table->string('document_number')->nullable()->comment('Nomor Surat Pengantar (nomor_surat_pengantar)');
            $table->date('document_date')->nullable()->comment('Tanggal Surat Pengantar (tanggal_surat_pengantar)');
            $table->string('no_berkas_perkara')->nullable()->comment('Nomor Berkas Perkara');
            $table->string('no_spdp')->nullable()->comment('Nomor SPDP terkait');
            $table->date('tanggal_terima_p21')->nullable()->comment('Tanggal terima P-21 (wajib untuk BPT2)');

            $table->string('document_classification_id')->nullable();
            $table->string('prosecutor_id')->nullable()->comment('ID Kejaksaan Tujuan');

            // Konten SPPT-TI (JSON) - BPT2 wajib barang bukti
            $table->json('messages')->nullable()->comment('Payload: uraian, tempat_kejadian, waktu_kejadian, tahun, bulan, tanggal, daftar_saksi, daftar_barang_bukti (WAJIB), daftar_ahli, signatory_id, dll');
            $table->json('suspect_ids')->nullable()->comment('Array UUID tersangka');
            $table->json('carbon_copies')->nullable()->comment('Tembusan');
            $table->bigInteger('appendix')->default(0);

            $table->boolean('is_active')->default(true);
            $table->boolean('is_legacy')->default(false);
            $table->string('status_id')->nullable();
            $table->string('document_category_id')->nullable();

            $table->json('timestamps_log')->nullable();
            $table->json('ip_addresses')->nullable();

            $table->dateTime('released_at')->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->dateTime('rejected_at')->nullable();
            $table->dateTime('last_synced_at')->nullable();

            $table->string('created_by_user_id')->nullable();
            $table->string('updated_by_user_id')->nullable();
            $table->string('deleted_by_user_id')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('accident_id', 'fk_bpt2_accident_id')
                ->references('id')->on('public.accidents')
                ->onDelete('set null')->onUpdate('cascade');
        });

        // Officers (penandatangan)
        Schema::create('doc.tahap_2_document_officers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('tahap_2_document_id');

            $table->bigInteger('sort')->default(0);
            $table->string('register_number');
            $table->string('first_title')->nullable();
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('last_title')->nullable();

            $table->string('rank_id')->nullable();
            $table->string('position_id')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('email')->nullable();
            $table->text('information')->nullable();
            $table->string('police_id')->nullable();

            $table->enum('status', ['PRESENT', 'PAST', 'EXTERNAL'])->default('PRESENT')->nullable();
            $table->enum('class', ['MEMBER', 'LEADER', 'SIGNATORY'])->default('SIGNATORY')->nullable();
            $table->enum('flag', ['INTERNAL', 'MOVED', 'EXTERNAL'])->default('INTERNAL')->nullable();
            $table->enum('insert_method', ['MANUAL', 'IMPORT'])->default('IMPORT')->nullable();

            $table->timestamps();

            $table->foreign('tahap_2_document_id', 'fk_bpt2_officers_doc_id')
                ->references('id')->on('doc.tahap_2_documents')
                ->onDelete('cascade')->onUpdate('cascade');
        });

        // Pivot: suspect
        Schema::create('pivot.tahap_2_document_suspect', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('tahap_2_document_id');
            $table->uuid('suspect_id');
            $table->timestamps();

            $table->foreign('tahap_2_document_id', 'fk_bpt2_pivot_doc_id')
                ->references('id')->on('doc.tahap_2_documents')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('suspect_id', 'fk_bpt2_pivot_suspect_id')
                ->references('id')->on('public.suspects')
                ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pivot.tahap_2_document_suspect');
        Schema::table('doc.tahap_2_document_officers', function (Blueprint $table) {
            $table->dropForeign('fk_bpt2_officers_doc_id');
        });
        Schema::dropIfExists('doc.tahap_2_document_officers');
        Schema::table('doc.tahap_2_documents', function (Blueprint $table) {
            $table->dropForeign('fk_bpt2_accident_id');
        });
        Schema::dropIfExists('doc.tahap_2_documents');
    }
};
