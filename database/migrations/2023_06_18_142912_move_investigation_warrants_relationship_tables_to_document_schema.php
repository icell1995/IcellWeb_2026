<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MoveInvestigationWarrantsRelationshipTablesToDocumentSchema extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE public.investigation_warrant_details SET SCHEMA document;');
        DB::statement('ALTER TABLE public.investigation_warrant_law SET SCHEMA document;');
        DB::statement('ALTER TABLE public.investigation_warrant_leader_officer SET SCHEMA document;');
        DB::statement('ALTER TABLE public.investigation_warrant_officer SET SCHEMA document;');
        DB::statement('ALTER TABLE public.authorized_signatory_investigation_warrant SET SCHEMA document;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE document.investigation_warrant_details SET SCHEMA public;');
        DB::statement('ALTER TABLE document.investigation_warrant_law SET SCHEMA public;');
        DB::statement('ALTER TABLE document.investigation_warrant_leader_officer SET SCHEMA public;');
        DB::statement('ALTER TABLE document.investigation_warrant_officer SET SCHEMA public;');
        DB::statement('ALTER TABLE document.authorized_signatory_investigation_warrant SET SCHEMA public;');
    }
}
