<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccidentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accidents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('no_lp');
            $table->string('officer_id');
            $table->string('rank_id')->nullable();
            $table->string('officer_first_name')->nullable();
            $table->string('officer_last_name')->nullable();
            $table->string('polres_id');
            $table->date('accident_date');
            $table->time('accident_time');
            $table->date('report_date');
            $table->time('report_time');
            $table->float('latitude')->nullable();
            $table->float('longtitude')->nullable();
            $table->text('road_name')->nullable();
            $table->string('accident_type_id',8)->nullable();
            $table->string('weather_cond_id',8)->nullable();
            $table->string('light_cond_id',8)->nullable();
            $table->string('road_function_id',8)->nullable();
            $table->string('road_state_id',8)->nullable();
            $table->string('urgent_accident_id',8)->nullable();
            $table->text('damage_lose_desc')->nullable();
            $table->integer('md');
            $table->integer('lb');
            $table->integer('lr');
            $table->uuid('surat_tugas_id')->nullable();
            $table->integer('state');
            $table->string('selra_flag','50')->nullable();
            $table->integer('state_selra_flag')->nullable();
            $table->datetime('last_update')->nullable();
            $table->string('category')->nullable();
            $table->string('tipe_update')->nullable();
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
        Schema::dropIfExists('accidents');
    }
}
