<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsActiveAndSortAndIsValidAndIsSignatoryColumnToOfficersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('officers', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->nullable();
            $table->integer('sort')->default(0);
            $table->boolean('is_valid')->default(true);
            $table->boolean('is_signatory')->default(false);
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
            $table->dropColumn([
                'is_active',
                'sort',
                'is_valid',
                'is_signatory',
            ]);
        });
    }
}
