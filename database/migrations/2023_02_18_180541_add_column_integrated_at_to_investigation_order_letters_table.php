<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnIntegratedAtToInvestigationOrderLettersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('investigation_order_letters', function (Blueprint $table) {
            $table->dateTime('integrated_at')->nullable()->comment('Tanggal terintegrasi dengan EMP');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('investigation_order_letters', function (Blueprint $table) {
            $table->dropColumn('integrated_at');
        });
    }
}
