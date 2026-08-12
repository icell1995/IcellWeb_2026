<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnMessageToDocSchemaInsideTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('doc.surat_perintah_penyelidikan_documents', function (Blueprint $table) {
            $table->json('messages')->nullable();
        });
       
        Schema::table('doc.surat_perintah_penyidikan_documents', function (Blueprint $table) {
            $table->json('messages')->nullable();
        });

        Schema::table('doc.surat_perintah_tugas_documents', function (Blueprint $table) {
            $table->json('messages')->nullable();
        });
      
        Schema::table('doc.laporan_hasil_gelar_perkara_documents', function (Blueprint $table) {
            $table->json('messages')->nullable();
        });
        
        Schema::table('doc.surat_ketetapan_tentang_penetapan_tersangka_documents', function (Blueprint $table) {
            $table->json('messages')->nullable();
        });
        
        Schema::table('doc.surat_pemberitahuan_dimulainya_penyidikan_documents', function (Blueprint $table) {
            $table->json('messages')->nullable();
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
        Schema::table('doc.surat_perintah_penyelidikan_documents', function (Blueprint $table) {
            $table->dropColumn('messages');
        });

        Schema::table('doc.surat_perintah_penyidikan_documents', function (Blueprint $table) {
            $table->dropColumn('messages');
        });

        Schema::table('doc.surat_perintah_tugas_documents', function (Blueprint $table) {
            $table->dropColumn('messages');
        });

        Schema::table('doc.laporan_hasil_gelar_perkara_documents', function (Blueprint $table) {
            $table->dropColumn('messages');
        });

        Schema::table('doc.surat_ketetapan_tentang_penetapan_tersangka_documents', function (Blueprint $table) {
            $table->dropColumn('messages');
        });

        Schema::table('doc.surat_pemberitahuan_dimulainya_penyidikan_documents', function (Blueprint $table) {
            $table->dropColumn('messages');
        });  
    }
}
