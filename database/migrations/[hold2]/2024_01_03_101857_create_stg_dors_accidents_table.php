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
            $table->uuid('id');
    
            $table->string('no_lp')->nullable(); //no lp
            $table->integer('report_id')->nullable(); 
            $table->string('officer_id')->nullable();
            $table->string('officer_name')->nullable();
            $table->string('officer_rank')->nullable();
            $table->string('recipient_id')->nullable();
            $table->string('polda_id')->nullable();
            $table->string('polres_id')->nullable();
            $table->string('polsek_id')->nullable();
            $table->date('report_date')->nullable();
            $table->time('report_time')->nullable();
            $table->date('accident_date')->nullable();
            $table->time('accident_time')->nullable();
            $table->string('accident_date_factual')->nullable();
            $table->text('road_name')->nullable();
            $table->text('criminal_act')->nullable();
            $table->text('accident')->nullable();
            $table->text('process_desc')->nullable();
            $table->text('pasal_kamtibmas')->nullable();
            $table->string('tkp_id_kota')->nullable();
            $table->string('tkp_id_provinsi')->nullable();
            $table->string('tkp_id_kecamatan')->nullable();
            $table->string('tkp_id_desa')->nullable();
            $table->double('material_loss')->nullable();
            $table->string('get_act')->nullable();
            $table->string('satuan')->nullable();
            $table->string('road_type_id')->nullable();
            $table->string('id_satker')->nullable();
            $table->string('no_spkt')->nullable();
            $table->smallInteger('state')->nullable();//
            $table->integer('flag_lp')->nullable();//
            $table->string('dors_id')->nullable();
            $table->string('kerugian')->nullable();
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
