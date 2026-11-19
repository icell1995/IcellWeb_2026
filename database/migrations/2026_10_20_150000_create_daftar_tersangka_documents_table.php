<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('doc.daftar_tersangka_documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('accident_id')->index();
            $table->string('document_category_id')->nullable()->default('0804');
            $table->string('status_id')->nullable()->default('2');

            // Header & Identitas Dokumen
            $table->string('nomor_surat')->nullable();
            $table->date('tanggal_surat')->nullable();
            $table->string('dikeluarkan_di')->nullable();
            $table->string('satker_penyidik')->nullable();

            // Rujukan Kasus
            $table->string('nomor_lp')->nullable();
            $table->date('tanggal_lp')->nullable();

            // Daftar Tersangka (Menyimpan snapshot array 9 kolom spesimen S-38.3)
            $table->jsonb('daftar_tersangka')->nullable();

            // Pejabat Penandatangan (Penyidik / Penyidik Pembantu)
            $table->string('nama_penandatangan')->nullable();
            $table->string('pangkat_penandatangan')->nullable();
            $table->string('nrp_penandatangan')->nullable();
            $table->string('jabatan_penandatangan')->nullable()->default('Penyidik / Penyidik Pembantu');

            // Tracking & Audit Trail
            $table->unsignedBigInteger('created_by_user_id')->nullable();
            $table->unsignedBigInteger('updated_by_user_id')->nullable();
            $table->unsignedBigInteger('deleted_by_user_id')->nullable();
            $table->jsonb('ip_addresses')->nullable();
            $table->jsonb('timestamps')->nullable();
            $table->jsonb('messages')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('doc.daftar_tersangka_documents');
    }
};
