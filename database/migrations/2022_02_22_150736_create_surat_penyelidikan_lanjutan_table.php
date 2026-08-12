<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuratPenyelidikanLanjutanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('surat_penyelidikan_lanjutan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('accident_id');
            $table->string('name');
            $table->string('category');
            $table->string('initial');
            $table->string('created_by');
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
        Schema::dropIfExists('surat_penyelidikan_lanjutan');
    }
}
