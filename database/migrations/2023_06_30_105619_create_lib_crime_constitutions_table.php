<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLibCrimeConstitutionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lib.crime_constitutions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('emp_crime_constitution_id')->nullable();

            $table->string('code')->nullable();
            $table->string('name');

            $table->bigInteger('sort')->default(0)->nullable();
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
        Schema::dropIfExists('lib.crime_constitutions');
    }
}
