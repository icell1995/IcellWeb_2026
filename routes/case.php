<?php 

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Case\CaseController;
use App\Http\Controllers\Case\ParticipantController;
use App\Http\Controllers\Case\Participant\ReportedPersonController;
use App\Http\Controllers\Case\Participant\ReportingPersonController;
use App\Http\Controllers\Case\Participant\ParticipantPersonController;

Route::get('/', [CaseController::class, 'index'])->name('case.index');
Route::get('/{id}/show', [CaseController::class, 'show'])->name('case.show');
Route::post('/{id}/save', [CaseController::class, 'save'])->name('case.save');

//reset selra
Route::post('/{id}/reset-resolution', [CaseController::class, 'resetResolution'])->name('case.reset-resolution');

Route::prefix('/{accidentId}/participant')->group(function () {
    Route::get('/', [ParticipantController::class, 'index'])->name('case.participant.index');
    
    // Terlapor
    Route::prefix('/reported-person')->group(function () {
        Route::get('/{id}/show', [ReportedPersonController::class, 'show'])->name('case.participant.reported-person.show');
        Route::get('/create', [ReportedPersonController::class, 'create'])->name('case.participant.reported-person.create');
        Route::post('/create', [ReportedPersonController::class, 'store'])->name('case.participant.reported-person.store');
        Route::get('/{id}/edit', [ReportedPersonController::class, 'edit'])->name('case.participant.reported-person.edit');
        Route::post('/{id}/edit', [ReportedPersonController::class, 'update'])->name('case.participant.reported-person.update');
        Route::delete('/{id}/delete', [ReportedPersonController::class, 'delete'])->name('case.participant.reported-person.delete');
        
        Route::get('/api/locations', [ReportedPersonController::class, 'getLocations'])->name('case.participant.reported-person.api.locations');
        Route::post('/api/validate-request-form', [ReportedPersonController::class, 'validateRequestForm'])->name('case.participant.reported-person.api.validate-request-form');
    });

    // Pelapor
    Route::prefix('/reporting-person')->group(function () {
        Route::get('/{id}/show', [ReportingPersonController::class, 'show'])->name('case.participant.reporting-person.show');
        Route::get('/create', [ReportingPersonController::class, 'create'])->name('case.participant.reporting-person.create');
        Route::post('/create', [ReportingPersonController::class, 'store'])->name('case.participant.reporting-person.store');
        Route::get('/{id}/edit', [ReportingPersonController::class, 'edit'])->name('case.participant.reporting-person.edit');
        Route::post('/{id}/edit', [ReportingPersonController::class, 'update'])->name('case.participant.reporting-person.update');
        Route::delete('/{id}/delete', [ReportingPersonController::class, 'delete'])->name('case.participant.reporting-person.delete');

        Route::get('/api/locations', [ReportingPersonController::class, 'getLocations'])->name('case.participant.reporting-person.api.locations');
        Route::post('/api/validate-request-form', [ReportingPersonController::class, 'validateRequestForm'])->name('case.participant.reporting-person.api.validate-request-form');
    });
    // Unified: Tambah Pihak Terlibat (Pelapor / Terlapor dalam 1 form)
    Route::prefix('/person')->group(function () {
        Route::get('/create', [ParticipantPersonController::class, 'create'])->name('case.participant.person.create');
        Route::post('/store', [ParticipantPersonController::class, 'store'])->name('case.participant.person.store');
        Route::get('/api/locations', [ParticipantPersonController::class, 'getLocations'])->name('case.participant.person.api.locations');
        Route::post('/api/validate-request-form', [ParticipantPersonController::class, 'validateRequestForm'])->name('case.participant.person.api.validate-request-form');
    });
});

// Route::prefix('/register')->group(function(){
        // Route::get('/', [App\Http\Controllers\CaseController::class, 'registerIndex'])->name('case.register.index');
// });