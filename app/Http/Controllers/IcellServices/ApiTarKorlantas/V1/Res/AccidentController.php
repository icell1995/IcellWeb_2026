<?php

namespace App\Http\Controllers\IcellServices\ApiTarKorlantas\V1\Res;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Helpers\PeopleNameHelper;

use App\Services\IcellServices\ApiTarKorlantas\LogService;
use App\Services\IcellServices\ApiTarKorlantas\UtilityService;

use App\Models\Accident;

class AccidentController extends Controller
{
    public function index(Request $request)
    {
        $mode = $request->input('mode');
        $perPage = $request->query('perPage', 100); // Jumlah item per halaman
        $page = $request->query('page', 1); // Nomor halaman saat ini, default 1
        $startAccidentDate = $request->input('start_accident_date');
        $endAccidentDate = $request->input('end_accident_date');
        $accidentResolutionName = $request->input('selra');

        $logService = new LogService();
        $utilityService = new UtilityService();

        // Initialize variable
        $responseData = [];

        if (!is_numeric($page)) {
            // Jika $page bukan angka, berikan nilai default 1
            $page = 1;
        }
        $page = intval($page);

        if (!is_numeric($perPage)) {
            // Jika $page bukan angka, berikan nilai default 100
            $perPage = 100;
        }
        if ($perPage > 100) {
            // Jika $page diatas 100 row, berikan nilai default 100
            $perPage = 100;
        }

        // Validate date parameter
        $dateParams = [$startAccidentDate, $endAccidentDate];

        foreach ($dateParams as $dateParam) {
            $validateDateParamRequestResponse = $utilityService->validateDateParamRequest($dateParam, $page);
            
            if (!empty($validateDateParamRequestResponse)) {
                return $validateDateParamRequestResponse;
            }
        }

        DB::beginTransaction();
        try{
            $accidents = Accident::with([
                    'suspects' => function ($querySuspects) {
                        $querySuspects->with('vehicleAssociatedSuspect', function ($queryVehicleAssociatedSuspect) {
                            $queryVehicleAssociatedSuspect->withRelated();
                        })
                        ->whereHas('suratKetetapanTentangPenetapanTersangkaDocument', function ($querySuratKetetapanTentangPenetapanTersangkaDocument) {
                            $querySuratKetetapanTentangPenetapanTersangkaDocument->whereIn('status_id', ['86']);
                        });
                    },
                    'reportedPersons',
                    'suratPerintahPenyidikanDocuments' => function ($querySuratPerintahPenyidikanDocuments) {
                        // Add a condition to the relationship
                        $querySuratPerintahPenyidikanDocuments->whereHas('suratPemberitahuanDimulainyaPenyidikanDocument', function ($querySuratPemberitahuanDimulainyaPenyidikanDocument) {
                            $querySuratPemberitahuanDimulainyaPenyidikanDocument->whereIn('status_id', ['86']);
                        });
                        $querySuratPerintahPenyidikanDocuments->whereIn('status_id', ['86']);

                        $querySuratPerintahPenyidikanDocuments->with([
                            'suratPerintahPenyidikanDocumentOfficers' => function ($querySuratPerintahPenyidikanDocumentOfficers) {
                                $querySuratPerintahPenyidikanDocumentOfficers->withRelated();
                            },
                            'suratPerintahPenyidikanDocumentLaws' => function ($querySuratPerintahPenyidikanDocumentLaws) {
                                $querySuratPerintahPenyidikanDocumentLaws->withRelated();
                            }
                        ]);
                    }
                ])
                ->select('accidents.*', 'ref.name as selra_name')
                ->leftJoin('ref', 'accidents.selra_flag', '=', 'ref.id')
                // ->where('accidents.id', '6b774a07-fad9-48e7-a715-eb0c355aca03')
                ->where('accidents.report_date', '>=' , date('Y-m-d H:i:s', strtotime('2023-10-01')))
                ->where('accidents.selra_flag', '!=', NULL)
                ->whereNotIn('accidents.selra_flag', ['S0107'])
                ->orderBy('accidents.accident_date', 'ASC');

            $selraList = [
                'P21' => 'S0101',
                'SP3' => 'S0102',
                'DIVERSI' => 'S0103',
                'SP2LID' => 'S0108',
                'POM/TNI' => 'S0104',
                "-" => ''
            ];
            // Filter data for 'selra'
            $accidents = $utilityService->applyParamFilter(
                $accidents,
                'selra_flag',
                $selraList[$accidentResolutionName ?? '-']
            );
            
            // Filter data for 'accident_date'
            $accidents = $utilityService->applyDateRangeFilter(
                $accidents,
                'accident_date',
                $startAccidentDate,
                $endAccidentDate
            );

            $accidents = $accidents->paginate($perPage, ['*'], 'page', $page);

            // Packing data
            $arrayKey = 0;
            foreach ($accidents as $accident) {
                $suspects = [];
                $reportedPersons = [];
                $officers = [];
                $laws = [];
                $totalVictims = null;
                $totalMaterialLoss = null;
                $accidentTypeName = null;
                $accidentCauseName = null;

                $suratPerintahPenyidikanDocuments = $accident->suratPerintahPenyidikanDocuments;

                foreach ($suratPerintahPenyidikanDocuments as $suratPerintahPenyidikanDocument) {
                    $suratPerintahPenyidikanDocumentOfficers = $suratPerintahPenyidikanDocument->suratPerintahPenyidikanDocumentOfficers;
                    $suratPerintahPenyidikanDocumentLaws = $suratPerintahPenyidikanDocument->suratPerintahPenyidikanDocumentLaws;

                    foreach ($suratPerintahPenyidikanDocumentOfficers as $suratPerintahPenyidikanDocumentOfficer) {
                        $officers[] = [
                            'name' => PeopleNameHelper::getFullName($suratPerintahPenyidikanDocumentOfficer->first_title, $suratPerintahPenyidikanDocumentOfficer->first_name, $suratPerintahPenyidikanDocumentOfficer->last_name, $suratPerintahPenyidikanDocumentOfficer->last_title),
                            'register_number' => $suratPerintahPenyidikanDocumentOfficer->register_number,
                            'rank_name ' => $suratPerintahPenyidikanDocumentOfficer->rank->name ?? null,
                            'is_leader' => ($suratPerintahPenyidikanDocumentOfficer->class == 'LEADER') ? true : false,
                            'position_name' => $suratPerintahPenyidikanDocumentOfficer->position->name ?? null,
                            'police_name' => $suratPerintahPenyidikanDocumentOfficer->police->full_name ?? null
                        ];
                    }

                    foreach ($suratPerintahPenyidikanDocumentLaws as $suratPerintahPenyidikanDocumentLaw) {
                        if($suratPerintahPenyidikanDocumentLaw->flag == 'MAIN'){
                            $laws[] = [
                                'flag' => 'MAIN',
                                'constitution' => $suratPerintahPenyidikanDocumentLaw->crimeConstitution->name ?? null,
                                'constitution_chapter' => $suratPerintahPenyidikanDocumentLaw->constitution_chapter
                            ];
                        }elseif($suratPerintahPenyidikanDocumentLaw->flag == 'ADDITIONAL'){
                            $laws[] = [
                                'flag' => 'ADDITIONAL',
                                'constitution' => $suratPerintahPenyidikanDocumentLaw->name,
                                'constitution_chapter' => null,
                            ];
                        }
                    }
                }

                foreach ($accident->suspects as $suspect) {
                    $suspects[] = [
                        'identity_number' => $suspect->identity_number,
                        'identity_name' => $suspect->identityType->name ?? null,
                        'name' => $suspect->name,
                        'driving_license' => [
                            'type' => $suspect->vehicleAssociatedSuspect->drivingLicenseType->name ?? null
                        ],
                        'vehicle' => [
                            'plate_number' => $suspect->vehicleAssociatedSuspect->plate_number ?? null,
                            'vehicle_type' => $suspect->vehicleAssociatedSuspect->vehicleType->name ?? null
                        ],
                    ];

                    $totalVictims =  $suspect->vehicleAssociatedSuspect->total_victim ?? null;
                    $totalMaterialLoss = $suspect->vehicleAssociatedSuspect->total_material_loss ?? null;
                    $accidentTypeName = $suspect->vehicleAssociatedSuspect->accidentType->name ?? null;
                    $accidentCauseName = $suspect->vehicleAssociatedSuspect->accidentCause->name ?? null;
                }
                
                foreach ($accident->reportedPersons as $reportedPerson) {
                    $reportedPersons[] = [
                        'identity_number' => $reportedPerson->identity_number,
                        'identity_name' => $reportedPerson->identityType->name ?? null,
                        'name' => $reportedPerson->name
                    ];
                }

                $responseData[$arrayKey]  = [
                    'id' => $accident->id,
                    'dors_id' => $accident->dors_id,
                    'accident_number' => $accident->no_lp,
                    'accident_date' => $accident->accident_date,
                    'accident_location' => [
                        'address' => $accident->road_name,
                        'latitude' => $accident->latitude,
                        'longitude' => $accident->longtitude,
                    ],
                    'accident_type' => $accidentTypeName,
                    'accident_cause' => $accidentCauseName,
                    'total_victims' => $totalVictims,
                    'total_material_loss' => $totalMaterialLoss,
                    'report_date' => $accident->report_date,
                    'selra' => $accident->selra_name,
                    'resume' => null,
                    'participants' => [
                        'suspects' => $suspects,
                        'reportedPersons' => $reportedPersons,
                        'officers' => $officers,
                    ],
                    'laws' => $laws
                ];

                //log
                $logService->transmitAccident(
                    $request, 
                    $accident, 
                    get_class(new Accident()),
                    $mode
                );

                $arrayKey++;
            }

            if ($accidents->isEmpty()) {
                return $this->errorResponse(
                    'NOT_FOUND', 
                    'Data not found.', 
                    404
                );
            }

            DB::commit();
           
            return $this->successResponse(
                $responseData,
                'SUCCESS',
                $accidents->currentPage(),
                $accidents->total(),
                $accidents->lastPage(),
                $accidents->count(),
            );

        }catch(\Exception $e){
            DB::rollback();
            return $this->errorResponse('INTERNAL_SERVER_ERROR', 'An error occurred while processing your request.', 500);
        }
    }

    /**
     * Generate a success response.
     *
     * @param  mixed  $data
     * @param  string  $message
     * @param  int  $page
     * @param  int  $totalData
     * @param  int  $totalPage
     * @param  int  $totalDataSent
     * @return \Illuminate\Http\Response
     */
    private function successResponse($data, $message, $page, $totalData, $totalPage, $totalDataSent)
    {
        return response()->json([
            'code' => 200,
            'status' => 'OK',
            'message' => $message,
            'pagination' => [
                'Page' => $page,
                'TotalData' => $totalData,
                'TotalPage' => $totalPage,
                'TotalDataSent' => $totalDataSent,
            ],
            'data' => $data,
        ]);
    }

    /**
     * Generate an error response.
     *
     * @param  string  $status
     * @param  string  $message
     * @param  int  $code
     * @return \Illuminate\Http\Response
     */
    private function errorResponse($status, $message, $code)
    {
        return response()->json([
            'code' => $code,
            'status' => $status,
            'message' => $message,
            'pagination' => [
                'Page' => 1,
                'TotalData' => 0,
                'TotalPage' => 1,
                'TotalDataSent' => 0,
            ],
            'data' => [],
        ]);
    }

    private function getPublicIpAddress(Request $request){
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
