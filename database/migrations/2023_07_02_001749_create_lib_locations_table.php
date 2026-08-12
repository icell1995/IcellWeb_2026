<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLibLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lib.locations', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('parent_id')->nullable();

            $table->enum('class', ['COUNTRY', 'PROVINCE', 'REGENCY', 'DISTRICT', 'VILLAGE'])->comment('COUNTRY, PROVINCE, REGENCY, DISTRICT, VILLAGE');
            
            $table->string('emp_id')->nullable();
            $table->string('local_id')->nullable();

            $table->string('iso_code')->nullable();
            $table->string('alpha_code')->nullable();
            $table->string('code')->nullable();

            $table->string('name');
            $table->string('full_name')->nullable();

            $table->string('timezone')->nullable();

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
        Schema::dropIfExists('lib.locations');
    }
}
