<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class MoveInvestigationWarrantsAndRelationshipTablesToLegacySchema extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE doc.investigation_warrants SET SCHEMA legacy;');
        DB::statement('ALTER TABLE doc.investigation_warrant_details SET SCHEMA legacy;');
        DB::statement('ALTER TABLE doc.investigation_warrant_law SET SCHEMA legacy;');
        DB::statement('ALTER TABLE doc.investigation_warrant_leader_officer SET SCHEMA legacy;');
        DB::statement('ALTER TABLE doc.investigation_warrant_officer SET SCHEMA legacy;');
        DB::statement('ALTER TABLE doc.authorized_signatory_investigation_warrant SET SCHEMA legacy;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE legacy.investigation_warrants SET SCHEMA doc;');
        DB::statement('ALTER TABLE legacy.investigation_warrant_details SET SCHEMA doc;');
        DB::statement('ALTER TABLE legacy.investigation_warrant_law SET SCHEMA doc;');
        DB::statement('ALTER TABLE legacy.investigation_warrant_leader_officer SET SCHEMA doc;');
        DB::statement('ALTER TABLE legacy.investigation_warrant_officer SET SCHEMA doc;');
        DB::statement('ALTER TABLE legacy.authorized_signatory_investigation_warrant SET SCHEMA doc;');
    }
}
