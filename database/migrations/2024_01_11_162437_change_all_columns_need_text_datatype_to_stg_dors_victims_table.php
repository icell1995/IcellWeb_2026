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
        Schema::table('public.stg_dors_victims', function (Blueprint $table) {
            $table->text('alamat')->nullable()->change();
            $table->text('alamat_non_nkri')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('public.stg_dors_victims', function (Blueprint $table) {
            $table->string('alamat')->nullable()->change();
            $table->string('alamat_non_nkri')->nullable()->change();
        });
    }
};
