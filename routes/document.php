<?php
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DocumentController;
use App\Http\Controllers\Docs\SuratPerintahPenyelidikanDocumentController;
use App\Http\Controllers\Docs\SuratPerintahPenyidikanDocumentController;
use App\Http\Controllers\Docs\SuratPerintahTugasDocumentController;
use App\Http\Controllers\Docs\LaporanHasilGelarPerkaraDocumentController;
use App\Http\Controllers\Docs\SuratKetetapanTentangPenetapanTersangkaDocumentController;
use App\Http\Controllers\Docs\SuratPemberitahuanDimulainyaPenyidikanDocumentController;
use App\Http\Controllers\Docs\DaftarTersangkaDocumentController;
use App\Http\Controllers\Docs\Tahap1DocumentController;
use App\Http\Controllers\Docs\P19DocumentController;
use App\Http\Controllers\Docs\P21DocumentController;
use App\Http\Controllers\Docs\Tahap2DocumentController;
use App\Http\Controllers\Docs\Sp2hpDocumentController;
use App\Http\Controllers\Doc\SpdpPusiknasDocumentController;
use App\Http\Controllers\Doc\Sp3PusiknasDocumentController;

Route::post('/create',[DocumentController::class, 'createDocumentRouter'])->name('doc.createDocumentRouter');
Route::get('/type-document/{id}',[DocumentController::class, 'getTypeDocument'])->name('doc.getTypeDocument');

Route::prefix('/surat-perintah-penyelidikan-document')->group(function(){
    Route::get('/', [SuratPerintahPenyelidikanDocumentController::class, 'index'])->name('doc.surat-perintah-penyelidikan-document.index');
    Route::get('/{id}/show', [SuratPerintahPenyelidikanDocumentController::class, 'show'])->name('doc.surat-perintah-penyelidikan-document.show');
    Route::get('/create', [SuratPerintahPenyelidikanDocumentController::class, 'create'])->name('doc.surat-perintah-penyelidikan-document.create');
    Route::post('/create', [SuratPerintahPenyelidikanDocumentController::class, 'store'])->name('doc.surat-perintah-penyelidikan-document.store');
    Route::get('/{id}/edit', [SuratPerintahPenyelidikanDocumentController::class, 'edit'])->name('doc.surat-perintah-penyelidikan-document.edit');
    Route::post('/{id}/edit', [SuratPerintahPenyelidikanDocumentController::class, 'update'])->name('doc.surat-perintah-penyelidikan-document.update');
    Route::delete('/{id}/delete', [SuratPerintahPenyelidikanDocumentController::class, 'delete'])->name('doc.surat-perintah-penyelidikan-document.delete');
    Route::get('/{id}/download', [SuratPerintahPenyelidikanDocumentController::class, 'download'])->name('doc.surat-perintah-penyelidikan-document.download');

    Route::get('/api/internal-officers', [SuratPerintahPenyelidikanDocumentController::class, 'getInternalOfficers'])->name('doc.surat-perintah-penyelidikan-document.api.internal-officers');
    Route::get('/api/moved-officers', [SuratPerintahPenyelidikanDocumentController::class, 'getMovedOfficers'])->name('doc.surat-perintah-penyelidikan-document.api.moved-officers');
    Route::get('/api/external-officers', [SuratPerintahPenyelidikanDocumentController::class, 'getExternalOfficers'])->name('doc.surat-perintah-penyelidikan-document.api.external-officers');
    Route::get('/api/polices', [SuratPerintahPenyelidikanDocumentController::class, 'getPolices'])->name('doc.surat-perintah-penyelidikan-document.api.polices');
    Route::post('/api/validate-request-form', [SuratPerintahPenyelidikanDocumentController::class, 'validateRequestForm'])->name('doc.surat-perintah-penyelidikan-document.api.validate-request-form');
});

