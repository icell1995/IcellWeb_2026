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
        Schema::create('return_documents', function (Blueprint $table) {
            $table->id();
            $table->uuid('accident_id');
            $table->string('documentable_type')->nullable();
            $table->uuid('documentable_id')->nullable();
            $table->string('document_category_id', 10);
            $table->bigInteger('returned_by_id');
            $table->string('returned_by_name', 255);
            $table->text('returned_reason');
            $table->timestamp('returned_at')->useCurrent();
            $table->timestamps();

            $table->foreign('accident_id')
                ->references('id')->on('public.accidents')
                ->onDelete('cascade');

            $table->foreign('document_category_id')
                ->references('id')->on('lib.document_categories')
                ->onDelete('restrict');

            $table->foreign('returned_by_id')
                ->references('id')->on('public.users')
                ->onDelete('restrict');

            $table->index(['accident_id']);
            $table->index(['documentable_type', 'documentable_id']);
            $table->index('document_category_id');
            $table->index('returned_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('return_documents');
    }
};
