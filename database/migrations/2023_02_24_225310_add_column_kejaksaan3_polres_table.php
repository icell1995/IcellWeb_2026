<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnKejaksaan3PolresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('polres', function (Blueprint $table) {
            $table->string('kejaksaan3_name')->nullable();
            $table->string('kejaksaan3_address')->nullable();
            $table->string('kejaksaan3_province')->nullable();
            $table->string('kejaksaan3_regency')->nullable();
            $table->string('kejaksaan3_district')->nullable();
            $table->string('kejaksaan3_village')->nullable();
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
            $table->dropColumn('kejaksaan3_name');
            $table->dropColumn('kejaksaan3_address');
            $table->dropColumn('kejaksaan3_province');
            $table->dropColumn('kejaksaan3_regency');
            $table->dropColumn('kejaksaan3_district');
            $table->dropColumn('kejaksaan3_village');
        });
    }
}
