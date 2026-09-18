<?php

namespace App\Http\Controllers\IcellServices\ApiPusiknasBareskrim\Doc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\IcellServices\ApiPusiknasBareskrim\DocService;

use App\Models\BeritaAcaraPenahanan;
use App\Models\Officer;

class BaHanDocumentController extends Controller
{
    protected $docService;
    private $tableName = 'berita_acara_penahanan';

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
            $documents = BeritaAcaraPenahanan::with([
                'accident',
                'accident.polres',
                'createdByUser.rank',
            ])
            ->whereHas('accident', function ($query) {
                $query->whereNotNull('id');
            })
            ->orderBy('document_date', 'ASC');

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
                $accident   = $document->accident;
                $properties = is_array($document->properties) ? $document->properties : [];

                // --- identitas_dokumen ---
                $nomorSuratPerintah = $properties['surat_perintah_penahanan_document_number']
                    ?? $document->reference_document_number
                    ?? $document->initial
                    ?? ($accident ? $accident->no_lp : '-');

                $tanggalDokumen = $document->document_date
                    ? date('Y-m-d', strtotime($document->document_date))
                    : ($document->created_at ? $document->created_at->format('Y-m-d') : date('Y-m-d'));

                $identitasDokumen = [
                    'nomor_surat_perintah_penahanan' => $nomorSuratPerintah,
                    'tanggal'                        => $tanggalDokumen,
                ];

                // --- konten_dokumen: tanggal_mulai_penahanan ---
                $tanggalMulaiPenahanan = !empty($properties['start_date'])
                    ? date('Y-m-d', strtotime($properties['start_date']))
                    : $tanggalDokumen;

                // --- konten_dokumen: kode_satker_tempat_penahanan ---
                $kodeSatker = null;
                if ($accident && $accident->polres) {
                    $kodeSatker = $accident->polres->emp_id
                        ?? $accident->polres->spptti_id
                        ?? $accident->polres->satker_code
                        ?? $accident->polres->name;
                }
                if (empty($kodeSatker)) {
                    $kodeSatker = $properties['detention_place'] ?? '-';
                }

                // --- konten_dokumen: pejabat_penandatangan[] ---
                $pejabatPenandatangan = [];
                $officerIds           = [];

                if (!empty($properties['officer_leader_id'])) {
                    $officerIds[] = $properties['officer_leader_id'];
                }

                if (!empty($properties['internal_officers']) && is_array($properties['internal_officers'])) {
                    foreach ($properties['internal_officers'] as $ioId) {
                        if (!in_array($ioId, $officerIds)) {
                            $officerIds[] = $ioId;
                        }
                    }
                }

                if (!empty($officerIds)) {
                    $officers = Officer::where(function ($query) use ($officerIds) {
                        $query->whereIn('register_number', $officerIds)
                            ->orWhereIn('id', $officerIds);
                    })
                    ->with(['rank', 'position'])
                    ->get();

                    foreach ($officers as $officer) {
                        $fullName = trim(
                            ($officer->first_title ? $officer->first_title . ' ' : '') .
                            $officer->first_name . ' ' . $officer->last_name .
                            ($officer->last_title ? ', ' . $officer->last_title : '')
                        );

                        if (empty($fullName)) {
                            $fullName = trim($officer->first_name . ' ' . $officer->last_name);
                        }

                        $pejabatPenandatangan[] = [
                            'nama'        => !empty($fullName) ? $fullName : ($officer->first_name ?? '-'),
                            'nomor_induk' => (string) ($officer->register_number ?? $officer->identity_number ?? $officer->id),
                            'jabatan'     => $officer->position_short_name ?? ($officer->position->name ?? 'Penyidik'),
                            'pangkat'     => $officer->rank_short_name ?? ($officer->rank->name ?? '-'),
                        ];
                    }
                }

                // Fallback pejabat jika belum didapatkan
                if (empty($pejabatPenandatangan)) {
                    $creator = $document->createdByUser;
                    if ($creator) {
                        $pejabatPenandatangan[] = [
                            'nama'        => $creator->name ?? 'Penyidik',
                            'nomor_induk' => (string) ($creator->username ?? $creator->id),
                            'jabatan'     => 'Penyidik',
                            'pangkat'     => $creator->rank->name ?? ($creator->rank->rank_short_name ?? '-'),
                        ];
                    } else {
                        $pejabatPenandatangan[] = [
                            'nama'        => 'Penyidik',
                            'nomor_induk' => '-',
                            'jabatan'     => 'Penyidik',
                            'pangkat'     => '-',
                        ];
                    }
                }

                // --- konten_dokumen ---
                $kontenDokumen = [
                    'tanggal_mulai_penahanan'      => $tanggalMulaiPenahanan,
                    'kode_satker_tempat_penahanan' => $kodeSatker,
                    'pejabat_penandatangan'        => $pejabatPenandatangan,
                ];

                // --- root document ---
                $responseData[$arrayKey] = [
                    'kode_jenis_dokumen'    => 'ba-han',
                    'identitas_dokumen'     => $identitasDokumen,
                    'konten_dokumen'        => $kontenDokumen,
                    'terenkripsi'           => false,
                    'daftar_kunci_enkripsi' => [],
                    'tanda_tangan_digital'  => null,
                ];

                $docService->putApiSyncMoment(
                    $request,
                    $document,
                    get_class(new BeritaAcaraPenahanan()),
                    $this->tableName,
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
