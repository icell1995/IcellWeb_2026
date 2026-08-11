<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::beginTransaction();
        try{
            Schema::table('doc.laporan_hasil_gelar_perkara_documents', function (Blueprint $table) {
                $table->unsignedBigInteger('created_by_user_id')->nullable();
                $table->unsignedBigInteger('updated_by_user_id')->nullable();
                $table->unsignedBigInteger('deleted_by_user_id')->nullable();
            });

            Schema::table('doc.laporan_hasil_gelar_perkara_documents', function (Blueprint $table) {
                $table->foreign('created_by_user_id', 'fk_lhgp_doc_created_by_user_id')
                    ->references('id')
                    ->on('users')
                    ->onUpdate('cascade')
                    ->onDelete('restrict');
                $table->foreign('updated_by_user_id', 'fk_lhgp_doc_updated_by_user_id')
                    ->references('id')
                    ->on('users')
                    ->onUpdate('cascade')
                    ->onDelete('restrict');
                $table->foreign('deleted_by_user_id', 'fk_lhgp_doc_deleted_by_user_id')
                    ->references('id')
                    ->on('users')
                    ->onUpdate('cascade')
                    ->onDelete('restrict');
            });

            //drop column created_by, updated_by, deleted_by if exists
            Schema::table('doc.laporan_hasil_gelar_perkara_documents', function (Blueprint $table) {
                $table->dropColumn('created_by');
                $table->dropColumn('updated_by');
                $table->dropColumn('deleted_by');
            });

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::beginTransaction();
        try{
            // drop fk
            Schema::table('doc.laporan_hasil_gelar_perkara_documents', function (Blueprint $table) {
                $table->dropForeign('fk_lhgp_doc_created_by_user_id');
                $table->dropForeign('fk_lhgp_doc_updated_by_user_id');
                $table->dropForeign('fk_lhgp_doc_deleted_by_user_id');
            });

            Schema::table('doc.laporan_hasil_gelar_perkara_documents', function (Blueprint $table) {
                $table->dropIndex('fk_lhgp_doc_created_by_user_id');
                $table->dropIndex('fk_lhgp_doc_updated_by_user_id');
                $table->dropIndex('fk_lhgp_doc_deleted_by_user_id');

                $table->dropColumn('created_by_user_id');
                $table->dropColumn('updated_by_user_id');
                $table->dropColumn('deleted_by_user_id');
            });

            // rollback column created_by, updated_by, deleted_by
            Schema::table('doc.laporan_hasil_gelar_perkara_documents', function (Blueprint $table) {
                $table->string('created_by')->nullable();
                $table->string('updated_by')->nullable();
                $table->string('deleted_by')->nullable();
            });

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
};
