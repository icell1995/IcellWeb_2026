<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('doc_daftar_tersangka_documents', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('Id Surat'); 
            $table->uuid('accident_id')->nullable()->comment('Id Kecelakaan'); 
    
            $table->string('document_number')->comment('No Surat');
            $table->dateTime('document_date')->comment('Tanggal Surat');
    
            $table->boolean('is_active')->default(true);
            $table->boolean('is_legacy')->default(false)->nullable();

            $table->string('status_id')->nullable();
            $table->string('document_category_id')->nullable();

            $table->unsignedBigInteger('created_by_user_id')->nullable();
            $table->unsignedBigInteger('updated_by_user_id')->nullable();
            $table->unsignedBigInteger('deleted_by_user_id')->nullable();

            $table->json('messages')->nullable();

            $table->json('timestamps')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->datetime('released_at')->nullable();
            $table->dateTime('last_synced_at')->nullable()->comment('Waktu terakhir ditarik dengan EMP');
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();

            $table->json('ip_addresses')->nullable();
            
            $table->foreign('accident_id', 'fk_daftar_tersangka_docs_accident_id')->references('id')->on('public.accidents')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('status_id', 'fk_daftar_tersangka_docs_status_id')->references('id')->on('opt.statuses')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('document_category_id', 'fk_daftar_tersangka_docs_document_category_id')->references('id')->on('lib.document_categories')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('created_by_user_id', 'fk_daftar_tersangka_docs_created_by_user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('updated_by_user_id', 'fk_daftar_tersangka_docs_updated_by_user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('deleted_by_user_id', 'fk_daftar_tersangka_docs_deleted_by_user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('doc_daftar_tersangka_documents', function (Blueprint $table) {
            $table->dropForeign('fk_daftar_tersangka_docs_accident_id');
            $table->dropForeign('fk_daftar_tersangka_docs_status_id');
            $table->dropForeign('fk_daftar_tersangka_docs_document_category_id');
            $table->dropForeign('fk_daftar_tersangka_docs_created_by_user_id');
            $table->dropForeign('fk_daftar_tersangka_docs_updated_by_user_id');
            $table->dropForeign('fk_daftar_tersangka_docs_deleted_by_user_id');
        });
        Schema::dropIfExists('doc_daftar_tersangka_documents');
    }
};
