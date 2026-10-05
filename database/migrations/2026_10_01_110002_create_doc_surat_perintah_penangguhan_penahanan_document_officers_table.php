<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Pejabat Penandatangan / Petugas S-18 (schema: aparat_negara: nama, nomor_induk, jabatan, pangkat)
     */
    public function up(): void
    {
        DB::beginTransaction();
        try {
            DB::statement('CREATE SCHEMA IF NOT EXISTS doc;');

            if (!Schema::hasTable('doc.surat_perintah_penangguhan_penahanan_document_officers')) {
                Schema::create('doc.surat_perintah_penangguhan_penahanan_document_officers', function (Blueprint $table) {
                    $table->id();
                    $table->uuid('surat_perintah_penangguhan_penahanan_document_id');
                    $table->bigInteger('sort')->default(1);
                    $table->string('register_number')->comment('nomor_induk (NRP/NIP)');
                    $table->string('first_title')->nullable();
                    $table->string('first_name');
                    $table->string('last_name')->nullable();
                    $table->string('last_title')->nullable();
                    $table->string('phone_number')->nullable();
                    $table->string('email')->nullable();
                    $table->text('information')->nullable();
                    $table->string('police_id')->nullable();
                    $table->string('rank_id')->nullable();
                    $table->string('position_id')->nullable();
                    $table->string('status')->nullable();
                    $table->string('class')->nullable()->default('SIGNATORY');
                    $table->string('flag')->nullable()->default('INTERNAL');
                    $table->string('insert_method')->nullable();
                    $table->timestamps();

                    $table->foreign('surat_perintah_penangguhan_penahanan_document_id', 'fk_spp_guhan_officers_doc_id')
                        ->references('id')->on('doc.surat_perintah_penangguhan_penahanan_documents')
                        ->onDelete('cascade')->onUpdate('cascade');
                });
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('doc.surat_perintah_penangguhan_penahanan_document_officers');
    }
};
