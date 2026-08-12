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
        Schema::create('doc.document_requirements', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('document_category_id');
            $table->string('required_document_category_id');
            $table->string('required_status_id')->nullable();

            $table->boolean('is_required')->default(false);
            $table->boolean('is_active')->default(true);

            $table->bigInteger('sort')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('document_category_id', 'fk_document_requirements_document_category_id')
                ->references('id')
                ->on('lib.document_categories')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreign('required_document_category_id', 'fk_document_requirements_required_document_category_id')
                ->references('id')
                ->on('lib.document_categories')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreign('required_status_id', 'fk_document_requirements_required_status_id')
                ->references('id')
                ->on('opt.statuses')
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //drop foreign key constraints
        Schema::table('doc.document_requirements', function (Blueprint $table) {
            $table->dropForeign('fk_document_requirements_document_category_id');
            $table->dropForeign('fk_document_requirements_required_document_category_id');
            $table->dropForeign('fk_document_requirements_required_status_id');
        });
        Schema::dropIfExists('doc.document_requirements');
    }
};
