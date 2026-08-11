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
        Schema::create('public.log_user_logins', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->nullable();
            
            $table->string('username');
            $table->string('name');

            $table->string('role_name')->nullable();
            $table->integer('role_id')->nullable();
            $table->string('police_name')->nullable();
            $table->string('police_id')->nullable();

            $table->text('user_agent')->nullable();
            $table->string('ip_address')->nullable();

            $table->timestamp('login_at')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id', 'fk_log_user_logins_user_id')->references('id')->on('public.users')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('role_id', 'fk_log_user_logins_role_id')->references('id')->on('lib.roles')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('police_id', 'fk_log_user_logins_police_id')->references('id')->on('lib.polices')->onDelete('set null')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('public.log_user_logins', function (Blueprint $table) {
            $table->dropForeign('fk_log_user_logins_user_id');
            $table->dropForeign('fk_log_user_logins_role_id');
            $table->dropForeign('fk_log_user_logins_police_id');
        });
        Schema::dropIfExists('public.log_user_logins');
    }
};
