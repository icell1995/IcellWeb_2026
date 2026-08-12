<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PdfController;
use App\Http\Controllers\CMS\HomeController;
use App\Http\Controllers\CMS\MaintenanceModeController;
use App\Http\Controllers\CMS\DB\PostgresqlController;
use App\Http\Controllers\CMS\Libs\PositionController;
use App\Http\Controllers\CMS\CheckOfficerDataController;
use App\Http\Controllers\CMS\DashboardValidatorController;
use App\Http\Controllers\CMS\DashboardValidatorIrsmsIcellController;
use App\Http\Controllers\CMS\Libs\PositionClusterController;
use App\Http\Controllers\CMS\EventGallery\EventGalleryController;
use App\Http\Controllers\CMS\CaseDocumentValidationReportController;
use App\Http\Controllers\CMS\CheckOfficerDigitalSignatureController;
use App\Http\Controllers\CMS\CaseDocumentValidation\CaseDocumentValidationController;
use App\Http\Controllers\CMS\CaseResolutionsValidation\CaseResolutionsAprrovalController as SelraCtrl;
use App\Http\Controllers\CMS\CaseResolutionValidationReportController;
use App\Http\Controllers\CMS\ReturnedDocuments\DocumentReturnController;
use App\Http\Controllers\CMS\TicketingController;
use PhpOffice\PhpWord\Writer\Word2007\Part\Document;
use App\Http\Controllers\CMS\RequestDataController;
use App\Http\Controllers\CMS\IntegrationMonitorController;

// Upload Surat Ketetapan - Hanya show (GET)
Route::get('/upload-surat-ketetapan/{id}', [PdfController::class, 'show']);

// Ubah route utama untuk mengarah langsung ke validation dashboard
Route::get('/', [DashboardValidatorController::class, 'index'])->name('cms.home.index');

Route::get('/validation-dashboard', [DashboardValidatorController::class, 'index'])
    ->name('cms.validation-dashboard');

Route::get('/leaderboard', [DashboardValidatorController::class, 'getLeaderboard'])
    ->name('cms.validation-dashboard.leaderboard');

// Dashboard Validator IRSMS dan ICELL
Route::get('/validation-dashboard-irsms-icell', [DashboardValidatorIrsmsIcellController::class, 'index'])
    ->name('cms.validation-dashboard-irsms-icell');

// Leaderboard untuk IRSMS dan ICELL (parameter system menentukan mana yang diambil)
Route::get('/validation-dashboard-irsms-icell/leaderboard', [DashboardValidatorIrsmsIcellController::class, 'getLeaderboard'])
    ->name('cms.validation-dashboard-irsms-icell.leaderboard');

// IRSMS Pending Validations API
Route::get('/validation-dashboard-irsms-icell/pending-irsms', [DashboardValidatorIrsmsIcellController::class, 'getPendingIrsmsValidations'])
    ->name('cms.validation-dashboard-irsms-icell.pending-irsms');

// ICELL Pending Validations API - Endpoint baru
Route::get('/validation-dashboard-irsms-icell/pending-icell', [DashboardValidatorIrsmsIcellController::class, 'getPendingIcellValidations'])
    ->name('cms.validation-dashboard-irsms-icell.pending-icell');

// IRSMS Documents API
Route::get('/validation-dashboard-irsms-icell/irsms-documents', [DashboardValidatorIrsmsIcellController::class, 'getIrsmsDocuments'])
    ->name('cms.validation-dashboard-irsms-icell.irsms-documents');

// ICELL Documents API - Endpoint baru
Route::get('/validation-dashboard-irsms-icell/icell-documents', [DashboardValidatorIrsmsIcellController::class, 'getIcellDocuments'])
    ->name('cms.validation-dashboard-irsms-icell.icell-documents');

// IRSMS Validator Statistics API
Route::get('/validation-dashboard-irsms-icell/irsms-validator-stats', [DashboardValidatorIrsmsIcellController::class, 'getIrsmsValidatorStats'])
    ->name('cms.validation-dashboard-irsms-icell.irsms-validator-stats');

