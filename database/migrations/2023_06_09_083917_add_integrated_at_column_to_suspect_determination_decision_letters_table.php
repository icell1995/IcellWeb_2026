<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIntegratedAtColumnToSuspectDeterminationDecisionLettersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('suspect_determination_decision_letters', function (Blueprint $table) {
            $table->timestamp('integrated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('suspect_determination_decision_letters', function (Blueprint $table) {
            $table->dropColumn('integrated_at');
        });
    }
}
