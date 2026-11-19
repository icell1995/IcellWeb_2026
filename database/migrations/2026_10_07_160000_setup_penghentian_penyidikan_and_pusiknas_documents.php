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
        // 1. SURAT PERINTAH PENGHENTIAN PENYIDIKAN (Sprint Henti) TABLES
        // ---------------------------------------------------------------------

        // 1.1 Main document table
        if (!Schema::hasTable('doc.surat_perintah_penghentian_penyidikan_documents')) {
            Schema::create('doc.surat_perintah_penghentian_penyidikan_documents', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('accident_id')->nullable();
                $table->string('document_number')->nullable();
                $table->date('document_date')->nullable();
                $table->string('document_location')->nullable();
                $table->string('document_classification_id')->nullable();

                // References to related documents
                $table->uuid('surat_perintah_penyidikan_document_id')->nullable();
                $table->uuid('surat_pemberitahuan_dimulainya_penyidikan_document_id')->nullable();
                $table->uuid('surat_ketetapan_tentang_penetapan_tersangka_document_id')->nullable();
                $table->uuid('surat_ketetapan_penghentian_penyidikan_document_id')->nullable();
                $table->uuid('laporan_hasil_gelar_perkara_document_id')->nullable();

                // Details & reasons
                $table->json('kode_alasan')->nullable();
                $table->text('alasan_penghentian')->nullable();
                $table->text('dugaan_tindak_pidana')->nullable();
                $table->text('pasal_list')->nullable();

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
                $table->foreign('accident_id', 'fk_sprint_henti_docs_accident_id')
                    ->references('id')->on('public.accidents')
                    ->onDelete('set null')->onUpdate('cascade');

                $table->foreign('document_classification_id', 'fk_sprint_henti_docs_class_id')
                    ->references('id')->on('lib.document_classifications')
                    ->onDelete('restrict')->onUpdate('cascade');

                $table->foreign('surat_perintah_penyidikan_document_id', 'fk_sprint_henti_docs_sprindik_id')
                    ->references('id')->on('doc.surat_perintah_penyidikan_documents')
                    ->onDelete('set null')->onUpdate('cascade');

                $table->foreign('laporan_hasil_gelar_perkara_document_id', 'fk_sprint_henti_docs_lhgp_id')
                    ->references('id')->on('doc.laporan_hasil_gelar_perkara_documents')
                    ->onDelete('set null')->onUpdate('cascade');
            });
        }

        // 1.2 Document officers table
        if (!Schema::hasTable('doc.surat_perintah_penghentian_penyidikan_document_officers')) {
            Schema::create('doc.surat_perintah_penghentian_penyidikan_document_officers', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->uuid('surat_perintah_penghentian_penyidikan_document_id');

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

                $table->foreign('surat_perintah_penghentian_penyidikan_document_id', 'fk_sprint_henti_off_doc_id')
                    ->references('id')->on('doc.surat_perintah_penghentian_penyidikan_documents')
                    ->onDelete('cascade')->onUpdate('cascade');

                $table->foreign('police_id', 'fk_sprint_henti_off_police_id')
                    ->references('id')->on('lib.polices')
                    ->onDelete('restrict')->onUpdate('cascade');
            });
        }

        // 1.3 Document attachments table
        if (!Schema::hasTable('doc.surat_perintah_penghentian_penyidikan_document_attachments')) {
            Schema::create('doc.surat_perintah_penghentian_penyidikan_document_attachments', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('surat_perintah_penghentian_penyidikan_document_id');

                $table->string('name')->nullable();
                $table->string('file_path')->nullable();
                $table->string('file_extension')->nullable();
                $table->bigInteger('file_size')->nullable();
                $table->boolean('is_active')->default(true);

                $table->timestamps();
                $table->softDeletes();

                $table->foreign('surat_perintah_penghentian_penyidikan_document_id', 'fk_sprint_henti_att_doc_id')
                    ->references('id')->on('doc.surat_perintah_penghentian_penyidikan_documents')
                    ->onDelete('cascade')->onUpdate('cascade');
            });
        }

        // 1.4 Suspect pivot table
        if (!Schema::hasTable('pivot.surat_perintah_penghentian_penyidikan_document_suspect')) {
            Schema::create('pivot.surat_perintah_penghentian_penyidikan_document_suspect', function (Blueprint $table) {
                $table->uuid('surat_perintah_penghentian_penyidikan_document_id');
                $table->uuid('suspect_id');
                $table->timestamps();

                $table->foreign('surat_perintah_penghentian_penyidikan_document_id', 'fk_sprint_henti_piv_doc_id')
                    ->references('id')->on('doc.surat_perintah_penghentian_penyidikan_documents')
                    ->onDelete('cascade')->onUpdate('cascade');

                $table->foreign('suspect_id', 'fk_sprint_henti_piv_susp_id')
                    ->references('id')->on('public.suspects')
                    ->onDelete('cascade')->onUpdate('cascade');

                $table->primary(['surat_perintah_penghentian_penyidikan_document_id', 'suspect_id'], 'pk_sprint_henti_suspect');
            });
        }

        // ---------------------------------------------------------------------
        // 2. SURAT KETETAPAN PENGHENTIAN PENYIDIKAN (Sket Henti) TABLES
        // ---------------------------------------------------------------------

        // 2.1 Main document table
        if (!Schema::hasTable('doc.surat_ketetapan_penghentian_penyidikan_documents')) {
            Schema::create('doc.surat_ketetapan_penghentian_penyidikan_documents', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('accident_id')->nullable();
                $table->string('document_number')->nullable();
                $table->date('document_date')->nullable();
                $table->date('effective_date')->nullable();
                $table->string('document_location')->nullable();
                $table->string('document_classification_id')->nullable();

                // References to related documents
                $table->uuid('surat_perintah_penyidikan_document_id')->nullable();
                $table->uuid('surat_pemberitahuan_dimulainya_penyidikan_document_id')->nullable();
                $table->uuid('surat_ketetapan_tentang_penetapan_tersangka_document_id')->nullable();
                $table->uuid('surat_perintah_penghentian_penyidikan_document_id')->nullable();
                $table->uuid('laporan_hasil_gelar_perkara_document_id')->nullable();
                $table->string('prosecutor_id')->nullable();
                $table->string('court_id')->nullable();

                // Details & reasons
                $table->json('kode_alasan')->nullable();
                $table->text('alasan_penghentian')->nullable();
                $table->text('dugaan_tindak_pidana')->nullable();
                $table->text('pasal_list')->nullable();

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
                $table->foreign('accident_id', 'fk_sket_henti_docs_accident_id')
                    ->references('id')->on('public.accidents')
                    ->onDelete('set null')->onUpdate('cascade');

                $table->foreign('document_classification_id', 'fk_sket_henti_docs_class_id')
                    ->references('id')->on('lib.document_classifications')
                    ->onDelete('restrict')->onUpdate('cascade');

                $table->foreign('surat_perintah_penyidikan_document_id', 'fk_sket_henti_docs_sprindik_id')
                    ->references('id')->on('doc.surat_perintah_penyidikan_documents')
                    ->onDelete('set null')->onUpdate('cascade');

                $table->foreign('laporan_hasil_gelar_perkara_document_id', 'fk_sket_henti_docs_lhgp_id')
                    ->references('id')->on('doc.laporan_hasil_gelar_perkara_documents')
                    ->onDelete('set null')->onUpdate('cascade');

                $table->foreign('prosecutor_id', 'fk_sket_henti_docs_prosecutor_id')
                    ->references('id')->on('lib.prosecutors')
                    ->onDelete('restrict')->onUpdate('cascade');

                $table->foreign('court_id', 'fk_sket_henti_docs_court_id')
                    ->references('id')->on('lib.courts')
                    ->onDelete('restrict')->onUpdate('cascade');
            });
        }

        // 2.2 Document officers table
        if (!Schema::hasTable('doc.surat_ketetapan_penghentian_penyidikan_document_officers')) {
            Schema::create('doc.surat_ketetapan_penghentian_penyidikan_document_officers', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->uuid('surat_ketetapan_penghentian_penyidikan_document_id');

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

                $table->foreign('surat_ketetapan_penghentian_penyidikan_document_id', 'fk_sket_henti_off_doc_id')
                    ->references('id')->on('doc.surat_ketetapan_penghentian_penyidikan_documents')
                    ->onDelete('cascade')->onUpdate('cascade');

                $table->foreign('police_id', 'fk_sket_henti_off_police_id')
                    ->references('id')->on('lib.polices')
                    ->onDelete('restrict')->onUpdate('cascade');
            });
        }

        // 2.3 Document attachments table
        if (!Schema::hasTable('doc.surat_ketetapan_penghentian_penyidikan_document_attachments')) {
            Schema::create('doc.surat_ketetapan_penghentian_penyidikan_document_attachments', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('surat_ketetapan_penghentian_penyidikan_document_id');

                $table->string('name')->nullable();
                $table->string('file_path')->nullable();
                $table->string('file_extension')->nullable();
                $table->bigInteger('file_size')->nullable();
                $table->boolean('is_active')->default(true);

                $table->timestamps();
                $table->softDeletes();

                $table->foreign('surat_ketetapan_penghentian_penyidikan_document_id', 'fk_sket_henti_att_doc_id')
                    ->references('id')->on('doc.surat_ketetapan_penghentian_penyidikan_documents')
                    ->onDelete('cascade')->onUpdate('cascade');
            });
        }

        // 2.4 Suspect pivot table
        if (!Schema::hasTable('pivot.surat_ketetapan_penghentian_penyidikan_document_suspect')) {
            Schema::create('pivot.surat_ketetapan_penghentian_penyidikan_document_suspect', function (Blueprint $table) {
                $table->uuid('surat_ketetapan_penghentian_penyidikan_document_id');
                $table->uuid('suspect_id');
                $table->timestamps();

                $table->foreign('surat_ketetapan_penghentian_penyidikan_document_id', 'fk_sket_henti_piv_doc_id')
                    ->references('id')->on('doc.surat_ketetapan_penghentian_penyidikan_documents')
                    ->onDelete('cascade')->onUpdate('cascade');

                $table->foreign('suspect_id', 'fk_sket_henti_piv_susp_id')
                    ->references('id')->on('public.suspects')
                    ->onDelete('cascade')->onUpdate('cascade');

                $table->primary(['surat_ketetapan_penghentian_penyidikan_document_id', 'suspect_id'], 'pk_sket_henti_suspect');
            });
        }

        // ---------------------------------------------------------------------
        // 3. ALTER SURAT PEMBERITAHUAN PENGHENTIAN PENYIDIKAN (SP3) TABLE
        // ---------------------------------------------------------------------
        if (Schema::hasTable('doc.surat_pemberitahuan_penghentian_penyidikan_documents')) {
            Schema::table('doc.surat_pemberitahuan_penghentian_penyidikan_documents', function (Blueprint $table) {
                if (!Schema::hasColumn('doc.surat_pemberitahuan_penghentian_penyidikan_documents', 'timestamps')) {
                    $table->json('timestamps')->nullable()->after('ip_addresses');
                }
                if (!Schema::hasColumn('doc.surat_pemberitahuan_penghentian_penyidikan_documents', 'submitted_at')) {
                    $table->dateTime('submitted_at')->nullable()->after('timestamps');
                }
                if (!Schema::hasColumn('doc.surat_pemberitahuan_penghentian_penyidikan_documents', 'approved_at')) {
                    $table->dateTime('approved_at')->nullable()->after('submitted_at');
                }
                if (!Schema::hasColumn('doc.surat_pemberitahuan_penghentian_penyidikan_documents', 'released_at')) {
                    $table->dateTime('released_at')->nullable()->after('approved_at');
                }
                if (!Schema::hasColumn('doc.surat_pemberitahuan_penghentian_penyidikan_documents', 'prosecutor_id')) {
                    $table->string('prosecutor_id')->nullable();
                    $table->foreign('prosecutor_id', 'fk_sp3_docs_prosecutor_id')
                        ->references('id')->on('lib.prosecutors')
                        ->onDelete('restrict')->onUpdate('cascade');
                }
                if (!Schema::hasColumn('doc.surat_pemberitahuan_penghentian_penyidikan_documents', 'court_id')) {
                    $table->string('court_id')->nullable();
                    $table->foreign('court_id', 'fk_sp3_docs_court_id')
                        ->references('id')->on('lib.courts')
                        ->onDelete('restrict')->onUpdate('cascade');
                }
            });
        }

        // ---------------------------------------------------------------------
        // 4. UPDATE LIB.DOCUMENT_CATEGORIES
        // ---------------------------------------------------------------------
        // 0204: SPDP Pusiknas
        DB::table('lib.document_categories')
            ->where('id', '0204')
            ->update([
                'route'       => 'doc.surat-pemberitahuan-dimulainya-penyidikan-pusiknas-document.create',
                'base_route'  => 'doc.surat-pemberitahuan-dimulainya-penyidikan-pusiknas-document',
                'model_class' => 'App\Models\Doc\SuratPemberitahuanDimulainyaPenyidikanPusiknasDocument\SuratPemberitahuanDimulainyaPenyidikanPusiknasDocument::class',
                'alt_code'    => 'surat-pemberitahuan-dimulainya-penyidikan-pusiknas-document',
                'sort'        => 2,
                'updated_at'  => now(),
            ]);

        // 0205: Sprint Penghentian Penyidikan
        DB::table('lib.document_categories')
            ->where('id', '0205')
            ->update([
                'route'                => 'doc.surat-perintah-penghentian-penyidikan-document.create',
                'base_route'           => 'doc.surat-perintah-penghentian-penyidikan-document',
                'model_class'          => 'App\Models\Doc\SuratPerintahPenghentianPenyidikanDocument\SuratPerintahPenghentianPenyidikanDocument::class',
                'alt_code'             => 'surat-perintah-penghentian-penyidikan-document',
                'is_active'            => true,
                'is_digital_signature' => false,
                'sort'                 => 4,
                'updated_at'           => now(),
            ]);

        // 0206: Sket Penghentian Penyidikan
        DB::table('lib.document_categories')
            ->where('id', '0206')
            ->update([
                'route'                => 'doc.surat-ketetapan-penghentian-penyidikan-document.create',
                'base_route'           => 'doc.surat-ketetapan-penghentian-penyidikan-document',
                'model_class'          => 'App\Models\Doc\SuratKetetapanPenghentianPenyidikanDocument\SuratKetetapanPenghentianPenyidikanDocument::class',
                'alt_code'             => 'surat-ketetapan-penghentian-penyidikan-document',
                'is_active'            => true,
                'is_digital_signature' => false,
                'sort'                 => 5,
                'updated_at'           => now(),
            ]);

        // 0216: SP3 (Surat Pemberitahuan Penghentian Penyidikan)
        DB::table('lib.document_categories')
            ->where('id', '0216')
            ->update([
                'is_digital_signature' => false,
                'sort'                 => 6,
                'updated_at'           => now(),
            ]);

        // Sort orders for preceding documents
        DB::table('lib.document_categories')->where('id', '0201')->update(['sort' => 1, 'updated_at' => now()]);
        DB::table('lib.document_categories')->where('id', '0215')->update(['sort' => 3, 'updated_at' => now()]);

        // 0806: Tahap 1
        DB::table('lib.document_categories')
            ->where('id', '0806')
            ->update([
                'base_route'  => 'doc.tahap-1-document',
                'model_class' => 'App\Models\Doc\Tahap1Document\Tahap1Document::class',
                'updated_at'  => now(),
            ]);

        // 0807: Tahap 2
        DB::table('lib.document_categories')
            ->where('id', '0807')
            ->update([
                'base_route'  => 'doc.tahap-2-document',
                'model_class' => 'App\Models\Doc\Tahap2Document\Tahap2Document::class',
                'updated_at'  => now(),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Rollback categories
        DB::table('lib.document_categories')
            ->where('id', '0204')
            ->update([
                'route'       => 'doc.surat-pemberitahuan-dimulainya-penyidikan-document.create',
                'base_route'  => 'doc.surat-pemberitahuan-dimulainya-penyidikan-document',
                'model_class' => 'App\Models\Doc\SuratPemberitahuanDimulainyaPenyidikanDocument\SuratPemberitahuanDimulainyaPenyidikanDocument::class',
                'alt_code'    => 'surat-pemberitahuan-dimulainya-penyidikan-document',
                'sort'        => 0,
            ]);

        DB::table('lib.document_categories')
            ->where('id', '0205')
            ->update([
                'route'                => null,
                'base_route'           => null,
                'model_class'          => null,
                'is_digital_signature' => true,
                'sort'                 => 0,
            ]);

        DB::table('lib.document_categories')
            ->where('id', '0206')
            ->update([
                'route'                => null,
                'base_route'           => null,
                'model_class'          => null,
                'is_digital_signature' => true,
                'sort'                 => 0,
            ]);

        DB::table('lib.document_categories')
            ->where('id', '0216')
            ->update([
                'is_digital_signature' => true,
                'sort'                 => 0,
            ]);

        DB::table('lib.document_categories')->whereIn('id', ['0201', '0215'])->update(['sort' => 0]);
        DB::table('lib.document_categories')->whereIn('id', ['0806', '0807'])->update(['base_route' => null, 'model_class' => null]);

        // 2. Rollback SP3 alters
        if (Schema::hasTable('doc.surat_pemberitahuan_penghentian_penyidikan_documents')) {
            Schema::table('doc.surat_pemberitahuan_penghentian_penyidikan_documents', function (Blueprint $table) {
                if (Schema::hasColumn('doc.surat_pemberitahuan_penghentian_penyidikan_documents', 'court_id')) {
                    $table->dropForeign('fk_sp3_docs_court_id');
                    $table->dropColumn('court_id');
                }
                if (Schema::hasColumn('doc.surat_pemberitahuan_penghentian_penyidikan_documents', 'prosecutor_id')) {
                    $table->dropForeign('fk_sp3_docs_prosecutor_id');
                    $table->dropColumn('prosecutor_id');
                }
                $cols = ['timestamps', 'submitted_at', 'approved_at', 'released_at'];
                foreach ($cols as $col) {
                    if (Schema::hasColumn('doc.surat_pemberitahuan_penghentian_penyidikan_documents', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }

        // 3. Drop Sket Henti tables
        if (Schema::hasTable('pivot.surat_ketetapan_penghentian_penyidikan_document_suspect')) {
            Schema::table('pivot.surat_ketetapan_penghentian_penyidikan_document_suspect', function (Blueprint $table) {
                $table->dropForeign('fk_sket_henti_piv_doc_id');
                $table->dropForeign('fk_sket_henti_piv_susp_id');
            });
            Schema::dropIfExists('pivot.surat_ketetapan_penghentian_penyidikan_document_suspect');
        }

        if (Schema::hasTable('doc.surat_ketetapan_penghentian_penyidikan_document_attachments')) {
            Schema::table('doc.surat_ketetapan_penghentian_penyidikan_document_attachments', function (Blueprint $table) {
                $table->dropForeign('fk_sket_henti_att_doc_id');
            });
            Schema::dropIfExists('doc.surat_ketetapan_penghentian_penyidikan_document_attachments');
        }

        if (Schema::hasTable('doc.surat_ketetapan_penghentian_penyidikan_document_officers')) {
            Schema::table('doc.surat_ketetapan_penghentian_penyidikan_document_officers', function (Blueprint $table) {
                $table->dropForeign('fk_sket_henti_off_doc_id');
                $table->dropForeign('fk_sket_henti_off_police_id');
            });
            Schema::dropIfExists('doc.surat_ketetapan_penghentian_penyidikan_document_officers');
        }

        if (Schema::hasTable('doc.surat_ketetapan_penghentian_penyidikan_documents')) {
            Schema::table('doc.surat_ketetapan_penghentian_penyidikan_documents', function (Blueprint $table) {
                $table->dropForeign('fk_sket_henti_docs_accident_id');
                $table->dropForeign('fk_sket_henti_docs_class_id');
                $table->dropForeign('fk_sket_henti_docs_sprindik_id');
                $table->dropForeign('fk_sket_henti_docs_lhgp_id');
                $table->dropForeign('fk_sket_henti_docs_prosecutor_id');
                $table->dropForeign('fk_sket_henti_docs_court_id');
            });
            Schema::dropIfExists('doc.surat_ketetapan_penghentian_penyidikan_documents');
        }

        // 4. Drop Sprint Henti tables
        if (Schema::hasTable('pivot.surat_perintah_penghentian_penyidikan_document_suspect')) {
            Schema::table('pivot.surat_perintah_penghentian_penyidikan_document_suspect', function (Blueprint $table) {
                $table->dropForeign('fk_sprint_henti_piv_doc_id');
                $table->dropForeign('fk_sprint_henti_piv_susp_id');
            });
            Schema::dropIfExists('pivot.surat_perintah_penghentian_penyidikan_document_suspect');
        }

        if (Schema::hasTable('doc.surat_perintah_penghentian_penyidikan_document_attachments')) {
            Schema::table('doc.surat_perintah_penghentian_penyidikan_document_attachments', function (Blueprint $table) {
                $table->dropForeign('fk_sprint_henti_att_doc_id');
            });
            Schema::dropIfExists('doc.surat_perintah_penghentian_penyidikan_document_attachments');
        }

        if (Schema::hasTable('doc.surat_perintah_penghentian_penyidikan_document_officers')) {
            Schema::table('doc.surat_perintah_penghentian_penyidikan_document_officers', function (Blueprint $table) {
                $table->dropForeign('fk_sprint_henti_off_doc_id');
                $table->dropForeign('fk_sprint_henti_off_police_id');
            });
            Schema::dropIfExists('doc.surat_perintah_penghentian_penyidikan_document_officers');
        }

        if (Schema::hasTable('doc.surat_perintah_penghentian_penyidikan_documents')) {
            Schema::table('doc.surat_perintah_penghentian_penyidikan_documents', function (Blueprint $table) {
                $table->dropForeign('fk_sprint_henti_docs_accident_id');
                $table->dropForeign('fk_sprint_henti_docs_class_id');
                $table->dropForeign('fk_sprint_henti_docs_sprindik_id');
                $table->dropForeign('fk_sprint_henti_docs_lhgp_id');
            });
            Schema::dropIfExists('doc.surat_perintah_penghentian_penyidikan_documents');
        }
    }
};
