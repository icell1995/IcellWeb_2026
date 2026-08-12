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
        Schema::create('public.officer_police_histories', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('officer_id');
            $table->string('police_id');
            $table->string('position_id')->nullable();
            $table->string('rank_id')->nullable();

            $table->date('enter_date')->nullable();
            $table->date('exit_date')->nullable();

            $table->boolean('is_present')->nullable();
            $table->string('status')->nullable();
            $table->string('flag')->nullable();

            $table->timestamps();

            $table->foreign('officer_id', 'fk_officer_police_histories_officer_id')->references('id')->on('public.officers')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('police_id', 'fk_officer_police_histories_police_id')->references('id')->on('lib.polices')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('position_id', 'fk_officer_police_histories_position_id')->references('id')->on('lib.positions')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('rank_id', 'fk_officer_police_histories_rank_id')->references('id')->on('lib.ranks')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //drop foreign key
        Schema::table('public.officer_police_histories', function (Blueprint $table) {
            $table->dropForeign('fk_officer_police_histories_officer_id');
            $table->dropForeign('fk_officer_police_histories_police_id');
            $table->dropForeign('fk_officer_police_histories_position_id');
            $table->dropForeign('fk_officer_police_histories_rank_id');
        });
        
        Schema::dropIfExists('public.officer_police_histories');
    }
};
