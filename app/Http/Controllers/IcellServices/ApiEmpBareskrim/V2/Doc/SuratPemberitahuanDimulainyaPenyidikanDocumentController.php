<?php

namespace App\Http\Controllers\IcellServices\ApiEmpBareskrim\V2\Doc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Helpers\PeopleNameHelper;
use App\Services\IcellServices\ApiEmpBareskrim\V2\DocService;

use App\Models\Doc\SuratPemberitahuanDimulainyaPenyidikanDocument\SuratPemberitahuanDimulainyaPenyidikanDocument;

class SuratPemberitahuanDimulainyaPenyidikanDocumentController extends Controller
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
            $suratPemberitahuanDimulainyaPenyidikanDocuments = SuratPemberitahuanDimulainyaPenyidikanDocument::with([
                'accident', 
                'suratPemberitahuanDimulainyaPenyidikanDocumentAttachment',
                'suratPemberitahuanDimulainyaPenyidikanDocumentOfficers',
                'suratPerintahPenyidikanDocument',
                'suratPerintahTugasDocument',
                'prosecutor',
                'court',
                'suspects',
                'reportedPersons'
            ])
            ->whereHas('accident.suratPemberitahuanDimulainyaPenyidikanDocuments', function($query){
                $query->whereIn('status_id', ['86']);
            })
            ->whereIn('status_id', $docService->requiredDocumentStatusIds)
            ->orderBy('document_date', 'ASC');

            // Filter data for include legacy document
            $suratPemberitahuanDimulainyaPenyidikanDocuments = $docService->applyIncludeLegacyDocumentFilter(
                $suratPemberitahuanDimulainyaPenyidikanDocuments,
                false
            );

            // Filter data for 'document_date'
            $suratPemberitahuanDimulainyaPenyidikanDocuments = $docService->applyDateRangeFilter(
                $suratPemberitahuanDimulainyaPenyidikanDocuments,
                'released_at',
                $startReleaseDate,
                $endReleaseDate
            );

            $suratPemberitahuanDimulainyaPenyidikanDocuments = $docService->applyDateRangeFilter(
                $suratPemberitahuanDimulainyaPenyidikanDocuments,
                'document_date',
                $startDocumentDate,
                $endDocumentDate
            );

            $suratPemberitahuanDimulainyaPenyidikanDocuments = $suratPemberitahuanDimulainyaPenyidikanDocuments->paginate($perPage, ['*'], 'page', $page);
            
            $suratPemberitahuanDimulainyaPenyidikanDocumentsTotal = $suratPemberitahuanDimulainyaPenyidikanDocuments->total();
            $suratPemberitahuanDimulainyaPenyidikanDocumentsTotalPage = $suratPemberitahuanDimulainyaPenyidikanDocuments->lastPage();
                                            
            // Packing data
            $arrayKey = 0;
            foreach($suratPemberitahuanDimulainyaPenyidikanDocuments as $suratPemberitahuanDimulainyaPenyidikanDocument){
                $dorsId = $suratPemberitahuanDimulainyaPenyidikanDocument->accident->dors_id ?? null;
                $documentSignatory = $suratPemberitahuanDimulainyaPenyidikanDocument
                    ->suratPemberitahuanDimulainyaPenyidikanDocumentOfficers
                    ->where('class', 'SIGNATORY')
                    ->first();

                $createdBy = $suratPemberitahuanDimulainyaPenyidikanDocument->createdByUser ?? null;
                $createdByRegisterNumber = $createdBy->register_number ?? null;

                $prosecutor = $suratPemberitahuanDimulainyaPenyidikanDocument->prosecutor ?? null;
                $prosecutorEmpId = $prosecutor->emp_id ?? null;

                $court = $suratPemberitahuanDimulainyaPenyidikanDocument->court ?? null;
                $courtEmpId = $court->emp_id ?? null;

                $carbonCopies = $suratPemberitahuanDimulainyaPenyidikanDocument->carbon_copies ?? null;
                // $carbonCopiesArray = ($carbonCopies) ? $carbonCopies : null;
                $carbonCopiesText = ($carbonCopies) ? implode(', ', $carbonCopies) : null;

                $suratPemberitahuanDimulainyaPenyidikanDocumentAttachment = $suratPemberitahuanDimulainyaPenyidikanDocument->suratPemberitahuanDimulainyaPenyidikanDocumentAttachment ?? null;

                $attachment = (isset($suratPemberitahuanDimulainyaPenyidikanDocumentAttachment->name)) ? base64_encode(File::get(public_path('documents/attachments/' . $suratPemberitahuanDimulainyaPenyidikanDocumentAttachment->name))) : null;

                $reportedPerson = $suratPemberitahuanDimulainyaPenyidikanDocument->reportedPersons->first();

                $suspects = [];
                foreach ($suratPemberitahuanDimulainyaPenyidikanDocument->suspects->where('flag', 'TERSANGKA') as $suspect) {
                    $suspects[] = [
                        "SKetetapanPenetapanTersangkaId"=> $suspect->suratKetetapanTentangPenetapanTersangkaDocument->first()->id ?? null,
                        "Id"=> $suspect->id ?? null,
                        "DORSId"=> strval($dorsId),
                        "SPDPId"=> $suratPemberitahuanDimulainyaPenyidikanDocument->id ?? null,
                        "NamaTersangka"=> $suspect->name ?? null,
                        "JenisKelaminTersangka"=> $suspect->gender->name ?? null,
                        "TempatLahirTersangka"=> $suspect->birth_place ?? null,
                        "TanggalLahirTersangka"=> $suspect->birth_date ?? null,
                        "PekerjaanIdTersangka"=> $suspect->job->emp_id ?? null,
                        "AlamatTersangka"=> $suspect->address ?? null,
                        "NegaraIdTersangka"=> $suspect->country->emp_id ?? null,
                        "AgamaIdTersangka"=> $suspect->religion->emp_id ?? null,
                        "CreatedDate" => ($suratPemberitahuanDimulainyaPenyidikanDocument->created_at) ? date('Y-m-d H:i:s', strtotime($suratPemberitahuanDimulainyaPenyidikanDocument->created_at)) : null,
                        "CreatedBy" => $createdByRegisterNumber,
                        "CreatedLocation" => $suratPemberitahuanDimulainyaPenyidikanDocument->ip_addresses['created_ip'] ?? null,
                    ];
                }
       
                $responseData[$arrayKey]  = [
                    "Id" => $suratPemberitahuanDimulainyaPenyidikanDocument->id,
                    "DORSId" => strval($dorsId),
                    "SprindikId" => ($suratPemberitahuanDimulainyaPenyidikanDocument->suratPerintahPenyidikanDocument) ? strval($suratPemberitahuanDimulainyaPenyidikanDocument->suratPerintahPenyidikanDocument->id) : null,
                    "SPTugasId"=> ($suratPemberitahuanDimulainyaPenyidikanDocument->suratPerintahTugasDocument) ? strval($suratPemberitahuanDimulainyaPenyidikanDocument->suratPerintahTugasDocument->id) : null,
                    "NoSurat" => $suratPemberitahuanDimulainyaPenyidikanDocument->document_number,
                    "Tanggal" => ($suratPemberitahuanDimulainyaPenyidikanDocument->document_date) ? date('Y-m-d H:i:s', strtotime($suratPemberitahuanDimulainyaPenyidikanDocument->document_date)) : null,
                    "KejaksaanId" => $prosecutorEmpId,
                    "PengadilanId" => $courtEmpId,
                    "Tembusan" => $carbonCopiesText,
                    "KategoriKejaksaan" => null,

                    "AdaTersangka" => ($suratPemberitahuanDimulainyaPenyidikanDocument->suspects->count() > 0) ? "Ada" : "Tidak Ada Tersangka",

                    "NamaPelapor" => null,
                    "TempatLahirPelapor" => null,
                    "TanggalLahirPelapor" => null,

                    "NamaTerlapor" => $reportedPerson->name ?? null,
                    "TempatLahirTerlapor" => $reportedPerson->birth_place ?? null,
                    "TanggalLahirTerlapor" => $reportedPerson->birth_date ?? null,

                    "Nama" => (!empty($documentSignatory)) ? PeopleNameHelper::getFullName($documentSignatory->first_title, $documentSignatory->first_name, $documentSignatory->last_name, $documentSignatory->last_title) : null,
                    "Nrp" => $documentSignatory->register_number ?? null,
                    "JabatanPenandatangan" => $documentSignatory->position->name ?? null,
                    "CreatedDate" => ($suratPemberitahuanDimulainyaPenyidikanDocument->created_at) ? date('Y-m-d H:i:s', strtotime($suratPemberitahuanDimulainyaPenyidikanDocument->created_at)) : null,
                    "CreatedBy" => $createdByRegisterNumber,
                    "CreatedLocation" => $suratPemberitahuanDimulainyaPenyidikanDocument->ip_addresses['created_ip'] ?? null,

                    "Tersangka_SPDP" => $suspects,

                    "Attachment" => $attachment,
                    "AttachmentMimeType" => $suratPemberitahuanDimulainyaPenyidikanDocumentAttachment->mimetype ?? null,

                    "released_at" => $suratPemberitahuanDimulainyaPenyidikanDocument->released_at,
                ];

                $docService->putApiSyncMoment(
                    $request, 
                    $suratPemberitahuanDimulainyaPenyidikanDocument,
                    get_class(new SuratPemberitahuanDimulainyaPenyidikanDocument()),
                    $this->tableSchemaName . 'surat_pemberitahuan_dimulainya_penyidikan_documents',
                    $mode
                );

                $arrayKey++;
            }
            
            // Check if data array is empty result
            if ($suratPemberitahuanDimulainyaPenyidikanDocuments->isEmpty()) {
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
                    "TotalData" => $suratPemberitahuanDimulainyaPenyidikanDocumentsTotal,
                    "TotalPage" => $suratPemberitahuanDimulainyaPenyidikanDocumentsTotalPage,
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