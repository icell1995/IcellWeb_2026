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
        Schema::table('accidents', function (Blueprint $table) {
            $table->string('selra_number')->nullable();
            $table->date('selra_date')->nullable();
            $table->dateTime('selra_uploaded_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accidents', function (Blueprint $table) {
            $table->dropColumn('selra_number');
            $table->dropColumn('selra_date');
            $table->dropColumn('selra_uploaded_at');
        });
    }
};
