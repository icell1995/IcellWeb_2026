<?php

namespace App\Http\Controllers\IcellServices\ApiEmpBareskrim\V2\Doc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Helpers\PeopleNameHelper;

use App\Services\IcellServices\ApiEmpBareskrim\V2\DocService;

use App\Models\Doc\P19Document\P19Document;

class P19DocumentController extends Controller
{
    protected $docService;
    private $tableSchemaName = 'doc' . '.';

    public function __construct(DocService $docService)
    {
        $this->docService = $docService;
    }

    public function index(Request $request){
        $docService = $this->docService;

         // Get request data
         $mode = $request->input('mode');
         $startDocumentDate = $request->input('start_doc_date');
         $endDocumentDate = $request->input('end_doc_date');
         $startReleaseDate = $request->input('start_release_date');
         $endReleaseDate = $request->input('end_release_date');
         $perPage = $request->query('perPage', 100); // Jumlah item per halaman
         $page = $request->query('page', 1); // Nomor halaman saat ini, default 1
 
         // Initialize variable
         $responseData = [];
 
         if (!is_numeric($page)) {
             // Jika $page bukan angka, berikan nilai default 1
             $page = 1;
         }
         if (!is_numeric($perPage)) {
             // Jika $page bukan angka, berikan nilai default 1
             $perPage = 100;
         }
 
         $page = intval($page);
 
         // Validate date parameter
         $dateParams = [$startDocumentDate, $endDocumentDate, $startReleaseDate, $endReleaseDate];
 
         foreach ($dateParams as $dateParam) {
             $validateDateParamRequestResponse = $docService->validateDateParamRequest($dateParam, $page);
             
             if (!empty($validateDateParamRequestResponse)) {
                 return $validateDateParamRequestResponse;
             }
         }
 
         DB::beginTransaction();
         try {
            //Transaction
            
            // Commit transaction
            DB::commit();

            // Return Result JSON
            return response()->json([
                "code" => "200",
                "status" => "OK",
                "message" => "Success",
                "pagination" => [
                    "Page" => $page,
                    "TotalData" => null,
                    "TotalPage" => null,
                    "TotalDataSent" => count($responseData)
                ],
                "data" => $responseData
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('IcellServices\ApiEmpBareskrim\V2\Doc\P19DocumentController::index', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            // If an exception occurs, return an error response
            return response()->json([
                "code" => "500",
                "status" => "INTERNAL_SERVER_ERROR",
                "message" => "An error occurred while processing your request.",
                "pagination" => [
                    "Page" => intval($page),
                    "TotalData" => 0,
                    "TotalPage" => 0,
                    "TotalDataSent" => 0
                ],
                "data" => []
            ], 500);
        }
    }
}
