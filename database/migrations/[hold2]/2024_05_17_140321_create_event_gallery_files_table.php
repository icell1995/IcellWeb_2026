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
        Schema::create('event_gallery_files', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('event_gallery_id');

            $table->string('name');
            $table->string('original_name');
            $table->string('path')->nullable();
            $table->string('extension')->nullable();
            $table->string('mimetype')->nullable();
            $table->string('size')->nullable();

            $table->timestamps();

            $table->foreign('event_gallery_id', 'fk_event_gallery_files_event_gallery_id')->references('id')->on('event_galleries')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_gallery_files', function (Blueprint $table) {
            $table->dropForeign('fk_event_gallery_files_event_gallery_id');
        });
        Schema::dropIfExists('event_gallery_files');
    }
};
