<?php 

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Case\CaseController;
use App\Http\Controllers\Case\ParticipantController;
use App\Http\Controllers\Case\Participant\ReportedPersonController;

Route::get('/', [CaseController::class, 'index'])->name('case.index');
Route::get('/{id}/show', [CaseController::class, 'show'])->name('case.show');
Route::post('/{id}/save', [CaseController::class, 'save'])->name('case.save');

//reset selra
Route::post('/{id}/reset-resolution', [CaseController::class, 'resetResolution'])->name('case.reset-resolution');

Route::prefix('/{accidentId}/participant')->group(function () {
    Route::get('/', [ParticipantController::class, 'index'])->name('case.participant.index');
    
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
});

// Route::prefix('/register')->group(function(){
        // Route::get('/', [App\Http\Controllers\CaseController::class, 'registerIndex'])->name('case.register.index');
// });