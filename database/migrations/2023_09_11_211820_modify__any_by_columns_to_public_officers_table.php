<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::beginTransaction();

        try{
            Schema::table('public.officers', function (Blueprint $table) {
                $table->unsignedBigInteger('created_by_user_id')->nullable();
                $table->unsignedBigInteger('updated_by_user_id')->nullable();
            });
    
            //drop column created_by, updated_by, deleted_by if exists
            Schema::table('public.officers', function (Blueprint $table) {
                $table->dropColumn('created_by');
                $table->dropColumn('updated_by');
            });

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::beginTransaction();
        try{
            Schema::table('public.officers', function (Blueprint $table) {
                $table->dropColumn('created_by_user_id');
                $table->dropColumn('updated_by_user_id');
            });

            // rollback column created_by, updated_by, deleted_by
            Schema::table('public.officers', function (Blueprint $table) {
                $table->string('created_by')->nullable();
                $table->string('updated_by')->nullable();
            });

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
};
