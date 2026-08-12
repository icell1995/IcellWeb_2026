<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\IcellServices\ApiIrsmsKorlantas\V1\Stg\DorsController;
use App\Http\Controllers\IcellServices\ApiIrsmsKorlantas\V1\Ext\BiroSdmController;
use App\Http\Controllers\IcellServices\ApiIrsmsKorlantas\V1\Ext\UpdateLPController;

Route::prefix('stg')->group(function () {
    Route::prefix('dors')->group(function () {
        Route::post('/store', [DorsController::class, 'store'])->name('api.irsms-korlantas.v1.stg.dors.store');
    });
    
});

Route::prefix('ext')->group(function () {
    Route::prefix('biro-sdm')->group(function () {
        Route::get('/officer', [BiroSdmController::class, 'getOfficer'])->name('api.irsms-korlantas.v1.ext.biro-sdm.officer');
    });
    
    Route::prefix('accidents')->group(function () {
        Route::match(['GET','PATCH'], '/lp/update', [UpdateLPController::class, 'updateLP'])
            ->name('api.irsms-korlantas.v1.ext.accidents.lp.update');

        Route::match(['GET','PATCH'], '/lp/state', [UpdateLPController::class, 'updateLPState'])
            ->name('api.irsms-korlantas.v1.ext.accidents.lp.state.update');
    });
});
