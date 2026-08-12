<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLibCrimeClassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lib.crime_classes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('emp_crime_class_id')->nullable();

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
        Schema::dropIfExists('lib.crime_classes');
    }
}
