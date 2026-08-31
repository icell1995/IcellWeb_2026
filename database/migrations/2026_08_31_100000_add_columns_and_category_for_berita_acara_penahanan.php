<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('berita_acara_penahanan', function (Blueprint $table) {
            if (!Schema::hasColumn('berita_acara_penahanan', 'status_id')) {
                $table->string('status_id', 20)->default('2')->nullable();
            }
            if (!Schema::hasColumn('berita_acara_penahanan', 'document_date')) {
                $table->date('document_date')->nullable();
            }
            if (!Schema::hasColumn('berita_acara_penahanan', 'properties')) {
                $table->jsonb('properties')->nullable();
            }
        });

        if (!Schema::hasTable('doc.berita_acara_penahanan_document_attachments')) {
            Schema::create('doc.berita_acara_penahanan_document_attachments', function (Blueprint $table) {
                $table->id();
                $table->uuid('berita_acara_penahanan_document_id');
                $table->string('name');
                $table->string('original_name')->nullable();
                $table->string('extension', 50)->nullable();
                $table->string('mimetype', 100)->nullable();
                $table->string('size', 100)->nullable();
                $table->string('path')->nullable();
                $table->string('type', 50)->default('DOCUMENT')->nullable();
                $table->string('flag', 100)->nullable();
                $table->timestamps();
            });
        }

        // Ensure category 0605 exists and is properly configured
        DB::table('lib.document_categories')->updateOrInsert(
            ['id' => '0605'],
            [
                'parent_id' => '06',
                'code' => 'DCT-0605',
                'name' => 'BERITA ACARA PENAHANAN',
                'category' => 'TYPE',
                'sort' => 0,
                'route' => 'doc.berita-acara-penahanan-document.create',
                'base_route' => 'doc.berita-acara-penahanan-document',
                'model_class' => 'App\Models\Doc\BeritaAcaraPenahananDocument\BeritaAcaraPenahananDocument',
                'alt_code' => 'berita-acara-penahanan-document',
                'is_active' => true,
                'is_digital_signature' => false,
                'updated_at' => now(),
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('berita_acara_penahanan', function (Blueprint $table) {
            if (Schema::hasColumn('berita_acara_penahanan', 'status_id')) {
                $table->dropColumn('status_id');
            }
            if (Schema::hasColumn('berita_acara_penahanan', 'document_date')) {
                $table->dropColumn('document_date');
            }
            if (Schema::hasColumn('berita_acara_penahanan', 'properties')) {
                $table->dropColumn('properties');
            }
        });
    }
};
