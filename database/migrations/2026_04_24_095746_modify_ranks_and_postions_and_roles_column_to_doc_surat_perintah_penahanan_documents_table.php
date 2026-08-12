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
        Schema::table('doc.surat_perintah_penahanan_document_officers', function (Blueprint $table) {
            //
            $table->dropColumn('rank');
            $table->dropColumn('position');
            $table->dropColumn('role');
            $table->string('position_id', 255)->nullable();
            $table->string('rank_id', 255)->nullable();

            $table->foreign('position_id', 'fk_sprin_penahanan_doc_officers_position_id')->references('id')->on('lib.positions')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('rank_id', 'fk_sprin_penahanan_doc_officers_rank_id')->references('id')->on('lib.ranks')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('doc.surat_perintah_penahanan_document_officers', function (Blueprint $table) {
            $table->dropForeign('fk_sprin_penahanan_doc_officers_position_id');
            $table->dropForeign('fk_sprin_penahanan_doc_officers_rank_id');
        });
        Schema::table('doc.surat_perintah_penahanan_document_officers', function (Blueprint $table) {
            $table->dropColumn('position_id');
            $table->dropColumn('rank_id');

            $table->json('rank')->nullable();
            $table->json('position')->nullable();
            $table->json('role')->nullable();
        });
    }
};
