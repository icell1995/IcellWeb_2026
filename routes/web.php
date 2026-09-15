<?php
use SimpleSoftwareIO\QrCode\Facades\QrCode;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\KDaftarLakaController;
use App\Http\Controllers\KDokumenController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\KPoldaController;
use App\Http\Controllers\KPolresController;
use App\Http\Controllers\KPangkatController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();
Route::get('search', [App\Http\Controllers\OfficerController::class, 'search'])->name('searchOfficer');
Route::get('search_user', [App\Http\Controllers\PenggunaController::class, 'search_user'])->name('searchUser');
Route::post('authenticate', [App\Http\Controllers\LoginController::class, 'authenticate'])->name('authenticate')->middleware('throttle:3,1');
Route::post('verifyOtp', [App\Http\Controllers\LoginController::class, 'verifyOtp'])->name('verifyOtp');
Route::post('resend-otp', [App\Http\Controllers\LoginController::class, 'resendOtp'])->name('resendOtp');
Route::get('forget-password', [App\Http\Controllers\LoginController::class, 'forget_password'])->name('forget-password');
Route::post('forget-password', [App\Http\Controllers\LoginController::class, 'post_forget_password'])->name('forget-password');
Route::get('refresh_captcha', [App\Http\Controllers\LoginController::class, 'refreshCaptcha'])->name('refresh_captcha');
// Route::get('reset-password/{token}', [App\Http\Controllers\LoginController::class, 'reset_password'])->name('reset-password');
// Route::post('reset-password', [App\Http\Controllers\LoginController::class, 'post_reset_password'])->name('reset-password');

Route::get('/dashboardicell', [App\Http\Controllers\DashboardicellController::class, 'index'])->name('index');
Route::get('/getDashBar', [App\Http\Controllers\DashboardicellController::class, 'getDashBar'])->name('getDashBar');
Route::get('/getDashPie', [App\Http\Controllers\DashboardicellController::class, 'getDashPie'])->name('getDashPie');
Route::get('/updateContent', [App\Http\Controllers\DashboardicellController::class, 'updateContent'])->name('updateContent');
// Route::get('/dashChartLine', [App\Http\Controllers\DashboardicellController::class, 'dashChartBar'])->name('dashChartLine');

// Route::get('/qr-codes', [App\Http\Controllers\QrCodeController::class, 'generateQRCode'])->name('generateQRCode');
// Route::get('/qr-codes', [App\Http\Controllers\QrCodeController::class, 'index'])->name('index');

// SSO IRSMS
Route::get('/sso-login', [\App\Http\Controllers\IcellServices\ApiIrsmsKorlantas\SsoLoginController::class, 'handleSSOLogin']);
Route::get('/sso/redirect/{target}', [\App\Http\Controllers\IcellServices\ApiIrsmsKorlantas\SsoLoginController::class, 'redirectTo'])->name('sso.redirect');
Route::get('/verify-token', [\App\Http\Controllers\IcellServices\ApiIrsmsKorlantas\SsoLoginController::class, 'verifyToken']);

Route::group(['middleware' => ['guest', 'prevent-back-history']], function () {
    Route::get('/', [App\Http\Controllers\LoginController::class, 'login'])->name('login');
    Route::get('/login', [App\Http\Controllers\LoginController::class, 'get_login'])->name('get_login');
});

Route::prefix('anev')->group(function () {
    Route::get('/', [App\Http\Controllers\AnevController::class, 'index_anev'])->name('index_anev');
    Route::get('/get_report_anev',  [App\Http\Controllers\AnevController::class, 'get_report_anev'])->name('get_report_anev');
    Route::get('/export_report_anev',  [App\Http\Controllers\AnevController::class, 'export_report_anev'])->name('export_report_anev');
    Route::get('/report_individu', [App\Http\Controllers\AnevController::class, 'report_individu'])->name('report_individu');
    Route::get('/get_report_individu',  [App\Http\Controllers\AnevController::class, 'get_report_individu'])->name('get_report_individu');
    Route::get('/export_report_individu',  [App\Http\Controllers\AnevController::class, 'export_report_individu'])->name('export_report_individu');
});

