<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use phpDocumentor\Reflection\PseudoTypes\False_;

class Spdpp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('spdpp', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('accident_id');
            $table->uuid('id_springas')->nullable();
            $table->uuid('id_sprindik')->nullable();
            $table->string('kejaksaan_id')->nullable();
            $table->integer('pengadilan_id')->nullable();
            $table->string('no_spdp');
            $table->string('no_lp');
            $table->string('no_sprindik');
            $table->date('sprindik_date');
            $table->date('spdp_date')->nullable();
            $table->string('category_spdp');
            $table->text('suspect_name');
            $table->string('endorsee_name');
            $table->string('lampiran');
            $table->text('tembusan');
            $table->string('pengadilan');
            $table->uuid('latter_signature');
            $table->string('klasifikasi');
            $table->string('for_attention')->nullable();
            $table->integer('Lokasi_Dibuat')->nullable();
            $table->binary('Attachment')->nullable();
            $table->boolean('is_integrated')->default(false);
            $table->string('Created_By')->nullable();
            $table->string('Updated_By')->nullable();
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
        Schema::dropIfExists('spdpp');
    }
}
