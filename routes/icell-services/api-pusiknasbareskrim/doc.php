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
| Mapping Kode Dokumen → Kode Proses SPPT-TI:
| spdp        → DIK-10  (Surat Pemberitahuan Dimulainya Penyidikan)
| sp3         → DIK-40  (Surat Pemberitahuan Penghentian Penyidikan)
| s22         → HAN-10.30 (Surat Permintaan Perpanjangan Penahanan Lanjutan)
| s23         → DAT-5   (Surat Perintah Pembantaran Penahanan)
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

Route::prefix('s22')->group(function () {
    Route::get(
        '/',
        [App\Http\Controllers\IcellServices\ApiPusiknasBareskrim\Doc\S22DocumentController::class, 'index']
    )->name('api.pusiknasbareskrim.doc.s22.index');
});

Route::prefix('s23')->group(function () {
    Route::get(
        '/',
        [App\Http\Controllers\IcellServices\ApiPusiknasBareskrim\Doc\S23DocumentController::class, 'index']
    )->name('api.pusiknasbareskrim.doc.s23.index');
});

