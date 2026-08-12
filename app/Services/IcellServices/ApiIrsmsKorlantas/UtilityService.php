<?php
namespace App\Services\IcellServices\ApiIrsmsKorlantas;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class UtilityService
{
    public function getPublicIpAddress(Request $request){
        // Check if the request has X-Forwarded-For header
        $ipAddress = $request->header('X-Forwarded-For');

        // If X-Forwarded-For header is not present, check X-Real-IP header
        if (empty($ipAddress)) {
            $ipAddress = $request->header('X-Real-IP');
        }

        // If both headers are not present, use the remote address
        if (empty($ipAddress)) {
            $ipAddress = $request->ip();
        }

        return $ipAddress;
    }
}