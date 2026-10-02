<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IcellServices\ApiPusiknasBareskrim\AuthController;

/*
|--------------------------------------------------------------------------
| API Pusiknas Bareskrim — Authentication / Token
|--------------------------------------------------------------------------
|
| Base URL : https://icell.korlantas.polri.go.id/icell-services/api-pusiknasbareskrim/
| Endpoint : POST /get-token
|
*/

Route::post('/get-token', [AuthController::class, 'getToken'])->name('api.pusiknasbareskrim.get-token');
