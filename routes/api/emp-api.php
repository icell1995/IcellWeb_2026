<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\EmpIntegration\Letter\InvestigationWarrantController;
use App\Http\Controllers\EmpIntegration\Letter\InvestigationOrderLetterController;
use App\Http\Controllers\EmpIntegration\Letter\AssignmentOrderLetterController;
use App\Http\Controllers\EmpIntegration\Report\SuspectInvestigationDeterminationReportController;
use App\Http\Controllers\EmpIntegration\Letter\SuspectDeterminationDecisionLetterController;
use App\Http\Controllers\EmpIntegration\Letter\InvestigationCommencementNotificationLetterController;

// Route::prefix('v1')->group(function () {
//     Route::prefix('letter')->group(function () {
//         Route::get('investigation-warrant', [InvestigationWarrantController::class, 'index'])->name('investigation-warrant.index');
        
//         Route::get('investigation-order-letter', [InvestigationOrderLetterController::class, 'index'])->name('investigation-order-letter.index');
        
//         Route::get('assignment-order-letter', [AssignmentOrderLetterController::class, 'index'])->name('assignment-order-letter.index');

//         Route::get('suspect-determination-decision-letter', [SuspectDeterminationDecisionLetterController::class, 'index'])->name('suspect-determination-decision-letter.index');
        
//         Route::get('investigation-commencement-notification-letter', [InvestigationCommencementNotificationLetterController::class, 'index'])->name('investigation-commencement-notification-letter.index');
//     });
    
//     Route::prefix('report')->group(function () {
//         Route::get('suspect-investigation-determination-report', [SuspectInvestigationDeterminationReportController::class, 'index'])->name('suspect-investigation-determination-report.index');
//     });
// });