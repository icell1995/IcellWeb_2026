<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnIsCanSignatoryToPositionClustersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('opt.position_clusters', function (Blueprint $table) {
            $table->boolean('is_can_signatory')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('opt.position_clusters', function (Blueprint $table) {
            $table->dropColumn('is_can_signatory');
        });
    }
}
