<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuratP21Tahap2Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('surat_p21_tahap_2', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('accident_id');
           
            $table->string('province_name')->nullable()->comment('Nama Provinsi');
            $table->string('polres_name')->nullable()->comment('Nama Polres');
            $table->string('polres_address')->nullable()->comment('Alamat Polres');
            $table->string('no_p21')->nullable()->comment('Nomor Surat P21');
            $table->date('p21_date')->nullable()->comment('Tanggal Surat P21');
            $table->date('p21_start_date')->nullable()->comment('Tanggal Mulai Surat P21');
            $table->string('p21_location')->nullable()->comment('Lokasi Surat P21');
            $table->string('classification')->nullable()->comment('Klasifikasi');
            $table->string('attachment')->nullable()->comment('Lampiran');
            $table->string('subject')->nullable()->comment('Perihal');
            $table->string('letter_recipient')->nullable()->comment('Kepada');
            $table->string('recipient_location')->nullable()->comment('Lokasi Kepada');
            $table->string('no_lp')->nullable()->comment('Nomor LP');
            $table->date('accident_date')->nullable()->comment('Tanggal Kejadian');
            $table->json('offense_articles')->nullable()->comment('Pasal Yang Dilanggar');
            $table->json('suspects')->nullable()->comment('Tersangka');
            $table->json('evidences')->nullable()->comment('Barang Bukti');
            $table->longText('description')->nullable()->comment('Uraian Kejadian');
            $table->json('cc')->nullable()->comment('Tembusan');
            $table->string('penyidik_name')->nullable()->comment('Nama Penyidik');
            $table->string('penyidik_position')->nullable()->comment('Jabatan Penyidik');
            $table->string('penyidik_nrp')->nullable()->comment('NRP Penyidik');
            
            $table->string('created_by')->nullable()->comment('Dibuat Oleh');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('surat_p21_tahap_2');
    }
}
