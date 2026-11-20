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
        Schema::table('lib.document_categories', function (Blueprint $table) {
            $table->unsignedBigInteger('updated_by')->nullable()->after('is_active');
            $table->foreign('updated_by', 'fk_document_categories_updated_by')
                ->references('id')
                ->on('public.users')
                ->onDelete('set null')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lib.document_categories', function (Blueprint $table) {
            $table->dropForeign('fk_document_categories_updated_by');
            $table->dropColumn('updated_by');
        });
    }
};
