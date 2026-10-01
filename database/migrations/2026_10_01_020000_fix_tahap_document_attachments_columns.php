<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Fixes tahap_1 attachment table (wrong column names) and
     * creates tahap_2 attachment table from scratch.
     */
    public function up(): void
    {
        // ── FIX: tahap_1_document_attachments columns ──────────────────────
        // Existing table has: file_name, file_path, file_size, file_type
        // Controller/model expects: original_name, name, extension, size, mimetype, type
        Schema::table('doc.tahap_1_document_attachments', function (Blueprint $table) {
            // Add the columns the controller/model expect
            if (!Schema::hasColumn('doc.tahap_1_document_attachments', 'original_name')) {
                $table->string('original_name')->nullable();
            }
            if (!Schema::hasColumn('doc.tahap_1_document_attachments', 'name')) {
                $table->string('name')->nullable();
            }
            if (!Schema::hasColumn('doc.tahap_1_document_attachments', 'extension')) {
                $table->string('extension')->nullable();
            }
            if (!Schema::hasColumn('doc.tahap_1_document_attachments', 'size')) {
                $table->bigInteger('size')->nullable();
            }
            if (!Schema::hasColumn('doc.tahap_1_document_attachments', 'mimetype')) {
                $table->string('mimetype')->nullable();
            }
            if (!Schema::hasColumn('doc.tahap_1_document_attachments', 'type')) {
                $table->string('type')->nullable();
            }
        });

        // ── CREATE: tahap_2_document_attachments ───────────────────────────
        if (!Schema::hasTable('doc.tahap_2_document_attachments')) {
            Schema::create('doc.tahap_2_document_attachments', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('tahap_2_document_id');
                $table->string('original_name')->nullable();
                $table->string('name')->nullable();
                $table->string('extension')->nullable();
                $table->bigInteger('size')->nullable();
                $table->string('mimetype')->nullable();
                $table->string('type')->nullable();
                $table->text('description')->nullable();
                $table->uuid('created_by_user_id')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('doc.tahap_1_document_attachments', function (Blueprint $table) {
            $table->dropColumn(['original_name', 'name', 'extension', 'size', 'mimetype', 'type']);
        });

        Schema::dropIfExists('doc.tahap_2_document_attachments');
    }
};