Route::group(['middleware' => ['auth', 'prevent-back-history']], function () {
    Route::prefix('/evaluation-form-fill')->group(function () {
        Route::get('/', [App\Http\Controllers\EvaluationFormController::class, 'index'])->name('evaluation-form-fill.index');
        Route::get('/redirect', [App\Http\Controllers\EvaluationFormController::class, 'redirect'])->name('evaluation-form-fill.redirect');
    });

    Route::prefix('/esignature-confirmation')->group(function () {
        Route::get('/', [App\Http\Controllers\ESignatureConfirmationController::class, 'index'])->name('esignature-confirmation.index');
        Route::post('/post', [App\Http\Controllers\ESignatureConfirmationController::class, 'post'])->name('esignature-confirmation.post');
    });

    Route::prefix('/forms')->group(function () {
        Route::get('/collect', [App\Http\Controllers\FormsController::class, 'formCollect'])->name('forms.collect');
        Route::post('/collect', [App\Http\Controllers\FormsController::class, 'formStore'])->name('forms.store');

        Route::get('/confirmation', [App\Http\Controllers\FormsController::class, 'formConfirmation'])->name('forms.confirmation');
        Route::post('/confirmation', [App\Http\Controllers\FormsController::class, 'formConfirmationStore'])->name('forms.confirmation.store');

        Route::get('/signatory/input', [App\Http\Controllers\FormsController::class, 'formSignatoryInput'])->name('forms.signatory.input');
        Route::post('/signatory/input', [App\Http\Controllers\FormsController::class, 'formSignatoryInputStore'])->name('forms.signatory.store');
        Route::delete('/signatory/input/{id}', [App\Http\Controllers\FormsController::class, 'formSignatoryInputDelete'])->name('forms.signatory.delete');
    });

    Route::group(['middleware' => ['is-evaluation-form-filled', 'is-signatory']], function () {
        Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
        Route::get('/dashboardlombaicell', [App\Http\Controllers\LombaDashboardController::class, 'index'])->name('lomba-dashboard');
        Route::get('/getChartBulan', [App\Http\Controllers\HomeController::class, 'getChartBulan'])->name('getChartBulan');
        Route::get('/getPieBulan', [App\Http\Controllers\HomeController::class, 'getPieBulan'])->name('getPieBulan');

        Route::get('/profile', [App\Http\Controllers\HomeController::class, 'profile'])->name('profile');
        Route::get('/reset_password', [App\Http\Controllers\HomeController::class, 'reset_password'])->name('reset_password');
        Route::post('/post-reset_password', [App\Http\Controllers\HomeController::class, 'post_reset_password'])->name('post_reset_password');
        Route::post('/update_profile', [App\Http\Controllers\HomeController::class, 'update_profile'])->name('update_profile');

        Route::group(['middleware' => 'can:manage-permissions'], function () {
            Route::prefix('permission')->group(function () {
                Route::get('/', [App\Http\Controllers\PermissionController::class, 'index'])->name('permission');
                Route::get('/permission-add', [App\Http\Controllers\PermissionController::class, 'add'])->name('permission-add');
                Route::post('/permission_add', [App\Http\Controllers\PermissionController::class, 'add_permission'])->name('permission_add');
                Route::get('/permission-edit/{id}', [App\Http\Controllers\PermissionController::class, 'edit'])->name('permission-edit');
                Route::post('/permission_edit', [App\Http\Controllers\PermissionController::class, 'edit_permission'])->name('permission_edit');
                Route::get('/edit_modal_permission', [App\Http\Controllers\PermissionController::class, 'edit_modal_permission'])->name('edit_modal_permission');
                // Route::get('/save', [App\Http\Controllers\PermissionController::class, 'save'])->name('save');
            });
        });

        Route::group(['middleware' => 'can:manage-roles'], function () {
            Route::prefix('role')->group(function () {
                Route::get('/', [App\Http\Controllers\RoleController::class, 'index'])->name('role');
                Route::get('/edit/{id}', [App\Http\Controllers\RoleController::class, 'edit'])->name('edit');
                Route::post('/update', [App\Http\Controllers\RoleController::class, 'update'])->name('update');
                Route::get('/role-add', [App\Http\Controllers\RoleController::class, 'add_role'])->name('role-add');
                Route::post('/role_add', [App\Http\Controllers\RoleController::class, 'submit_add_role'])->name('role_add');
            });
        });

        Route::group(['middleware' => 'can:manage-users'], function () {

            Route::prefix('pengguna')->group(function () {
                Route::get('/', [App\Http\Controllers\PenggunaController::class, 'index'])->name('pengguna');
                Route::get('/pengguna-add', [App\Http\Controllers\PenggunaController::class, 'add'])->name('pengguna-add');
                Route::get('polres_list/{poldaId}', [App\Http\Controllers\PenggunaController::class, 'polres_list'])->name('polres_list');
                // function($poldaId) {
                //     $polda = App\Polda::find($poldaId);
                //     $polres = $polda->polres()->get();
                //     return response()->json($polres);
                // });
                Route::post('/pengguna_add', [App\Http\Controllers\PenggunaController::class, 'add_pengguna'])->name('pengguna_add');
                Route::get('/pengguna-edit/{id}', [App\Http\Controllers\PenggunaController::class, 'edit'])->name('pengguna-edit');
                Route::post('/pengguna_edit', [App\Http\Controllers\PenggunaController::class, 'edit_pengguna'])->name('pengguna_edit');
                Route::get('/pengguna_delete/{id}', [App\Http\Controllers\PenggunaController::class, 'delete_pengguna'])->name('pengguna_delete');
                Route::get('/edit_modal_pengguna', [App\Http\Controllers\PenggunaController::class, 'edit_modal_pengguna'])->name('edit_modal_pengguna');
            });

            Route::prefix('petugas')->group(function () {
                Route::get('/', [App\Http\Controllers\OfficerController::class, 'index'])->name('petugas');
                Route::get('/petugas-add', [App\Http\Controllers\OfficerController::class, 'add'])->name('petugas-add');
                Route::post('/petugas_add', [App\Http\Controllers\OfficerController::class, 'add_petugas'])->name('petugas_add');
                Route::get('/petugas-edit/{id}', [App\Http\Controllers\OfficerController::class, 'edit'])->name('petugas-edit');
                Route::post('/petugas_edit', [App\Http\Controllers\OfficerController::class, 'edit_petugas'])->name('petugas_edit');
                Route::get('/petugas_delete/{id}', [App\Http\Controllers\OfficerController::class, 'delete_petugas'])->name('petugas_delete');
                Route::get('/edit_modal_petugas', [App\Http\Controllers\OfficerController::class, 'edit_modal_petugas'])->name('edit_modal_petugas');
                Route::get('/export_officer', [App\Http\Controllers\OfficerController::class, 'export_petugas'])->name('export_petugas');
            });

            Route::prefix('signatories')->group(function () {
                Route::get('/', [App\Http\Controllers\SignatoryController::class, 'index'])->name('signatories');
                Route::get('/create', [App\Http\Controllers\SignatoryController::class, 'create'])->name('signatories.create');
                Route::post('/create', [App\Http\Controllers\SignatoryController::class, 'store'])->name('signatories.store');
                Route::get('/{id}/edit', [App\Http\Controllers\SignatoryController::class, 'edit'])->name('signatories.edit');
                Route::put('/{id}/edit', [App\Http\Controllers\SignatoryController::class, 'update'])->name('signatories.update');
                Route::delete('/{id}/delete', [App\Http\Controllers\SignatoryController::class, 'destroy'])->name('signatories.destroy');
            });

            // Route::prefix('polda')->group(function () {
            Route::resource('/polda', KPoldaController::class);
            // });

        });

        Route::group(['middleware' => 'can:view-data'], function () {
            // KATALOG -> Daftar Laka

            // Route::get('titik-acuan', [App\Http\Controllers\KDaftarLakaController::class, 'index'])->name('titik-acuan');
            //katalog - daftar
            Route::resource('/titik-acuan', KDaftarLakaController::class);
            // Route::delete('/titik-acuan/{id}', [KDaftarLakaController::class, 'destroy'])->name('titik-acuan.destroy'); // Laravel 8
            Route::resource('/tipe-kecelakaan', KDaftarLakaController::class);
            Route::resource('/kondisi-cahaya', KDaftarLakaController::class);
            Route::resource('/pengaturan-simpang', KDaftarLakaController::class);
            Route::resource('/kerusakan', KDaftarLakaController::class);
            Route::resource('/pendidikan', KDaftarLakaController::class);

            //katalog - dokumen
            Route::resource('/dpo-dpb', KDokumenController::class);
            Route::resource('/labfor', KDokumenController::class);
            Route::resource('/penahanan', KDokumenController::class);
            Route::resource('/penggeledahan', KDokumenController::class);
            Route::resource('/penyegelan', KDokumenController::class);
            Route::resource('/penyitaan', KDokumenController::class);
            Route::resource('/rekening-bank', KDokumenController::class);
            Route::resource('/saksi', KDokumenController::class);
            Route::resource('/tersangka', KDokumenController::class);

            //katalog - polda
            Route::resource('/polda', KPoldaController::class);

            //katalog - polres
            Route::resource('/polres', KPolresController::class);
            // Route::post('/polres/search',KPolresController::class,'search');
            Route::post('/polres/search', [App\Http\Controllers\KPolresController::class, 'search'])->name('polres_search');

            //katalog pangkat
            Route::resource('/pangkat', KPangkatController::class);


            //kategori 1
            // Route::resource('/surat-tugas', PdfController::class);
            Route::resource('/springas', PdfController::class);
            Route::resource('/surat-penyelidikan', PdfController::class);
            Route::resource('/surat-penyidikan', PdfController::class);
            // Route::resource('/surat-spdp', PdfController::class);
            Route::resource('/spdp', PdfController::class);
            Route::resource('/laporan_polisi', PdfController::class);
            Route::resource('/BA_Penangkapan', PdfController::class);
            Route::resource('/BA_Pemotretan', PdfController::class);
            Route::resource('/BA-pengambilan-darah', PdfController::class);
            Route::resource('/laporan-hasil-penyelidikan', PdfController::class);
            Route::resource('/BA-introgasi', PdfController::class);
            Route::resource('/SPDP-Upload', PdfController::class);

            //kategori 2
            Route::resource('/surat-panggilan-saksi', PdfController::class);
            Route::resource('/surat-perintah-membawa-saksi', PdfController::class);
            Route::resource('/berita-acara-membawa-saksi', PdfController::class);
            Route::resource('/berita-acara-penyumpahan-saksi', PdfController::class);
            Route::resource('/berita-pemeriksaan-saksi', PdfController::class);
            Route::resource('/berita-pemeriksaan-ahli', PdfController::class);

            //kategori 3
            Route::resource('/surat-panggilan-tersangka', PdfController::class);
            // Route::resource('/surat-perintah-penangkapan', PdfController::class);
            Route::resource('/berita-pemeriksaan-tersangka', PdfController::class);
            Route::resource('/berita-acara-konfrontasi', PdfController::class);
            Route::resource('/berita-acara-rekonstruksi', PdfController::class);
            Route::resource('/sket-tkp', PdfController::class);
            Route::resource('/surat-bantuan-penangkapan', PdfController::class);
            Route::resource('/penyerahan-tersangka', PdfController::class);
            // Route::resource('/pelepasan-tersangka', PdfController::class);

            //kategori 4
            Route::resource('/surat-perintah-penahanan', PdfController::class);
            Route::resource('/berita-acara-penahanan', PdfController::class);
            Route::resource('/perpanjangan-penahanan-hakim', PdfController::class);
            Route::resource('/surat-perpanjangan-penahanan', PdfController::class);
            Route::resource('/berita-pengeluaran-penahanan', PdfController::class);
            Route::resource('/surat-pembatalan-penahanan', PdfController::class);
            Route::resource('/pencabutan-pembatalan-penahanan', PdfController::class);
            Route::resource('/berita-pembatalan-penahanan', PdfController::class);
            Route::resource('/penahanan-lanjutan', PdfController::class);
            Route::resource('/berita-penahanan-lanjutan', PdfController::class);

            //kategori 5
            Route::resource('/permintaan-izin-penggeledahan', PdfController::class);
            Route::resource('/perintah-penggeledahan', PdfController::class);
            Route::resource('/persetujuan-penggeledahan', PdfController::class);
            Route::resource('/berita-penggeledahan', PdfController::class);

            //kategori 6
            Route::resource('/surat-izin-penyitaan', PdfController::class);
            Route::resource('/surat-persetujuan-penyitaan', PdfController::class);
            Route::resource('/daftar-barang-bukti', PdfController::class);
            Route::resource('/surat-penyitaan', PdfController::class);
            Route::resource('/berita-acara-penyitaan', PdfController::class);
            Route::resource('/surat-pengiriman-berkas-perkara', PdfController::class);
            Route::resource('/tanda-terima-berkas-perkara', PdfController::class);
            Route::resource('/pengiriman-barang-bukti', PdfController::class);
            Route::resource('/berita-acara-terima-tersangka', PdfController::class);
            Route::resource('/surat-bantuan-penyelidikan', PdfController::class);
            Route::resource('/surat-pentitipan-barang', PdfController::class);
            Route::resource('/surat-pengembalian-sitaan', PdfController::class);
            Route::resource('/berita-penitipan-barang', PdfController::class);
            Route::resource('/berita-pengembalian-sitaan', PdfController::class);
            Route::resource('/ketetapan-ijin-penyitaan', PdfController::class);
            Route::resource('/ketetapan-persetujuan-penyitaan', PdfController::class);
            Route::resource('/surat-tanda-penerimaan', PdfController::class);
            Route::resource('/surat-pengantar', PdfController::class);
            Route::resource('/berita-penyerahan-berkas', PdfController::class);
            Route::resource('/laporan-gelar-perkara', PdfController::class);
            Route::resource('/laporan-perkara-khusus', PdfController::class);

            //kategori 7
            Route::resource('/surat-persetujuan-penyegelan', PdfController::class);
            Route::resource('/berita-acara-penyegelan', PdfController::class);
            Route::resource('/surat-penyegelan', PdfController::class);

            //kategori 8
            Route::resource('/surat-permintaan-bantuan-labfor', PdfController::class);
            Route::resource('/surat-hasil-pemeriksaan-labfor', PdfController::class);
            Route::resource('/surat-bantuan-identifikasi', PdfController::class);
            Route::resource('/surat-pemeriksaan-identifikasi', PdfController::class);
            Route::resource('/ketetapan-khusus-surat', PdfController::class);
            Route::resource('/perintah-pemeriksaan-surat', PdfController::class);
            Route::resource('/berita-pemeriksaan-surat', PdfController::class);

            //kategori 9
            Route::resource('/surat-blokir-rekening-bank', PdfController::class);
            Route::resource('/berita-acara-blokir', PdfController::class);
            Route::resource('/surat-pembukaan-blokir', PdfController::class);
            Route::resource('/berita-acara-pembukaan-blokir', PdfController::class);

            //kategori 10
            Route::resource('/surat-pencabutan-tersangka', PdfController::class);
            Route::resource('/surat-pencabutan-barang', PdfController::class);

            //kategori 11
            Route::resource('/surat-perintah-penyelidikan', PdfController::class);
            Route::resource('/surat-ketetapan-penyelidikan', PdfController::class);
            Route::resource('/surat-pencabutan-penyelidikan', PdfController::class);
            Route::resource('/surat-penyelidikan-lanjutan', PdfController::class);
            Route::resource('/berita-penghentian-penyelidikan', PdfController::class);
            Route::resource('/persetujuan-pejabat-berwenang', PdfController::class);
            Route::resource('/surat-perintah-penyidikan', PdfController::class);
            Route::resource('/surat-ketetapan-penyidikan', PdfController::class);
            Route::resource('/putusan-pra-peradilan', PdfController::class);
            Route::resource('/surat-pencabutan-penyidikan', PdfController::class);
            Route::resource('/surat-penyidikan-lanjutan', PdfController::class);
            Route::resource('/berita-penghentian-penyidikan', PdfController::class);
            Route::resource('/surat-pernyataan', PdfController::class);
            Route::resource('/surat-kesepakatan-perdamaian', PdfController::class);
            Route::resource('/upload-surat-ketetapan', PdfController::class);

            //kategori 12
            Route::resource('/surat-penetapan-tersangka', PdfController::class);
            Route::resource('/surat-perintah-penangkapan', PdfController::class);
            Route::resource('/surat-membawa-menghadapkan', PdfController::class);
            Route::resource('/surat-pelepasan-tersangka', PdfController::class);
            Route::resource('/berita-acara-penangkapan', PdfController::class);
            Route::resource('/pelepasan-tersangka', PdfController::class);

            //kategori 13
            Route::resource('/surat-p21-tahap-1', PdfController::class);
            Route::resource('/surat-p21-tahap-2', PdfController::class);

            Route::prefix('accident')->group(function () {
                Route::get('/', [App\Http\Controllers\AccidentController::class, 'index'])->name('accident');
                Route::get('/search', [App\Http\Controllers\AccidentController::class, 'search'])->name('search_accident');
                Route::get('/view', [App\Http\Controllers\AccidentController::class, 'view'])->name('view_accident');
                Route::post('/save', [App\Http\Controllers\AccidentController::class, 'save'])->name('save_accident');
            });

            Route::prefix('statistika')->group(function () {
                // Route::get('/', [App\Http\Controllers\statistikaController::class, 'index'])->name('statistika');
                Route::get('/month', [App\Http\Controllers\statistikaController::class, 'index_month'])->name('index_month');
                Route::post('/get_months', [App\Http\Controllers\statistikaController::class, 'get_months'])->name('get_months');
                Route::get('/ExportMonth', [App\Http\Controllers\ExportController::class, 'ExportMonth'])->name('ExportMonth');
                Route::get('/ExportDays', [App\Http\Controllers\ExportController::class, 'ExportDays'])->name('ExportDays');
                Route::get('/ExportWeeks', [App\Http\Controllers\ExportController::class, 'ExportWeeks'])->name('ExportWeeks');
                Route::get('/chartcalculationMonth', [App\Http\Controllers\statistikaController::class, 'chartcalculationMonth'])->name('chartcalculationMonth');
                Route::get('/chartcalculationWeek', [App\Http\Controllers\statistikaController::class, 'chartcalculationWeek'])->name('chartcalculationWeek');
                Route::post('/chartcalculationDays', [App\Http\Controllers\statistikaController::class, 'chartcalculationDays'])->name('chartcalculationDays');
                Route::post('/get_weeks', [App\Http\Controllers\statistikaController::class, 'get_weeks'])->name('get_weeks');
                Route::post('/get_days', [App\Http\Controllers\statistikaController::class, 'get_days'])->name('get_days');
                Route::get('/week', [App\Http\Controllers\statistikaController::class, 'index_week'])->name('index_week');
                Route::get('/day', [App\Http\Controllers\statistikaController::class, 'index_day'])->name('index_day');
            });

            // Route::prefix('pdf')->group(function () {
            //     Route::get('/get-pdf', [App\Http\Controllers\PdfController::class, 'get_pdf'])->name('get_pdf');
            // });

            Route::prefix('rekap')->group(function () {
                Route::get('/', [App\Http\Controllers\rekapController::class, 'index'])->name('rekap');
                Route::get('/rekap-show/{id}', [App\Http\Controllers\rekapController::class, 'show'])->name('rekap-show');
                // Route::get('/search', [App\Http\Controllers\rekapController::class, 'index'])->name('rekap-search');

                // Client-side data source (semua baris, untuk export via DataTables)
                Route::get('/api/list', [App\Http\Controllers\rekapController::class, 'listAll'])->name('rekap.api.list');

                // Export
                // Route::get('/export', [App\Http\Controllers\ExportRekapController::class, 'ExportRekap'])->name('ExportRekap');
                Route::post('/add-surat-tugas', [App\Http\Controllers\AccidentController::class, 'add_surat_tugas'])->name('add_surat_tugas');
                Route::post('/autocomplete/fetch', [App\Http\Controllers\AccidentController::class, 'fetch'])->name('autocomplete.fetch');
                Route::get('file-upload', [App\Http\Controllers\FileUploadController::class, 'fileUpload'])->name('file.upload');
                Route::post('file-upload', [App\Http\Controllers\FileUploadController::class, 'fileUploadPost'])->name('file.upload.post');
                // Route::post('/petugas/getPetugas/', [App\Http\Controllers\AccidentController::class, 'getPetugas'])->name('get_petugas');
            });

            Route::prefix('wilayah')->group(function () {
                Route::get('/', [App\Http\Controllers\daftarWilayahController::class, 'index'])->name('index');
            });

            Route::prefix('organisasi')->group(function () {
                Route::get('/', [App\Http\Controllers\SturkturOrganisasiController::class, 'index'])->name('index');
            });

            Route::get('/createword-surat-tugas/{id}', [App\Http\Controllers\WordController::class, 'createword_surat_tugas'])->name('createword_surat_tugas');
            Route::get('/createword-surat-penyitaan/{id}', [App\Http\Controllers\WordController::class, 'createword_surat_penyitaan'])->name('createword_surat_penyitaan');
            Route::get('/createword-springas/{id}', [App\Http\Controllers\WordController::class, 'createword_springas'])->name('createword_springas');
            Route::get('/createword-surat-penyelidikan/{id}', [App\Http\Controllers\WordController::class, 'createword_surat_penyelidikan'])->name('createword_surat_penyelidikan');
            Route::get('/createword-surat-penyidikan/{id}', [App\Http\Controllers\WordController::class, 'createword_surat_penyidikan'])->name('createword_surat_penyidikan');
            Route::get('/createword-lhgp/{id}', [App\Http\Controllers\WordController::class, 'createword_lhgp'])->name('createword_lhgp');
            Route::get('/createword-sddl/{id}', [App\Http\Controllers\WordController::class, 'createword_sddl'])->name('createword_sddl');
            Route::get('/createword-surat-spdp/{id}', [App\Http\Controllers\WordController::class, 'createword_surat_spdp'])->name('createword_surat_spdp');
            Route::get('/createword-surat-p21-tahap-1/{id}', [App\Http\Controllers\WordController::class, 'createword_surat_p21_tahap_1'])->name('createword_surat_p21_tahap_1');
            Route::get('/createword-surat-p21-tahap-2/{id}', [App\Http\Controllers\WordController::class, 'createword_surat_p21_tahap_2'])->name('createword_surat_p21_tahap_2');
            Route::get('/createword-sp3/{id}', [App\Http\Controllers\WordController::class, 'createword_sp3'])->name('createword_sp3');
            Route::get('/daftar-saksi/{id}', [App\Http\Controllers\WordController::class, 'daftarSaksi'])->name('daftarSaksi');
            Route::get('/daftar-tersangka/{id}', [App\Http\Controllers\WordController::class, 'daftarTersangka'])->name('daftarTersangka');

            Route::prefix('dpo')->group(function () {
                Route::get('/', [App\Http\Controllers\DpoController::class, 'index_dpo'])->name('index_dpo');
                Route::get('/list-dpo', [App\Http\Controllers\DpoController::class, 'list_dpo'])->name('list_dpo');
                Route::get('/search-dpo', [App\Http\Controllers\DpoController::class, 'search_dpo'])->name('search_dpo');
            });

            Route::prefix('dpb')->group(function () {
                Route::get('/', [App\Http\Controllers\DpbController::class, 'index_dpb'])->name('index_dpb');
                Route::get('/list-dpb', [App\Http\Controllers\DpbController::class, 'list_dpb'])->name('list_dpb');
                Route::get('/search-dpb', [App\Http\Controllers\DpbController::class, 'search_dpb'])->name('search_dpb');
            });

            Route::prefix('caraousel')->group(function () {
                Route::get('/caraousel', [App\Http\Controllers\ImageCarouselController::class, 'index'])->name('carousel_index');
                Route::get('/add_image', [App\Http\Controllers\ImageCarouselController::class, 'add_image'])->name('add_image_carousel');
                Route::post('/save_image', [App\Http\Controllers\ImageCarouselController::class, 'save_image'])->name('save_image_carousel');
                Route::post('/deleteCarousel/{name}', [App\Http\Controllers\ImageCarouselController::class, 'deleteImage'])->name('deleteCarousel');
            });

            Route::prefix('organisasi')->group(function (){
                Route::get('/struktur-organisasi', [App\Http\Controllers\SturkturOrganisasiController::class, 'index'])->name('index');
                Route::post('/struktur-organisasi',[App\Http\Controllers\SturkturOrganisasiController::class, 'store'])->name('store');
                Route::post('/delete_img/{name}',[App\Http\Controllers\SturkturOrganisasiController::class, 'delete_img'])->name('delete_img');
            });
        });

        Route::get('/2', [App\Http\Controllers\KPoldaController::class, 'index'])->name('2');
        Route::get('/3', [App\Http\Controllers\KPoldaController::class, 'index'])->name('3');
        Route::get('/4', [App\Http\Controllers\KPoldaController::class, 'index'])->name('4');
        Route::get('/5', [App\Http\Controllers\KPoldaController::class, 'index'])->name('5');


        // Route::get('/login', [App\Http\Controllers\LoginController::class, 'index'])->name('login');


        // Route::post('/postlogin', [App\Http\Controllers\LoginController::class, 'authenticate']);
	
	Route::prefix('/commander-wish')->group(function(){
            Route::get('/', [App\Http\Controllers\CommanderWishController::class, 'index'])->name('commander-wish.index');

            Route::get('/api/resort-polices', [App\Http\Controllers\CommanderWishController::class, 'getResortPolices'])->name('commander-wish.api.resort-polices');
        });

        Route::prefix('/personnel')->group(function(){
            Route::get('/', [App\Http\Controllers\PersonnelController::class, 'index'])->name('personnel.index');
	    Route::get('/certification', [App\Http\Controllers\PersonnelController::class, 'certification'])->name('personnel.certification');
	    Route::get('/signatory', [App\Http\Controllers\PersonnelController::class, 'signatory'])->name('personnel.signatory');
            Route::get('/{id}/show', [App\Http\Controllers\PersonnelController::class, 'show'])->name('personnel.show');
            Route::get('/{id}/validation', [App\Http\Controllers\PersonnelController::class, 'validation'])->name('personnel.validation');
            Route::post('/{id}/validation', [App\Http\Controllers\PersonnelController::class, 'validationProcess'])->name('personnel.validation.process');
            Route::get('/create', [App\Http\Controllers\PersonnelController::class, 'create'])->name('personnel.create');
            Route::post('/create', [App\Http\Controllers\PersonnelController::class, 'store'])->name('personnel.store');
            Route::get('/{id}/edit', [App\Http\Controllers\PersonnelController::class, 'edit'])->name('personnel.edit');
            Route::post('/{id}/edit', [App\Http\Controllers\PersonnelController::class, 'update'])->name('personnel.update');
            Route::get('/{id}/move', [App\Http\Controllers\PersonnelController::class, 'move'])->name('personnel.move');
            Route::put('/{id}/move', [App\Http\Controllers\PersonnelController::class, 'updateMove'])->name('personnel.move.update');
            Route::get('/{id}/change-password', [App\Http\Controllers\PersonnelController::class, 'changePassword'])->name('personnel.change-password');
            Route::put('/{id}/change-password', [App\Http\Controllers\PersonnelController::class, 'updatePassword'])->name('personnel.update-password');

            Route::get('/api/polices', [App\Http\Controllers\PersonnelController::class, 'getPolices'])->name('personnel.api.polices');
            Route::get('/api/polices/search', [App\Http\Controllers\PersonnelController::class, 'getSearchPolices'])->name('personnel.api.polices.search');
            Route::get('/api/ranks', [App\Http\Controllers\PersonnelController::class, 'getRanks'])->name('personnel.api.ranks');
            Route::get('/api/positions', [App\Http\Controllers\PersonnelController::class, 'getPositions'])->name('personnel.api.positions');
            Route::get('/api/check-officer', [App\Http\Controllers\PersonnelController::class, 'checkOfficer'])->name('personnel.api.check-officer');
            Route::post('/api/validate-request-form', [App\Http\Controllers\PersonnelController::class, 'validateRequestForm'])->name('personnel.api.validate-request-form');
            Route::post('/api/validate-request-move-form', [App\Http\Controllers\PersonnelController::class, 'validateRequestMoveForm'])->name('personnel.api.validate-request-move-form');
        });

        Route::prefix('/document-action')->group(function(){
            Route::prefix('/request-approval')->group(function(){
                Route::get('/request', [App\Http\Controllers\DocumentActionController::class, 'requestApprovalRequest'])->name('document-action.request-approval.request');
                Route::put('/request', [App\Http\Controllers\DocumentActionController::class, 'requestApprovalRequestSave'])->name('document-action.request-approval.request.save');
            });

            Route::prefix('/upload-document')->group(function(){
                Route::get('/upload', [App\Http\Controllers\DocumentActionController::class, 'uploadDocumentUpload'])->name('document-action.upload-document.upload');
                Route::put('/upload', [App\Http\Controllers\DocumentActionController::class, 'uploadDocumentUploadSave'])->name('document-action.upload-document.upload.save');
            });

            Route::prefix('/document-preview')->group(function(){
                Route::get('/view', [App\Http\Controllers\DocumentActionController::class, 'documentPreviewView'])->name('document-action.document-preview.view');
            });
        });

        Route::prefix('document-signature')->group(function (){
            Route::get('/', [App\Http\Controllers\DocumentSignatureController::class, 'index'])->name('document-signature.index');
            Route::get('/view', [App\Http\Controllers\DocumentSignatureController::class, 'view'])->name('document-signature.view');
            Route::get('/sign', [App\Http\Controllers\DocumentSignatureController::class, 'sign'])->name('document-signature.sign');
            Route::post('/sign', [App\Http\Controllers\DocumentSignatureController::class, 'signProcess'])->name('document-signature.sign.process');
            Route::post('/store', [App\Http\Controllers\DocumentSignatureController::class, 'store'])->name('document-signature.store');
            Route::get('/print', [App\Http\Controllers\DocumentSignatureController::class, 'print'])->name('document-signature.print');

            Route::prefix('/verification')->group(function (){
                Route::get('/', [App\Http\Controllers\DocumentSignatureController::class, 'verificationIndex'])->name('document-signature.verification.index');
                Route::get('/view', [App\Http\Controllers\DocumentSignatureController::class, 'verificationView'])->name('document-signature.verification.view');
                Route::put('/view', [App\Http\Controllers\DocumentSignatureController::class, 'verificationSave'])->name('document-signature.verification.save');
                Route::get('/finish', [App\Http\Controllers\DocumentSignatureController::class, 'verificationFinish'])->name('document-signature.verification.finish');
                Route::put('/finish', [App\Http\Controllers\DocumentSignatureController::class, 'verificationFinishSave'])->name('document-signature.verification.finish.save');

                Route::put('/rollback', [App\Http\Controllers\DocumentSignatureController::class, 'verificationRollback'])->name('document-signature.verification.rollback');
            });
        });

        Route::prefix('document-approval')->group(function (){
            Route::get('/', [App\Http\Controllers\DocumentApprovalController::class, 'index'])->name('document-approval.index');
            Route::get('/view', [App\Http\Controllers\DocumentApprovalController::class, 'view'])->name('document-approval.view');
            Route::put('/view', [App\Http\Controllers\DocumentApprovalController::class, 'save'])->name('document-approval.save');

            Route::prefix('/upload')->group(function (){
                Route::get('/', [App\Http\Controllers\DocumentApprovalController::class, 'uploadIndex'])->name('document-approval.upload.index');
                Route::get('/view', [App\Http\Controllers\DocumentApprovalController::class, 'uploadView'])->name('document-approval.upload.view');
                Route::put('/view', [App\Http\Controllers\DocumentApprovalController::class, 'uploadSave'])->name('document-approval.upload.save');
            });
        });
    });
});

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->middleware('auth')->name('home');

// Route::get('/profile', [App\Http\Controllers\HomeController::class, 'profile'])->middleware('auth')->name('profile');
