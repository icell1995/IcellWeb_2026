<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvestigationCommencementNotificationLettersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('investigation_commencement_notification_letters', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('Id Surat');
            $table->uuid('id_sprindik')->comment('Id Surat Sprindik');
            $table->string('letter_number')->comment('Nomor Surat SPDP');
            $table->datetime('letter_date')->comment('Tanggal dokumen');
            $table->integer('prosecutor_office_id')->comment('Id Mater data Kejaksaan');
            $table->json('suspect_data')->comment('Data Tersangka atau Data Terlapor (Nama, Tempat lahir, tanggal lahir, Id jenis kelamin, alamat, Id pendidikan, Id pekerjaan, gelar depan, gelar belakang, nama bapak, nama ibu, Id agama, Id status perkawinan, Id kewarganegaraan, Id jenis identitas, nomor identitas, umur saat lk, nama alias, status (1 untuk terlapor dan 2 untuk tersangka))');
            $table->string('receiver_name')->comment('Nama Kejaksaan yang menerima perkara');
            $table->string('attachments')->comment('keterangan untuk jumlah lampiran surat');
            $table->json('cc')->comment('Tembusan Surat');
            $table->integer('created_location_id')->comment('Lokasi Dibuat Sprindik (diperlukan master data lokasi dari EPPNS)');
            $table->integer('court_id')->comment('Id Master Data Pengadilan');
            $table->uuid('springas_id')->comment('Id Surat Perintah Tugas');
            $table->binary('attachment_file')->comment('Diisi dengan lampiran hasil output yang telah di scan (PDF)');
            $table->datetime('created_at')->comment('Tanggal dibuat dokumen ini');
            $table->string('created_by')->comment('Diisi data user pembuat');
            $table->datetime('updated_at')->nullable()->comment('Tanggal diubah dokumen ini');
            $table->string('updated_by')->nullable()->comment('Diisi data user pembuat');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('investigation_commencement_notification_letters');
    }
}
