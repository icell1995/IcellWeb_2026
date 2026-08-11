<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('public.stg_dors_accidents', function (Blueprint $table) {
            $table->uuid('id')->primary();
    
            $table->string('no_lp')->nullable(); //no lp
            $table->string('nrp_pembuat')->nullable();
            $table->string('nama_pembuat')->nullable();
            $table->string('pangkat_pembuat')->nullable();
            $table->string('nrp_penerima')->nullable();
            $table->string('id_polda')->nullable();
            $table->string('id_polres')->nullable();
            $table->string('id_polsek')->nullable();
            $table->dateTime('tgl_laporan')->nullable();
            $table->dateTime('waktu_kejadian')->nullable();
            $table->string('waktu_kejadian_faktual')->nullable();
            $table->text('tempat_kejadian')->nullable();
            $table->text('apa_terjadi')->nullable();
            $table->text('bagaimana_terjadi')->nullable();
            $table->text('pasal_kamtibmas')->nullable();
            $table->string('tkp_id_kota')->nullable();
            $table->string('tkp_id_provinsi')->nullable();
            $table->string('tkp_id_kecamatan')->nullable();
            $table->string('tkp_id_desa')->nullable();
            $table->string('kerugian')->nullable();
            $table->text('tindakan_diambil')->nullable();
            $table->string('satuan')->nullable();
            $table->string('kategori_lokasi')->nullable();
            $table->string('id_satker')->nullable();
            $table->string('dors_id')->nullable();
            $table->text('uraian_kejadian')->nullable();
            $table->text('kesimpulan_sementara')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('public.stg_dors_accidents');
    }
};
