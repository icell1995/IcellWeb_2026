<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLibPolicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lib.polices', function (Blueprint $table) {
            $table->string('id')->primary();

            $table->string('parent_id')->nullable();
            $table->enum('class', ['PUSAT', 'DAERAH', 'RESOR', 'SEKTOR', 'SUBSEKTOR'])->comment('PUSAT, DAERAH, RESOR, SEKTOR, SUBSEKTOR');
            
            $table->string('divtik_id')->nullable();
            $table->string('puskarda_id')->nullable();
            $table->string('emp_id')->nullable();
            $table->string('spptti_id')->nullable();
            
            $table->string('code')->nullable();

            $table->string('name');
            $table->string('full_name')->nullable();

            $table->string('address')->nullable();
            $table->string('location_id')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('timezone')->nullable();

            $table->bigInteger('sort')->default(0)->nullable();
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('location_id', 'fk_polices_location_id')->references('id')->on('lib.locations')->onDelete('restrict')->onUpdate('cascade');
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
        Schema::table('lib.polices', function (Blueprint $table) {
            $table->dropForeign('fk_polices_location_id');
        });
        Schema::dropIfExists('lib.polices');
    }
}
