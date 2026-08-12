<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class MoveInvestigationWarrantsTableToDocumentSchema extends Migration
{
    private $table = 'investigation_warrants';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE public.' . $this->table . ' SET SCHEMA document;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE document.' . $this->table . ' SET SCHEMA public;');
    }
}
