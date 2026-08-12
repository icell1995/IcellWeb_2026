<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOptPositionClustersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('opt.position_clusters', function (Blueprint $table) {
            $table->string('id')->unique()->primary();
            
            $table->string('code')->unique()->nullable();
            $table->string('name');

            $table->boolean('is_can_signatory')->default(false);

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
        Schema::dropIfExists('opt.position_clusters');
    }
}
