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
        Schema::table('doc.surat_pemberitahuan_dimulainya_penyidikan_pusiknas_documents', function (Blueprint $table) {
            // Ubah kembali menjadi NOT NULL (wajib diisi sejak awal)
            $table->string('document_number')->nullable(false)->change();
            $table->date('document_date')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('doc.surat_pemberitahuan_dimulainya_penyidikan_pusiknas_documents', function (Blueprint $table) {
            // Kembalikan ke nullable jika di-rollback
            $table->string('document_number')->nullable()->change();
            $table->date('document_date')->nullable()->change();
        });
    }
};
