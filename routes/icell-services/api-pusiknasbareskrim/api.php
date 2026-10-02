<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IcellServices\ApiPusiknasBareskrim\AuthController;

/*
|--------------------------------------------------------------------------
| API Pusiknas Bareskrim — Authentication / Token
|--------------------------------------------------------------------------
|
| Base URL : https://icell.korlantas.polri.go.id/icell-services/api-pusiknasbareskrim/
| Endpoint : POST /GetTokenICELL
|
*/

Route::post('/GetTokenICELL', [AuthController::class, 'getToken'])->name('api.pusiknasbareskrim.get-token-icell');
Route::post('/get-token', [AuthController::class, 'getToken'])->name('api.pusiknasbareskrim.get-token');
