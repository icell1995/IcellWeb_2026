<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // ---------------------------------------------------------------------
        // 1. SURAT PERINTAH PENGHENTIAN PENYELIDIKAN (Sprint Henti Lidik) TABLES
        // ---------------------------------------------------------------------

        // 1.1 Main document table
        if (!Schema::hasTable('doc.surat_perintah_penghentian_penyelidikan_documents')) {
            Schema::create('doc.surat_perintah_penghentian_penyelidikan_documents', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('accident_id')->nullable();
                $table->string('document_number')->nullable();
                $table->date('document_date')->nullable();
                $table->string('document_location')->nullable();
                $table->string('document_classification_id')->nullable();

                // References to related documents
                $table->uuid('surat_perintah_penyelidikan_document_id')->nullable();
                $table->uuid('surat_ketetapan_penghentian_penyelidikan_document_id')->nullable();
                $table->uuid('laporan_hasil_gelar_perkara_document_id')->nullable();

                // Details & reasons
                $table->json('kode_alasan')->nullable();
                $table->text('alasan_penghentian')->nullable();
                $table->text('dugaan_tindak_pidana')->nullable();
                $table->text('pasal_list')->nullable();
                $table->text('pertimbangan')->nullable();
                $table->text('dasar')->nullable();
                $table->text('untuk')->nullable();
                $table->json('carbon_copies')->nullable();

                // Status & audit metadata
                $table->boolean('is_active')->default(true);
                $table->string('status_id')->nullable();
                $table->string('document_category_id')->nullable();
                $table->json('messages')->nullable();
                $table->json('ip_addresses')->nullable();
                $table->json('timestamps')->nullable();
                $table->dateTime('submitted_at')->nullable();
                $table->dateTime('approved_at')->nullable();
                $table->dateTime('released_at')->nullable();
                $table->unsignedBigInteger('created_by_user_id')->nullable();
                $table->unsignedBigInteger('updated_by_user_id')->nullable();
                $table->unsignedBigInteger('deleted_by_user_id')->nullable();
                $table->timestamps();
                $table->softDeletes();
                $table->dateTime('last_synced_at')->nullable();

                // Foreign keys
                $table->foreign('accident_id', 'fk_sprint_henti_lidik_docs_accident_id')
                    ->references('id')->on('public.accidents')
                    ->onDelete('set null')->onUpdate('cascade');

                $table->foreign('document_classification_id', 'fk_sprint_henti_lidik_docs_class_id')
                    ->references('id')->on('lib.document_classifications')
                    ->onDelete('restrict')->onUpdate('cascade');

                $table->foreign('surat_perintah_penyelidikan_document_id', 'fk_sprint_henti_lidik_docs_sprinlidik_id')
                    ->references('id')->on('doc.surat_perintah_penyelidikan_documents')
                    ->onDelete('set null')->onUpdate('cascade');

                $table->foreign('laporan_hasil_gelar_perkara_document_id', 'fk_sprint_henti_lidik_docs_lhgp_id')
                    ->references('id')->on('doc.laporan_hasil_gelar_perkara_documents')
                    ->onDelete('set null')->onUpdate('cascade');
            });
        }

        // 1.2 Document officers table
        if (!Schema::hasTable('doc.surat_perintah_penghentian_penyelidikan_document_officers')) {
            Schema::create('doc.surat_perintah_penghentian_penyelidikan_document_officers', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->uuid('surat_perintah_penghentian_penyelidikan_document_id');

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

                $table->foreign('surat_perintah_penghentian_penyelidikan_document_id', 'fk_sprint_henti_lidik_off_doc_id')
                    ->references('id')->on('doc.surat_perintah_penghentian_penyelidikan_documents')
                    ->onDelete('cascade')->onUpdate('cascade');

                $table->foreign('police_id', 'fk_sprint_henti_lidik_off_police_id')
                    ->references('id')->on('lib.polices')
                    ->onDelete('restrict')->onUpdate('cascade');
            });
        }

        // 1.3 Document attachments table
        if (!Schema::hasTable('doc.surat_perintah_penghentian_penyelidikan_document_attachments')) {
            Schema::create('doc.surat_perintah_penghentian_penyelidikan_document_attachments', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('surat_perintah_penghentian_penyelidikan_document_id');

                $table->string('name')->nullable();
                $table->string('file_path')->nullable();
                $table->string('file_extension')->nullable();
                $table->bigInteger('file_size')->nullable();
                $table->boolean('is_active')->default(true);

                $table->timestamps();
                $table->softDeletes();

                $table->foreign('surat_perintah_penghentian_penyelidikan_document_id', 'fk_sprint_henti_lidik_att_doc_id')
                    ->references('id')->on('doc.surat_perintah_penghentian_penyelidikan_documents')
                    ->onDelete('cascade')->onUpdate('cascade');
            });
        }

        // 1.4 Suspect pivot table (Pihak terlapor/pelaku yang dihentikan)
        if (!Schema::hasTable('pivot.surat_perintah_penghentian_penyelidikan_document_suspect')) {
            Schema::create('pivot.surat_perintah_penghentian_penyelidikan_document_suspect', function (Blueprint $table) {
                $table->uuid('surat_perintah_penghentian_penyelidikan_document_id');
                $table->uuid('suspect_id');
                $table->timestamps();

                $table->foreign('surat_perintah_penghentian_penyelidikan_document_id', 'fk_sprint_henti_lidik_piv_doc_id')
                    ->references('id')->on('doc.surat_perintah_penghentian_penyelidikan_documents')
                    ->onDelete('cascade')->onUpdate('cascade');

                $table->foreign('suspect_id', 'fk_sprint_henti_lidik_piv_susp_id')
                    ->references('id')->on('public.suspects')
                    ->onDelete('cascade')->onUpdate('cascade');

                $table->primary(['surat_perintah_penghentian_penyelidikan_document_id', 'suspect_id'], 'pk_sprint_henti_lidik_suspect');
            });
        }

        // ---------------------------------------------------------------------
        // 2. SURAT KETETAPAN PENGHENTIAN PENYELIDIKAN (Sket Henti Lidik) TABLES
        // ---------------------------------------------------------------------

        // 2.1 Main document table
        if (!Schema::hasTable('doc.surat_ketetapan_penghentian_penyelidikan_documents')) {
            Schema::create('doc.surat_ketetapan_penghentian_penyelidikan_documents', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('accident_id')->nullable();
                $table->string('document_number')->nullable();
                $table->date('document_date')->nullable();
                $table->date('effective_date')->nullable();
                $table->string('document_location')->nullable();
                $table->string('document_classification_id')->nullable();

                // References to related documents
                $table->uuid('surat_perintah_penyelidikan_document_id')->nullable();
                $table->uuid('surat_perintah_penghentian_penyelidikan_document_id')->nullable();
                $table->uuid('laporan_hasil_gelar_perkara_document_id')->nullable();

                // Details & reasons
                $table->json('kode_alasan')->nullable();
                $table->text('alasan_penghentian')->nullable();
                $table->text('dugaan_tindak_pidana')->nullable();
                $table->text('pasal_list')->nullable();
                $table->text('menimbang')->nullable();
                $table->text('mengingat')->nullable();
                $table->text('memperhatikan')->nullable();
                $table->text('memutuskan')->nullable();
                $table->text('menetapkan')->nullable();
                $table->json('carbon_copies')->nullable();

                // Status & audit metadata
                $table->boolean('is_active')->default(true);
                $table->string('status_id')->nullable();
                $table->string('document_category_id')->nullable();
                $table->json('messages')->nullable();
                $table->json('ip_addresses')->nullable();
                $table->json('timestamps')->nullable();
                $table->dateTime('submitted_at')->nullable();
                $table->dateTime('approved_at')->nullable();
                $table->dateTime('released_at')->nullable();
                $table->unsignedBigInteger('created_by_user_id')->nullable();
                $table->unsignedBigInteger('updated_by_user_id')->nullable();
                $table->unsignedBigInteger('deleted_by_user_id')->nullable();
                $table->timestamps();
                $table->softDeletes();
                $table->dateTime('last_synced_at')->nullable();

                // Foreign keys
                $table->foreign('accident_id', 'fk_sket_henti_lidik_docs_accident_id')
                    ->references('id')->on('public.accidents')
                    ->onDelete('set null')->onUpdate('cascade');

                $table->foreign('document_classification_id', 'fk_sket_henti_lidik_docs_class_id')
                    ->references('id')->on('lib.document_classifications')
                    ->onDelete('restrict')->onUpdate('cascade');

                $table->foreign('surat_perintah_penyelidikan_document_id', 'fk_sket_henti_lidik_docs_sprinlidik_id')
                    ->references('id')->on('doc.surat_perintah_penyelidikan_documents')
                    ->onDelete('set null')->onUpdate('cascade');

                $table->foreign('surat_perintah_penghentian_penyelidikan_document_id', 'fk_sket_henti_lidik_docs_sph_id')
                    ->references('id')->on('doc.surat_perintah_penghentian_penyelidikan_documents')
                    ->onDelete('set null')->onUpdate('cascade');

                $table->foreign('laporan_hasil_gelar_perkara_document_id', 'fk_sket_henti_lidik_docs_lhgp_id')
                    ->references('id')->on('doc.laporan_hasil_gelar_perkara_documents')
                    ->onDelete('set null')->onUpdate('cascade');
            });
        }

        // 2.2 Document officers table
        if (!Schema::hasTable('doc.surat_ketetapan_penghentian_penyelidikan_document_officers')) {
            Schema::create('doc.surat_ketetapan_penghentian_penyelidikan_document_officers', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->uuid('surat_ketetapan_penghentian_penyelidikan_document_id');

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
                $table->enum('class', ['MEMBER', 'LEADER', 'SIGNATORY'])->default('SIGNATORY')->nullable();
                $table->enum('flag', ['INTERNAL', 'MOVED', 'EXTERNAL'])->default('INTERNAL')->nullable();
                $table->enum('insert_method', ['MANUAL', 'IMPORT'])->default('IMPORT')->nullable();

                $table->timestamps();

                $table->foreign('surat_ketetapan_penghentian_penyelidikan_document_id', 'fk_sket_henti_lidik_off_doc_id')
                    ->references('id')->on('doc.surat_ketetapan_penghentian_penyelidikan_documents')
                    ->onDelete('cascade')->onUpdate('cascade');

                $table->foreign('police_id', 'fk_sket_henti_lidik_off_police_id')
                    ->references('id')->on('lib.polices')
                    ->onDelete('restrict')->onUpdate('cascade');
            });
        }

        // 2.3 Document attachments table
        if (!Schema::hasTable('doc.surat_ketetapan_penghentian_penyelidikan_document_attachments')) {
            Schema::create('doc.surat_ketetapan_penghentian_penyelidikan_document_attachments', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('surat_ketetapan_penghentian_penyelidikan_document_id');

                $table->string('name')->nullable();
                $table->string('file_path')->nullable();
                $table->string('file_extension')->nullable();
                $table->bigInteger('file_size')->nullable();
                $table->boolean('is_active')->default(true);

                $table->timestamps();
                $table->softDeletes();

                $table->foreign('surat_ketetapan_penghentian_penyelidikan_document_id', 'fk_sket_henti_lidik_att_doc_id')
                    ->references('id')->on('doc.surat_ketetapan_penghentian_penyelidikan_documents')
                    ->onDelete('cascade')->onUpdate('cascade');
            });
        }

        // 2.4 Suspect pivot table
        if (!Schema::hasTable('pivot.surat_ketetapan_penghentian_penyelidikan_document_suspect')) {
            Schema::create('pivot.surat_ketetapan_penghentian_penyelidikan_document_suspect', function (Blueprint $table) {
                $table->uuid('surat_ketetapan_penghentian_penyelidikan_document_id');
                $table->uuid('suspect_id');
                $table->timestamps();

                $table->foreign('surat_ketetapan_penghentian_penyelidikan_document_id', 'fk_sket_henti_lidik_piv_doc_id')
                    ->references('id')->on('doc.surat_ketetapan_penghentian_penyelidikan_documents')
                    ->onDelete('cascade')->onUpdate('cascade');

                $table->foreign('suspect_id', 'fk_sket_henti_lidik_piv_susp_id')
                    ->references('id')->on('public.suspects')
                    ->onDelete('cascade')->onUpdate('cascade');

                $table->primary(['surat_ketetapan_penghentian_penyelidikan_document_id', 'suspect_id'], 'pk_sket_henti_lidik_suspect');
            });
        }

        // ---------------------------------------------------------------------
        // 3. UPDATE / INSERT INTO LIB.DOCUMENT_CATEGORIES
        // ---------------------------------------------------------------------

        // 0113: Surat Perintah Penghentian Penyelidikan
        DB::table('lib.document_categories')->updateOrInsert(
            ['id' => '0113'],
            [
                'code'                 => 'DCT-0113',
                'parent_id'            => '01',
                'name'                 => 'SURAT PERINTAH PENGHENTIAN PENYELIDIKAN',
                'category'             => 'TYPE',
                'route'                => 'doc.surat-perintah-penghentian-penyelidikan-document.create',
                'base_route'           => 'doc.surat-perintah-penghentian-penyelidikan-document',
                'model_class'          => 'App\Models\Doc\SuratPerintahPenghentianPenyelidikanDocument\SuratPerintahPenghentianPenyelidikanDocument::class',
                'alt_code'             => 'surat-perintah-penghentian-penyelidikan-document',
                'is_active'            => true,
                'is_digital_signature' => false,
                'sort'                 => 12,
                'is_case_finish'       => false,
                'created_at'           => now(),
                'updated_at'           => now(),
            ]
        );

        // 0112: Surat Ketetapan Penghentian Penyelidikan
        DB::table('lib.document_categories')->updateOrInsert(
            ['id' => '0112'],
            [
                'code'                 => 'DCT-0112',
                'parent_id'            => '01',
                'name'                 => 'SURAT KETETAPAN PENGHENTIAN PENYELIDIKAN',
                'category'             => 'TYPE',
                'route'                => 'doc.surat-ketetapan-penghentian-penyelidikan-document.create',
                'base_route'           => 'doc.surat-ketetapan-penghentian-penyelidikan-document',
                'model_class'          => 'App\Models\Doc\SuratKetetapanPenghentianPenyelidikanDocument\SuratKetetapanPenghentianPenyelidikanDocument::class',
                'alt_code'             => 'surat-ketetapan-penghentian-penyelidikan-document',
                'is_active'            => true,
                'is_digital_signature' => false,
                'sort'                 => 13,
                'is_case_finish'       => false,
                'created_at'           => now(),
                'updated_at'           => now(),
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Rollback categories
        DB::table('lib.document_categories')
            ->where('id', '0112')
            ->update([
                'route'                => null,
                'base_route'           => null,
                'model_class'          => null,
                'is_active'            => false,
                'is_digital_signature' => false,
                'sort'                 => 0,
            ]);

        DB::table('lib.document_categories')
            ->where('id', '0113')
            ->delete();

        // 2. Drop Sket Henti Lidik tables
        if (Schema::hasTable('pivot.surat_ketetapan_penghentian_penyelidikan_document_suspect')) {
            Schema::dropIfExists('pivot.surat_ketetapan_penghentian_penyelidikan_document_suspect');
        }

        if (Schema::hasTable('doc.surat_ketetapan_penghentian_penyelidikan_document_attachments')) {
            Schema::dropIfExists('doc.surat_ketetapan_penghentian_penyelidikan_document_attachments');
        }

        if (Schema::hasTable('doc.surat_ketetapan_penghentian_penyelidikan_document_officers')) {
            Schema::dropIfExists('doc.surat_ketetapan_penghentian_penyelidikan_document_officers');
        }

        if (Schema::hasTable('doc.surat_ketetapan_penghentian_penyelidikan_documents')) {
            Schema::dropIfExists('doc.surat_ketetapan_penghentian_penyelidikan_documents');
        }

        // 3. Drop Sprint Henti Lidik tables
        if (Schema::hasTable('pivot.surat_perintah_penghentian_penyelidikan_document_suspect')) {
            Schema::dropIfExists('pivot.surat_perintah_penghentian_penyelidikan_document_suspect');
        }

        if (Schema::hasTable('doc.surat_perintah_penghentian_penyelidikan_document_attachments')) {
            Schema::dropIfExists('doc.surat_perintah_penghentian_penyelidikan_document_attachments');
        }

        if (Schema::hasTable('doc.surat_perintah_penghentian_penyelidikan_document_officers')) {
            Schema::dropIfExists('doc.surat_perintah_penghentian_penyelidikan_document_officers');
        }

        if (Schema::hasTable('doc.surat_perintah_penghentian_penyelidikan_documents')) {
            Schema::dropIfExists('doc.surat_perintah_penghentian_penyelidikan_documents');
        }
    }
};
