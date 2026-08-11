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
        Schema::table('lib.polices', function (Blueprint $table) {
            $table->date('start_date_whitelisted_document_legacy')->nullable()->default('2021-01-01');
            $table->date('end_date_whitelisted_document_legacy')->nullable()->default('2024-12-31');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lib.polices', function (Blueprint $table) {
            $table->dropColumn('start_date_whitelisted_document_legacy');
            $table->dropColumn('end_date_whitelisted_document_legacy');
        });
    }
};
