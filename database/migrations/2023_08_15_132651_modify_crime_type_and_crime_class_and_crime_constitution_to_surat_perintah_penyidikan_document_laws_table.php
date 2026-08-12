<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyCrimeTypeAndCrimeClassAndCrimeConstitutionToSuratPerintahPenyidikanDocumentLawsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('doc.surat_perintah_penyidikan_document_laws', function (Blueprint $table) {
            $table->dropColumn('crime_type');
            $table->dropColumn('crime_class');
            $table->dropColumn('constitution');
        });

        Schema::table('doc.surat_perintah_penyidikan_document_laws', function (Blueprint $table) {
            $table->string('crime_type_id')->nullable();
            $table->string('crime_class_id')->nullable();
            $table->string('crime_constitution_id')->nullable();
            $table->string('constitution')->nullable();

            $table->foreign('crime_type_id', 'fk_spsidik_doc_laws_crime_type_id')->references('id')->on('lib.crime_types')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('crime_class_id', 'fk_spsidik_doc_laws_crime_class_id')->references('id')->on('lib.crime_classes')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('crime_constitution_id', 'fk_spsidik_doc_laws_crime_constitution_id')->references('id')->on('lib.crime_constitutions')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //drop fk
        Schema::table('doc.surat_perintah_penyidikan_document_laws', function (Blueprint $table) {
            $table->dropForeign('fk_spsidik_doc_laws_crime_type_id');
            $table->dropForeign('fk_spsidik_doc_laws_crime_class_id');
            $table->dropForeign('fk_spsidik_doc_laws_crime_constitution_id');
        });

        Schema::table('doc.surat_perintah_penyidikan_document_laws', function (Blueprint $table) {
            $table->dropColumn('crime_type_id');
            $table->dropColumn('crime_class_id');
            $table->dropColumn('crime_constitution_id');
            $table->dropColumn('constitution');
        });

        Schema::table('doc.surat_perintah_penyidikan_document_laws', function (Blueprint $table) {
            $table->json('crime_type')->nullable();
            $table->json('crime_class')->nullable();
            $table->json('constitution')->nullable();
        });
    }
}
