<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvestigationWarrantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('investigation_warrants', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('Id Surat'); 
            $table->uuid('accident_id')->nullable()->comment('Id Kecelakaan'); 

            $table->string('letter_number')->comment('No Surat Sprindik');
            $table->dateTime('issued_date')->comment('Tanggal Sprindik');
            $table->integer('location_created')->nullable()->comment('Lokasi Dibuat Sprindik (diperlukan master data lokasi dari EPPNS)');
            $table->dateTime('start_date')->comment('Tanggal Mulai Sprindik');
            $table->dateTime('end_date')->comment('Tanggal Berakhir Sprindik');
            $table->binary('attachment')->nullable()->comment('Diisi dengan lampiran hasil output yang telah di scan (PDF)');
            
            $table->boolean('is_integrated')->default(false)->comment('Apakah sudah terintegrasi dengan EMP?');

            $table->string('created_by')->comment('Diisi data user pembuat');
            $table->string('updated_by')->nullable()->comment('Diisi data user pembuat');
            
            $table->timestamps();

            $table->foreign('accident_id')->references('id')->on('accidents')->onDelete('set null')->onUpdate('cascade');
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
        Schema::table('investigation_warrants', function (Blueprint $table) {
            $table->dropForeign('investigation_warrants_accident_id_foreign');
        });
        
        Schema::dropIfExists('investigation_warrants');
    }
}
