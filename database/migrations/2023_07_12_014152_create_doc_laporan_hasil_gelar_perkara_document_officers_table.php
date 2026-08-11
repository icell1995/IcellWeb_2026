<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateDocLaporanHasilGelarPerkaraDocumentOfficersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::beginTransaction();
        try{
            Schema::create('doc.laporan_hasil_gelar_perkara_document_officers', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->uuid('laporan_hasil_gelar_perkara_document_id');

                
                $table->bigInteger('sort')->default(0);
                $table->string('register_number');
                $table->string('first_title')->nullable();
                $table->string('first_name');
                $table->string('last_name')->nullable();
                $table->string('last_title')->nullable();
                
                $table->boolean('is_on_behalf')->default(false);
                $table->string('on_behalf_name')->nullable();
                
                $table->json('rank')->nullable();
                $table->json('position')->nullable();
                $table->json('role')->nullable();
                
                $table->string('phone_number')->nullable();
                $table->string('email')->nullable();
                $table->text('information')->nullable();
                
                $table->string('police_id')->nullable();
                
                $table->enum('status', ['PRESENT', 'PAST', 'EXTERNAL', 'UPPER_UNIT_LEVEL'])->default('PRESENT')->nullable(true);
                $table->enum('class', ['MEMBER', 'LEADER', 'SIGNATORY'])->default('MEMBER')->nullable(true);
                $table->enum('flag', ['INTERNAL', 'MOVED', 'EXTERNAL', 'UPPER_UNIT_LEVEL'])->default('INTERNAL')->nullable(true);
                $table->enum('insert_method', ['MANUAL', 'IMPORT'])->default('IMPORT')->nullable(true);
                
                $table->timestamps();
                
                $table->foreign('laporan_hasil_gelar_perkara_document_id', 'fk_lhgp_doc_officers_lh_gelar_perkara_document_id')->references('id')->on('doc.laporan_hasil_gelar_perkara_documents')->onDelete('cascade')->onUpdate('cascade');
                $table->foreign('police_id', 'fk_lhgp_doc_officers_police_id')->references('id')->on('lib.polices')->onDelete('restrict')->onUpdate('cascade');
            });

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }   
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
            Schema::table('doc.laporan_hasil_gelar_perkara_document_officers', function (Blueprint $table) {
                $table->dropForeign('fk_lhgp_doc_officers_lh_gelar_perkara_document_id');
                $table->dropForeign('fk_lhgp_doc_officers_police_id');
            });

            Schema::dropIfExists('doc.laporan_hasil_gelar_perkara_document_officers');

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
