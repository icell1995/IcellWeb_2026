<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateDocLaporanHasilGelarPerkaraDocumentSuspectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('doc.laporan_hasil_gelar_perkara_document_suspects', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->uuid('laporan_hasil_gelar_perkara_document_id');
                
                $table->enum('status', ['IMPORT', 'WITH_IDENTITY', 'WITHOUT_IDENTITY'])->default('IMPORT')->nullable(true);
                $table->enum('class', ['DETERMINATION', 'ARREST', 'REVOCATION'])->default('MEMBER')->nullable(true);
                $table->enum('flag', ['TERLAPOR', 'TERDUGA', 'TERSANGKA'])->default('INTERNAL')->nullable(true);
                
                $table->unsignedBigInteger('identity_type_id')->nullable();
                $table->string('identity_number')->nullable();
                
                $table->string('name')->nullable();
                $table->unsignedBigInteger('gender_id')->nullable();
                
                $table->string('birth_place')->nullable();
                $table->date('birth_date')->nullable();
                
                $table->string('mother_name')->nullable();
                $table->string('father_name')->nullable();
                
                $table->unsignedBigInteger('ethnic_id')->nullable();
                $table->unsignedBigInteger('job_id')->nullable();
                $table->unsignedBigInteger('religion_id')->nullable();
                $table->unsignedBigInteger('education_id')->nullable();
                $table->unsignedBigInteger('marital_status_id')->nullable();
                
                $table->boolean('is_phone')->default(true);
                $table->string('phone')->nullable();
                
                $table->boolean('is_email')->default(true);
                $table->string('email')->nullable();
                
                $table->unsignedBigInteger('location_id')->nullable();
                $table->string('address')->nullable();

                $table->text('information')->nullable();
                
                $table->bigInteger('sort')->default(0);
                
                $table->enum('insert_method', ['MANUAL', 'IMPORT'])->default('IMPORT')->nullable(true);
                
                $table->timestamps();
                
                $table->foreign('laporan_hasil_gelar_perkara_document_id', 'fk_lhgp_doc_suspects_lh_gelar_perkara_document_id')->references('id')->on('doc.laporan_hasil_gelar_perkara_documents')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::beginTransaction();
        try{
            // drop foreign key
            Schema::table('doc.laporan_hasil_gelar_perkara_document_suspects', function (Blueprint $table) {
                $table->dropForeign('fk_lhgp_doc_suspects_lh_gelar_perkara_document_id');
            });

            Schema::dropIfExists('doc.laporan_hasil_gelar_perkara_document_suspects');

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
