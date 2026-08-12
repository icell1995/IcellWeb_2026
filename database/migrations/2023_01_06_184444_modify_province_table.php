<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyProvinceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('province', function (Blueprint $table) {
            $table->string('sort')->nullable(true)->change();
            $table->string('timezone', 2)->nullable()->change();
            $table->string('state')->default(1)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('province', function (Blueprint $table) {
            $table->string('sort')->nullable(false)->change();
            $table->string('timezone', 2)->nullable(false)->change();
            $table->string('state')->default(1)->change();
        });
    }
}
