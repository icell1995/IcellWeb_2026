<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('doc.daftar_pencarian_barang_documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('accident_id')->nullable()->index();
            $table->string('status_id')->nullable()->default('2');
            $table->string('document_category_id')->nullable()->default('0705');

            // Kop Surat
            $table->string('daerah_police_full_name')->nullable();
            $table->string('resor_police_full_name')->nullable();
            $table->text('resor_police_address')->nullable();

            // Judul & Nomor
            $table->string('document_number')->nullable();

            // Dasar Rujukan (Berdasarkan)
            $table->string('accident_number')->nullable();
            $table->string('accident_date')->nullable();
            $table->text('court_permit_text')->nullable();

            // Perkara & Narahubung
            $table->string('dugaan_tindak_pidana')->nullable();
            $table->string('pasal_diduga')->nullable();
            $table->string('uu_nomor')->nullable()->default('22');
            $table->string('uu_tahun')->nullable()->default('2009');
            $table->string('uu_tentang')->nullable()->default('Lalu Lintas dan Angkutan Jalan');
            $table->text('tempat_kejadian')->nullable();
            $table->text('waktu_kejadian')->nullable();
            $table->string('contact_officer_id')->nullable();
            $table->string('contact_officer_role')->nullable()->default('Penyidik Pembantu');
            $table->string('contact_officer_name')->nullable();
            $table->string('contact_officer_phone')->nullable();

            // Tabel Keterangan Barang (1 - 12)
            $table->string('case_vehicle_id')->nullable();
            $table->string('item_name')->nullable();
            $table->string('quantity')->nullable()->default('1 (satu) unit');
            
            // 3. Tanda-tanda khusus
            $table->string('registration_number')->nullable();
            $table->string('cylinder_capacity')->nullable();
            $table->string('fuel_type')->nullable();
            $table->string('tnkb_color')->nullable();
            $table->string('registration_year')->nullable();
            $table->string('bpkb_number')->nullable();
            $table->text('special_signs_other')->nullable();

            // 4. Ciri-ciri
            $table->string('item_type')->nullable();
            $table->string('item_category')->nullable();
            $table->string('item_model')->nullable();
            $table->text('physical_features_other')->nullable();

            // 5 - 12
            $table->string('item_color')->nullable();
            $table->string('manufacture_year')->nullable();
            $table->string('estimated_price')->nullable();
            $table->string('trade_mark')->nullable();
            $table->text('engine_frame_number')->nullable();
            $table->string('manufacture_place')->nullable();
            $table->text('loss_chronology')->nullable();
            $table->text('important_notes')->nullable();

            // Kepada
            $table->string('kepada_3')->nullable()->default('Pertinggal');

            // Tanggal & Penandatangan
            $table->string('document_location')->nullable();
            $table->string('document_date')->nullable();
            $table->string('signatory_id')->nullable();
            $table->string('signatory_head_text')->nullable();
            $table->string('signatory_position_name')->nullable()->default('KASAT LANTAS');
            $table->string('signatory_name')->nullable();
            $table->string('signatory_rank_name')->nullable();
            $table->string('signatory_register_number')->nullable();

            // User tracking (users.id is bigint)
            $table->unsignedBigInteger('created_by_user_id')->nullable();
            $table->unsignedBigInteger('updated_by_user_id')->nullable();
            $table->unsignedBigInteger('deleted_by_user_id')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('doc.daftar_pencarian_barang_documents');
    }
};

