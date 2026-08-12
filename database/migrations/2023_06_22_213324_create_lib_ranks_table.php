<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLibRanksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lib.ranks', function (Blueprint $table) {
            $table->string('id')->primary();
            
            $table->string('emp_rank_id')->nullable();
            $table->string('type')->nullable();
            
            $table->string('code')->nullable();
            $table->string('name');
            $table->string('full_name');
            
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
        Schema::dropIfExists('lib.ranks');
    }
}
