<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPoliceIdToDocSuratPerintahPenyidikanDocumentOfficersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('doc.surat_perintah_penyidikan_document_officers', function (Blueprint $table) {
            $table->string('police_id')->nullable();

            $table->foreign('police_id', 'fk_spsidik_doc_officers_police_id')->references('id')->on('lib.polices')->onDelete('restrict')->onUpdate('cascade');
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
            // Drop constraint
            $table->dropForeign('fk_spsidik_doc_officers_police_id');

            $table->dropColumn('police_id');
        });
    }
}
