<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doc.surat_pemberitahuan_dimulainya_penyidikan_pusiknas_document_officers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('surat_pemberitahuan_dimulainya_penyidikan_pusiknas_document_id');

            $table->bigInteger('sort')->default(0);
            $table->string('register_number');
            $table->string('first_title')->nullable();
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('last_title')->nullable();

            $table->string('rank_id')->nullable();
            $table->string('position_id')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('email')->nullable();
            $table->text('information')->nullable();

            $table->string('police_id')->nullable();

            $table->enum('status', ['PRESENT', 'PAST', 'EXTERNAL'])->default('PRESENT')->nullable();
            $table->enum('class', ['MEMBER', 'LEADER', 'SIGNATORY'])->default('MEMBER')->nullable();
            $table->enum('flag', ['INTERNAL', 'MOVED', 'EXTERNAL'])->default('INTERNAL')->nullable();
            $table->enum('insert_method', ['MANUAL', 'IMPORT'])->default('IMPORT')->nullable();

            $table->timestamps();

            $table->foreign('surat_pemberitahuan_dimulainya_penyidikan_pusiknas_document_id', 'fk_spdp_pusiknas_officers_doc_id')
                ->references('id')->on('doc.surat_pemberitahuan_dimulainya_penyidikan_pusiknas_documents')
                ->onDelete('cascade')->onUpdate('cascade');

            $table->foreign('police_id', 'fk_spdp_pusiknas_officers_police_id')
                ->references('id')->on('lib.polices')
                ->onDelete('restrict')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('doc.surat_pemberitahuan_dimulainya_penyidikan_pusiknas_document_officers', function (Blueprint $table) {
            $table->dropForeign('fk_spdp_pusiknas_officers_doc_id');
            $table->dropForeign('fk_spdp_pusiknas_officers_police_id');
        });
        Schema::dropIfExists('doc.surat_pemberitahuan_dimulainya_penyidikan_pusiknas_document_officers');
    }
};
