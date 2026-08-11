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
            Schema::create('accident_resolutions', function (Blueprint $table) {
                $table->bigIncrements('id');

                $table->uuid('accident_id');
                $table->string('type_id')->nullable();
                $table->string('type_name')->nullable();
                $table->string('flag')->nullable();
                $table->string('number')->nullable();
                $table->date('date')->nullable();
                $table->string('file')->nullable();
                
                $table->timestamps();
                $table->dateTime('uploaded_at')->default(now());

                $table->foreign('accident_id', 'fk_accident_resolutions_accident_id')->references('id')->on('public.accidents')->onDelete('cascade')->onUpdate('cascade');
            });

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::beginTransaction();
        try{
            // Drop foreign keys
            Schema::table('accident_resolutions', function (Blueprint $table) {
                $table->dropForeign('fk_accident_resolutions_accident_id');
            });
            Schema::dropIfExists('accident_resolutions');

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
};
