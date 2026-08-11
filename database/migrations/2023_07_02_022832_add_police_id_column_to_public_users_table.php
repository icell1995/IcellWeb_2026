<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPoliceIdColumnToPublicUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('public.users', function (Blueprint $table) {
            $table->string('police_id')->nullable();

            $table->foreign('police_id', 'fk_users_police_id')->references('id')->on('lib.polices')->onDelete('set null')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('public.users', function (Blueprint $table) {
            $table->dropForeign('fk_users_police_id');
            
            $table->dropColumn('police_id');
        });
    }
}
