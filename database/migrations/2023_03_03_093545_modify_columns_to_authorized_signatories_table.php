<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyColumnsToAuthorizedSignatoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('authorized_signatories', function (Blueprint $table) {
            // Rename Name to First Name
            $table->renameColumn('name', 'first_name');

            // Add Last Name
            $table->string('last_name')->after('first_name')->nullable();

            // Add First Title
            $table->string('first_title')->before('first_name')->nullable();

            // Add Last Title
            $table->string('last_title')->after('last_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('authorized_signatories', function (Blueprint $table) {
            // Rename First Name to Name
            $table->renameColumn('first_name', 'name');

            // Drop Last Name
            $table->dropColumn('last_name');

            // Drop First Title
            $table->dropColumn('first_title');

            // Drop Last Title
            $table->dropColumn('last_title');
        });
    }
}
