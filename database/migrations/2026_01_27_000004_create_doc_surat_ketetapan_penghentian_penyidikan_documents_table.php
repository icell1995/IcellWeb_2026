<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateDocSuratKetetapanPenghentianPenyidikanDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::beginTransaction();
        try {
            Schema::create('doc.surat_ketetapan_penghentian_penyidikan_documents', function (Blueprint $table) {
                $table->uuid('id')->primary()->comment('Id Surat');
                $table->uuid('accident_id')->nullable()->comment('Id Kecelakaan');
                $table->string('document_category_id')->default('0206')->comment('ID Kategori Dokumen');
                
                $table->string('document_number')->comment('Nomor Surat Ketetapan Penghentian Penyidikan');
                $table->dateTime('document_date')->comment('Tanggal Surat');
                $table->date('effective_date')->nullable()->comment('Tanggal Berlaku SP3');
                
                $table->integer('prosecutor_id')->nullable()->comment('Penuntut Umum (JPU)');
                $table->integer('court_id')->nullable()->comment('Pengadilan Negeri');
                
                $table->string('nomor_serah_terima', 255)->nullable()->comment('Nomor Serah Terima');
                $table->date('tanggal_serah_terima')->nullable()->comment('Tanggal Serah Terima');
                $table->json('barang_bukti')->nullable()->comment('Daftar barang bukti yang diserahkan/dikembalikan');
                $table->integer('jumlah_bb')->nullable()->comment('Jumlah barang bukti');
                
                $table->string('case_classification')->nullable()->comment('Klasifikasi perkara');
                
                // Referensi dokumen lain
                $table->uuid('surat_perintah_penyidikan_id')->nullable()->comment('Referensi ke dokumen surat perintah penyidikan');
                $table->uuid('surat_pemberitahuan_dimulainya_penyidikan_id')->nullable()->comment('Referensi ke dokumen SPDP');
                $table->uuid('laporan_hasil_gelar_perkara_id')->nullable()->comment('Referensi ke dokumen laporan hasil gelar perkara');
                $table->string('alasan_penghentian', 255)->nullable()->comment('Alasan penghentian penyidikan');
                $table->text('menetapkan_alasan')->nullable()->comment('Alasan/Penjelasan detail keputusan');
                
                // Restorative Justice Fields
                $table->date('rj_tanggal_kesepakatan')->nullable()->comment('Tanggal kesepakatan keadilan restoratif (KUHAP Pasal 79-84)');
                $table->string('rj_nomor_kesepakatan', 255)->nullable()->comment('Nomor berita acara kesepakatan');
                $table->text('rj_pihak_korban')->nullable()->comment('Nama dan identitas pihak korban');
                $table->text('rj_pihak_pelaku')->nullable()->comment('Nama dan identitas pihak pelaku');
                $table->text('rj_bentuk_ganti_rugi')->nullable()->comment('Bentuk ganti rugi yang disepakati');
                $table->string('rj_nilai_ganti_rugi', 255)->nullable()->comment('Nilai ganti rugi (jika berupa uang)');
                $table->text('rj_keterangan_tambahan')->nullable()->comment('Keterangan tambahan kesepakatan restorative justice');
                $table->text('rj_dokumen_pendukung')->nullable()->comment('Path file dokumen pendukung keadilan restoratif (JSON array)');
                
                // Status and approval fields
                $table->string('status_id')->default('1')->comment('Status dokumen');
                $table->boolean('is_active')->default(true);
                $table->boolean('is_legacy')->default(false)->comment('Apakah dokumen legacy');
                
                // User tracking fields  
                $table->string('created_by')->nullable()->comment('Diisi data user (legacy)');
                $table->string('updated_by')->nullable()->comment('Diisi data user (legacy)');
                $table->string('deleted_by')->nullable()->comment('Diisi data user (legacy)');
                $table->uuid('created_by_user_id')->nullable()->comment('ID from users.id');
                $table->uuid('updated_by_user_id')->nullable()->comment('ID from users.id');
                $table->uuid('deleted_by_user_id')->nullable()->comment('ID from users.id');
                
                // Timestamps
                $table->timestamps();
                $table->softDeletes();
                $table->dateTime('submitted_at')->nullable()->comment('Waktu submit dokumen');
                $table->dateTime('approved_at')->nullable()->comment('Waktu approve dokumen');
                $table->dateTime('rejected_at')->nullable()->comment('Waktu reject dokumen');
                $table->dateTime('released_at')->nullable()->comment('Waktu rilis dokumen');
                $table->dateTime('last_synced_at')->nullable()->comment('Waktu terakhir ditarik dengan EMP');
                
                // Messages and metadata
                $table->json('messages')->nullable()->comment('Pesan approval/rejection');
                $table->json('timestamps')->nullable()->comment('Additional timestamps data');
                $table->json('ip_addresses')->nullable()->comment('IP address logs');
                
                // Foreign key will be added after accidents table is created
                // $table->foreign('accident_id', 'fk_skppy_docs_accident_id')
                //       ->references('id')
                //       ->on('public.accidents')
                //       ->onDelete('set null')
                //       ->onUpdate('cascade');
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
        try {
            // Drop foreign key
            Schema::table('doc.surat_ketetapan_tentang_penghentian_penyidikan_documents', function (Blueprint $table) {
                $table->dropForeign('fk_skppy_docs_accident_id');
            });
            
            Schema::dropIfExists('doc.surat_ketetapan_penghentian_penyidikan_documents');
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
