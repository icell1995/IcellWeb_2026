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
            $table->string('polda_id')->nullable();
            $table->string('polda_name')->nullable();
            $table->string('polres_name')->nullable();
            $table->text('temporary_deductive')->nullable();
            $table->text('accident_description')->nullable();
            $table->string('case_flag')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accidents', function (Blueprint $table) {
            $table->dropColumn('case_flag');
            $table->dropColumn('accident_description');
            $table->dropColumn('temporary_deductive');
            $table->dropColumn('polres_name');
            $table->dropColumn('polda_name');
            $table->dropColumn('polda_id');
        });
    }
};
