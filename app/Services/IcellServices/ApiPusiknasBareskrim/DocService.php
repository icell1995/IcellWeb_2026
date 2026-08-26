<?php
namespace App\Services\IcellServices\ApiPusiknasBareskrim;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

use App\Models\History\DocumentApiSyncHistory;

class DocService
{
    public $requiredDocumentStatusIds = ['86'];

    public function putApiSyncMoment($request, $document, $classDocumentModel, $tableName, $mode = null)
    {
        // if mode is empty, then update last_synced_at
        if (empty($mode) || $mode != 'STAGING') {
            // Update last_synced_at
            $documentId = $document->id;
            DB::table($tableName)
                ->where('id', $documentId)
                ->update([
                    'last_synced_at' => Carbon::now()->format('Y-m-d H:i:s'),
                ]);

            // Insert to history
            DocumentApiSyncHistory::create([
                'document_category_id' => '0706',
                'document_id'          => $documentId,
                'document_type'        => $classDocumentModel,
                'accident_id'          => $document->accident_id,
                'ip_address'           => $this->getPublicIpAddress($request),
            ]);
        }
    }

    // Function to validate date and return JSON response
    public function validateDateParamRequest($date, $page = 0)
    {
        if (!empty($date) && date('Y-m-d', strtotime($date)) !== $date) {
            return response()->json([
                "code"       => "400",
                "status"     => "BAD_REQUEST",
                "message"    => "Invalid date or format. Date format should be YYYY-MM-DD.",
                "pagination" => [
                    "Page"          => $page,
                    "TotalData"     => 0,
                    "TotalPage"     => 0,
                    "TotalDataSent" => 0,
                ],
                "data" => [],
            ], 400);
        }

        return null; // Return null if validation is successful
    }

    // Function to apply date range filter
    public function applyDateRangeFilter($query, $fieldName, $startDate, $endDate)
    {
        if (!empty($startDate) && !empty($endDate)) {
            $query = $query->whereBetween($fieldName, [
                date('Y-m-d 00:00:00', strtotime($startDate)),
                date('Y-m-d 23:59:59', strtotime($endDate)),
            ]);
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

    public function applyIncludeLegacyDocumentFilter($query, $state)
    {
        $fieldName = 'is_legacy';

        if ($state == true) {
            $query = $query->whereIn($fieldName, [true, false, null]);
        } elseif ($state == false) {
            $query = $query->whereIn($fieldName, [false, null]);
        }

        return $query;
    }

    public function getPublicIpAddress(Request $request)
    {
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
