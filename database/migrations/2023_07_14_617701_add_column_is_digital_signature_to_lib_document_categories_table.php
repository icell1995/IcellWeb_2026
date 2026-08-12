<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnIsDigitalSignatureToLibDocumentCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lib.document_categories', function (Blueprint $table) {
            $table->boolean('is_digital_signature')->nullable();
            
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
            $table->dropColumn('is_digital_signature');
        });
    }
}
