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
        if (!Schema::hasTable('doc.surat_perintah_penggeledahan_badan_documents')) {
            Schema::create('doc.surat_perintah_penggeledahan_badan_documents', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('accident_id')->nullable()->index();
                $table->string('document_number')->nullable();
                $table->date('document_date')->nullable();
                $table->json('data')->nullable();
                $table->string('created_by')->nullable();
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
        Schema::dropIfExists('doc.surat_perintah_penggeledahan_badan_documents');
    }
};
