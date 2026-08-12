<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('log_api_divtik_polri', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('class_model', 255)->index();
            $table->string('ip_address', 100)->nullable()->index();
            $table->timestamps(); // created_at, updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_api_divtik_polri');
    }
};
