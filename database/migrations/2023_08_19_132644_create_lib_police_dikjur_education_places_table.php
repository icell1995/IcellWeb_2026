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
        Schema::create('lib.police_dikjur_education_places', function (Blueprint $table) {
            $table->string('id')->primary()->unique();

            $table->string('emp_id')->nullable();
            $table->string('code')->nullable()->unique();

            $table->string('name');
            $table->string('full_name')->nullable();

            $table->bigInteger('sort')->default(0)->nullable();
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lib.police_dikjur_education_places');
    }
};