Route::prefix('/surat-perintah-penyidikan-document')->group(function(){
    Route::get('/', [SuratPerintahPenyidikanDocumentController::class, 'index'])->name('doc.surat-perintah-penyidikan-document.index');
    Route::get('/{id}/show', [SuratPerintahPenyidikanDocumentController::class, 'show'])->name('doc.surat-perintah-penyidikan-document.show');
    Route::get('/create', [SuratPerintahPenyidikanDocumentController::class, 'create'])->name('doc.surat-perintah-penyidikan-document.create');
    Route::post('/create', [SuratPerintahPenyidikanDocumentController::class, 'store'])->name('doc.surat-perintah-penyidikan-document.store');
    Route::get('/{id}/edit', [SuratPerintahPenyidikanDocumentController::class, 'edit'])->name('doc.surat-perintah-penyidikan-document.edit');
    Route::post('/{id}/edit', [SuratPerintahPenyidikanDocumentController::class, 'update'])->name('doc.surat-perintah-penyidikan-document.update');
    Route::delete('/{id}/delete', [SuratPerintahPenyidikanDocumentController::class, 'delete'])->name('doc.surat-perintah-penyidikan-document.delete');
    Route::get('/{id}/download', [SuratPerintahPenyidikanDocumentController::class, 'download'])->name('doc.surat-perintah-penyidikan-document.download');

    Route::get('/api/leader-officer', [SuratPerintahPenyidikanDocumentController::class, 'getLeaderOfficer'])->name('doc.surat-perintah-penyidikan-document.api.leader-officer');
    Route::get('/api/internal-officers', [SuratPerintahPenyidikanDocumentController::class, 'getInternalOfficers'])->name('doc.surat-perintah-penyidikan-document.api.internal-officers');
    Route::get('/api/moved-officers', [SuratPerintahPenyidikanDocumentController::class, 'getMovedOfficers'])->name('doc.surat-perintah-penyidikan-document.api.moved-officers');
    Route::get('/api/external-officers', [SuratPerintahPenyidikanDocumentController::class, 'getExternalOfficers'])->name('doc.surat-perintah-penyidikan-document.api.external-officers');
    Route::get('/api/polices', [SuratPerintahPenyidikanDocumentController::class, 'getPolices'])->name('doc.surat-perintah-penyidikan-document.api.polices');
    Route::post('/api/validate-request-form', [SuratPerintahPenyidikanDocumentController::class, 'validateRequestForm'])->name('doc.surat-perintah-penyidikan-document.api.validate-request-form');
});

Route::prefix('/surat-perintah-tugas-document')->group(function(){
    Route::get('/', [SuratPerintahTugasDocumentController::class, 'index'])->name('doc.surat-perintah-tugas-document.index');
    Route::get('/{id}/show', [SuratPerintahTugasDocumentController::class, 'show'])->name('doc.surat-perintah-tugas-document.show');
    Route::get('/create', [SuratPerintahTugasDocumentController::class, 'create'])->name('doc.surat-perintah-tugas-document.create');
    Route::post('/create', [SuratPerintahTugasDocumentController::class, 'store'])->name('doc.surat-perintah-tugas-document.store');
    Route::get('/{id}/edit', [SuratPerintahTugasDocumentController::class, 'edit'])->name('doc.surat-perintah-tugas-document.edit');
    Route::post('/{id}/edit', [SuratPerintahTugasDocumentController::class, 'update'])->name('doc.surat-perintah-tugas-document.update');
    Route::delete('/{id}/delete', [SuratPerintahTugasDocumentController::class, 'delete'])->name('doc.surat-perintah-tugas-document.delete');
    Route::get('/{id}/download', [SuratPerintahTugasDocumentController::class, 'download'])->name('doc.surat-perintah-tugas-document.download');

    Route::get('/api/related-document', [SuratPerintahTugasDocumentController::class, 'getRelatedDocument'])->name('doc.surat-perintah-tugas-document.api.related-document');
    Route::get('/api/officer', [SuratPerintahTugasDocumentController::class, 'getOfficer'])->name('doc.surat-perintah-tugas-document.api.officer');
    Route::get('/api/polices', [SuratPerintahTugasDocumentController::class, 'getPolices'])->name('doc.surat-perintah-tugas-document.api.polices');
    Route::post('/api/validate-request-form', [SuratPerintahTugasDocumentController::class, 'validateRequestForm'])->name('doc.surat-perintah-tugas-document.api.validate-request-form');
});

