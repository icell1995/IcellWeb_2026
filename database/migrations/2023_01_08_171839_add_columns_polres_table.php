<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsPolresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('polres', function (Blueprint $table) {
            $table->string('polres_province')->nullable();
            $table->string('polres_regency')->nullable();
            $table->string('polres_district')->nullable();
            $table->string('polres_village')->nullable();
            $table->string('polres_zipcode')->nullable();

            $table->string('kejaksaan_name')->nullable();
            $table->string('kejaksaan_address')->nullable();
            $table->string('kejaksaan_province')->nullable();
            $table->string('kejaksaan_regency')->nullable();
            $table->string('kejaksaan_district')->nullable();
            $table->string('kejaksaan_village')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('polres', function (Blueprint $table) {
            $table->dropColumn('polres_province');
            $table->dropColumn('polres_regency');
            $table->dropColumn('polres_district');
            $table->dropColumn('polres_village');
            $table->dropColumn('polres_zipcode');

            $table->dropColumn('kejaksaan_name');
            $table->dropColumn('kejaksaan_address');
            $table->dropColumn('kejaksaan_province');
            $table->dropColumn('kejaksaan_regency');
            $table->dropColumn('kejaksaan_district');
            $table->dropColumn('kejaksaan_village');
        });
    }
}
