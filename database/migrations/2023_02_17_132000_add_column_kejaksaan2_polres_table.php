<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnKejaksaan2PolresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('polres', function (Blueprint $table) {
            $table->string('kejaksaan2_name')->nullable();
            $table->string('kejaksaan2_address')->nullable();
            $table->string('kejaksaan2_province')->nullable();
            $table->string('kejaksaan2_regency')->nullable();
            $table->string('kejaksaan2_district')->nullable();
            $table->string('kejaksaan2_village')->nullable();
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
            $table->dropColumn('kejaksaan2_name');
            $table->dropColumn('kejaksaan2_address');
            $table->dropColumn('kejaksaan2_province');
            $table->dropColumn('kejaksaan2_regency');
            $table->dropColumn('kejaksaan2_district');
            $table->dropColumn('kejaksaan2_village');
        });
    }
}
