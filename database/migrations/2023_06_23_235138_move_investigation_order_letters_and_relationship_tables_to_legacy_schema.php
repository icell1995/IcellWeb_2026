<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class MoveInvestigationOrderLettersAndRelationshipTablesToLegacySchema extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE doc.investigation_order_letters SET SCHEMA legacy;');
        DB::statement('ALTER TABLE doc.investigation_order_letter_details SET SCHEMA legacy;');
        DB::statement('ALTER TABLE doc.investigation_order_letter_law SET SCHEMA legacy;');
        DB::statement('ALTER TABLE doc.investigation_order_letter_leader_officer SET SCHEMA legacy;');
        DB::statement('ALTER TABLE doc.investigation_order_letter_officer SET SCHEMA legacy;');
        DB::statement('ALTER TABLE doc.authorized_signatory_investigation_order_letter SET SCHEMA legacy;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE legacy.investigation_order_letters SET SCHEMA doc;');
        DB::statement('ALTER TABLE legacy.investigation_order_letter_details SET SCHEMA doc;');
        DB::statement('ALTER TABLE legacy.investigation_order_letter_law SET SCHEMA doc;');
        DB::statement('ALTER TABLE legacy.investigation_order_letter_leader_officer SET SCHEMA doc;');
        DB::statement('ALTER TABLE legacy.investigation_order_letter_officer SET SCHEMA doc;');
        DB::statement('ALTER TABLE legacy.authorized_signatory_investigation_order_letter SET SCHEMA doc;');
    }
}
