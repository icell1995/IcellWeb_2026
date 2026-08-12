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
        Schema::create('history.check_officer_data_histories', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('register_number');
            $table->string('name');
            $table->string('rank_name')->nullable();
            $table->string('position_name')->nullable();
            $table->string('unit_name')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('gender_name')->nullable();
            $table->string('work_email')->nullable();
            $table->string('investigator_certificate')->nullable();
            $table->string('investigator_number')->nullable();
            $table->json('work_units')->nullable();

            $table->unsignedBigInteger('created_by_user_id')->nullable();
            $table->unsignedBigInteger('updated_by_user_id')->nullable();

            $table->timestamps();

            $table->foreign('created_by_user_id', 'fk_check_officer_data_histories_created_by_user_id')->references('id')->on('public.users')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('updated_by_user_id', 'fk_check_officer_data_histories_updated_by_user_id')->references('id')->on('public.users')->onDelete('set null')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //drop foreign key
        Schema::table('history.check_officer_data_histories', function (Blueprint $table) {
            $table->dropForeign('fk_check_officer_data_histories_created_by_user_id');
            $table->dropForeign('fk_check_officer_data_histories_updated_by_user_id');
        });
        Schema::dropIfExists('history.check_officer_data_histories');
    }
};
