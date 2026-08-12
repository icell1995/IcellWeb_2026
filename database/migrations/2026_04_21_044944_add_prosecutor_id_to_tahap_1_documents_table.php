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
        Schema::table('doc.tahap_1_documents', function (Blueprint $table) {
            $table->string('prosecutor_id', 255)->nullable()->after('lampiran');
            
            // Add foreign key constraint to lib.prosecutors if it doesn't exist
            // Note: In this system, prosecutor_id usually refers to lib.prosecutors.id
            $table->foreign('prosecutor_id', 'tahap_1_documents_prosecutor_id_foreign')
                  ->references('id')
                  ->on('lib.prosecutors')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('doc.tahap_1_documents', function (Blueprint $table) {
            $table->dropForeign('tahap_1_documents_prosecutor_id_foreign');
            $table->dropColumn('prosecutor_id');
        });
    }
};
