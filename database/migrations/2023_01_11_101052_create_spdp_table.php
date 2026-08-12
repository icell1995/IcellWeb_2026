<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpdpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('spdp', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('accident_id');
            $table->string('no_spdp');
            $table->string('no_lp');
            $table->string('no_sprindik');
            $table->date('sprindik_date');
            $table->date('spdp_date')->nullable();
            $table->string('category_spdp');
            $table->string('endorsee_name');
            $table->string('pengadilan');
            $table->string('klasifikasi');
            $table->string('suspect_name');
            $table->string('lampiran');
            $table->string('latter_signature');
            $table->string('for_attention')->nullable();
            $table->string('tembusan');
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
        Schema::dropIfExists('spdp');
    }
}
