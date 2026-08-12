<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLibCourtsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lib.courts', function (Blueprint $table) {
            $table->string('id')->primary();

            $table->string('parent_id')->nullable();
            $table->enum('class', ['AGUNG', 'TINGGI', 'NEGERI'])->comment('AGUNG, TINGGI, NEGERI')->nullable();

            $table->string('emp_id')->nullable();
            $table->string('code')->nullable();

            $table->string('name');
            $table->string('full_name')->nullable();

            $table->string('address')->nullable();
            $table->string('location_id')->nullable();
            $table->string('postal_code')->nullable();
            
            $table->string('police_id')->nullable();

            $table->bigInteger('sort')->default(0)->nullable();
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('location_id', 'fk_courts_location_id')->references('id')->on('lib.locations')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('police_id', 'fk_courts_police_id')->references('id')->on('lib.polices')->onDelete('set null')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // drop foreign key
        Schema::table('lib.courts', function (Blueprint $table) {
            $table->dropForeign('fk_courts_location_id');
            $table->dropForeign('fk_courts_police_id');
        });
        Schema::dropIfExists('lib.courts');
    }
}
