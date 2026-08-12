<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //
        DB::beginTransaction();
        try {
            Schema::table('doc.surat_perintah_penahanan_documents', function (Blueprint $table) {
                $table->unsignedBigInteger('created_by_user_id')->nullable();
                $table->unsignedBigInteger('updated_by_user_id')->nullable();
                $table->unsignedBigInteger('deleted_by_user_id')->nullable();
            });

            Schema::table('doc.surat_perintah_penahanan_documents', function (Blueprint $table) {
                $table->foreign('created_by_user_id', 'fk_sprin_penahanan_doc_created_by_user_id')
                    ->references('id')
                    ->on('users')
                    ->onUpdate('cascade')
                    ->onDelete('restrict');
                $table->foreign('updated_by_user_id', 'fk_sprin_penahanan_doc_updated_by_user_id')
                    ->references('id')
                    ->on('users')
                    ->onUpdate('cascade')
                    ->onDelete('restrict');
                $table->foreign('deleted_by_user_id', 'fk_sprin_penahanan_doc_deleted_by_user_id')
                    ->references('id')
                    ->on('users')
                    ->onUpdate('cascade')
                    ->onDelete('restrict');
            });

            //drop column created_by, updated_by, deleted_by if exists
            Schema::table('doc.surat_perintah_penahanan_documents', function (Blueprint $table) {
                $table->dropColumn('created_by_id');
                $table->dropColumn('updated_by_id');
                $table->dropColumn('deleted_by_id');
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
        //
        DB::beginTransaction();
        try {
            // drop fk
            Schema::table('doc.surat_perintah_penahanan_documents', function (Blueprint $table) {
                $table->dropForeign('fk_sprin_penahanan_doc_created_by_user_id');
                $table->dropForeign('fk_sprin_penahanan_doc_updated_by_user_id');
                $table->dropForeign('fk_sprin_penahanan_doc_deleted_by_user_id');
            });

            Schema::table('doc.surat_perintah_penahanan_documents', function (Blueprint $table) {
                $table->dropIndex('fk_sprin_penahanan_doc_created_by_user_id');
                $table->dropIndex('fk_sprin_penahanan_doc_updated_by_user_id');
                $table->dropIndex('fk_sprin_penahanan_doc_deleted_by_user_id');

                $table->dropColumn('created_by_user_id');
                $table->dropColumn('updated_by_user_id');
                $table->dropColumn('deleted_by_user_id');
            });

            // rollback column created_by, updated_by, deleted_by
            Schema::table('doc.surat_perintah_penahanan_documents', function (Blueprint $table) {
                $table->string('created_by_id')->nullable();
                $table->string('updated_by_id')->nullable();
                $table->string('deleted_by_id')->nullable();
            });

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
};
