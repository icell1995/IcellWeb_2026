<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnValidToAuthorizedSignatoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('authorized_signatories', function (Blueprint $table) {
            $table->boolean('valid')->default(false)->after('position')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return voi
     */
    public function down()
    {
        Schema::table('authorized_signatories', function (Blueprint $table) {
            $table->dropColumn('valid');
        });
    }
}
