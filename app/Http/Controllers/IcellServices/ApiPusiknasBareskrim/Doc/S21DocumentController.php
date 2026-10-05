<?php

namespace App\Http\Controllers\IcellServices\ApiPusiknasBareskrim\Doc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\IcellServices\ApiPusiknasBareskrim\DocService;
use App\Models\Doc\SuratPermohonanPerpanjanganPenahananKejaksaanDocument\SuratPermohonanPerpanjanganPenahananKejaksaanDocument;
use App\Http\Controllers\Docs\SuratPermohonanPerpanjanganPenahananKejaksaanDocumentController;

class S21DocumentController extends Controller
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
        $mode              = $request->input('mode');
        $startDocumentDate = $request->input('start_doc_date');
        $endDocumentDate   = $request->input('end_doc_date');
        $startReleaseDate  = $request->input('start_release_date');
        $endReleaseDate    = $request->input('end_release_date');
        $perPage           = $request->query('perPage', 100);
        $page              = $request->query('page', 1);
        $id                = $request->input('id');
        $nomor             = $request->input('nomor');
        $accidentId        = $request->input('accident_id');

        if (!is_numeric($page)) {
            $page = 1;
        }

        if (!is_numeric($perPage)) {
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
            $query = SuratPermohonanPerpanjanganPenahananKejaksaanDocument::with([
                'accident',
                'accident.polres',
                'accident.polres.polda',
                'accident.suratPemberitahuanDimulainyaPenyidikanDocuments',
                'accident.suratPerintahPenahananDocuments',
                'suratPermohonanPerpanjanganPenahananKejaksaanDocumentOfficers.rank',
                'suratPermohonanPerpanjanganPenahananKejaksaanDocumentOfficers.position',
                'suratPermohonanPerpanjanganPenahananKejaksaanDocumentOfficers.police',
                'status',
                'attachment'
            ])
            ->whereHas('accident', function ($q) {
                $q->whereNotNull('id');
            })
            ->orderBy('tanggal', 'ASC')
            ->orderBy('created_at', 'ASC');

            if ($id) {
                $query->where('id', $id);
            }
            if ($nomor) {
                $query->where(function ($q) use ($nomor) {
                    $q->where('nomor', $nomor)->orWhere('document_number', $nomor);
                });
            }
            if ($accidentId) {
                $query->where('accident_id', $accidentId);
            }

            // Exclude legacy documents
            $query = $docService->applyIncludeLegacyDocumentFilter($query, false);

            // Filter: released_at or created_at
            $query = $docService->applyDateRangeFilter($query, 'released_at', $startReleaseDate, $endReleaseDate);

            // Filter: tanggal dokumen
            $query = $docService->applyDateRangeFilter($query, 'tanggal', $startDocumentDate, $endDocumentDate);

            $documents = $query->paginate($perPage, ['*'], 'page', $page);

            if ($documents->isEmpty()) {
                return response()->json([
                    "code"       => "404",
                    "status"     => "NOT_FOUND",
                    "message"    => "Data not found.",
                    "pagination" => [
                        "Page"          => $page,
                        "TotalData"     => 0,
                        "TotalPage"     => 0,
                        "TotalDataSent" => 0,
                    ],
                    "data" => [],
                ], 404);
            }

            $responseData = [];

            foreach ($documents as $document) {
                $payload = SuratPermohonanPerpanjanganPenahananKejaksaanDocumentController::buildJsonPayloadStatic($document);
                $responseData[] = $payload;

                $docService->putApiSyncMoment(
                    $request,
                    $document,
                    get_class(new SuratPermohonanPerpanjanganPenahananKejaksaanDocument()),
                    $this->tableSchemaName . 'surat_permohonan_perpanjangan_penahanan_kejaksaan_documents',
                    $mode
                );
            }

            DB::commit();

            return response()->json([
                "code"       => "200",
                "status"     => "OK",
                "message"    => "Success",
                "pagination" => [
                    "Page"          => $page,
                    "TotalData"     => $documents->total(),
                    "TotalPage"     => $documents->lastPage(),
                    "TotalDataSent" => count($responseData),
                ],
                "data"       => $responseData,
            ], 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                "code"    => "500",
                "status"  => "INTERNAL_SERVER_ERROR",
                "message" => $e->getMessage(),
            ], 500);
        }
    }

    public function show(Request $request, $id)
    {
        try {
            $document = SuratPermohonanPerpanjanganPenahananKejaksaanDocument::with([
                'accident',
                'accident.polres',
                'accident.polres.polda',
                'accident.suratPemberitahuanDimulainyaPenyidikanDocuments',
                'accident.suratPerintahPenahananDocuments',
                'suratPermohonanPerpanjanganPenahananKejaksaanDocumentOfficers.rank',
                'suratPermohonanPerpanjanganPenahananKejaksaanDocumentOfficers.position',
                'suratPermohonanPerpanjanganPenahananKejaksaanDocumentOfficers.police',
                'status',
                'attachment'
            ])->findOrFail($id);

            $payload = SuratPermohonanPerpanjanganPenahananKejaksaanDocumentController::buildJsonPayloadStatic($document);

            // If raw requested, return the exact object requested by user
            if ($request->has('raw') || $request->wantsJson()) {
                return response()->json($payload, 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            }

            return response()->json([
                "code"    => "200",
                "status"  => "OK",
                "message" => "Success",
                "data"    => $payload,
            ], 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        } catch (\Exception $e) {
            return response()->json([
                "code"    => "404",
                "status"  => "NOT_FOUND",
                "message" => "Document not found.",
            ], 404);
        }
    }
}
