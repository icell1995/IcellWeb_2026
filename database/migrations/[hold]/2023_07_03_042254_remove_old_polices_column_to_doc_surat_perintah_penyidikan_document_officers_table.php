<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveOldPolicesColumnToDocSuratPerintahPenyidikanDocumentOfficersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('doc.surat_perintah_penyidikan_document_officers', function (Blueprint $table) {
            $table->dropColumn('headquarter_police');
            $table->dropColumn('regional_police');
            $table->dropColumn('resort_police');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('doc.surat_perintah_penyidikan_document_officers', function (Blueprint $table) {
            $table->json('headquarter_police')->nullable();
            $table->json('regional_police')->nullable();
            $table->json('resort_police')->nullable();
        });
    }
}
