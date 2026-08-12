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
        Schema::create('information_board_files', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('information_board_id');

            $table->string('name');
            $table->string('original_name');
            $table->string('path')->nullable();
            $table->string('extension')->nullable();
            $table->string('mimetype')->nullable();
            $table->string('size')->nullable();

            $table->timestamps();

            $table->foreign('information_board_id', 'fk_information_board_files_information_board_id')->references('id')->on('information_boards')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('information_board_files', function (Blueprint $table) {
            $table->dropForeign('fk_information_board_files_information_board_id');
        });
        Schema::dropIfExists('information_board_files');
    }
};
