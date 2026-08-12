<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Nomor tiket (diisi sementara di controller, lalu bisa di-update di model)
            $table->string('ticket_number')->unique(); 

            // Kode Polda / Polres disimpan sebagai string supaya leading zero tidak hilang
            $table->string('polda_id', 10)->nullable(); 
            $table->string('polres_id', 10)->nullable(); 

            // Status tiket
            $table->enum('status', ['open', 'pending', 'solved'])->default('open'); 

            // Relasi ke users (boleh tetap bigInteger karena ini id user biasa)
            $table->unsignedBigInteger('assigned_to')->nullable(); // user id
            $table->unsignedBigInteger('created_by')->nullable(); 

            // Field baru langsung digabung di sini
            $table->string('kategori')->nullable();                     // A1 / A2 / A3
            $table->text('deskripsi_permasalahan')->nullable();         // permasalahan yang dilaporkan
            $table->text('deskripsi_solusi')->nullable();               // solusi saat solved

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
