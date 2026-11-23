<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('esignature_tokens')) {
            Schema::create('esignature_tokens', function (Blueprint $table) {
                $table->id();
                $table->string('provider', 64)->unique();
                $table->text('encrypted_token');
                $table->timestamp('refreshed_at')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('esignature_tokens');
    }
};
