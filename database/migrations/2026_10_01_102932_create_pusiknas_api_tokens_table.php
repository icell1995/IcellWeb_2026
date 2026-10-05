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
        Schema::create('pusiknas_api_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('token', 255)->unique();
            $table->string('username', 50);
            $table->string('ip_address', 100)->nullable();
            $table->timestamp('expires_at')->index();
            $table->string('last_used_ip', 100)->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pusiknas_api_tokens');
    }
};
