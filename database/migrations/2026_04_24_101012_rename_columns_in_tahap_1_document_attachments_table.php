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
        Schema::table('doc.tahap_1_document_attachments', function (Blueprint $table) {
            $table->renameColumn('file_path', 'path');
            $table->renameColumn('file_name', 'name');
            $table->renameColumn('file_type', 'mimetype');
            $table->renameColumn('file_size', 'size');
            $table->renameColumn('category', 'type');
            
            $table->string('original_name')->nullable()->after('name');
            $table->string('extension')->nullable()->after('original_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('doc.tahap_1_document_attachments', function (Blueprint $table) {
            $table->renameColumn('path', 'file_path');
            $table->renameColumn('name', 'file_name');
            $table->renameColumn('mimetype', 'file_type');
            $table->renameColumn('size', 'file_size');
            $table->renameColumn('type', 'category');
            
            $table->dropColumn(['original_name', 'extension']);
        });
    }
};
