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
        Schema::create('log_case_document_validations', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->uuid('accident_id')->nullable();
            $table->uuid('document_id')->nullable();
            $table->string('document_category_id')->nullable();

            $table->bigInteger('approved_by_id')->nullable();
            $table->bigInteger('rejected_by_id')->nullable();
            
            $table->string('updated_status_id')->nullable();
            
            $table->string('accident_number');
            $table->string('document_number');
            $table->string('document_category_name');
            $table->string('model_class')->nullable();
            
            $table->string('updated_status_name');
            
            $table->text('reject_reason')->nullable();

            $table->string('approved_by_name')->nullable();
            $table->string('rejected_by_name')->nullable();

            $table->dateTime('approved_at')->nullable();
            $table->dateTime('rejected_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('accident_id', 'fk_log_case_document_validations_accident_id')
                ->references('id')
                ->on('public.accidents')
                ->onUpdate('cascade')
                ->onDelete('set null');

            $table->foreign('document_category_id', 'fk_log_case_document_validations_document_category_id')
                ->references('id')
                ->on('lib.document_categories')
                ->onUpdate('cascade')
                ->onDelete('set null');

            $table->foreign('approved_by_id', 'fk_log_case_document_validations_approved_by_id')
                ->references('id')
                ->on('public.users')
                ->onUpdate('cascade')
                ->onDelete('set null');

            $table->foreign('rejected_by_id', 'fk_log_case_document_validations_rejected_by_id')
                ->references('id')
                ->on('public.users')
                ->onUpdate('cascade')
                ->onDelete('set null');

            $table->foreign('updated_status_id', 'fk_log_case_document_validations_updated_status_id')
                ->references('id')
                ->on('opt.statuses')
                ->onUpdate('cascade')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('log_case_document_validations', function (Blueprint $table) {
            $table->dropForeign('fk_log_case_document_validations_accident_id');
            $table->dropForeign('fk_log_case_document_validations_document_category_id');
            $table->dropForeign('fk_log_case_document_validations_approved_by_id');
            $table->dropForeign('fk_log_case_document_validations_rejected_by_id');
            $table->dropForeign('fk_log_case_document_validations_updated_status_id');
        });
        Schema::dropIfExists('log_case_document_validations');
    }
};
