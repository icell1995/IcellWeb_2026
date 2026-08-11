<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyColumnsToSuspectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('suspects', function (Blueprint $table) {
            $table->string('identity_type')->nullable()->change();
            $table->string('identity_number')->nullable()->change();
            $table->string('name')->nullable()->change();
            $table->string('gender')->nullable()->change();
            $table->string('place_of_birth')->nullable()->change();
            $table->date('date_of_birth')->nullable()->change();
            $table->string('mother_name')->nullable()->change();
            $table->string('father_name')->nullable()->change();
            $table->string('ethnicity')->nullable()->change();
            $table->string('occupation')->nullable()->change();
            $table->string('religion')->nullable()->change();
            $table->string('education')->nullable()->change();
            $table->string('marital_status')->nullable()->change();
            $table->string('phone_number')->nullable()->change();
            $table->string('email_address')->nullable()->change();
            $table->string('country')->nullable()->change();
            $table->string('province')->nullable()->change();
            $table->string('city')->nullable()->change();
            $table->string('district')->nullable()->change();
            $table->string('sub_district')->nullable()->change();
            $table->string('address')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('suspects', function (Blueprint $table) {
            $table->string('identity_type')->change();
            $table->string('identity_number')->change();
            $table->string('name')->change();
            $table->string('gender')->change();
            $table->string('place_of_birth')->change();
            $table->date('date_of_birth')->change();
            $table->string('mother_name')->change();
            $table->string('father_name')->change();
            $table->string('ethnicity')->change();
            $table->string('occupation')->change();
            $table->string('religion')->change();
            $table->string('education')->change();
            $table->string('marital_status')->change();
            $table->string('phone_number')->change();
            $table->string('email_address')->change();
            $table->string('country')->change();
            $table->string('province')->change();
            $table->string('city')->change();
            $table->string('district')->change();
            $table->string('sub_district')->change();
            $table->string('address')->change();
        });
    }
}
