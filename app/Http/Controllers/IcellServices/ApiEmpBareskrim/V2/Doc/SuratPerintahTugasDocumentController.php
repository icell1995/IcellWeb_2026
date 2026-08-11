<?php

namespace App\Http\Controllers\IcellServices\ApiEmpBareskrim\V2\Doc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\PeopleNameHelper;
use App\Services\IcellServices\ApiEmpBareskrim\V2\DocService;

use App\Models\Doc\SuratPerintahTugasDocument\SuratPerintahTugasDocument;

class SuratPerintahTugasDocumentController extends Controller
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
            $suratPerintahTugasDocuments = SuratPerintahTugasDocument::with([
                'accident', 
                'related',
                'suratPerintahTugasDocumentOfficers',
            ])
            ->whereHas('accident.suratPemberitahuanDimulainyaPenyidikanDocuments', function($query){
                $query->whereIn('status_id', ['86']);
            })
            ->whereIn('status_id', $docService->requiredDocumentStatusIds)
            ->where('related_type', 'App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument')
            ->orderBy('document_date', 'ASC');

            // Filter data for include legacy document
            $suratPerintahTugasDocuments = $docService->applyIncludeLegacyDocumentFilter(
                $suratPerintahTugasDocuments,
                false
            );

            // Filter data for 'document_date'
            $suratPerintahTugasDocuments = $docService->applyDateRangeFilter(
                $suratPerintahTugasDocuments,
                'released_at',
                $startReleaseDate,
                $endReleaseDate
            );

            $suratPerintahTugasDocuments = $docService->applyDateRangeFilter(
                $suratPerintahTugasDocuments,
                'document_date',
                $startDocumentDate,
                $endDocumentDate
            );

            $suratPerintahTugasDocuments = $suratPerintahTugasDocuments->paginate($perPage, ['*'], 'page', $page);
            
            $suratPerintahTugasDocumentsTotal = $suratPerintahTugasDocuments->total();
            $suratPerintahTugasDocumentsTotalPage = $suratPerintahTugasDocuments->lastPage();
                                            
            // Packing data
            $arrayKey = 0;
            foreach($suratPerintahTugasDocuments as $suratPerintahTugasDocument){
                $dorsId = $suratPerintahTugasDocument->accident->dors_id ?? null;
                $documentSignatory = $suratPerintahTugasDocument
                    ->suratPerintahTugasDocumentOfficers
                    ->where('class', 'SIGNATORY')
                    ->first();

                $createdBy = $suratPerintahTugasDocument->createdByUser ?? NULL;
                $createdByRegisterNumber = $createdBy->register_number ?? null;

                $police = $suratPerintahTugasDocument->accident->police ?? null;
                $policeEmpId = $police->emp_id ?? null;

                $officers = [];
                foreach($suratPerintahTugasDocument->suratPerintahTugasDocumentOfficers as $suratPerintahTugasDocumentOfficer){
                    $officers[] = [
                        "Id" => $suratPerintahTugasDocumentOfficer->id,
                        "DORSId" => strval($dorsId),
                        "SprindikId" => $suratPerintahTugasDocument->related_id,
                        "SPTugasId" => $suratPerintahTugasDocumentOfficer->surat_perintah_tugas_document_id,
                        "NamaPersonel" => PeopleNameHelper::getFullName($suratPerintahTugasDocumentOfficer->first_title, $suratPerintahTugasDocumentOfficer->first_name, $suratPerintahTugasDocumentOfficer->last_name, $suratPerintahTugasDocumentOfficer->last_title),
                        "Nrp" => $suratPerintahTugasDocumentOfficer->register_number,
                        "CreatedDate" => ($suratPerintahTugasDocument->created_at) ? date('Y-m-d H:i:s', strtotime($suratPerintahTugasDocument->created_at)) : null,
                        "CreatedBy" => $createdByRegisterNumber,
                        "CreatedLocation" => $suratPerintahTugasDocument->ip_addresses['created_ip'] ?? null,
                    ];
                }
                
                $responseData[$arrayKey]  = [
                    "Id" => $suratPerintahTugasDocument->id,
                    "DORSId" => strval($dorsId),
                    "SprindikId" => ($suratPerintahTugasDocument->related) ? strval($suratPerintahTugasDocument->related->id) : null,
                    "NoSurat" => $suratPerintahTugasDocument->document_number,
                    "Tanggal" => ($suratPerintahTugasDocument->document_date) ? date('Y-m-d H:i:s', strtotime($suratPerintahTugasDocument->document_date)) : null,
                    "TanggalMulai" => ($suratPerintahTugasDocument->start_date) ? date('Y-m-d H:i:s', strtotime($suratPerintahTugasDocument->start_date)) : null,
                    "TanggalBerakhir" => ($suratPerintahTugasDocument->end_date) ? date('Y-m-d H:i:s', strtotime($suratPerintahTugasDocument->end_date)) : 'Selesai',
                    "Tugas" => $suratPerintahTugasDocument->task_description,
                    "LokasiID" => $policeEmpId,
                    "Nama" => (!empty($documentSignatory)) ? PeopleNameHelper::getFullName($documentSignatory->first_title, $documentSignatory->first_name, $documentSignatory->last_name, $documentSignatory->last_title) : null,
                    "Nrp" => $documentSignatory->register_number ?? null,
                    "JabatanPenandatangan" => $documentSignatory->position->name ?? null,
                    "CreatedDate" => ($suratPerintahTugasDocument->created_at) ? date('Y-m-d H:i:s', strtotime($suratPerintahTugasDocument->created_at)) : null,
                    "CreatedBy" => $createdByRegisterNumber,
                    "CreatedLocation" => $suratPerintahTugasDocument->ip_addresses['created_ip'] ?? null,

                    "DP_SPTugas" => $officers,

                    "released_at" => $suratPerintahTugasDocument->released_at,
                ];

                $docService->putApiSyncMoment(
                    $request, 
                    $suratPerintahTugasDocument,
                    get_class(new SuratPerintahTugasDocument),
                    $this->tableSchemaName . 'surat_perintah_tugas_documents',
                    $mode
                );

                $arrayKey++;
            }
            
            // Check if data array is empty result
            if ($suratPerintahTugasDocuments->isEmpty()) {
                return response()->json([
                    "code" => "404",
                    "status" => "NOT_FOUND",
                    "message" => "Data not found.",
                    "pagination" => [
                        "Page" => $page,
                        "TotalData" => 0,
                        "TotalPage" => 0,
                        "TotalDataSent" => 0
                    ],
                    "data" => []
                ], 404);
            }

            // Commit transaction
            DB::commit();

            // Return Result JSON
            return response()->json([
                "code" => "200",
                "status" => "OK",
                "message" => "Success",
                "pagination" => [
                    "Page" => $page,
                    "TotalData" => $suratPerintahTugasDocumentsTotal,
                    "TotalPage" => $suratPerintahTugasDocumentsTotalPage,
                    "TotalDataSent" => count($responseData)
                ],
                "data" => $responseData
            ], 200);

        } catch (\Exception $e) {
            DB::rollback();
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
