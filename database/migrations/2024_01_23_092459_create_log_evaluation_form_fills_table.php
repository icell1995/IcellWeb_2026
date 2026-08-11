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
        Schema::create('log_evaluation_form_fills', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->nullable();
            $table->string('police_id')->nullable();
            $table->string('register_number')->nullable();
            $table->string('name')->nullable();
            $table->string('rank_name')->nullable();
            $table->boolean('is_valid')->default(false);

            $table->timestamps();

            $table->foreign('user_id', 'fk_log_evaluation_form_fills_user_id')->references('id')->on('users')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('police_id', 'fk_log_evaluation_form_fills_police_id')->references('id')->on('lib.polices')->onDelete('set null')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('log_evaluation_form_fills', function (Blueprint $table) {
            $table->dropForeign('fk_log_evaluation_form_fills_user_id');
            $table->dropForeign('fk_log_evaluation_form_fills_police_id');
        });
        Schema::dropIfExists('log_evaluation_form_fills');
    }
};
