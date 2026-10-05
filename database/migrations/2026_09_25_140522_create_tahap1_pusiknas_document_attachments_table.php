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
        Schema::create('doc.tahap_1_document_attachments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tahap_1_document_id');
            $table->string('file_name');
            $table->string('file_path');
            $table->integer('file_size')->nullable();
            $table->string('file_type')->nullable();
            $table->text('description')->nullable();
            
            $table->uuid('created_by_user_id')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doc.tahap_1_document_attachments');
    }
};
