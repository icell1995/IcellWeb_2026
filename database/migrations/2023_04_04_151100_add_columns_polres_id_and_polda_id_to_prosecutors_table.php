<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsPolresIdAndPoldaIdToProsecutorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('prosecutors', function (Blueprint $table) {
            $table->string('polres_id', 4)->nullable();
            $table->string('polda_id', 2)->nullable();

            $table->foreign('polres_id')->references('id')->on('polres')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('polda_id')->references('id')->on('polda')->onDelete('set null')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('prosecutors', function (Blueprint $table) {
            $table->dropForeign('prosecutors_polres_id_foreign');
            $table->dropForeign('prosecutors_polda_id_foreign');

            $table->dropColumn('polres_id');
            $table->dropColumn('polda_id');
        });
    }
}
