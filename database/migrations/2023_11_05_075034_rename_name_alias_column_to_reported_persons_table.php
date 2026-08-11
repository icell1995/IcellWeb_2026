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
        Schema::table('reported_persons', function (Blueprint $table) {
            $table->renameColumn('name_alias', 'alias_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reported_persons', function (Blueprint $table) {
            $table->renameColumn('alias_name', 'name_alias');
        });
    }
};
