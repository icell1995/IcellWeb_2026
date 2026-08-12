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
        Schema::table('lib.roles', function (Blueprint $table) {
            $table->string('name', 100)->change();
            $table->text('description')->nullable()->after('name');
            $table->integer('level')->default(4)->after('description');
        });

        Schema::table('permissions', function (Blueprint $table) {
            $table->string('name', 100)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lib.roles', function (Blueprint $table) {
            $table->dropColumn(['description', 'level']);
            $table->string('name', 20)->change();
        });

        Schema::table('permissions', function (Blueprint $table) {
            $table->string('name', 30)->change();
        });
    }
};