// ICELL Validator Statistics API - Endpoint baru
Route::get('/validation-dashboard-irsms-icell/icell-validator-stats', [DashboardValidatorIrsmsIcellController::class, 'getIcellValidatorStats'])
    ->name('cms.validation-dashboard-irsms-icell.icell-validator-stats');

Route::prefix('/check-officer-data')->group(function () {
    Route::get('/', [CheckOfficerDataController::class, 'index'])->name('cms.check-officer-data.index');
    Route::post('/api/officer-data', [CheckOfficerDataController::class, 'getOfficerData'])->name('cms.check-officer-data.api.officer-data');
});

Route::prefix('/check-officer-digital-signature')->group(function () {
    Route::get('/', [CheckOfficerDigitalSignatureController::class, 'index'])->name('cms.check-officer-digital-signature.index');
    Route::post('/api/test', [CheckOfficerDigitalSignatureController::class, 'test'])->name('cms.check-officer-digital-signature.api.test');
});

Route::prefix('/db')->group(function () {
    Route::prefix('/postgresql')->group(function () {
        Route::prefix('/query')->group(function () {
            Route::get('/', [PostgresqlController::class, 'queryIndex'])->name('cms.db.postgresql.query.index');
            Route::post('/', [PostgresqlController::class, 'queryExecute'])->name('cms.db.postgresql.query.execute');

            Route::get('/saved', [PostgresqlController::class, 'retrieveSavedQuery'])->name('cms.db.postgresql.query.saved');
            Route::post('/save', [PostgresqlController::class, 'savingQuery'])->name('cms.db.postgresql.query.save');
        });
    });
});

Route::prefix('/case-document-validation-report')->group(function () {
    Route::get('/', [CaseDocumentValidationReportController::class, 'index'])->name('cms.case-document-validation-report.index');
});

Route::prefix('/case-resolution-validation-report')->group(function () {
    Route::get('/', [CaseResolutionValidationReportController::class, 'index'])->name('cms.case-resolution-validation-report.index');
});

