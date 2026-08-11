<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('suspects', function (Blueprint $table) {
            $table->renameColumn('gender', 'gender_short_name');
            $table->renameColumn('religion', 'religion_short_name');
            $table->renameColumn('education', 'education_short_name');
            $table->renameColumn('marital_status', 'marital_status_short_name');
            $table->renameColumn('country', 'country_short_name');
            $table->renameColumn('province', 'province_short_name');
            $table->renameColumn('district', 'district_short_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suspects', function (Blueprint $table) {
            $table->renameColumn('gender_short_name', 'gender');
            $table->renameColumn('religion_short_name', 'religion');
            $table->renameColumn('education_short_name', 'education');
            $table->renameColumn('marital_status_short_name', 'marital_status');
            $table->renameColumn('country_short_name', 'country');
            $table->renameColumn('province_short_name', 'province');
            $table->renameColumn('district_short_name', 'district');
        });
    }
};
