<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifySpringasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Tambahkan kolom baru pejabat_penandatangan_uuid
        Schema::table('springas', function (Blueprint $table) {
            $table->uuid('pejabat_penandatangan_uuid')->nullable();
        });

        // Hapus kolom lama pejabat_penandatangan
        Schema::table('springas', function (Blueprint $table) {
            $table->dropColumn('pejabat_penandatangan');
        });

        // Ubah nama kolom baru menjadi pejabat_penandatangan
        Schema::table('springas', function (Blueprint $table) {
            $table->renameColumn('pejabat_penandatangan_uuid', 'pejabat_penandatangan');
        });

        // Tambahkan kolom baru ketua_tim
        Schema::table('springas', function (Blueprint $table) {
            $table->string('ketua_tim')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('springas', function (Blueprint $table) {
            $table->dropColumn('ketua_tim');
        });

        // Ubah nama kolom pejabat_penandatangan menjadi pejabat_penandatangan_uuid
        Schema::table('springas', function (Blueprint $table) {
            $table->renameColumn('pejabat_penandatangan', 'pejabat_penandatangan_uuid');
        });

        // Tambahkan kolom lama pejabat_penandatangan
        Schema::table('springas', function (Blueprint $table) {
            $table->string('pejabat_penandatangan')->nullable();
        });

        // Hapus kolom pejabat_penandatangan_uuid
        Schema::table('springas', function (Blueprint $table) {
            $table->dropColumn('pejabat_penandatangan_uuid');
        });
    }
}