Route::prefix('/laporan-hasil-gelar-perkara-document')->middleware(['document-access'])->group(function(){
    Route::get('/', [LaporanHasilGelarPerkaraDocumentController::class, 'index'])->name('doc.laporan-hasil-gelar-perkara-document.index');
    Route::get('/{id}/show', [LaporanHasilGelarPerkaraDocumentController::class, 'show'])->name('doc.laporan-hasil-gelar-perkara-document.show');
    Route::get('/create', [LaporanHasilGelarPerkaraDocumentController::class, 'create'])->name('doc.laporan-hasil-gelar-perkara-document.create');
    Route::post('/create', [LaporanHasilGelarPerkaraDocumentController::class, 'store'])->name('doc.laporan-hasil-gelar-perkara-document.store');
    Route::get('/{id}/edit', [LaporanHasilGelarPerkaraDocumentController::class, 'edit'])->name('doc.laporan-hasil-gelar-perkara-document.edit');
    Route::post('/{id}/edit', [LaporanHasilGelarPerkaraDocumentController::class, 'update'])->name('doc.laporan-hasil-gelar-perkara-document.update');
    Route::delete('/{id}/delete', [LaporanHasilGelarPerkaraDocumentController::class, 'delete'])->name('doc.laporan-hasil-gelar-perkara-document.delete');
    Route::get('/{id}/download', [LaporanHasilGelarPerkaraDocumentController::class, 'download'])->name('doc.laporan-hasil-gelar-perkara-document.download');

    Route::get('/api/locations', [LaporanHasilGelarPerkaraDocumentController::class, 'getLocations'])->name('doc.laporan-hasil-gelar-perkara-document.api.locations');
    Route::get('/api/witnesses', [LaporanHasilGelarPerkaraDocumentController::class, 'getWitnesses'])->name('doc.laporan-hasil-gelar-perkara-document.api.witnesses');
    Route::get('/api/suspects', [LaporanHasilGelarPerkaraDocumentController::class, 'getSuspects'])->name('doc.laporan-hasil-gelar-perkara-document.api.suspects');
    Route::get('/api/case-degree-types', [LaporanHasilGelarPerkaraDocumentController::class, 'getCaseDegreeTypes'])->name('doc.laporan-hasil-gelar-perkara-document.api.case-degree-types');
    Route::post('/api/validate-request-form', [LaporanHasilGelarPerkaraDocumentController::class, 'validateRequestForm'])->name('doc.laporan-hasil-gelar-perkara-document.api.validate-request-form');
});

Route::prefix('/surat-ketetapan-tentang-penetapan-tersangka-document')->middleware(['document-access'])->group(function(){
    Route::get('/', [SuratKetetapanTentangPenetapanTersangkaDocumentController::class, 'index'])->name('doc.surat-ketetapan-tentang-penetapan-tersangka-document.index');
    Route::get('/{id}/show', [SuratKetetapanTentangPenetapanTersangkaDocumentController::class, 'show'])->name('doc.surat-ketetapan-tentang-penetapan-tersangka-document.show');
    Route::get('/create', [SuratKetetapanTentangPenetapanTersangkaDocumentController::class, 'create'])->name('doc.surat-ketetapan-tentang-penetapan-tersangka-document.create');
    Route::post('/create', [SuratKetetapanTentangPenetapanTersangkaDocumentController::class, 'store'])->name('doc.surat-ketetapan-tentang-penetapan-tersangka-document.store');
    Route::get('/{id}/edit', [SuratKetetapanTentangPenetapanTersangkaDocumentController::class, 'edit'])->name('doc.surat-ketetapan-tentang-penetapan-tersangka-document.edit');
    Route::post('/{id}/edit', [SuratKetetapanTentangPenetapanTersangkaDocumentController::class, 'update'])->name('doc.surat-ketetapan-tentang-penetapan-tersangka-document.update');
    Route::delete('/{id}/delete', [SuratKetetapanTentangPenetapanTersangkaDocumentController::class, 'delete'])->name('doc.surat-ketetapan-tentang-penetapan-tersangka-document.delete');
    Route::get('/{id}/download', [SuratKetetapanTentangPenetapanTersangkaDocumentController::class, 'download'])->name('doc.surat-ketetapan-tentang-penetapan-tersangka-document.download');

    Route::get('/api/locations', [SuratKetetapanTentangPenetapanTersangkaDocumentController::class, 'getLocations'])->name('doc.surat-ketetapan-tentang-penetapan-tersangka-document.api.locations');
    Route::get('/api/suspects', [SuratKetetapanTentangPenetapanTersangkaDocumentController::class, 'getSuspects'])->name('doc.surat-ketetapan-tentang-penetapan-tersangka-document.api.suspects');
    Route::post('/api/validate-request-form', [SuratKetetapanTentangPenetapanTersangkaDocumentController::class, 'validateRequestForm'])->name('doc.surat-ketetapan-tentang-penetapan-tersangka-document.api.validate-request-form');
});

