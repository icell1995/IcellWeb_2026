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
        Schema::create('pivot.surat_pemberitahuan_penghentian_penyidikan_document_suspect', function (Blueprint $table) {
            $table->uuid('surat_pemberitahuan_penghentian_penyidikan_document_id')->nullable();
            $table->uuid('suspect_id')->nullable();
        });

        Schema::create('pivot.surat_pemberitahuan_penghentian_penyidikan_doc_reported_person', function (Blueprint $table) {
            $table->uuid('surat_pemberitahuan_penghentian_penyidikan_document_id')->nullable();
            $table->uuid('reported_person_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pivot.surat_pemberitahuan_penghentian_penyidikan_document_suspect');
        Schema::dropIfExists('pivot.surat_pemberitahuan_penghentian_penyidikan_doc_reported_person');
    }
};
