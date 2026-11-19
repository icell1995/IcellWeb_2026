<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        DB::beginTransaction();
        try {
            Schema::table('doc.surat_permintaan_izin_penyitaan_document_laws', function (Blueprint $table) {
                $table->string('crime_type_id')->nullable()->after('surat_permintaan_izin_penyitaan_document_id');
                $table->string('crime_class_id')->nullable()->after('crime_type_id');

                $table->foreign('crime_type_id', 'fk_spizinsita_laws_crime_type_id')
                    ->references('id')->on('lib.crime_types')
                    ->onDelete('restrict')->onUpdate('cascade');

                $table->foreign('crime_class_id', 'fk_spizinsita_laws_crime_class_id')
                    ->references('id')->on('lib.crime_classes')
                    ->onDelete('restrict')->onUpdate('cascade');
            });

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        DB::beginTransaction();
        try {
            Schema::table('doc.surat_permintaan_izin_penyitaan_document_laws', function (Blueprint $table) {
                $table->dropForeign('fk_spizinsita_laws_crime_type_id');
                $table->dropForeign('fk_spizinsita_laws_crime_class_id');
                $table->dropColumn(['crime_type_id', 'crime_class_id']);
            });

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
};
