<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSp2hpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sp2hp', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('accident_id');
            $table->string('tipe')->nullable();
            $table->string('tingkat')->nullable();
            $table->string('kota')->nullable();
            $table->date('tanggal_terbit');
            $table->string('nomor_surat_1')->nullable();
            $table->string('nomor_surat_2')->nullable();
            $table->string('nomor_surat_3')->nullable();
            $table->string('nomor_surat_4')->nullable();
            $table->string('nomor_surat_5')->nullable();
            $table->string('name')->nullable();
            $table->string('address')->nullable();
            $table->text('deskripsi');
            $table->string('created_by')->nullable();
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
        Schema::dropIfExists('sp2hp');
    }
}
