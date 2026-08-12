<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToPoldaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('polda', function (Blueprint $table) {
            $table->string('spptti_id', 32)->nullable();
            $table->string('work_unit_code', 32)->nullable();
            $table->string('puskarda_code', 128)->nullable();
            
            $table->renameColumn('alamat', 'address');
            $table->string('province_name')->nullable();
            $table->string('regency_name')->nullable();
            $table->string('district_name')->nullable();
            $table->string('village_name')->nullable();
            $table->string('postal_code')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('polda', function (Blueprint $table) {
            $table->dropColumn('spptti_id');
            $table->dropColumn('work_unit_code');
            $table->dropColumn('puskarda_code');
            
            $table->renameColumn('address', 'alamat');
            $table->dropColumn('province_name');
            $table->dropColumn('regency_name');
            $table->dropColumn('district_name');
            $table->dropColumn('village_name');
            $table->dropColumn('postal_code');
        });
    }
}
