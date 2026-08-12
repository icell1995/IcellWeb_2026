<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameSpringasAndRelationshipTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('springas', 'surat_perintah_tugas_documents');
        Schema::rename('officer_springas', 'surat_perintah_tugas_document_officer');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename('surat_perintah_tugas_documents', 'springas');
        Schema::rename('surat_perintah_tugas_document_officer', 'officer_springas');
    }
}
