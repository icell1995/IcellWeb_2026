<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateLibPrisonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Ensure the lib schema exists (PostgreSQL specific)
        DB::statement('CREATE SCHEMA IF NOT EXISTS lib;');

        Schema::create('lib.prisons', function (Blueprint $table) {
            $table->id();
            $table->string('province')->nullable();
            $table->string('name')->nullable();
            $table->string('branch')->nullable();
            $table->string('puskarda_id')->nullable();
            $table->string('spptti_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lib.prisons');
    }
}
