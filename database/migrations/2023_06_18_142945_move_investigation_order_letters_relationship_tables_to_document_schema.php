<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class MoveInvestigationOrderLettersRelationshipTablesToDocumentSchema extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE public.investigation_order_letter_details SET SCHEMA document;');
        DB::statement('ALTER TABLE public.investigation_order_letter_law SET SCHEMA document;');
        DB::statement('ALTER TABLE public.investigation_order_letter_leader_officer SET SCHEMA document;');
        DB::statement('ALTER TABLE public.investigation_order_letter_officer SET SCHEMA document;');
        DB::statement('ALTER TABLE public.authorized_signatory_investigation_order_letter SET SCHEMA document;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE document.investigation_order_letter_details SET SCHEMA public;');
        DB::statement('ALTER TABLE document.investigation_order_letter_law SET SCHEMA public;');
        DB::statement('ALTER TABLE document.investigation_order_letter_leader_officer SET SCHEMA public;');
        DB::statement('ALTER TABLE document.investigation_order_letter_officer SET SCHEMA public;');
        DB::statement('ALTER TABLE document.authorized_signatory_investigation_order_letter SET SCHEMA public;');
    }
}
