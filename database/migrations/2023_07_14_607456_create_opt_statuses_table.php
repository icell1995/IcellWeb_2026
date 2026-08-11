<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOptStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('opt.statuses', function (Blueprint $table) {
            $table->string('id')->unique()->primary();
            
            $table->string('code')->unique()->nullable();
            $table->string('name');

            $table->string('group_id')->nullable();

            $table->bigInteger('sort')->default(0)->nullable();
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('group_id', 'fk_opt_statuses_group_id')
                ->references('id')
                ->on('opt.groups')
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // drop fk
        Schema::table('opt.statuses', function (Blueprint $table) {
            $table->dropForeign('fk_opt_statuses_group_id');
        });
        Schema::dropIfExists('opt.statuses');
    }
}
