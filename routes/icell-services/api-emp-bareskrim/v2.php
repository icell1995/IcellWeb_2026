<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('res')->group(function () {
    Route::prefix('officers')->group(function () {
        Route::get('/', [App\Http\Controllers\IcellServices\ApiEmpBareskrim\V2\Res\OfficerController::class, 'index'])->name('api.v2.res.officer.index');
    });
});

Route::prefix('lib')->group(function () {
    Route::prefix('polices')->group(function () {
        Route::get('/', [App\Http\Controllers\IcellServices\ApiEmpBareskrim\V2\Lib\PoliceController::class, 'index'])->name('api.v2.lib.police.index');
    });
});

Route::prefix('doc')->group(function () {
    // Route::prefix('surat-perintah-penyelidikan-documents')->group(function () {
    //     Route::get('/', [App\Http\Controllers\IcellServices\ApiEmpBareskrim\V2\Doc\SuratPerintahPenyelidikanDocumentController::class, 'index'])->name('api.v2.doc.surat-perintah-penyelidikan-documents.index');
    // });

    Route::prefix('surat-perintah-penyidikan-documents')->group(function () {
        Route::get('/', [App\Http\Controllers\IcellServices\ApiEmpBareskrim\V2\Doc\SuratPerintahPenyidikanDocumentController::class, 'index'])->name('api.v2.doc.surat-perintah-penyidikan-documents.index');
    });
    
    Route::prefix('surat-perintah-tugas-documents')->group(function () {
        Route::get('/', [App\Http\Controllers\IcellServices\ApiEmpBareskrim\V2\Doc\SuratPerintahTugasDocumentController::class, 'index'])->name('api.v2.doc.surat-perintah-tugas-documentsindex');
    });
    
    Route::prefix('laporan-hasil-gelar-perkara-documents')->group(function () {
        Route::get('/', [App\Http\Controllers\IcellServices\ApiEmpBareskrim\V2\Doc\LaporanHasilGelarPerkaraDocumentController::class, 'index'])->name('api.v2.doc.laporan-hasil-gelar-perkara-documents.index');
    });
    
    Route::prefix('surat-ketetapan-tentang-penetapan-tersangka-documents')->group(function () {
        Route::get('/', [App\Http\Controllers\IcellServices\ApiEmpBareskrim\V2\Doc\SuratKetetapanTentangPenetapanTersangkaDocumentController::class, 'index'])->name('api.v2.doc.surat-ketetapan-tentang-penetapan-tersangka-documents.index');
    });
    
    Route::prefix('surat-pemberitahuan-dimulainya-penyidikan-documents')->group(function () {
        Route::get('/', [App\Http\Controllers\IcellServices\ApiEmpBareskrim\V2\Doc\SuratPemberitahuanDimulainyaPenyidikanDocumentController::class, 'index'])->name('api.v2.doc.surat-pemberitahuan-dimulainya-penyidikan-documents.index');
    });
});