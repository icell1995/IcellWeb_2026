<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Make old columns nullable so they don't violate NOT NULL constraint
        DB::statement('ALTER TABLE "doc"."tahap_1_document_attachments" ALTER COLUMN "file_name" DROP NOT NULL');
        DB::statement('ALTER TABLE "doc"."tahap_1_document_attachments" ALTER COLUMN "file_path" DROP NOT NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE "doc"."tahap_1_document_attachments" ALTER COLUMN "file_name" SET NOT NULL');
        DB::statement('ALTER TABLE "doc"."tahap_1_document_attachments" ALTER COLUMN "file_path" SET NOT NULL');
    }
};
