<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('doc.tahap_2_document_attachments', function (Blueprint $table) {
            if (!Schema::hasColumn('doc.tahap_2_document_attachments', 'original_name')) {
                $table->string('original_name')->nullable();
            }
            if (!Schema::hasColumn('doc.tahap_2_document_attachments', 'name')) {
                $table->string('name')->nullable();
            }
            if (!Schema::hasColumn('doc.tahap_2_document_attachments', 'extension')) {
                $table->string('extension')->nullable();
            }
            if (!Schema::hasColumn('doc.tahap_2_document_attachments', 'size')) {
                $table->bigInteger('size')->nullable();
            }
            if (!Schema::hasColumn('doc.tahap_2_document_attachments', 'mimetype')) {
                $table->string('mimetype')->nullable();
            }
            if (!Schema::hasColumn('doc.tahap_2_document_attachments', 'type')) {
                $table->string('type')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('doc.tahap_2_document_attachments', function (Blueprint $table) {
            $table->dropColumn(['original_name', 'name', 'extension', 'size', 'mimetype', 'type']);
        });
    }
};