Route::prefix('/case-document-validation')->group(function () {
    Route::get('/', [CaseDocumentValidationController::class, 'index'])->name('cms.case-document-validation.index');

    Route::get('api/documents', [CaseDocumentValidationController::class, 'getDocuments'])->name('cms.case-document-validation.api.documents');

    Route::prefix('/module')->group(function () {
        Route::prefix('/surat-perintah-penyelidikan-document')->group(function () {
            Route::get('/{id}/validation', [App\Http\Controllers\CMS\CaseDocumentValidation\Module\SuratPerintahPenyelidikanDocumentValidationController::class, 'validation'])
                ->name('cms.case-document-validation.module.surat-perintah-penyelidikan-document.validation');
            Route::post('/{id}/validation/approve', [App\Http\Controllers\CMS\CaseDocumentValidation\Module\SuratPerintahPenyelidikanDocumentValidationController::class, 'approveValidation'])
                ->name('cms.case-document-validation.module.surat-perintah-penyelidikan-document.validation.approve');
            Route::post('/{id}/validation/reject', [App\Http\Controllers\CMS\CaseDocumentValidation\Module\SuratPerintahPenyelidikanDocumentValidationController::class, 'rejectValidation'])
                ->name('cms.case-document-validation.module.surat-perintah-penyelidikan-document.validation.reject');
        });
        Route::prefix('/surat-perintah-penyidikan-document')->group(function () {
            Route::get('/{id}/validation', [App\Http\Controllers\CMS\CaseDocumentValidation\Module\SuratPerintahPenyidikanDocumentValidationController::class, 'validation'])
                ->name('cms.case-document-validation.module.surat-perintah-penyidikan-document.validation');
            Route::post('/{id}/validation/approve', [App\Http\Controllers\CMS\CaseDocumentValidation\Module\SuratPerintahPenyidikanDocumentValidationController::class, 'approveValidation'])
                ->name('cms.case-document-validation.module.surat-perintah-penyidikan-document.validation.approve');
            Route::post('/{id}/validation/reject', [App\Http\Controllers\CMS\CaseDocumentValidation\Module\SuratPerintahPenyidikanDocumentValidationController::class, 'rejectValidation'])
                ->name('cms.case-document-validation.module.surat-perintah-penyidikan-document.validation.reject');
        });
        Route::prefix('/surat-perintah-tugas-document')->group(function () {
            Route::get('/{id}/validation', [App\Http\Controllers\CMS\CaseDocumentValidation\Module\SuratPerintahTugasDocumentValidationController::class, 'validation'])
                ->name('cms.case-document-validation.module.surat-perintah-tugas-document.validation');
            Route::post('/{id}/validation/approve', [App\Http\Controllers\CMS\CaseDocumentValidation\Module\SuratPerintahTugasDocumentValidationController::class, 'approveValidation'])
                ->name('cms.case-document-validation.module.surat-perintah-tugas-document.validation.approve');
            Route::post('/{id}/validation/reject', [App\Http\Controllers\CMS\CaseDocumentValidation\Module\SuratPerintahTugasDocumentValidationController::class, 'rejectValidation'])
                ->name('cms.case-document-validation.module.surat-perintah-tugas-document.validation.reject');
        });
        Route::prefix('/laporan-hasil-gelar-perkara-document')->group(function () {
            Route::get('/{id}/validation', [App\Http\Controllers\CMS\CaseDocumentValidation\Module\LaporanHasilGelarPerkaraDocumentValidationController::class, 'validation'])
                ->name('cms.case-document-validation.module.laporan-hasil-gelar-perkara-document.validation');
            Route::post('/{id}/validation/approve', [App\Http\Controllers\CMS\CaseDocumentValidation\Module\LaporanHasilGelarPerkaraDocumentValidationController::class, 'approveValidation'])
                ->name('cms.case-document-validation.module.laporan-hasil-gelar-perkara-document.validation.approve');
            Route::post('/{id}/validation/reject', [App\Http\Controllers\CMS\CaseDocumentValidation\Module\LaporanHasilGelarPerkaraDocumentValidationController::class, 'rejectValidation'])
                ->name('cms.case-document-validation.module.laporan-hasil-gelar-perkara-document.validation.reject');
        });
        Route::prefix('/surat-ketetapan-tentang-penetapan-tersangka-document')->group(function () {
            Route::get('/{id}/validation', [App\Http\Controllers\CMS\CaseDocumentValidation\Module\SuratKetetapanTentangPenetapanTersangkaDocumentValidationController::class, 'validation'])
                ->name('cms.case-document-validation.module.surat-ketetapan-tentang-penetapan-tersangka-document.validation');
            Route::post('/{id}/validation/approve', [App\Http\Controllers\CMS\CaseDocumentValidation\Module\SuratKetetapanTentangPenetapanTersangkaDocumentValidationController::class, 'approveValidation'])
                ->name('cms.case-document-validation.module.surat-ketetapan-tentang-penetapan-tersangka-document.validation.approve');
            Route::post('/{id}/validation/reject', [App\Http\Controllers\CMS\CaseDocumentValidation\Module\SuratKetetapanTentangPenetapanTersangkaDocumentValidationController::class, 'rejectValidation'])
                ->name('cms.case-document-validation.module.surat-ketetapan-tentang-penetapan-tersangka-document.validation.reject');
        });
        Route::prefix('/surat-pemberitahuan-dimulainya-penyidikan-document')->group(function () {
            Route::get('/{id}/validation', [App\Http\Controllers\CMS\CaseDocumentValidation\Module\SuratPemberitahuanDimulainyaPenyidikanDocumentValidationController::class, 'validation'])
                ->name('cms.case-document-validation.module.surat-pemberitahuan-dimulainya-penyidikan-document.validation');
            Route::post('/{id}/validation/approve', [App\Http\Controllers\CMS\CaseDocumentValidation\Module\SuratPemberitahuanDimulainyaPenyidikanDocumentValidationController::class, 'approveValidation'])
                ->name('cms.case-document-validation.module.surat-pemberitahuan-dimulainya-penyidikan-document.validation.approve');
            Route::post('/{id}/validation/reject', [App\Http\Controllers\CMS\CaseDocumentValidation\Module\SuratPemberitahuanDimulainyaPenyidikanDocumentValidationController::class, 'rejectValidation'])
                ->name('cms.case-document-validation.module.surat-pemberitahuan-dimulainya-penyidikan-document.validation.reject');
        });
    });
});



