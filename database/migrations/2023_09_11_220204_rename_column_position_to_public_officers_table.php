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
        Schema::table('public.officers', function (Blueprint $table) {
            $table->renameColumn('position', 'position_short_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('public.officers', function (Blueprint $table) {
            $table->renameColumn('position_short_name', 'position');
        });
    }
};
