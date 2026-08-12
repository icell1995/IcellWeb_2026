<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocSuratKetetapanPenghentianPenyidikanDocumentOfficersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('doc.surat_ketetapan_penghentian_penyidikan_document_officers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('surat_ketetapan_penghentian_penyidikan_document_id');

            $table->bigInteger('sort')->default(0);
            $table->string('register_number');
            $table->string('first_title')->nullable();
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('last_title')->nullable();

            $table->json('rank')->nullable();
            $table->json('position')->nullable();
            $table->json('role')->nullable();

            $table->string('phone_number')->nullable();
            $table->string('email')->nullable();
            $table->text('information')->nullable();

            $table->json('headquarter_police')->nullable();
            $table->json('regional_police')->nullable();
            $table->json('resort_police')->nullable();

            $table->enum('status', ['PRESENT', 'PAST', 'EXTERNAL'])->default('PRESENT');
            $table->enum('class', ['MEMBER', 'LEADER', 'SIGNATORY'])->default('MEMBER');
            $table->enum('flag', ['INTERNAL', 'MOVED', 'EXTERNAL'])->default('INTERNAL');
            $table->enum('insert_method', ['MANUAL', 'IMPORT'])->default('IMPORT');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('surat_ketetapan_penghentian_penyidikan_document_id', 'fk_skppy_doc_officers_skppy_document_id')
                  ->references('id')
                  ->on('doc.surat_ketetapan_penghentian_penyidikan_documents')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop foreign key
        Schema::table('doc.surat_ketetapan_penghentian_penyidikan_document_officers', function (Blueprint $table) {
            $table->dropForeign('fk_skppy_doc_officers_skppy_document_id');
        });
        
        Schema::dropIfExists('doc.surat_ketetapan_penghentian_penyidikan_document_officers');
    }
}
