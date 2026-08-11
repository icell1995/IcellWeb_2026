<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyColumnsToPublicOfficersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('public.officers', function (Blueprint $table) {
            $table->renameColumn('rank_id', 'rank');
        });

        Schema::table('public.officers', function (Blueprint $table) {
            $table->string('first_title')->nullable();
            $table->string('last_title')->nullable();

            $table->string('rank_id')->nullable();
            $table->string('position_id')->nullable();
            $table->string('role_id')->nullable();

            $table->boolean('is_active')->default(true);
            $table->boolean('is_valid')->default(true)->nullable();

            $table->foreign('rank_id', 'fk_officers_rank_id')->references('id')->on('lib.ranks')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('position_id', 'fk_officers_position_id')->references('id')->on('lib.positions')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('role_id', 'fk_officers_role_id')->references('id')->on('lib.roles')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('public.officers', function (Blueprint $table) {
            $table->dropForeign('fk_officers_rank_id');
            $table->dropForeign('fk_officers_position_id');
            $table->dropForeign('fk_officers_role_id');
        });
        
        Schema::table('public.officers', function (Blueprint $table) {
            $table->dropColumn('rank_id');
            $table->dropColumn('position_id');
    
            $table->dropColumn('is_active');
            $table->dropColumn('is_valid');
    
            $table->dropColumn('last_title');
            $table->dropColumn('first_title');
        });

        Schema::table('public.officers', function (Blueprint $table) {
            $table->renameColumn('rank', 'rank_id');
        });
    }
}
