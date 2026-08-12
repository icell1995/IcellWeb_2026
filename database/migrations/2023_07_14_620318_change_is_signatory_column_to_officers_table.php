<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeIsSignatoryColumnToOfficersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('officers', function (Blueprint $table) {
            $table->string('is_signatory')->nullable()->change();
        });

        Schema::table('officers', function (Blueprint $table) {
            $table->renameColumn('is_signatory', 'class');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('officers', function (Blueprint $table) {
            $table->renameColumn('class', 'is_signatory');
        });

        Schema::table('officers', function (Blueprint $table) {
            $table->string('is_signatory')->nullable(false)->change();
        });
    }
}
