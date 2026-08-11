<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('history.document_api_sync_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('document_category_id');
            $table->uuid('document_id');
            $table->string('document_type')->nullable();
            $table->uuid('accident_id');
            $table->string('ip_address')->nullable();

            $table->timestamps();

            $table->foreign('document_category_id', 'fk_doc_api_sync_histories_document_category_id')
                ->references('id')
                ->on('lib.document_categories')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('accident_id', 'fk_doc_api_sync_histories_accident_id')
                ->references('id')
                ->on('public.accidents')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //drop foreign key
        Schema::table('history.document_api_sync_histories', function (Blueprint $table) {
            $table->dropForeign('fk_doc_api_sync_histories_document_category_id');
            $table->dropForeign('fk_doc_api_sync_histories_accident_id');
        });
        Schema::dropIfExists('history.document_api_sync_histories');
    }
};