Route::prefix('/surat-pemberitahuan-dimulainya-penyidikan-document')->middleware(['document-access'])->group(function(){
    Route::get('/', [SuratPemberitahuanDimulainyaPenyidikanDocumentController::class, 'index'])->name('doc.surat-pemberitahuan-dimulainya-penyidikan-document.index');
    Route::get('/{id}/show', [SuratPemberitahuanDimulainyaPenyidikanDocumentController::class, 'show'])->name('doc.surat-pemberitahuan-dimulainya-penyidikan-document.show');
    Route::get('/create', [SuratPemberitahuanDimulainyaPenyidikanDocumentController::class, 'create'])->name('doc.surat-pemberitahuan-dimulainya-penyidikan-document.create');
    Route::post('/create', [SuratPemberitahuanDimulainyaPenyidikanDocumentController::class, 'store'])->name('doc.surat-pemberitahuan-dimulainya-penyidikan-document.store');
    Route::get('/{id}/edit', [SuratPemberitahuanDimulainyaPenyidikanDocumentController::class, 'edit'])->name('doc.surat-pemberitahuan-dimulainya-penyidikan-document.edit');
    Route::post('/{id}/edit', [SuratPemberitahuanDimulainyaPenyidikanDocumentController::class, 'update'])->name('doc.surat-pemberitahuan-dimulainya-penyidikan-document.update');
    Route::delete('/{id}/delete', [SuratPemberitahuanDimulainyaPenyidikanDocumentController::class, 'delete'])->name('doc.surat-pemberitahuan-dimulainya-penyidikan-document.delete');
    Route::get('/{id}/download', [SuratPemberitahuanDimulainyaPenyidikanDocumentController::class, 'download'])->name('doc.surat-pemberitahuan-dimulainya-penyidikan-document.download');

    Route::post('/api/validate-request-form', [SuratPemberitahuanDimulainyaPenyidikanDocumentController::class, 'validateRequestForm'])->name('doc.surat-pemberitahuan-dimulainya-penyidikan-document.api.validate-request-form');
});

Route::prefix('/daftar-tersangka-document')->middleware(['document-access'])->group(function(){
    Route::get('/', [DaftarTersangkaDocumentController::class, 'index'])->name('doc.daftar-tersangka-document.index');
    Route::get('/{id}/show', [DaftarTersangkaDocumentController::class, 'show'])->name('doc.daftar-tersangka-document.show');
    Route::get('/create', [DaftarTersangkaDocumentController::class, 'create'])->name('doc.daftar-tersangka-document.create');
    Route::post('/create', [DaftarTersangkaDocumentController::class, 'store'])->name('doc.daftar-tersangka-document.store');
    Route::get('/{id}/edit', [DaftarTersangkaDocumentController::class, 'edit'])->name('doc.daftar-tersangka-document.edit');
    Route::post('/{id}/edit', [DaftarTersangkaDocumentController::class, 'update'])->name('doc.daftar-tersangka-document.update');
    Route::delete('/{id}/delete', [DaftarTersangkaDocumentController::class, 'delete'])->name('doc.daftar-tersangka-document.delete');
    Route::get('/{id}/download', [DaftarTersangkaDocumentController::class, 'download'])->name('doc.daftar-tersangka-document.download');

    Route::post('/api/validate-request-form', [DaftarTersangkaDocumentController::class, 'validateRequestForm'])->name('doc.daftar-tersangka-document.api.validate-request-form');
});

Route::prefix('/tahap-1-document')->middleware(['document-access'])->group(function(){
    Route::get('/', [Tahap1DocumentController::class, 'index'])->name('doc.tahap-1-document.index');
    Route::get('/{id}/show', [Tahap1DocumentController::class, 'show'])->name('doc.tahap-1-document.show');
    Route::get('/create', [Tahap1DocumentController::class, 'create'])->name('doc.tahap-1-document.create');
    Route::post('/create', [Tahap1DocumentController::class, 'store'])->name('doc.tahap-1-document.store');
    Route::get('/{id}/edit', [Tahap1DocumentController::class, 'edit'])->name('doc.tahap-1-document.edit');
    Route::post('/{id}/edit', [Tahap1DocumentController::class, 'update'])->name('doc.tahap-1-document.update');
    Route::delete('/{id}/delete', [Tahap1DocumentController::class, 'delete'])->name('doc.tahap-1-document.delete');
    Route::get('/{id}/download', [Tahap1DocumentController::class, 'download'])->name('doc.tahap-1-document.download');

    Route::post('/api/validate-request-form', [Tahap1DocumentController::class, 'validateRequestForm'])->name('doc.tahap-1-document.api.validate-request-form');
});

