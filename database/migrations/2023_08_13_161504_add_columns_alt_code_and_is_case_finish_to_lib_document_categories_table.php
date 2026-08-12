<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsAltCodeAndIsCaseFinishToLibDocumentCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lib.document_categories', function (Blueprint $table) {
            $table->string('alt_code')->nullable();
            $table->boolean('is_case_finish')->nullable()->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lib.document_categories', function (Blueprint $table) {
            $table->dropColumn('alt_code');
            $table->dropColumn('is_case_finish');
        });
    }
}
