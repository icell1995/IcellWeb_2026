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
        Schema::create('lib.document_category_status_logs', function (Blueprint $table) {
            $table->id();
            $table->string('document_category_id');
            $table->boolean('previous_status');
            $table->boolean('new_status');
            $table->text('reason')->nullable();
            $table->unsignedBigInteger('changed_by')->nullable();
            $table->timestamps();

            $table->foreign('document_category_id', 'fk_doc_cat_status_logs_cat_id')
                ->references('id')
                ->on('lib.document_categories')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('changed_by', 'fk_doc_cat_status_logs_changed_by')
                ->references('id')
                ->on('public.users')
                ->onDelete('set null')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lib.document_category_status_logs');
    }
};
