<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyColumnsToPublicUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('public_officers', function (Blueprint $table) {
            $table->renameColumn('role_id', 'role');
        });

        Schema::table('public.users', function (Blueprint $table) {
            $table->string('first_title')->nullable();
            $table->string('last_title')->nullable();

            $table->string('register_number')->nullable();

            $table->string('role_id')->nullable();
            $table->string('rank_id')->nullable();
            $table->string('position_id')->nullable();

            $table->boolean('is_active')->default(true);

            $table->foreign('rank_id', 'fk_users_rank_id')->references('id')->on('lib.ranks')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('position_id', 'fk_users_position_id')->references('id')->on('lib.positions')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('role_id', 'fk_users_role_id')->references('id')->on('lib.roles')->onDelete('restrict')->onUpdate('cascade');
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
            $table->dropForeign('fk_users_rank_id');
            $table->dropForeign('fk_users_position_id');
            $table->dropForeign('fk_users_role_id');
        });

        Schema::table('public.users', function (Blueprint $table) {
            $table->dropColumn('rank_id');
            $table->dropColumn('position_id');
            $table->dropColumn('role_id');

            $table->dropColumn('is_active');
            $table->dropColumn('register_number');
            
            $table->dropColumn('last_title');
            $table->dropColumn('first_title');
        });

        Schema::table('public.users', function (Blueprint $table) {
            $table->renameColumn('role', 'role_id');
        });
    }
}
