<?php
namespace App\Services\IcellServices\ApiIrsmsKorlantas;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

use App\Services\IcellServices\ApiIrsmsKorlantas\UtilityService;

use App\Models\Log\ApiIrsmsKorlantasPostStgDorsAccident;

class LogService
{
    public function post($request, $classModel = null, $data = [], $mode = null, $code, $status, $method, $endpoint, $message = null)
    {
        $utilityService = new UtilityService();

        // if mode is empty, then update last_synced_at
        if(empty($mode) || $mode != 'STAGING'){
            // // Insert to history
            ApiIrsmsKorlantasPostStgDorsAccident::create([
                'code' => $code,
                'status' => $status,
                'method' => $method,
                'endpoint' => $endpoint,
                'class_model' => $classModel,
                'ip_address' => $utilityService->getPublicIpAddress($request),
                'message' => $message,
                'data' => $data,
            ]);
        }
    }
}