<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTembusanToSuratPencabutanDpbDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('doc.surat_pencabutan_dpb_documents', function (Blueprint $table) {
            if (!Schema::hasColumn('doc.surat_pencabutan_dpb_documents', 'tembusan')) {
                $table->jsonb('tembusan')->nullable()->after('keterangan_lain');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('doc.surat_pencabutan_dpb_documents', function (Blueprint $table) {
            if (Schema::hasColumn('doc.surat_pencabutan_dpb_documents', 'tembusan')) {
                $table->dropColumn('tembusan');
            }
        });
    }
}