Route::prefix('/p19-document')->middleware(['document-access'])->group(function(){
    Route::get('/', [P19DocumentController::class, 'index'])->name('doc.p19-document.index');
    Route::get('/{id}/show', [P19DocumentController::class, 'show'])->name('doc.p19-document.show');
    Route::get('/create', [P19DocumentController::class, 'create'])->name('doc.p19-document.create');
    Route::post('/create', [P19DocumentController::class, 'store'])->name('doc.p19-document.store');
    Route::get('/{id}/edit', [P19DocumentController::class, 'edit'])->name('doc.p19-document.edit');
    Route::post('/{id}/edit', [P19DocumentController::class, 'update'])->name('doc.p19-document.update');
    Route::delete('/{id}/delete', [P19DocumentController::class, 'delete'])->name('doc.p19-document.delete');
    Route::get('/{id}/download', [P19DocumentController::class, 'download'])->name('doc.p19-document.download');

    Route::post('/api/validate-request-form', [P19DocumentController::class, 'validateRequestForm'])->name('doc.p19-document.api.validate-request-form');
});

Route::prefix('/p21-document')->middleware(['document-access'])->group(function(){
    Route::get('/', [P21DocumentController::class, 'index'])->name('doc.p21-document.index');
    Route::get('/{id}/show', [P21DocumentController::class, 'show'])->name('doc.p21-document.show');
    Route::get('/create', [P21DocumentController::class, 'create'])->name('doc.p21-document.create');
    Route::post('/create', [P21DocumentController::class, 'store'])->name('doc.p21-document.store');
    Route::get('/{id}/edit', [P21DocumentController::class, 'edit'])->name('doc.p21-document.edit');
    Route::post('/{id}/edit', [P21DocumentController::class, 'update'])->name('doc.p21-document.update');
    Route::delete('/{id}/delete', [P21DocumentController::class, 'delete'])->name('doc.p21-document.delete');
    Route::get('/{id}/download', [P21DocumentController::class, 'download'])->name('doc.p21-document.download');

    Route::post('/api/validate-request-form', [P21DocumentController::class, 'validateRequestForm'])->name('doc.p21-document.api.validate-request-form');
});

Route::prefix('/tahap-2-document')->middleware(['document-access'])->group(function(){
    Route::get('/', [Tahap2DocumentController::class, 'index'])->name('doc.tahap-2-document.index');
    Route::get('/{id}/show', [Tahap2DocumentController::class, 'show'])->name('doc.tahap-2-document.show');
    Route::get('/create', [Tahap2DocumentController::class, 'create'])->name('doc.tahap-2-document.create');
    Route::post('/create', [Tahap2DocumentController::class, 'store'])->name('doc.tahap-2-document.store');
    Route::get('/{id}/edit', [Tahap2DocumentController::class, 'edit'])->name('doc.tahap-2-document.edit');
    Route::post('/{id}/edit', [Tahap2DocumentController::class, 'update'])->name('doc.tahap-2-document.update');
    Route::delete('/{id}/delete', [Tahap2DocumentController::class, 'delete'])->name('doc.tahap-2-document.delete');
    Route::get('/{id}/download', [Tahap2DocumentController::class, 'download'])->name('doc.tahap-2-document.download');

    Route::post('/api/validate-request-form', [Tahap2DocumentController::class, 'validateRequestForm'])->name('doc.tahap-2-document.api.validate-request-form');
});

// SP2HP Document Routes (SP2HP Regulation)
Route::prefix('/sp2hp-document')->group(function(){
    Route::get('/list', [Sp2hpDocumentController::class, 'getList'])->name('doc.sp2hp-document.list');
    Route::get('/show/{id}', [Sp2hpDocumentController::class, 'show'])->name('doc.sp2hp-document.show');
    Route::get('/edit/{id}', [Sp2hpDocumentController::class, 'edit'])->name('doc.sp2hp-document.edit');
    Route::post('/store', [Sp2hpDocumentController::class, 'store'])->name('doc.sp2hp-document.store');
    Route::put('/update/{id}', [Sp2hpDocumentController::class, 'update'])->name('doc.sp2hp-document.update');
    Route::post('/destroy', [Sp2hpDocumentController::class, 'destroy'])->name('doc.sp2hp-document.destroy');
    Route::post('/submit', [Sp2hpDocumentController::class, 'submit'])->name('doc.sp2hp-document.submit');
    Route::get('/export-pdf/{id}', [Sp2hpDocumentController::class, 'exportPdf'])->name('doc.sp2hp-document.export-pdf');
    Route::post('/generate-nomor-surat', [Sp2hpDocumentController::class, 'generateNomorSurat'])->name('doc.sp2hp-document.generate-nomor-surat');
});

