<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssignmentOrderLettersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assignment_order_letters', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('Id Surat');
            $table->string('letter_number')->comment('No Surat Springas');
            $table->dateTime('letter_date')->comment('Tanggal Springas');
            $table->integer('location_id')->comment('Lokasi Dibuat Springas (diperlukan master data lokasi dari EPPNS)');
            $table->dateTime('start_date')->comment('Tanggal Mulai Springas');
            $table->dateTime('end_date')->comment('Tanggal Berakhir Springas');
            $table->json('officials')->comment('Diisi data pejabat yang menandatangani dokumen Springas (Nama, Pangkat, NRP/NIP, Jabatan)');
            $table->json('personnels')->comment('Diisi data anggota yang masuk dalam Springas (Nama, Pangkat, NRP/NIP, Jabatan)');
            $table->binary('attachment')->comment('Diisi dengan lampiran hasil output yang telah di-scan (PDF)');
            $table->dateTime('created_date')->comment('Tanggal dibuat dokumen ini');
            $table->string('created_by')->comment('Diisi data user pembuat');
            $table->dateTime('updated_date')->nullable()->comment('Tanggal diubah dokumen ini');
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
        Schema::dropIfExists('assignment_order_letters');
    }
}
