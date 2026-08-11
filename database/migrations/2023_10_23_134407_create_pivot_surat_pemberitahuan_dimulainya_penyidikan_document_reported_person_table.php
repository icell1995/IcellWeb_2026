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
        Schema::create('pivot.surat_pemberitahuan_dimulainya_penyidikan_doc_reported_person', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->uuid('surat_pemberitahuan_dimulainya_penyidikan_document_id');
            $table->uuid('reported_person_id');

            $table->timestamps();

            $table->foreign('surat_pemberitahuan_dimulainya_penyidikan_document_id', 'fk_spdp_doc_reported_person_spdp_document_id')
                ->references('id')
                ->on('doc.surat_pemberitahuan_dimulainya_penyidikan_documents')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreign('reported_person_id', 'fk_spdp_doc_reported_person_reported_person_id')
                ->references('id')
                ->on('public.reported_persons')
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //drop foreign key
        Schema::table('pivot.surat_pemberitahuan_dimulainya_penyidikan_doc_reported_person', function (Blueprint $table) {
            $table->dropForeign('fk_spdp_doc_reported_person_spdp_document_id');
            $table->dropForeign('fk_spdp_doc_reported_person_reported_person_id');
        });

        Schema::dropIfExists('pivot.surat_pemberitahuan_dimulainya_penyidikan_doc_reported_person');
    }
};
