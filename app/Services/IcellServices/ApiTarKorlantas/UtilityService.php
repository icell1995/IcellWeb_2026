<?php
namespace App\Services\IcellServices\ApiTarKorlantas;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

use App\Models\Log\ApiTarKorlantasTransmitAccident;

class UtilityService
{
    public function validateDateParamRequest($date, $page = 0) {
        if (!empty($date) && date('Y-m-d', strtotime($date)) !== $date) {
            return response()->json([
                "code" => "400",
                "status" => "BAD_REQUEST",
                "message" => "Invalid date or format. Date format should be YYYY-MM-DD.",
                "pagination" => [
                    "Page" => $page,
                    "TotalData" => 0,
                    "TotalPage" => 0,
                    "TotalDataSent" => 0
                ],
                "data" => []
            ], 400);
        }

        return null;  // Return null if validation is successful
    }

    public function applyDateRangeFilter($query, $fieldName, $startDate, $endDate) {
        if (!empty($startDate) && !empty($endDate)) {
            $query = $query->whereBetween($fieldName, [date('Y-m-d 00:00:00', strtotime($startDate)), date('Y-m-d 23:59:59', strtotime($endDate))]);
        } else {
            if (!empty($startDate)) {
                $query = $query->where($fieldName, '>=', date('Y-m-d 00:00:00', strtotime($startDate)));
            }

            if (!empty($endDate)) {
                $query = $query->where($fieldName, '<=', date('Y-m-d 23:59:59', strtotime($endDate)));
            }
        }

        return $query;
    }

    public function applyParamFilter($query, $fieldName, $param) {
        if (!empty($param)) {
            $query = $query->where($fieldName, $param);
        }

        return $query;
    }

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