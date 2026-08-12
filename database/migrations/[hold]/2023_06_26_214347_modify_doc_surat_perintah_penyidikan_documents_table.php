<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ModifyDocSuratPerintahPenyidikanDocumentsTable extends Migration
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
            Schema::table('doc.surat_perintah_penyidikan_documents', function (Blueprint $table) {
                $table->renameColumn('letter_number', 'document_number');
                $table->renameColumn('issued_date', 'document_date');
                $table->renameColumn('integrated_at', 'last_synced_at');
                $table->dropColumn('location_created');
                $table->dropColumn('attachment');
                $table->dropColumn('is_integrated');

                $table->boolean('is_renewal')->default(false)->after('endDate');
                $table->uuid('renewal_reference_document_id')->nullable()->after('is_renewal');
                $table->string('renewal_reference_document_number')->nullable()->after('renewal_reference_document_id');
                $table->string('case_classification')->nullable()->after('letter_date');
                $table->boolean('is_active')->default(true)->after('id');
                $table->softDeletes()->after('updated_at');
                $table->string('deleted_by')->nullable()->after('deleted_at');

                $table->dateTime('end_date')->change();

                $table->dropForeign('investigation_order_letters_accident_id_foreign');
                $table->foreign('accident_id', 'fk_splidilk_docs_accident_id')->references('id')->on('public.accidents')->onDelete('set null')->onUpdate('cascade');
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
            Schema::table('doc.surat_perintah_penyidikan_documents', function (Blueprint $table) {
                $table->renameColumn('document_number', 'letter_number');
                $table->renameColumn('document_date', 'issued_date');
                $table->renameColumn('last_synced_at', 'integrated_at');
                $table->string('location_created')->nullable()->after('issued_date');
                $table->string('attachment')->nullable()->after('location_created');
                $table->boolean('is_integrated')->default(false)->after('attachment');

                $table->dropColumn('is_renewal');
                $table->dropColumn('renewal_reference_document_id');
                $table->dropColumn('renewal_reference_document_number');
                $table->dropColumn('case_classification');
                $table->dropColumn('is_active');
                $table->dropColumn('deleted_at');
                $table->dropColumn('deleted_by');

                $table->date('end_date')->change();
                
                $table->dropForeign('fk_splidilk_docs_accident_id');
                $table->foreign('accident_id', 'investigation_order_letters_accident_id_foreign')->references('id')->on('public.accidents')->onDelete('set null')->onUpdate('cascade');
            });

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
