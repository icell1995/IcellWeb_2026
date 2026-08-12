<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Snapshot petugas untuk Surat Perintah Penangkapan (ketua, anggota, penandatangan, yang menyerahkan).
     */
    public function up(): void
    {
        Schema::create('doc.surat_perintah_penangkapan_document_officers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('surat_perintah_penangkapan_document_id');

            $table->bigInteger('sort')->default(0);
            $table->string('officer_id', 255)->nullable()->comment('FK public.officers (snapshot source)');

            $table->string('register_number')->nullable();
            $table->string('first_title')->nullable();
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('last_title')->nullable();

            $table->string('position_id', 255)->nullable();
            $table->string('rank_id', 255)->nullable();

            $table->string('phone_number')->nullable();
            $table->string('email')->nullable();
            $table->text('information')->nullable();

            $table->string('police_id')->nullable();

            $table->enum('status', ['PRESENT', 'PAST', 'EXTERNAL'])->default('PRESENT')->nullable(true);
            $table->enum('class', ['MEMBER', 'LEADER', 'SIGNATORY', 'SUBMITTED'])->default('MEMBER')->nullable(true);
            $table->enum('flag', ['INTERNAL', 'MOVED', 'EXTERNAL'])->default('INTERNAL')->nullable(true);
            $table->enum('insert_method', ['MANUAL', 'IMPORT'])->default('IMPORT')->nullable(true);

            $table->timestamps();

            $table->foreign('surat_perintah_penangkapan_document_id', 'fk_spp_doc_officers_spp_document_id')
                ->references('id')
                ->on('doc.surat_perintah_penangkapan_documents')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('officer_id', 'fk_spp_doc_officers_officer_id')
                ->references('id')
                ->on('public.officers')
                ->onDelete('set null')
                ->onUpdate('cascade');

            $table->foreign('police_id', 'fk_spp_doc_officers_police_id')
                ->references('id')
                ->on('lib.polices')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->foreign('position_id', 'fk_spp_doc_officers_position_id')
                ->references('id')
                ->on('lib.positions')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->foreign('rank_id', 'fk_spp_doc_officers_rank_id')
                ->references('id')
                ->on('lib.ranks')
                ->onDelete('restrict')
                ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('doc.surat_perintah_penangkapan_document_officers', function (Blueprint $table) {
            $table->dropForeign('fk_spp_doc_officers_spp_document_id');
            $table->dropForeign('fk_spp_doc_officers_officer_id');
            $table->dropForeign('fk_spp_doc_officers_police_id');
            $table->dropForeign('fk_spp_doc_officers_position_id');
            $table->dropForeign('fk_spp_doc_officers_rank_id');
        });

        Schema::dropIfExists('doc.surat_perintah_penangkapan_document_officers');
    }
};
