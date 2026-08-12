<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IcellServices\ApiDivtikPolri\DivtikAnevController;

Route::get('/get-divtik', [DivtikAnevController::class, 'getDivtik'])->name('icell-services.api-divtik-polri.get-divtik');
