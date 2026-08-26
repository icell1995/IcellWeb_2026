<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
| Mapping Kode Dokumen → Kode Proses SPPT-TI:
| spdp        → DIK-10  (Surat Pemberitahuan Dimulainya Penyidikan)
| sp3         → DIK-40  (Surat Pemberitahuan Penghentian Penyidikan)
|
*/

Route::prefix('spdp')->group(function () {
    Route::get(
        '/',
        [App\Http\Controllers\IcellServices\ApiPusiknasBareskrim\Doc\SpdpDocumentController::class, 'index']
    )->name('api.pusiknasbareskrim.doc.spdp.index');
});

Route::prefix('sp3')->group(function () {
    Route::get(
        '/',
        [App\Http\Controllers\IcellServices\ApiPusiknasBareskrim\Doc\Sp3DocumentController::class, 'index']
    )->name('api.pusiknasbareskrim.doc.sp3.index');
});
