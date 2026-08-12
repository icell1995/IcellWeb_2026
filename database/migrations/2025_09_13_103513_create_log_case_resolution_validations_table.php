<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('log_case_resolution_validations', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Referensi LP (accident)
            $table->uuid('accident_id')->nullable()->index();

            // Jenis SELRA (snapshot)
            $table->string('type_id')->nullable();
            $table->string('type_name')->nullable();

            // Actor
            $table->bigInteger('approved_by_id')->nullable()->index();
            $table->bigInteger('rejected_by_id')->nullable()->index();

            // Status perubahan (mengikuti pola updated_status_*)
            $table->string('updated_status_id')->nullable()->index();
            $table->string('updated_status_name');

            // Snapshot identitas LP & dokumen
            $table->string('accident_number')->nullable();   // = no_lp
            $table->string('document_number')->nullable();   // nomor ketetapan SELRA
            $table->string('model_class')->nullable();       // konsisten dengan tabel log sebelumnya

            // Alasan & nama petugas (snapshot)
            $table->text('reject_reason')->nullable();
            $table->string('approved_by_name')->nullable();
            $table->string('rejected_by_name')->nullable();

            // Timestamp aksi
            $table->dateTime('approved_at')->nullable();
            $table->dateTime('rejected_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // ===== Foreign Keys: gunakan penamaan penuh (tidak disingkat) =====
            $table->foreign('accident_id', 'fk_log_case_resolution_validations_accident_id')
                ->references('id')->on('public.accidents')
                ->onUpdate('cascade')->onDelete('set null');

            $table->foreign('approved_by_id', 'fk_log_case_resolution_validations_approved_by_id')
                ->references('id')->on('public.users')
                ->onUpdate('cascade')->onDelete('set null');

            $table->foreign('rejected_by_id', 'fk_log_case_resolution_validations_rejected_by_id')
                ->references('id')->on('public.users')
                ->onUpdate('cascade')->onDelete('set null');

            $table->foreign('updated_status_id', 'fk_log_case_resolution_validations_updated_status_id')
                ->references('id')->on('opt.statuses')
                ->onUpdate('cascade')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('log_case_resolution_validations', function (Blueprint $table) {
            $table->dropForeign('fk_log_case_resolution_validations_accident_id');
            $table->dropForeign('fk_log_case_resolution_validations_approved_by_id');
            $table->dropForeign('fk_log_case_resolution_validations_rejected_by_id');
            $table->dropForeign('fk_log_case_resolution_validations_updated_status_id');
        });

        Schema::dropIfExists('log_case_resolution_validations');
    }
};
