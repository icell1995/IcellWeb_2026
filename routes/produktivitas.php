<?php
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Letters\InvestigationOrderLetterController;
use App\Http\Controllers\Letters\InvestigationWarrantController;
use App\Http\Controllers\SuratTugasController;
use App\Http\Controllers\DocumentSignatureController;
use App\Http\Controllers\Letters\SPDPController;
use App\Models\SuratTugas;

// =================================================================================================

Route::group(['middleware' => ['is-evaluation-form-filled', 'is-signatory']], function () {
    Route::prefix('/investigation-order-letter')->group(function(){
        Route::get('/', [InvestigationOrderLetterController::class, 'index'])->name('investigation-order-letter.index');
        Route::get('/show', [InvestigationOrderLetterController::class, 'show'])->name('investigation-order-letter.show');
        Route::get('/create', [InvestigationOrderLetterController::class, 'create'])->name('investigation-order-letter.create');
        Route::post('/store', [InvestigationOrderLetterController::class, 'store'])->name('investigation-order-letter.store');
        Route::get('/edit', [InvestigationOrderLetterController::class, 'edit'])->name('investigation-order-letter.edit');
        Route::post('/update', [InvestigationOrderLetterController::class, 'update'])->name('investigation-order-letter.update');
        Route::delete('/delete', [InvestigationOrderLetterController::class, 'delete'])->name('investigation-order-letter.delete');
        Route::get('/print', [InvestigationOrderLetterController::class, 'print'])->name('investigation-order-letter.print');
    });

    Route::prefix('/investigation-warrant')->group(function(){
        Route::get('/', [InvestigationWarrantController::class, 'index'])->name('investigation-warrant.index');
        Route::get('/show', [InvestigationWarrantController::class, 'show'])->name('investigation-warrant.show');
        Route::get('/create', [InvestigationWarrantController::class, 'create'])->name('investigation-warrant.create');
        Route::post('/store', [InvestigationWarrantController::class, 'store'])->name('investigation-warrant.store');
        Route::get('/edit', [InvestigationWarrantController::class, 'edit'])->name('investigation-warrant.edit');
        Route::post('/update', [InvestigationWarrantController::class, 'update'])->name('investigation-warrant.update');
        Route::delete('/delete', [InvestigationWarrantController::class, 'delete'])->name('investigation-warrant.delete');
        Route::get('/print', [InvestigationWarrantController::class, 'print'])->name('investigation-warrant.print');
    });

    Route::prefix('laporan-hasil-gelar-perkara')->group(function(){
        Route::get('/lhgp_show',[SuratTugasController::class, 'lhgp_show'])->name('lhgp.show');
        Route::get('/lhgp_create',[SuratTugasController::class, 'lhgp_create'])->name('lhgp.create');
        Route::post('/lhgp_store', [SuratTugasController::class, 'lhgp_store'])->name('lhgp.store');
        Route::get('/lhgp_edit', [SuratTugasController::class, 'lhgp_edit'])->name('lhgp.edit');
        Route::post('/lhgp_update', [SuratTugasController::class, 'lhgp_update'])->name('lhgp.update');
        Route::delete('/lhgp_delete', [SuratTugasController::class, 'lhgp_delete'])->name('lhgp.delete');
        Route::get('/lhgp_view', [SuratTugasController::class, 'lhgp_view'])->name('lhgp.view');
    });

    Route::post('/store-tersangka', [App\Http\Controllers\DaftarTersangkaController::class, 'store_tersangka'])->name('store_tersangka');
    Route::get('/suspects/list', [App\Http\Controllers\DaftarTersangkaController::class, 'read_tersangka'])->name('read_tersangka');
    Route::get('/suspects/edit', [App\Http\Controllers\DaftarTersangkaController::class, 'edit_tersangka'])->name('edit_tersangka');
    Route::post('/suspects/delete', [App\Http\Controllers\DaftarTersangkaController::class, 'destroy_tersangka'])->name('destroy_tersangka');


    Route::prefix('surat-penetapan-tersangka')->group(function(){
        Route::get('/sddl_show',[SuratTugasController::class, 'sddl_show'])->name('sddl.show');
        Route::get('/sddl_create',[SuratTugasController::class, 'sddl_create'])->name('sddl.create');
        Route::get('/sddl_store',[SuratTugasController::class, 'sddl_store'])->name('sddl.store');
        Route::delete('/sddl_delete', [SuratTugasController::class, 'sddl_delete'])->name('sddl.delete');
        Route::get('/sddl_view',[SuratTugasController::class, 'sddl_view'])->name('sddl.view');
        Route::get('/sddl_edit',[SuratTugasController::class, 'sddl_edit'])->name('sddl.edit');
        Route::post('/sddl_update',[SuratTugasController::class, 'sddl_update'])->name('sddl.update');
    });

    Route::prefix('spdp')->group(function(){
        Route::get('/spdp_show',[SPDPController::class, 'spdp_show'])->name('spdp.show');
        Route::get('/spdp_create',[SPDPController::class, 'spdp_create'])->name('spdp.create');
        Route::get('/spdp_store',[SPDPController::class, 'spdp_store'])->name('spdp.store');
        Route::delete('/spdp_delete', [SPDPController::class, 'spdp_delete'])->name('spdp.delete');
        Route::get('/spdp_view',[SPDPController::class, 'spdp_view'])->name('spdp.view');
        Route::get('/spdp_edit',[SPDPController::class, 'spdp_edit'])->name('spdp.edit');
        Route::post('/spdp_update',[SPDPController::class, 'spdp_update'])->name('spdp.update');
    });



    // Route::get('/', [App\Http\Controllers\AccidentController::class, 'produktivitas'])->name('produktivitas');
    Route::get('/', [App\Http\Controllers\AccidentController::class, 'list_produktivitas'])->name('produktivitas');
    Route::put('downloadall', [App\Http\Controllers\AccidentController::class, 'downloadall'])->name('downloadall');
    Route::get('/search', [App\Http\Controllers\AccidentController::class, 'search_produktivitas'])->name('produktivitas-search');
    Route::post('/file-upload-ketetapan', [App\Http\Controllers\AccidentController::class, 'file_upload_ketetapan'])->name('file.upload.ketetapan');

    Route::post('/{accidentId}/submit-selra', [App\Http\Controllers\AccidentController::class, 'submitSelra'])->name('submit_selra');
    Route::post('/update-selra', [App\Http\Controllers\AccidentController::class, 'update_selra'])->name('update_selra');
    Route::post('/update-state-selra', [App\Http\Controllers\AccidentController::class, 'update_state_selra'])->name('update_state_selra');

    Route::get('/view-produktivitas', [App\Http\Controllers\AccidentController::class, 'view_produktivitas_accident'])->name('view_produktivitas_accident');
    Route::post('/add-surat-tugas', [App\Http\Controllers\AccidentController::class, 'add_surat_tugas'])->name('add_surat_tugas');
    Route::post('/edit-surat-tugas', [App\Http\Controllers\AccidentController::class, 'edit_surat_tugas'])->name('edit_surat_tugas');
    Route::get('/view-surat-tugas', [App\Http\Controllers\AccidentController::class, 'view_surat_tugas'])->name('view_surat_tugas');

    Route::post('/add-surat-penyelidikan', [App\Http\Controllers\AccidentController::class, 'add_surat_penyelidikan'])->name('add_surat_penyelidikan');
    Route::post('/edit-surat-penyelidikan', [App\Http\Controllers\AccidentController::class, 'edit_surat_penyelidikan'])->name('edit_surat_penyelidikan');
    Route::get('/view-surat-penyelidikan', [App\Http\Controllers\AccidentController::class, 'view_surat_penyelidikan'])->name('view_surat_penyelidikan');

    Route::post('/add-surat-penyidikan', [App\Http\Controllers\AccidentController::class, 'add_surat_penyidikan'])->name('add_surat_penyidikan');
    Route::post('/edit-surat-penyidikan', [App\Http\Controllers\AccidentController::class, 'edit_surat_penyidikan'])->name('edit_surat_penyidikan');
    Route::get('/view-surat-penyidikan', [App\Http\Controllers\AccidentController::class, 'view_surat_penyidikan'])->name('view_surat_penyidikan');

    Route::post('/add-surat-spdp', [App\Http\Controllers\AccidentController::class, 'add_surat_spdp'])->name('add_surat_spdp');
    Route::post('/edit-surat-spdp', [App\Http\Controllers\AccidentController::class, 'edit_surat_spdp'])->name('edit_surat_spdp');
    Route::get('/view-surat-spdp', [App\Http\Controllers\AccidentController::class, 'view_surat_spdp'])->name('view_surat_spdp');

    Route::post('/add-surat-p21-tahap-1', [App\Http\Controllers\AccidentController::class, 'add_surat_p21_tahap_1'])->name('add_surat_p21_tahap_1');
    Route::post('/edit-surat-p21-tahap-1', [App\Http\Controllers\AccidentController::class, 'edit_surat_p21_tahap_1'])->name('edit_surat_p21_tahap_1');
    Route::post('/json-surat-p21-tahap-1', [App\Http\Controllers\AccidentController::class, 'json_surat_p21_tahap_1'])->name('json_surat_p21_tahap_1');
    Route::get('/view-surat-p21-tahap-1', [App\Http\Controllers\AccidentController::class, 'view_surat_p21_tahap_1'])->name('view_surat_p21_tahap_1');

    Route::post('/add-surat-p21-tahap-2', [App\Http\Controllers\AccidentController::class, 'add_surat_p21_tahap_2'])->name('add_surat_p21_tahap_2');
    Route::post('/edit-surat-p21-tahap-2', [App\Http\Controllers\AccidentController::class, 'edit_surat_p21_tahap_2'])->name('edit_surat_p21_tahap_2');
    Route::post('/json-surat-p21-tahap-2', [App\Http\Controllers\AccidentController::class, 'json_surat_p21_tahap_2'])->name('json_surat_p21_tahap_2');
    Route::get('/view-surat-p21-tahap-2', [App\Http\Controllers\AccidentController::class, 'view_surat_p21_tahap_2'])->name('view_surat_p21_tahap_2');

    Route::post('/add-sp2hp', [App\Http\Controllers\AccidentController::class, 'add_sp2hp'])->name('add_sp2hp');
    Route::get('/sp2hp/list', [App\Http\Controllers\AccidentController::class, 'get_sp2hp'])->name('get_sp2hp');
    // Route::get('/saksi/edit/{id}', [App\Http\Controllers\AccidentController::class, 'edit_saksi'])->name('edit_saksi');
    Route::get('/sp2hp/edit', [App\Http\Controllers\AccidentController::class, 'edit_sp2hp'])->name('edit_sp2hp');
    Route::post('/sp2hp/delete', [App\Http\Controllers\AccidentController::class, 'delete_sp2hp'])->name('delete_sp2hp');

    Route::post('/autocomplete/fetch', [App\Http\Controllers\AccidentController::class, 'fetch'])->name('autocomplete.fetch');
    // Route::get('file-upload', [FileUploadController::class, 'fileUpload'])->name('file.upload');
    // Route::post('file-upload', [App\Http\Controllers\AccidentDetailController::class, 'fileUploadPost' ])->name('file.upload.post');
    Route::post('file-upload', [App\Http\Controllers\FileUploadController::class, 'fileUploadPost'])->name('file.upload.post');

    //kategori 2
    Route::post('/add-saksi', [App\Http\Controllers\AccidentController::class, 'add_saksi'])->name('add_saksi');
    Route::get('/saksi/list', [App\Http\Controllers\AccidentController::class, 'get_saksi'])->name('get_saksi');
    // Route::get('/saksi/edit/{id}', [App\Http\Controllers\AccidentController::class, 'edit_saksi'])->name('edit_saksi');
    Route::get('/saksi/edit', [App\Http\Controllers\AccidentController::class, 'edit_saksi'])->name('edit_saksi');
    Route::post('/saksi/delete', [App\Http\Controllers\AccidentController::class, 'delete_saksi'])->name('delete_saksi');

    // Route::get('/saksi/list', [App\Http\Controllers\AccidentController::class, 'edit_saksi'])->name('edit_saksi');
    // Route::post('/petugas/getPetugas/', [App\Http\Controllers\AccidentController::class, 'getPetugas'])->name('get_petugas');

    //kategori 3
    Route::post('/add-tersangka', [App\Http\Controllers\AccidentController::class, 'add_tersangka'])->name('add_tersangka');
    Route::get('/tersangka/list', [App\Http\Controllers\AccidentController::class, 'get_tersangka'])->name('get_tersangka');
    Route::get('/tersangka/edit', [App\Http\Controllers\AccidentController::class, 'edit_tersangka'])->name('edit_tersangka');
    Route::post('/tersangka/delete', [App\Http\Controllers\AccidentController::class, 'delete_tersangka'])->name('delete_tersangka');

    //kategori 6
    Route::post('/add-barang-bukti', [App\Http\Controllers\AccidentController::class, 'add_barang_bukti'])->name('add_barang_bukti');
    Route::get('/barang-bukti/list', [App\Http\Controllers\AccidentController::class, 'get_barang_bukti'])->name('get_barang_bukti');
    Route::get('/tersabarang-buktingka/edit', [App\Http\Controllers\AccidentController::class, 'edit_barang_bukti'])->name('edit_barang_bukti');
    Route::post('/add-surat-penyitaan', [App\Http\Controllers\AccidentController::class, 'add_surat_penyitaan'])->name('add_surat_penyitaan');
    Route::post('/edit-surat-penyitaan', [App\Http\Controllers\AccidentController::class, 'edit_surat_penyitaan'])->name('edit_surat_penyitaan');
    Route::get('view-surat-penyitaan', [App\Http\Controllers\AccidentController::class, 'view_surat_penyitaan'])->name('view_surat_penyitaan');

    //kategori 7
    Route::post('/add-surat-perintah-penyegelan', [App\Http\Controllers\AccidentController::class, 'add_surat_perintah_penyegelan'])->name('add_surat_perintah_penyegelan');
    Route::post('/edit-surat-penyegelan', [App\Http\Controllers\AccidentController::class, 'edit_surat_penyegelan'])->name('edit_surat_penyegelan');

    //kategori 11
    Route::get('/dpo/list', [App\Http\Controllers\AccidentController::class, 'get_dpo'])->name('get_dpo');
    Route::post('/add-dpo', [App\Http\Controllers\AccidentController::class, 'add_dpo'])->name('add_dpo');
    Route::get('/dpo/edit', [App\Http\Controllers\AccidentController::class, 'edit_dpo'])->name('edit_dpo');
    Route::post('/dpo/delete', [App\Http\Controllers\AccidentController::class, 'delete_dpo'])->name('delete_dpo');

    Route::get('/dpb/list', [App\Http\Controllers\AccidentController::class, 'get_dpb'])->name('get_dpb');
    Route::post('/add-dpb', [App\Http\Controllers\AccidentController::class, 'add_dpb'])->name('add_dpb');
    Route::get('/dpb/edit', [App\Http\Controllers\AccidentController::class, 'edit_dpb'])->name('edit_dpb');
    Route::post('/dpb/delete', [App\Http\Controllers\AccidentController::class, 'delete_dpb'])->name('delete_dpb');

    Route::post('/add-sp3',[App\Http\Controllers\AccidentController::class, 'add_sp3'])->name('add_sp3');
    Route::post('/edit-sp3',[App\Http\Controllers\AccidentController::class, 'edit_sp3'])->name('edit_sp3');
    Route::get('/view-sp3',[App\Http\Controllers\AccidentController::class, 'view_sp3'])->name('view_sp3');

    //image upload rekap
    Route::post('/deleteImage/{id}', [App\Http\Controllers\AccidentController::class, 'deleteImage'])->name('deleteImage');
    Route::post('imageUpload', [App\Http\Controllers\imageUploadController::class, 'imageUpload'])->name('upload.imageUpload');
});