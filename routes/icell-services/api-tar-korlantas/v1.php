<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\IcellServices\ApiTarKorlantas\V1\Res\AccidentController;

Route::get('/res/accidents', [AccidentController::class, 'index'])->name('api.tar-korlantas.v1.res.accidents');