Route::prefix('/case-resolutions-validations')
    ->name('cms.case-resolutions-validations.')
    ->group(function () {

        // Index
        Route::get('/', [SelraCtrl::class, 'index'])->name('index');

        // API duluan (hindari ketabrak {id})
        Route::get('api/resolutions', [SelraCtrl::class, 'apiResolutions'])->name('api.resolutions');
        Route::get('api/table',       [SelraCtrl::class, 'dataTable'])->name('api.table');

        // Show + actions
        // Jika ID AccidentResolution bertipe BIGINT:
        Route::get('{id}',          [SelraCtrl::class, 'show'])->whereNumber('id')->name('show');
        Route::put('{id}/approve',  [SelraCtrl::class, 'approve'])->whereNumber('id')->name('approve');
        Route::put('{id}/reject',   [SelraCtrl::class, 'reject'])->whereNumber('id')->name('reject');
    });

Route::prefix('/libs')->group(function () {
    Route::prefix('/position')->group(function () {
        Route::get('/', [PositionController::class, 'index'])->name('cms.libs.position.index');
        Route::get('/create', [PositionController::class, 'create'])->name('cms.libs.position.create');
        Route::post('/create', [PositionController::class, 'store'])->name('cms.libs.position.store');
        Route::get('/{id}/edit', [PositionController::class, 'edit'])->name('cms.libs.position.edit');
        Route::put('/{id}/edit', [PositionController::class, 'update'])->name('cms.libs.position.update');
        Route::delete('/{id}/delete', [PositionController::class, 'delete'])->name('cms.libs.position.delete');
    });

    Route::prefix('/position-cluster')->group(function () {
        Route::get('/', [PositionClusterController::class, 'index'])->name('cms.libs.position-cluster.index');
        Route::get('/create', [PositionClusterController::class, 'create'])->name('cms.libs.position-cluster.create');
        Route::post('/create', [PositionClusterController::class, 'store'])->name('cms.libs.position-cluster.store');
        Route::get('/{id}/edit', [PositionClusterController::class, 'edit'])->name('cms.libs.position-cluster.edit');
        Route::put('/{id}/edit', [PositionClusterController::class, 'update'])->name('cms.libs.position-cluster.update');
        Route::delete('/{id}/delete', [PositionClusterController::class, 'delete'])->name('cms.libs.position-cluster.delete');
    });
});

Route::prefix('/event-gallery')->group(function () {
    Route::get('/', [EventGalleryController::class, 'index'])->name('cms.event-gallery.index');
    Route::get('/{id}/show', [EventGalleryController::class, 'show'])->name('cms.event-gallery.show');
    Route::get('/create', [EventGalleryController::class, 'create'])->name('cms.event-gallery.create');
    Route::post('/create', [EventGalleryController::class, 'store'])->name('cms.event-gallery.store');
    Route::get('/{id}/edit', [EventGalleryController::class, 'edit'])->name('cms.event-gallery.edit');
    Route::put('/{id}/edit', [EventGalleryController::class, 'update'])->name('cms.event-gallery.update');
    Route::delete('/{id}/delete', [EventGalleryController::class, 'delete'])->name('cms.event-gallery.delete');
});

