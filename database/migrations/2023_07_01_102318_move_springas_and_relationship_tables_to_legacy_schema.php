<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;  
class MoveSpringasAndRelationshipTablesToLegacySchema extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE public.springas SET SCHEMA legacy;');
        DB::statement('ALTER TABLE public.officer_springas SET SCHEMA legacy;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE legacy.springas SET SCHEMA public;');
        DB::statement('ALTER TABLE legacy.officer_springas SET SCHEMA public;');
    }
}
