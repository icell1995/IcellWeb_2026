<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAccidentIdColumnForeignKeyToPublicSuspectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('public.suspects', function (Blueprint $table) {
            $table->foreign('accident_id', 'fk_suspects_accident_id')->references('id')->on('public.accidents')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('public.suspects', function (Blueprint $table) {
            $table->dropForeign('fk_suspects_accident_id');
        });
    }
}
