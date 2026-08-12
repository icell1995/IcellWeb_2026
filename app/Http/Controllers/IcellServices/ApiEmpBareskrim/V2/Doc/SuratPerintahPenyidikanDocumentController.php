<?php

namespace App\Http\Controllers\IcellServices\ApiEmpBareskrim\V2\Doc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\PeopleNameHelper;
use App\Services\IcellServices\ApiEmpBareskrim\V2\DocService;

use App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument;

class SuratPerintahPenyidikanDocumentController extends Controller
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
            $suratPerintahPenyidikanDocuments = SuratPerintahPenyidikanDocument::with([
                'accident', 
                'suratPerintahPenyidikanDocumentOfficers',
                'suratPerintahPenyidikanDocumentLaws',
            ])
            ->whereHas('accident.suratPemberitahuanDimulainyaPenyidikanDocuments', function($query){
                $query->whereIn('status_id', ['86']);
            })
            ->whereIn('status_id', $docService->requiredDocumentStatusIds)
            ->orderBy('document_date', 'ASC');

            // Filter data for include legacy document
            $suratPerintahPenyidikanDocuments = $docService->applyIncludeLegacyDocumentFilter(
                $suratPerintahPenyidikanDocuments,
                false
            );

            // Filter data for 'document_date'
            $suratPerintahPenyidikanDocuments = $docService->applyDateRangeFilter(
                $suratPerintahPenyidikanDocuments,
                'released_at',
                $startReleaseDate,
                $endReleaseDate
            );

            $suratPerintahPenyidikanDocuments = $docService->applyDateRangeFilter(
                $suratPerintahPenyidikanDocuments,
                'document_date',
                $startDocumentDate,
                $endDocumentDate
            );

            $suratPerintahPenyidikanDocuments = $suratPerintahPenyidikanDocuments->paginate($perPage, ['*'], 'page', $page);
            
            $suratPerintahPenyidikanDocumentsTotal = $suratPerintahPenyidikanDocuments->total();
            $suratPerintahPenyidikanDocumentsTotalPage = $suratPerintahPenyidikanDocuments->lastPage();
                                            
            // Packing data
            $arrayKey = 0;
            foreach($suratPerintahPenyidikanDocuments as $suratPerintahPenyidikanDocument){
                $dorsId = $suratPerintahPenyidikanDocument->accident->dors_id ?? null;
                $documentSignatory = $suratPerintahPenyidikanDocument
                    ->suratPerintahPenyidikanDocumentOfficers
                    ->where('class', 'SIGNATORY')
                    ->first();

                $createdBy = $suratPerintahPenyidikanDocument->createdByUser;
                $createdByRegisterNumber = $createdBy->register_number ?? null;

                $police = $suratPerintahPenyidikanDocument->accident->police ?? null;
                $policeEmpId = $police->emp_id ?? null;

                $mainLaws = [];
                $suratPerintahPenyidikanDocumentLaws = $suratPerintahPenyidikanDocument->suratPerintahPenyidikanDocumentLaws->where('flag', 'MAIN');
                foreach($suratPerintahPenyidikanDocumentLaws as $suratPerintahPenyidikanDocumentLaw){
                    $mainLaws[] = [
                        "Id" => $suratPerintahPenyidikanDocumentLaw->id,
                        "DORSId" => strval($dorsId),
                        "SprindikId" => $suratPerintahPenyidikanDocumentLaw->surat_perintah_penyidikan_document_id,
                        "JenisKejahatanId" => $suratPerintahPenyidikanDocumentLaw->crimeType->id ?? null,
                        "JenisKejahatanName" => $suratPerintahPenyidikanDocumentLaw->crimeType->name ?? null,
                        "GolonganKejahatanId" => $suratPerintahPenyidikanDocumentLaw->crimeClass->id ?? null,
                        "GolonganKejahatanName" => $suratPerintahPenyidikanDocumentLaw->crimeClass->name ?? null,
                        "UndangUndangId" => $suratPerintahPenyidikanDocumentLaw->crimeConstitution->id ?? null,
                        "UndangUndangName" => $suratPerintahPenyidikanDocumentLaw->crimeConstitution->name ?? null,
                        "Pasal" => (!empty($suratPerintahPenyidikanDocumentLaw->constitution_chapter)) ? $suratPerintahPenyidikanDocumentLaw->constitution_chapter : ((isset($suratPerintahPenyidikanDocumentLaw->crimeConstitution->chapter)) ? $suratPerintahPenyidikanDocumentLaw->crimeConstitution->chapter : null),
                        "CreatedDate" => ($suratPerintahPenyidikanDocument->created_at) ? date('Y-m-d H:i:s', strtotime($suratPerintahPenyidikanDocument->created_at)) : null,
                        "CreatedBy" => $createdByRegisterNumber,
                        "CreatedLocation" => $suratPerintahPenyidikanDocument->ip_addresses['created_ip'] ?? null,
                    ];
                }

                $additionalLaws = [];
                $suratPerintahPenyidikanDocumentLaws = $suratPerintahPenyidikanDocument->suratPerintahPenyidikanDocumentLaws->where('flag', 'ADDITIONAL');
                foreach($suratPerintahPenyidikanDocumentLaws as $suratPerintahPenyidikanDocumentLaw){
                    $additionalLaws[] = [
                        "Id"=> $suratPerintahPenyidikanDocumentLaw->id,
                        "DORSId"=> strval($dorsId),
                        "SprindikId"=> $suratPerintahPenyidikanDocumentLaw->surat_perintah_penyidikan_document_id,
                        "Nama"=> $suratPerintahPenyidikanDocumentLaw->constitution,
                        "CreatedDate" => ($suratPerintahPenyidikanDocument->created_at) ? date('Y-m-d H:i:s', strtotime($suratPerintahPenyidikanDocument->created_at)) : null,
                        "CreatedBy" => $createdByRegisterNumber,
                        "CreatedLocation" => $suratPerintahPenyidikanDocument->ip_addresses['created_ip'] ?? null,
                    ];
                }

                $officers = [];
                $suratPerintahPenyidikanDocumentOfficers = $suratPerintahPenyidikanDocument->suratPerintahPenyidikanDocumentOfficers->where('class', '!=', 'SIGNATORY');
                foreach($suratPerintahPenyidikanDocumentOfficers as $suratPerintahPenyidikanDocumentOfficer){
                    $officers[] = [
                        "Id"=>  $suratPerintahPenyidikanDocumentOfficer->id,
                        "DORSId"=> strval($dorsId),
                        "SprindikId"=> $suratPerintahPenyidikanDocumentOfficer->surat_perintah_penyidikan_document_id,
                        "KetuaTim"=> ($suratPerintahPenyidikanDocumentOfficer->class == 'LEADER') ? '1' : '0',
                        "NamaPersonel"=> PeopleNameHelper::getFullName($suratPerintahPenyidikanDocumentOfficer->first_title, $suratPerintahPenyidikanDocumentOfficer->first_name, $suratPerintahPenyidikanDocumentOfficer->last_name, $suratPerintahPenyidikanDocumentOfficer->last_title),
                        "Nrp"=> $suratPerintahPenyidikanDocumentOfficer->register_number,
                        "CreatedDate" => ($suratPerintahPenyidikanDocument->created_at) ? date('Y-m-d H:i:s', strtotime($suratPerintahPenyidikanDocument->created_at)) : null,
                        "CreatedBy" => $createdByRegisterNumber,
                        "CreatedLocation" => $suratPerintahPenyidikanDocument->ip_addresses['created_ip'] ?? null,
                    ];
                }

                $responseData[$arrayKey]  = [
                    "Id" => $suratPerintahPenyidikanDocument->id,
                    "DORSId" => strval($dorsId),
                    "NoSurat" => $suratPerintahPenyidikanDocument->document_number,
                    "TanggalSprindik" => ($suratPerintahPenyidikanDocument->document_date) ? date('Y-m-d H:i:s', strtotime($suratPerintahPenyidikanDocument->document_date)) : null,
                    "TanggalMulai" => ($suratPerintahPenyidikanDocument->start_date) ? date('Y-m-d H:i:s', strtotime($suratPerintahPenyidikanDocument->start_date)) : null,
                    "TanggalBerakhir" => ($suratPerintahPenyidikanDocument->end_date) ? date('Y-m-d H:i:s', strtotime($suratPerintahPenyidikanDocument->end_date)) : null,
                    "LokasiID" => $policeEmpId,
                    "Nama" => (!empty($documentSignatory)) ? PeopleNameHelper::getFullName($documentSignatory->first_title, $documentSignatory->first_name, $documentSignatory->last_name, $documentSignatory->last_title) : null,
                    "Nrp" => $documentSignatory->register_number ?? null,
                    "JabatanPenandatangan" => $documentSignatory->position->name ?? null,
                    "CreatedDate" => ($suratPerintahPenyidikanDocument->created_at) ? date('Y-m-d H:i:s', strtotime($suratPerintahPenyidikanDocument->created_at)) : null,
                    "CreatedBy" => $createdByRegisterNumber,
                    "CreatedLocation" => $suratPerintahPenyidikanDocument->ip_addresses['created_ip'] ?? null,

                    "LaporanPolisiKejahatan_Sprindik" => $mainLaws,

                    "UndangUndangKhusus" => $additionalLaws,

                    "AssigmentPersonil" => $officers,

                    "released_at" => $suratPerintahPenyidikanDocument->released_at,
                ];

                $docService->putApiSyncMoment(
                    $request, 
                    $suratPerintahPenyidikanDocument,
                    get_class(new SuratPerintahPenyidikanDocument()),
                    $this->tableSchemaName . 'surat_perintah_penyidikan_documents',
                    $mode
                );
                
                $arrayKey++;
            }
            
            // Check if data array is empty result
            if ($suratPerintahPenyidikanDocuments->isEmpty()) {
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
                    "TotalData" => $suratPerintahPenyidikanDocumentsTotal,
                    "TotalPage" => $suratPerintahPenyidikanDocumentsTotalPage,
                    "TotalDataSent" => count($responseData)
                ],
                "data" => $responseData
            ], 200);

        } catch (\Exception $e) {
            DB::rollback();
            //If an exception occurs, return an error response
            return response()->json([
                "code" => "500",
                "status" => "INTERNAL_SERVER_ERROR",
                "message" => "An error occurred while processing your request.",
                "pagination" => [
                    "Page" => $page,
                    "TotalData" => 0,
                    "TotalPage" => 0,
                    "TotalDataSent" => 0
                ],
                "data" => []
            ], 500);
        }
    }
}
