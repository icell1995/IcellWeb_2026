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
            Schema::create('doc.surat_permintaan_penggeledahan_documents', function (Blueprint $table) {

                $table->uuid('id')->primary();

                $table->uuid('accident_id')->nullable();
                $table->uuid('surat_pemberitahuan_dimulainya_penyidikan_document_id')->nullable();

                $table->string('document_number');
                $table->date('document_date');
                $table->string('no_spdp')->nullable();
                $table->uuid('no_sprindik')->nullable();
                $table->string('daftar_penggeledahan')->nullable();
                $table->string('alamat_penggeledahan')->nullable();
                $table->string('suspect_id')->nullable();
                $table->string('signatory_id')->nullable();

                // status & workflow
                $table->string('status_id')->nullable(); //status id documet 
                $table->string('document_category_id')->nullable();
                $table->boolean('is_active')->default(true);
               

                // audit & sync
                $table->json('timestamps_log')->nullable();
                $table->json('ip_addresses')->nullable();
                $table->dateTime('released_at')->nullable();
                $table->dateTime('approved_at')->nullable();
                $table->dateTime('rejected_at')->nullable();
                $table->dateTime('last_synced_at')->nullable()
                    ->comment('Waktu terakhir disinkronkan ke Pusiknas');

                $table->unsignedBigInteger('created_by_user_id')->nullable();
                $table->unsignedBigInteger('updated_by_user_id')->nullable();
                $table->unsignedBigInteger('deleted_by_user_id')->nullable();

                $table->timestamps();
                $table->softDeletes();


                 // Foreign Keys
                 $table->foreign('accident_id', 'fk_penggeledahan_pus_docs_accident_id')
                    ->references('id')->on('public.accidents')
                    ->onDelete('set null')->onUpdate('cascade');

                $table->foreign('no_sprindik', 'fk_penggeledahan_pus_docs_sprindik_id')
                    ->references('id')->on('doc.surat_perintah_penyidikan_documents')
                    ->onDelete('restrict')->onUpdate('cascade');

                $table->foreign('surat_pemberitahuan_dimulainya_penyidikan_document_id', 'fk_penggeledahan_pus_docs_spdp_id')
                    ->references('id')->on('doc.surat_pemberitahuan_dimulainya_penyidikan_documents')
                    ->onDelete('restrict')->onUpdate('cascade');

                $table->foreign('created_by_user_id', 'fk_penggeledahan_pus_docs_created_by')
                    ->references('id')->on('users')
                    ->onDelete('restrict')->onUpdate('cascade');

                $table->foreign('updated_by_user_id', 'fk_penggeledahan_pus_docs_updated_by')
                    ->references('id')->on('users')
                    ->onDelete('restrict')->onUpdate('cascade');

                $table->foreign('deleted_by_user_id', 'fk_penggeledahan_pus_docs_deleted_by')
                    ->references('id')->on('users')
                    ->onDelete('restrict')->onUpdate('cascade');
            
            DB::commit();
        });
        }catch (\Throwable $th) {
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
        try {
            Schema::dropIfExists('doc.surat_permintaan_penggeledahan_document');
            Schema::dropIfExists('doc.surat_permintaan_penggeledahan_documents');

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
};
