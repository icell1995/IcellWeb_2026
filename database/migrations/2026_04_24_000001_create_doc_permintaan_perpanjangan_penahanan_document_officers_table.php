<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Migration sebelumnya sempat gagal di FK; pastikan rerun aman.
        Schema::dropIfExists('doc.permintaan_perpanjangan_penahanan_document_officers');

        Schema::create('doc.permintaan_perpanjangan_penahanan_document_officers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('permintaan_perpanjangan_penahanan_document_id');
            // public.officers.id bertipe varchar(255) di environment ini (bukan uuid).
            $table->string('officer_id', 255)->nullable()->comment('FK public.officers (snapshot source)');

            $table->bigInteger('sort')->default(0);
            $table->string('register_number')->nullable();
            $table->string('first_title')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('last_title')->nullable();

            // Konsisten dengan migration 2023_07_14_620445: pakai FK rank_id/position_id (bukan JSON).
            $table->string('position_id', 255)->nullable();
            $table->string('rank_id', 255)->nullable();

            $table->string('phone_number')->nullable();
            $table->string('email')->nullable();
            $table->text('information')->nullable();

            $table->string('police_id')->nullable();

            $table->enum('status', ['PRESENT', 'PAST', 'EXTERNAL'])->default('PRESENT')->nullable(true);
            $table->enum('class', ['MEMBER', 'LEADER', 'SIGNATORY', 'CONTACT'])->default('MEMBER')->nullable(true);
            $table->enum('flag', ['INTERNAL', 'MOVED', 'EXTERNAL'])->default('INTERNAL')->nullable(true);
            $table->enum('insert_method', ['MANUAL', 'IMPORT'])->default('IMPORT')->nullable(true);

            $table->timestamps();

            $table->foreign(
                'permintaan_perpanjangan_penahanan_document_id',
                'fk_ppp_doc_officers_ppp_document_id'
            )
                ->references('id')
                ->on('doc.permintaan_perpanjangan_penahanan_documents')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('officer_id', 'fk_ppp_doc_officers_officer_id')
                ->references('id')
                ->on('public.officers')
                ->onDelete('set null')
                ->onUpdate('cascade');

            $table->foreign('police_id', 'fk_ppp_doc_officers_police_id')
                ->references('id')
                ->on('lib.polices')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->foreign('position_id', 'fk_ppp_doc_officers_position_id')
                ->references('id')
                ->on('lib.positions')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->foreign('rank_id', 'fk_ppp_doc_officers_rank_id')
                ->references('id')
                ->on('lib.ranks')
                ->onDelete('restrict')
                ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('doc.permintaan_perpanjangan_penahanan_document_officers', function (Blueprint $table) {
            $table->dropForeign('fk_ppp_doc_officers_ppp_document_id');
            $table->dropForeign('fk_ppp_doc_officers_officer_id');
            $table->dropForeign('fk_ppp_doc_officers_police_id');
            $table->dropForeign('fk_ppp_doc_officers_position_id');
            $table->dropForeign('fk_ppp_doc_officers_rank_id');
        });

        Schema::dropIfExists('doc.permintaan_perpanjangan_penahanan_document_officers');
    }
};