// Tambahkan di dalam group validation-dashboard-irsms-icell
Route::get('/validation-dashboard-irsms-icell/pending-selra', [
    \App\Http\Controllers\CMS\DashboardValidatorIrsmsIcellController::class,
    'getPendingSelraValidations'
])->name('cms.validation-dashboard-irsms-icell.pending-selra');

// Ticketing routes
Route::prefix('ticketing')->name('ticketing.')->group(function () {
    Route::get('/',        [TicketingController::class, 'index'])->name('index');

    // Menu di sidebar:
    Route::get('/open',    [TicketingController::class, 'open'])->name('open');
    Route::get('/pending', [TicketingController::class, 'pending'])->name('pending');
    Route::get('/solved',  [TicketingController::class, 'solved'])->name('solved');

    // Export tiket solved (filter tanggal ikut query ?from=&to=)
    Route::get('/solved/export', [TicketingController::class, 'exportSolved'])
        ->name('solved.export');

    // Form buat ticket baru
    Route::get('/create',  [TicketingController::class, 'create'])->name('create');

    // Simpan ticket baru
    Route::post('/',       [TicketingController::class, 'store'])->name('store');

    // AJAX: ambil daftar Polres untuk Polda tertentu
    Route::get('/polda/{polda}/polres', [TicketingController::class, 'polresList'])
        ->name('api.polres');

    // Ubah status ticket (dipakai modal Change Status)
    Route::post('/{ticket}/status', [TicketingController::class, 'updateStatus'])
        ->name('updateStatus');

    // Hapus ticket
    Route::delete('/{ticket}', [TicketingController::class, 'destroy'])
        ->name('destroy');
});
// End Ticketing routes

// Request Data routes
Route::prefix('request-data')->name('request-data.')->group(function () {
    Route::get('/',              [RequestDataController::class, 'index'])->name('index');
    Route::post('/',             [RequestDataController::class, 'store'])->name('store');
    Route::get('/{id}',          [RequestDataController::class, 'show'])->name('show');
    Route::post('/{id}',         [RequestDataController::class, 'update'])->name('update');
    Route::delete('/{id}',       [RequestDataController::class, 'destroy'])->name('destroy');
    Route::get('/export/excel',  [RequestDataController::class, 'exportExcel'])->name('exportExcel');
    Route::get('/api/polres/{poldaId}', [RequestDataController::class, 'polresList'])->name('api.polres');
    Route::get('/{id}/download', [RequestDataController::class, 'download'])->name('download');
});
// End Request Data routes

// Maintenance Mode routes
Route::prefix('/maintenance-mode')->group(function () {
    Route::get('/', [MaintenanceModeController::class, 'index'])->name('cms.maintenance-mode.index');
    Route::post('/activate', [MaintenanceModeController::class, 'activate'])->name('cms.maintenance-mode.activate');
    Route::post('/deactivate', [MaintenanceModeController::class, 'deactivate'])->name('cms.maintenance-mode.deactivate');
    Route::get('/status', [MaintenanceModeController::class, 'status'])->name('cms.maintenance-mode.status');
});

Route::prefix('/document-return')
    ->name('cms.document-return.')
    ->group(function () {
        Route::get('/', [DocumentReturnController::class, 'index'])
            ->name('index');

        Route::get('/search-accident', [DocumentReturnController::class, 'searchAccident'])
            ->name('search-accident');

        Route::get('/accident/{accident}/documents', [DocumentReturnController::class, 'getDocumentsByAccident'])
            ->name('accident.documents');

        Route::get('/cascade/{accidentId}/{documentId}', [DocumentReturnController::class, 'getCascadeInfo'])
            ->name('cascade');

        Route::post('/return', [DocumentReturnController::class, 'storeReturn'])
            ->name('return');
    });

// Integration Monitor routes
Route::prefix('/integration-monitor')
    ->name('cms.integration-monitor.')
    ->group(function () {
        Route::get('/', [IntegrationMonitorController::class, 'index'])->name('index');
        Route::get('/data', [IntegrationMonitorController::class, 'getData'])->name('data');
    });
