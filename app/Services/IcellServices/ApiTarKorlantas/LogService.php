<?php
namespace App\Services\IcellServices\ApiTarKorlantas;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

use App\Services\IcellServices\ApiTarKorlantas\UtilityService;

use App\Models\Log\ApiTarKorlantasTransmitAccident;

class LogService
{
    public function transmitAccident($request, $accident, $classModel, $mode = null)
    {
        $utilityService = new UtilityService();

        // if mode is empty, then update last_synced_at
        if(empty($mode) || $mode != 'STAGING'){
            // Update last_synced_at
            $accidentId = $accident->id;

            // // Insert to history
            ApiTarKorlantasTransmitAccident::create([
                'accident_id' => $accidentId,
                'class_model' => $classModel,
                'ip_address' => $utilityService->getPublicIpAddress($request),
            ]);
        }
    }
}