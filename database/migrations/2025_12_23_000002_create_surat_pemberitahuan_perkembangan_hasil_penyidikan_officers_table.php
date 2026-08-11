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
        Schema::create('doc.surat_pemberitahuan_perkembangan_hasil_penyidikan_officers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('sp2hp_document_id'); // Reference to sp2hp documents table
            $table->string('officer_id')->nullable(); // Reference to officers table (string, not UUID)
            
            // Officer/Penyidik data (unified - digunakan untuk SIGNATORY dan INVESTIGATOR)
            $table->string('register_number')->nullable()->comment('NRP');
            $table->string('name')->nullable()->comment('Nama lengkap');
            $table->string('rank_id')->nullable()->comment('ID atau Nama Pangkat (string untuk fleksibilitas)');
            $table->string('position_id')->nullable()->comment('ID atau Nama Jabatan (string untuk fleksibilitas)');
            $table->string('phone_number')->nullable()->comment('Nomor Telepon');
            $table->string('email')->nullable()->comment('Email');
            $table->string('police_id')->nullable()->comment('ID atau Nama Satuan/Unit (string untuk fleksibilitas)');
            
            // Order untuk sorting
            $table->integer('sort_order')->default(0);
            
            // Class, Status, Flag fields (yang membedakan SIGNATORY vs INVESTIGATOR)
            $table->string('class')->nullable()->comment('SIGNATORY, INVESTIGATOR, etc');
            $table->string('status')->nullable()->comment('PRESENT, ABSENT, etc');
            $table->string('flag')->nullable()->comment('INTERNAL, EXTERNAL, etc');
            $table->string('insert_method')->nullable()->comment('IMPORT, MANUAL, etc');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes with explicit short names
            $table->index('sp2hp_document_id', 'sp2hp_officers_sp2hp_doc_id_idx');
            $table->index('officer_id', 'sp2hp_officers_officer_id_idx');
            $table->index('sort_order', 'sp2hp_officers_sort_order_idx');
            $table->index('class', 'sp2hp_officers_class_idx');
            
            // Foreign keys with explicit short names
            $table->foreign('sp2hp_document_id', 'sp2hp_officers_sp2hp_doc_id_fk')
                  ->references('id')
                  ->on('doc.surat_pemberitahuan_perkembangan_hasil_penyidikan_documents')
                  ->onDelete('cascade');
            // Note: Foreign key to officers table removed - officer_id dapat nullable untuk fleksibilitas
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doc.surat_pemberitahuan_perkembangan_hasil_penyidikan_officers');
    }
};
