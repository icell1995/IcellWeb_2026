<?php

namespace App\Http\Controllers\IcellServices\ApiEmpBareskrim\V2\Doc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Helpers\PeopleNameHelper;
use App\Services\IcellServices\ApiEmpBareskrim\V2\DocService;

use App\Models\Doc\SuratPemberitahuanDimulainyaPenyidikanDocument\SuratPemberitahuanDimulainyaPenyidikanDocument;
use App\Models\Doc\SuratPemberitahuanDimulainyaPenyidikanPusiknasDocument\SuratPemberitahuanDimulainyaPenyidikanPusiknasDocument;

class SuratPemberitahuanDimulainyaPenyidikanDocumentController extends Controller
{
    protected $docService;
    private $tableSchemaName = 'doc.';

    public function __construct(DocService $docService)
    {
        $this->docService = $docService;
    }

    public function index(Request $request)
    {
        $docService = $this->docService;

        // Get request data
        $mode = $request->input('mode');
        $startDocumentDate = $request->input('start_doc_date');
        $endDocumentDate = $request->input('end_doc_date');
        $startReleaseDate = $request->input('start_release_date');
        $endReleaseDate = $request->input('end_release_date');
        $perPage = $request->query('perPage', 100);
        $page = $request->query('page', 1);

        $responseData = [];

        if (!is_numeric($page)) {
            $page = 1;
        }

        if (!is_numeric($perPage)) {
            $perPage = 100;
        }

        $page = intval($page);
        $perPage = intval($perPage);

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
            // Query 1: SPDP Lama
            $queryOld = SuratPemberitahuanDimulainyaPenyidikanDocument::with([
                'accident',
                'suratPemberitahuanDimulainyaPenyidikanDocumentAttachment',
                'suratPemberitahuanDimulainyaPenyidikanDocumentOfficers.position',
                'suratPerintahPenyidikanDocument',
                'suratPerintahTugasDocument',
                'prosecutor',
                'court',
                'suspects',
                'reportedPersons',
                'createdByUser'
            ])
            ->whereHas('accident.suratPemberitahuanDimulainyaPenyidikanDocuments', function ($query) {
                $query->whereIn('status_id', ['86']);
            })
            ->whereIn('status_id', $docService->requiredDocumentStatusIds)
            ->orderBy('document_date', 'ASC');

            $queryOld = $docService->applyIncludeLegacyDocumentFilter($queryOld, false);
            $queryOld = $docService->applyDateRangeFilter($queryOld, 'released_at', $startReleaseDate, $endReleaseDate);
            $queryOld = $docService->applyDateRangeFilter($queryOld, 'document_date', $startDocumentDate, $endDocumentDate);

            // Query 2: SPDP Pusiknas Baru
            $queryNew = SuratPemberitahuanDimulainyaPenyidikanPusiknasDocument::with([
                'accident',
                'attachment',
                'attachments',
                'officers.position',
                'suratPerintahPenyidikanDocument',
                'suratPerintahTugasDocument',
                'prosecutor',
                'court',
                'suspects',
                'reportedPersons',
                'createdByUser'
            ])
            ->whereHas('accident.suratPemberitahuanDimulainyaPenyidikanPusiknasDocuments', function ($query) {
                $query->whereIn('status_id', ['86']);
            })
            ->whereIn('status_id', $docService->requiredDocumentStatusIds)
            ->orderBy('document_date', 'ASC');

            $queryNew = $docService->applyIncludeLegacyDocumentFilter($queryNew, false);
            $queryNew = $docService->applyDateRangeFilter($queryNew, 'released_at', $startReleaseDate, $endReleaseDate);
            $queryNew = $docService->applyDateRangeFilter($queryNew, 'document_date', $startDocumentDate, $endDocumentDate);

            $totalOld = $queryOld->count();
            $totalNew = $queryNew->count();
            $totalCombined = $totalOld + $totalNew;
            $totalPage = $totalCombined > 0 ? (int) ceil($totalCombined / $perPage) : 0;

            if ($totalCombined === 0) {
                DB::commit();
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

            // Pagination calculation across both queries
            $offset = ($page - 1) * $perPage;
            if ($offset < $totalOld) {
                $limitOld = min($perPage, $totalOld - $offset);
                $itemsOld = $queryOld->skip($offset)->take($limitOld)->get();

                $remainingNeeded = $perPage - $limitOld;
                if ($remainingNeeded > 0 && $totalNew > 0) {
                    $itemsNew = $queryNew->skip(0)->take($remainingNeeded)->get();
                } else {
                    $itemsNew = collect();
                }
                $documents = $itemsOld->concat($itemsNew);
            } else {
                $offsetNew = $offset - $totalOld;
                $documents = $queryNew->skip($offsetNew)->take($perPage)->get();
            }

            // Packing data with 100% identical element structure
            $arrayKey = 0;
            foreach ($documents as $document) {
                $isPusiknas = ($document instanceof SuratPemberitahuanDimulainyaPenyidikanPusiknasDocument);
                $dorsId = $document->accident->dors_id ?? null;

                if ($isPusiknas) {
                    $documentSignatory = $document->officers->where('class', 'SIGNATORY')->first();
                    $attachmentObj = $document->attachment ?? ($document->attachments->first() ?? null);
                    $tableName = $this->tableSchemaName . 'surat_pemberitahuan_dimulainya_penyidikan_pusiknas_documents';
                    $classModel = SuratPemberitahuanDimulainyaPenyidikanPusiknasDocument::class;
                } else {
                    $documentSignatory = $document->suratPemberitahuanDimulainyaPenyidikanDocumentOfficers->where('class', 'SIGNATORY')->first();
                    $attachmentObj = $document->suratPemberitahuanDimulainyaPenyidikanDocumentAttachment ?? null;
                    $tableName = $this->tableSchemaName . 'surat_pemberitahuan_dimulainya_penyidikan_documents';
                    $classModel = SuratPemberitahuanDimulainyaPenyidikanDocument::class;
                }

                $createdBy = $document->createdByUser ?? null;
                $createdByRegisterNumber = $createdBy->register_number ?? null;

                $prosecutor = $document->prosecutor ?? null;
                $prosecutorEmpId = $prosecutor->emp_id ?? null;

                $court = $document->court ?? null;
                $courtEmpId = $court->emp_id ?? null;

                $carbonCopies = $document->carbon_copies ?? null;
                $carbonCopiesText = ($carbonCopies && is_array($carbonCopies)) ? implode(', ', $carbonCopies) : (is_string($carbonCopies) ? $carbonCopies : null);

                $attachment = (isset($attachmentObj->name) && File::exists(public_path('documents/attachments/' . $attachmentObj->name)))
                    ? base64_encode(File::get(public_path('documents/attachments/' . $attachmentObj->name)))
                    : null;

                $reportedPerson = $document->reportedPersons->first();

                $suspects = [];
                foreach ($document->suspects->where('flag', 'TERSANGKA') as $suspect) {
                    $suspects[] = [
                        "SKetetapanPenetapanTersangkaId" => $suspect->suratKetetapanTentangPenetapanTersangkaDocument->first()->id ?? null,
                        "Id" => $suspect->id ?? null,
                        "DORSId" => strval($dorsId),
                        "SPDPId" => $document->id ?? null,
                        "NamaTersangka" => $suspect->name ?? null,
                        "JenisKelaminTersangka" => $suspect->gender->name ?? null,
                        "TempatLahirTersangka" => $suspect->birth_place ?? null,
                        "TanggalLahirTersangka" => $suspect->birth_date ?? null,
                        "PekerjaanIdTersangka" => $suspect->job->emp_id ?? null,
                        "AlamatTersangka" => $suspect->address ?? null,
                        "NegaraIdTersangka" => $suspect->country->emp_id ?? null,
                        "AgamaIdTersangka" => $suspect->religion->emp_id ?? null,
                        "CreatedDate" => ($document->created_at) ? date('Y-m-d H:i:s', strtotime($document->created_at)) : null,
                        "CreatedBy" => $createdByRegisterNumber,
                        "CreatedLocation" => is_array($document->ip_addresses) ? ($document->ip_addresses['created_ip'] ?? null) : null,
                    ];
                }

                $responseData[$arrayKey] = [
                    "Id" => $document->id,
                    "DORSId" => strval($dorsId),
                    "SprindikId" => ($document->suratPerintahPenyidikanDocument) ? strval($document->suratPerintahPenyidikanDocument->id) : null,
                    "SPTugasId" => ($document->suratPerintahTugasDocument) ? strval($document->suratPerintahTugasDocument->id) : null,
                    "NoSurat" => $document->document_number,
                    "Tanggal" => ($document->document_date) ? date('Y-m-d H:i:s', strtotime($document->document_date)) : null,
                    "KejaksaanId" => $prosecutorEmpId,
                    "PengadilanId" => $courtEmpId,
                    "Tembusan" => $carbonCopiesText,
                    "KategoriKejaksaan" => null,

                    "AdaTersangka" => ($document->suspects->count() > 0) ? "Ada" : "Tidak Ada Tersangka",

                    "NamaPelapor" => null,
                    "TempatLahirPelapor" => null,
                    "TanggalLahirPelapor" => null,

                    "NamaTerlapor" => $reportedPerson->name ?? null,
                    "TempatLahirTerlapor" => $reportedPerson->birth_place ?? null,
                    "TanggalLahirTerlapor" => $reportedPerson->birth_date ?? null,

                    "Nama" => (!empty($documentSignatory)) ? PeopleNameHelper::getFullName($documentSignatory->first_title, $documentSignatory->first_name, $documentSignatory->last_name, $documentSignatory->last_title) : null,
                    "Nrp" => $documentSignatory->register_number ?? null,
                    "JabatanPenandatangan" => $documentSignatory->position->name ?? null,
                    "CreatedDate" => ($document->created_at) ? date('Y-m-d H:i:s', strtotime($document->created_at)) : null,
                    "CreatedBy" => $createdByRegisterNumber,
                    "CreatedLocation" => is_array($document->ip_addresses) ? ($document->ip_addresses['created_ip'] ?? null) : null,

                    "Tersangka_SPDP" => $suspects,

                    "Attachment" => $attachment,
                    "AttachmentMimeType" => $attachmentObj->mimetype ?? null,

                    "released_at" => $document->released_at,
                ];

                $docService->putApiSyncMoment(
                    $request,
                    $document,
                    $classModel,
                    $tableName,
                    $mode
                );

                $arrayKey++;
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
                    "TotalData" => $totalCombined,
                    "TotalPage" => $totalPage,
                    "TotalDataSent" => count($responseData)
                ],
                "data" => $responseData
            ], 200);

        } catch (\Exception $e) {
            DB::rollback();
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