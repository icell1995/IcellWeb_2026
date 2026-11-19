<?php

namespace App\Http\Controllers\IcellServices\ApiPusiknasBareskrim\Doc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Services\IcellServices\ApiPusiknasBareskrim\DocService;

use App\Models\Doc\SuratPermohonanPenetapanDiversiDocument\SuratPermohonanPenetapanDiversiDocument;
use App\Models\LaporanPolisi;

class PpdPolDocumentController extends Controller
{
    protected $docService;
    private $tableSchemaName = 'doc' . '.';

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

        // Initialize variable
        $responseData = [];

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
            $documents = SuratPermohonanPenetapanDiversiDocument::with([
                'accident',
                'accident.polres',
                'accident.suspects',
                'accident.suratPerintahPenyidikanDocuments.attachment',
                'attachment',
                'suspect',
                'suratPerintahPenyidikanDocument.attachment',
            ])
            ->whereHas('accident', function ($query) {
                $query->whereNotNull('id');
            })
            ->orderBy('document_date', 'ASC');

            // Filter: exclude legacy documents
            $documents = $docService->applyIncludeLegacyDocumentFilter($documents, false);

            // Filter: released_at date range
            $documents = $docService->applyDateRangeFilter(
                $documents,
                'released_at',
                $startReleaseDate,
                $endReleaseDate
            );

            // Filter: document_date date range
            $documents = $docService->applyDateRangeFilter(
                $documents,
                'document_date',
                $startDocumentDate,
                $endDocumentDate
            );

            $documents = $documents->paginate($perPage, ['*'], 'page', $page);

            $totalData = $documents->total();
            $totalPage = $documents->lastPage();

            // Build SPPT-TI response
            $arrayKey = 0;
            foreach ($documents as $document) {
                $accident = $document->accident;
                $payload  = is_array($document->payload) ? $document->payload : [];

                // --- identitas_dokumen ---
                $identitasDokumen = [
                    'nomor'   => $document->document_number,
                    'tanggal' => $document->document_date
                        ? date('Y-m-d', strtotime($document->document_date))
                        : null,
                ];

                // --- daftar_nama_anak ---
                $daftarNamaAnak = [];
                if (!empty($payload['suspect_name'])) {
                    if (is_array($payload['suspect_name'])) {
                        $daftarNamaAnak = array_values(array_filter($payload['suspect_name']));
                    } else {
                        $daftarNamaAnak[] = (string) $payload['suspect_name'];
                    }
                } elseif (!empty($payload['childName'])) {
                    $daftarNamaAnak[] = (string) $payload['childName'];
                } elseif ($document->suspect && !empty($document->suspect->name)) {
                    $daftarNamaAnak[] = $document->suspect->name;
                } elseif ($accident && $accident->suspects && $accident->suspects->isNotEmpty()) {
                    $daftarNamaAnak = $accident->suspects->pluck('name')->filter()->values()->toArray();
                }

                // --- tanggal_musyawarah ---
                $tanggalMusyawarah = null;
                if (!empty($payload['meeting_date'])) {
                    $tanggalMusyawarah = date('Y-m-d', strtotime($payload['meeting_date']));
                } elseif (!empty($payload['agreement_date'])) {
                    $tanggalMusyawarah = date('Y-m-d', strtotime($payload['agreement_date']));
                } elseif (!empty($payload['diversionDate'])) {
                    $tanggalMusyawarah = date('Y-m-d', strtotime($payload['diversionDate']));
                }

                // --- daftar_dokumen_digital ---
                $daftarDokumenDigital = [];

                // Dokumen PPD-POL
                $attachment = $document->attachment ?? $document->suratPermohonanPenetapanDiversiDocumentAttachment;
                if ($attachment && isset($attachment->name)) {
                    $filePath = public_path('documents/attachments/' . $attachment->name);
                    $daftarDokumenDigital[] = [
                        'kode_jenis_dokumen' => 'ppd-pol',
                        'mime_type'          => $attachment->mimetype ?? 'application/pdf',
                        'file'               => File::exists($filePath)
                            ? base64_encode(File::get($filePath))
                            : null,
                        'url'                => url('documents/attachments/' . $attachment->name),
                    ];
                }

                // Dokumen Sprindik
                $sprindik = $document->suratPerintahPenyidikanDocument
                    ?? ($accident && $accident->suratPerintahPenyidikanDocuments ? $accident->suratPerintahPenyidikanDocuments->sortByDesc('created_at')->first() : null);
                $sprindikAttachment = $sprindik ? ($sprindik->suratPerintahPenyidikanDocumentAttachment ?? $sprindik->attachment) : null;
                if ($sprindikAttachment && isset($sprindikAttachment->name)) {
                    $filePath = public_path('documents/attachments/' . $sprindikAttachment->name);
                    $daftarDokumenDigital[] = [
                        'kode_jenis_dokumen' => 'sprindik',
                        'mime_type'          => $sprindikAttachment->mimetype ?? 'application/pdf',
                        'file'               => File::exists($filePath)
                            ? base64_encode(File::get($filePath))
                            : null,
                    ];
                }

                // Dokumen Laporan Polisi (LP)
                if ($accident) {
                    $lp = LaporanPolisi::where('accident_id', $accident->id)->latest()->first();
                    if ($lp && isset($lp->name)) {
                        $filePath = public_path('file/tugas/laporan_polisi/' . $lp->name);
                        $daftarDokumenDigital[] = [
                            'kode_jenis_dokumen' => 'lp',
                            'mime_type'          => 'application/pdf',
                            'file'               => File::exists($filePath)
                                ? base64_encode(File::get($filePath))
                                : null,
                        ];
                    }
                }

                // --- konten_dokumen ---
                $kontenDokumen = [
                    'daftar_nama_anak'       => $daftarNamaAnak,
                    'tanggal_musyawarah'     => $tanggalMusyawarah,
                    'daftar_dokumen_digital' => $daftarDokumenDigital,
                ];

                // --- root document ---
                $responseData[$arrayKey] = [
                    'kode_jenis_dokumen'    => 'ppd-pol',
                    'identitas_dokumen'     => $identitasDokumen,
                    'konten_dokumen'        => $kontenDokumen,
                    'terenkripsi'           => false,
                    'daftar_kunci_enkripsi' => [],
                    'tanda_tangan_digital'  => null,
                ];

                $docService->putApiSyncMoment(
                    $request,
                    $document,
                    get_class(new SuratPermohonanPenetapanDiversiDocument()),
                    $this->tableSchemaName . 'surat_permohonan_penetapan_diversi_documents',
                    $mode
                );

                $arrayKey++;
            }

            // Check if data array is empty
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

            // Commit transaction
            DB::commit();

            // Return Result JSON
            return response()->json([
                "code"       => "200",
                "status"     => "OK",
                "message"    => "Success",
                "pagination" => [
                    "Page"          => $page,
                    "TotalData"     => $totalData,
                    "TotalPage"     => $totalPage,
                    "TotalDataSent" => count($responseData),
                ],
                "data" => $responseData,
            ], 200);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                "code"       => "500",
                "status"     => "INTERNAL_SERVER_ERROR",
                "message"    => "An error occurred while processing your request.",
                "pagination" => [
                    "Page"          => $page,
                    "TotalData"     => 0,
                    "TotalPage"     => 0,
                    "TotalDataSent" => 0,
                ],
                "data" => [],
            ], 500);
        }
    }
}
