<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLhgpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lhgp', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('accident_id');
            $table->string('no_lp');
            $table->string('no_sprindik');
            $table->string('jenis_lhgp');
            $table->string('jenis_gelar_perkara');
            $table->string('surat_undangan');
            $table->date('tanggal_pelaksanaan');
            $table->time('waktu_pelaksanaan');
            $table->string('zona_waktu');
            $table->string('tempat_pelaksanaan');
            $table->string('pimpinan_gelar_perkara');
            $table->string('pemapar');
            $table->text('Pembahasan');
            $table->text('Kesimpulan');
            $table->text('Penutup');
            $table->string('pejabat_penandatangan');
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
        Schema::dropIfExists('lhgp');
    }
}
