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
        Schema::table('history.check_officer_data_histories', function (Blueprint $table) {
            $table->string('created_by_name')->nullable();
            $table->string('updated_by_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('history.check_officer_data_histories', function (Blueprint $table) {
            $table->dropColumn('created_by_name');
            $table->dropColumn('updated_by_name');
        });
    }
};
