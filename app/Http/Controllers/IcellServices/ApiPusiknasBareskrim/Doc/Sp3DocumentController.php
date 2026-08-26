<?php

namespace App\Http\Controllers\IcellServices\ApiPusiknasBareskrim\Doc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Helpers\PeopleNameHelper;
use App\Services\IcellServices\ApiPusiknasBareskrim\DocService;

use App\Models\SP3;
use App\Models\Officer;

class Sp3DocumentController extends Controller
{
    protected $docService;
    private $tableSchemaName = 'public' . '.';

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
            $documents = SP3::with([
                'accident',
                'accident.polres',
                'accident.suspects',
                'accident.suratPemberitahuanDimulainyaPenyidikanDocuments',
            ])
            ->whereHas('accident', function ($query) {
                $query->whereNotNull('id');
            })
            ->orderBy('created_at', 'ASC');

            // Filter: created_at date range (sebagai pengganti released_at)
            $documents = $docService->applyDateRangeFilter(
                $documents,
                'created_at',
                $startReleaseDate,
                $endReleaseDate
            );

            // Filter: tanggal dokumen (created_at)
            $documents = $docService->applyDateRangeFilter(
                $documents,
                'created_at',
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

                // --- identitas_dokumen ---
                // SP3: nomor, tanggal, nomor_spdp
                $nomorSpdp = $document->no_spdp ?? null;

                // Ambil nomor SPDP dari relasi jika ada
                if (empty($nomorSpdp) && $accident) {
                    $spdpDoc = $accident->suratPemberitahuanDimulainyaPenyidikanDocuments->first();
                    $nomorSpdp = $spdpDoc->document_number ?? null;
                }

                $identitasDokumen = [
                    'nomor'      => $document->no_sp3,
                    'tanggal'    => $document->tanggal_berlaku
                        ? date('Y-m-d', strtotime($document->tanggal_berlaku))
                        : ($document->created_at
                            ? date('Y-m-d', strtotime($document->created_at))
                            : null),
                    'nomor_spdp' => $nomorSpdp,
                ];

                // --- kode_alasan ---
                // Field 'alasan' di tabel sp3 adalah string;
                // di SPPT-TI kode_alasan adalah array integer.
                // Jika alasan tersimpan sebagai JSON array, decode; jika string angka, wrap ke array.
                $rawAlasan = $document->alasan;
                $kodeAlasan = [];
                if (!empty($rawAlasan)) {
                    $decoded = json_decode($rawAlasan, true);
                    if (is_array($decoded)) {
                        $kodeAlasan = array_map('intval', $decoded);
                    } elseif (is_numeric($rawAlasan)) {
                        $kodeAlasan = [intval($rawAlasan)];
                    }
                }

                // --- parse lampiran metadata ---
                $lampiranMetadata = json_decode($document->lampiran, true);
                $signatoryId = null;
                $suspectIds = [];
                $fileName = null;

                if (is_array($lampiranMetadata)) {
                    $signatoryId = $lampiranMetadata['signatory_id'] ?? null;
                    $suspectIds = $lampiranMetadata['suspect_ids'] ?? [];
                    $fileName = $lampiranMetadata['file_name'] ?? null;
                } else {
                    $fileName = $document->lampiran;
                }

                // --- pejabat_penandatangan ---
                // SP3 tidak memiliki relasi officer tersendiri;
                // gunakan officer dari accident/polres jika tersedia.
                $pejabatPenandatangan = [];
                if ($signatoryId) {
                    $officer = Officer::with(['position', 'rank'])->find($signatoryId);
                    if ($officer) {
                        $pejabatPenandatangan[] = [
                            'nama'        => $officer->full_name ?? ($officer->first_name . ' ' . $officer->last_name),
                            'nomor_induk' => $officer->nrp ?? null,
                            'jabatan'     => $officer->position->name ?? null,
                            'pangkat'     => $officer->rank->name ?? null,
                        ];
                    }
                }

                // --- daftar_terlapor_atau_tersangka ---
                $daftarTerlapor = [];
                if ($accident) {
                    foreach ($accident->suspects as $suspect) {
                        if (!empty($suspectIds) && !in_array($suspect->id, $suspectIds)) {
                            continue;
                        }
                        $daftarTerlapor[] = [
                            'nama'                    => $suspect->name ?? null,
                            'tempat_lahir'            => $suspect->birth_place ?? null,
                            'kode_jenis_kelamin'      => $suspect->gender->emp_id ?? null,
                            'alamat'                  => $suspect->address ?? null,
                            'kode_wilayah'            => $suspect->location->emp_id ?? null,
                            'kode_pendidikan'         => $suspect->education->emp_id ?? null,
                            'kode_pekerjaan'          => $suspect->job->emp_id ?? null,
                            'nama_ibu'                => $suspect->mother_name ?? null,
                            'kode_agama'              => $suspect->religion->emp_id ?? null,
                            'kode_status_perkawinan'  => $suspect->maritalStatus->emp_id ?? null,
                            'kode_warga_negara'       => $suspect->country->emp_id ?? null,
                            'umur_saat_tindak_pidana' => $suspect->age_at_crime ?? null,
                            'daftar_uu_pasal'         => [],
                        ];
                    }
                }

                // --- daftar_dokumen_digital ---
                // SP3 model lama tidak memiliki attachment terpisah;
                // lampiran tersimpan di field 'lampiran' (string nama file)
                $daftarDokumenDigital = [];
                if (!empty($fileName)) {
                    $filePath = public_path('documents/attachments/' . $fileName);
                    $daftarDokumenDigital[] = [
                        'kode_jenis_dokumen' => 'sp3',
                        'mime_type'          => 'application/pdf',
                        'file'               => File::exists($filePath)
                            ? base64_encode(File::get($filePath))
                            : null,
                        'url'                => null,
                    ];
                }

                // --- konten_dokumen ---
                $kontenDokumen = [
                    'kode_alasan'                   => $kodeAlasan,
                    'pejabat_penandatangan'          => $pejabatPenandatangan,
                    'daftar_terlapor_atau_tersangka' => !empty($daftarTerlapor) ? $daftarTerlapor : null,
                    'daftar_dokumen_digital'         => $daftarDokumenDigital,
                ];

                // --- root document ---
                $responseData[$arrayKey] = [
                    'kode_jenis_dokumen'    => 'sp3',
                    'identitas_dokumen'     => $identitasDokumen,
                    'konten_dokumen'        => $kontenDokumen,
                    'terenkripsi'           => false,
                    'daftar_kunci_enkripsi' => [],
                    'tanda_tangan_digital'  => null,
                ];

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
