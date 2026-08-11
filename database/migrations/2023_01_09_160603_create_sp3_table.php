<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSp3Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sp3', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('accident_id');
            $table->string('no_spdp');
            $table->string('no_sp3');
            $table->string('no_lp');
            $table->string('no_surat_perintah_penyidikan');
            $table->date('tanggal_sp_dik');
            $table->string('no_sprindik');
            $table->string('penerima_surat');
            $table->string('klasifikasi');
            $table->date('tanggal_berlaku');
            $table->string('alasan');
            $table->string('lampiran');
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
        Schema::dropIfExists('sp3');
    }
}
