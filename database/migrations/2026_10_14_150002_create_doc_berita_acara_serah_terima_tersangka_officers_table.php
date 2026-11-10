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
        try {
            DB::statement('CREATE SCHEMA IF NOT EXISTS doc;');

            if (!Schema::hasTable('doc.berita_acara_serah_terima_tersangka_officers')) {
                Schema::create('doc.berita_acara_serah_terima_tersangka_officers', function (Blueprint $table) {
                    $table->uuid('id')->primary();
                    $table->uuid('doc_id')->nullable();
                    $table->string('officer_id')->nullable();

                    $table->string('first_name')->nullable();
                    $table->string('last_name')->nullable();
                    $table->string('first_title')->nullable();
                    $table->string('last_title')->nullable();
                    $table->string('register_number')->nullable();
                    $table->string('position_id')->nullable();
                    $table->string('rank_id')->nullable();
                    $table->string('police_id')->nullable();

                    // Peran: 'DELIVERER' (Penyidik yang Menyerahkan), 'SIGNATORY' (Atasan Penandatangan), 'MEMBER' (Saksi/Anggota)
                    $table->string('class')->nullable()->default('DELIVERER');
                    $table->string('status_id')->nullable()->default('1');
                    $table->integer('order_number')->nullable()->default(0);

                    $table->unsignedBigInteger('created_by_user_id')->nullable();
                    $table->unsignedBigInteger('updated_by_user_id')->nullable();
                    $table->unsignedBigInteger('deleted_by_user_id')->nullable();

                    $table->timestamps();
                    $table->softDeletes();

                    $table->foreign('doc_id', 'fk_ba_st_tsk_officers_doc_id')
                        ->references('id')->on('doc.berita_acara_serah_terima_tersangka_documents')
                        ->onDelete('cascade')->onUpdate('cascade');
                });
            }

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
        Schema::dropIfExists('doc.berita_acara_serah_terima_tersangka_officers');
    }
};
