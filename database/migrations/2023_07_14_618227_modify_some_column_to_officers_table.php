<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifySomeColumnToOfficersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('officers', function (Blueprint $table) {
            $table->renameColumn('nik', 'identity_number');
            $table->renameColumn('phone', 'phone_number');

            $table->string('first_title')->nullable();
            $table->string('last_title')->nullable();
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
            $table->renameColumn('identity_number', 'nik');
            $table->renameColumn('phone_number', 'phone');
            
            $table->dropColumn('first_title');
            $table->dropColumn('last_title');
        });
    }
}
