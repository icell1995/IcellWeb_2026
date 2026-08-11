<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLibCaseClassificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lib.case_classifications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('emp_case_classification_id')->nullable();

            $table->string('code')->nullable();
            $table->string('name');

            $table->bigInteger('duration')->nullable();

            $table->text('description');

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
        Schema::dropIfExists('lib.case_classifications');
    }
}
