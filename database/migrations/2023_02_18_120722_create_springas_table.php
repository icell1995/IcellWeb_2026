<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSprinGasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('springas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('accident_id');
            $table->string('no_surat');
            $table->string('no_lp');
            $table->date('tanggal_springas');
            $table->string('lokasi')->nullable();
            $table->date('tanggal_dimulai');
            $table->date('tanggal_berakhir');
            $table->string('pejabat_penandatangan');
            $table->string('officer_id')->nullable();
            $table->boolean('is_integrated')->default(false);
            $table->string('created_by');
            $table->string('updated_by')->nullable();
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
        Schema::dropIfExists('sprintgas');
    }
}
