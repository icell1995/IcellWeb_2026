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
        // 1. Perbaiki doc.surat_ketetapan_penghentian_penyidikan_documents
        Schema::table('doc.surat_ketetapan_penghentian_penyidikan_documents', function (Blueprint $table) {
            $table->dropColumn(['created_by_user_id', 'updated_by_user_id', 'deleted_by_user_id']);
        });

        Schema::table('doc.surat_ketetapan_penghentian_penyidikan_documents', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by_user_id')->nullable()->after('deleted_by')->comment('ID from users.id');
            $table->unsignedBigInteger('updated_by_user_id')->nullable()->after('created_by_user_id')->comment('ID from users.id');
            $table->unsignedBigInteger('deleted_by_user_id')->nullable()->after('updated_by_user_id')->comment('ID from users.id');
        });

        // 2. Perbaiki doc.tahap_1_documents
        Schema::table('doc.tahap_1_documents', function (Blueprint $table) {
            $table->dropColumn(['created_by_user_id', 'updated_by_user_id', 'deleted_by_user_id']);
        });

        Schema::table('doc.tahap_1_documents', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by_user_id')->nullable()->comment('ID from users.id');
            $table->unsignedBigInteger('updated_by_user_id')->nullable()->comment('ID from users.id');
            $table->unsignedBigInteger('deleted_by_user_id')->nullable()->comment('ID from users.id');
            
            // Re-connect the Foreign Keys mapped back to BigInt users
            $table->foreign('created_by_user_id', 'fk_thp1_created_by_uid')->references('id')->on('public.users')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('updated_by_user_id', 'fk_thp1_updated_by_uid')->references('id')->on('public.users')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('deleted_by_user_id', 'fk_thp1_deleted_by_uid')->references('id')->on('public.users')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 
    }
};