// Surat Pemberitahuan Perkembangan Hasil Penyidikan Document Routes (for document modal)
Route::prefix('/surat-pemberitahuan-perkembangan-hasil-penyidikan-document')->group(function(){
    Route::get('/create', [Sp2hpDocumentController::class, 'create'])->name('doc.surat-pemberitahuan-perkembangan-hasil-penyidikan-document.create');
    Route::get('/{id}/edit', [Sp2hpDocumentController::class, 'edit'])->name('doc.surat-pemberitahuan-perkembangan-hasil-penyidikan-document.edit');
    Route::get('/{id}/show', [Sp2hpDocumentController::class, 'downloadShow'])->name('doc.surat-pemberitahuan-perkembangan-hasil-penyidikan-document.download');
    Route::get('/{id}/download', [Sp2hpDocumentController::class, 'download'])->name('doc.surat-pemberitahuan-perkembangan-hasil-penyidikan-document.download');
    
    Route::get('/api/locations', [Sp2hpDocumentController::class, 'getLocations'])->name('doc.surat-pemberitahuan-perkembangan-hasil-penyidikan-document.api.locations');
});

// ─────────────────────────────────────────────────────────────────────────────
// SPDP Pusiknas — DIK-10 (SPPT-TI)
// ─────────────────────────────────────────────────────────────────────────────
Route::prefix('/spdp-pusiknas-document')->middleware(['document-access'])->group(function () {
    Route::get('/', [SpdpPusiknasDocumentController::class, 'index'])->name('doc.spdp-pusiknas-document.index');
    Route::get('/{id}/show', [SpdpPusiknasDocumentController::class, 'show'])->name('doc.spdp-pusiknas-document.show');
    Route::get('/create', [SpdpPusiknasDocumentController::class, 'create'])->name('doc.spdp-pusiknas-document.create');
    Route::post('/create', [SpdpPusiknasDocumentController::class, 'store'])->name('doc.spdp-pusiknas-document.store');
    Route::get('/{id}/edit', [SpdpPusiknasDocumentController::class, 'edit'])->name('doc.spdp-pusiknas-document.edit');
    Route::post('/{id}/edit', [SpdpPusiknasDocumentController::class, 'update'])->name('doc.spdp-pusiknas-document.update');
    Route::delete('/{id}/delete', [SpdpPusiknasDocumentController::class, 'delete'])->name('doc.spdp-pusiknas-document.delete');
    Route::get('/{id}/download', [SpdpPusiknasDocumentController::class, 'show'])->name('doc.spdp-pusiknas-document.download');

    Route::post('/api/validate-request-form', [SpdpPusiknasDocumentController::class, 'validateRequestForm'])
        ->name('doc.spdp-pusiknas-document.api.validate-request-form');
});

// ─────────────────────────────────────────────────────────────────────────────
// SP3 Pusiknas — DIK-40 (SPPT-TI)
// ─────────────────────────────────────────────────────────────────────────────
Route::prefix('/sp3-pusiknas-document')->middleware(['document-access'])->group(function () {
    Route::get('/', [Sp3PusiknasDocumentController::class, 'index'])->name('doc.sp3-pusiknas-document.index');
    Route::get('/{id}/show', [Sp3PusiknasDocumentController::class, 'show'])->name('doc.sp3-pusiknas-document.show');
    Route::get('/create', [Sp3PusiknasDocumentController::class, 'create'])->name('doc.sp3-pusiknas-document.create');
    Route::post('/create', [Sp3PusiknasDocumentController::class, 'store'])->name('doc.sp3-pusiknas-document.store');
    Route::get('/{id}/edit', [Sp3PusiknasDocumentController::class, 'edit'])->name('doc.sp3-pusiknas-document.edit');
    Route::post('/{id}/edit', [Sp3PusiknasDocumentController::class, 'update'])->name('doc.sp3-pusiknas-document.update');
    Route::delete('/{id}/delete', [Sp3PusiknasDocumentController::class, 'delete'])->name('doc.sp3-pusiknas-document.delete');
    Route::get('/{id}/download', [Sp3PusiknasDocumentController::class, 'show'])->name('doc.sp3-pusiknas-document.download');

    Route::post('/api/validate-request-form', [Sp3PusiknasDocumentController::class, 'validateRequestForm'])
        ->name('doc.sp3-pusiknas-document.api.validate-request-form');
});