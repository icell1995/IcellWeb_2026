<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnGeographyIdToCourtsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('courts', function (Blueprint $table) {
            // add column province_id
            $table->string('province_id', 2)->nullable()->after('address');

            // add column regency_id
            $table->string('regency_id', 4)->nullable()->after('province_id');

            // add column district_id
            $table->string('district_id', 8)->nullable()->after('regency_id');

            // add column village_id
            $table->string('village_id', 16)->nullable()->after('district_id');

            //foreign key
            $table->foreign('province_id')->references('id')->on('province')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('regency_id')->references('id')->on('regency')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('district_id')->references('id')->on('district')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('village_id')->references('id')->on('village')->onDelete('set null')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('courts', function (Blueprint $table) {
            // drop foreign key
            $table->dropForeign(['province_id']);
            $table->dropForeign(['regency_id']);
            $table->dropForeign(['district_id']);
            $table->dropForeign(['village_id']);

            // drop column
            $table->dropColumn('province_id');
            $table->dropColumn('regency_id');
            $table->dropColumn('district_id');
            $table->dropColumn('village_id');
        });
    }
}
