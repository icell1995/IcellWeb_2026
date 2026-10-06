<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IcellServices\ApiPusiknasBareskrim\Doc\SuratPemberitahuanDimulainyaPenyidikanPusiknasDocumentController;
use App\Http\Controllers\IcellServices\ApiPusiknasBareskrim\Doc\SuratPemberitahuanPenghentianPenyidikanDocumentController;
use App\Http\Controllers\IcellServices\ApiPusiknasBareskrim\Doc\Tahap1PusiknasDocumentController;
use App\Http\Controllers\IcellServices\ApiPusiknasBareskrim\Doc\Tahap2PusiknasDocumentController;

/*
|--------------------------------------------------------------------------
| API Pusiknas Bareskrim — Dokumen Penyidikan Laka Lantas
|--------------------------------------------------------------------------
|
| Base URL  : https://icell.korlantas.polri.go.id/icell-services/
|             api-pusiknasbareskrim/doc/{suffix}
| Method    : GET
| Auth      : Authorization: Bearer {token}
| Middleware: api-auth (terdaftar di RouteServiceProvider)
|
*/

Route::prefix('spdp-pusiknas')->group(function () {
    Route::get(
        '/',
        [SuratPemberitahuanDimulainyaPenyidikanPusiknasDocumentController::class, 'index']
    )->name('api.pusiknasbareskrim.doc.spdp-pusiknas.index');
});

Route::prefix('sp3-pusiknas')->group(function () {
    Route::get(
        '/',
        [SuratPemberitahuanPenghentianPenyidikanDocumentController::class, 'index']
    )->name('api.pusiknasbareskrim.doc.sp3-pusiknas.index');
});

Route::prefix('tahap-1-pusiknas')->group(function () {
    Route::get(
        '/',
        [Tahap1PusiknasDocumentController::class, 'index']
    )->name('api.pusiknasbareskrim.doc.tahap-1-pusiknas.index');
});

Route::prefix('tahap-2-pusiknas')->group(function () {
    Route::get(
        '/',
        [Tahap2PusiknasDocumentController::class, 'index']
    )->name('api.pusiknasbareskrim.doc.tahap-2-pusiknas.index');
});

Route::prefix('s17')->group(function () {
    Route::get(
        '/',
        [App\Http\Controllers\IcellServices\ApiPusiknasBareskrim\Doc\S17DocumentController::class, 'index']
    )->name('api.pusiknasbareskrim.doc.s17.index');
    Route::get(
        '/{id}',
        [App\Http\Controllers\IcellServices\ApiPusiknasBareskrim\Doc\S17DocumentController::class, 'show']
    )->name('api.pusiknasbareskrim.doc.s17.show');
});

Route::prefix('s18')->group(function () {
    Route::get(
        '/',
        [App\Http\Controllers\IcellServices\ApiPusiknasBareskrim\Doc\S18DocumentController::class, 'index']
    )->name('api.pusiknasbareskrim.doc.s18.index');
    Route::get(
        '/{id}',
        [App\Http\Controllers\IcellServices\ApiPusiknasBareskrim\Doc\S18DocumentController::class, 'show']
    )->name('api.pusiknasbareskrim.doc.s18.show');
});

Route::prefix('s19')->group(function () {
    Route::get(
        '/',
        [App\Http\Controllers\IcellServices\ApiPusiknasBareskrim\Doc\S19DocumentController::class, 'index']
    )->name('api.pusiknasbareskrim.doc.s19.index');
    Route::get(
        '/{id}',
        [App\Http\Controllers\IcellServices\ApiPusiknasBareskrim\Doc\S19DocumentController::class, 'show']
    )->name('api.pusiknasbareskrim.doc.s19.show');
});

Route::prefix('s21')->group(function () {
    Route::get(
        '/',
        [App\Http\Controllers\IcellServices\ApiPusiknasBareskrim\Doc\S21DocumentController::class, 'index']
    )->name('api.pusiknasbareskrim.doc.s21.index');
    Route::get(
        '/{id}',
        [App\Http\Controllers\IcellServices\ApiPusiknasBareskrim\Doc\S21DocumentController::class, 'show']
    )->name('api.pusiknasbareskrim.doc.s21.show');
});

Route::prefix('surat-permintaan-penggeledahan')->group(function () {
    Route::get(
        '/',
        [App\Http\Controllers\IcellServices\ApiPusiknasBareskrim\Doc\SuratPermintaanPenggeledahan::class, 'index']
    )->name('api.pusiknasbareskrim.doc.surat-permintaan-penggeledahan.index');
});

Route::prefix('surat-persetujuan-penggeledahan')->group(function () {
    Route::get(
        '/',
        [App\Http\Controllers\IcellServices\ApiPusiknasBareskrim\Doc\SuratPersetujuanPenggeledahan::class, 'index']
    )->name('api.pusiknasbareskrim.doc.surat-persetujuan-penggeledahan.index');
});
