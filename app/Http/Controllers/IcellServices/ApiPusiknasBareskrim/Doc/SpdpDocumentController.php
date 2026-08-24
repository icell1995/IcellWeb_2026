<?php

namespace App\Http\Controllers\IcellServices\ApiPusiknasBareskrim\Doc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Helpers\PeopleNameHelper;
use App\Services\IcellServices\ApiPusiknasBareskrim\DocService;

use App\Models\Doc\SuratPemberitahuanDimulainyaPenyidikanDocument\SuratPemberitahuanDimulainyaPenyidikanDocument;

class SpdpDocumentController extends Controller
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
        $mode             = $request->input('mode');
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
            $documents = SuratPemberitahuanDimulainyaPenyidikanDocument::with([
                'accident',
                'suratPemberitahuanDimulainyaPenyidikanDocumentAttachment',
                'suratPemberitahuanDimulainyaPenyidikanDocumentOfficers',
                'suratPerintahPenyidikanDocument',
                'suratPerintahTugasDocument',
                'prosecutor',
                'court',
                'suspects',
                'reportedPersons',
            ])
            ->whereHas('accident.suratPemberitahuanDimulainyaPenyidikanDocuments', function ($query) {
                $query->whereIn('status_id', ['86']);
            })
            ->whereIn('status_id', $docService->requiredDocumentStatusIds)
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

            $totalData     = $documents->total();
            $totalPage     = $documents->lastPage();

            // Build SPPT-TI response
            $arrayKey = 0;
            foreach ($documents as $document) {
                $accident = $document->accident;

                // --- identitas_dokumen ---
                $identitasDokumen = [
                    'nomor'   => $document->document_number,
                    'tanggal' => $document->document_date
                        ? date('Y-m-d', strtotime($document->document_date))
                        : null,
                ];

                // --- pejabat_penandatangan ---
                $signatory = $document
                    ->suratPemberitahuanDimulainyaPenyidikanDocumentOfficers
                    ->where('class', 'SIGNATORY')
                    ->first();

                $pejabatPenandatangan = [];
                if (!empty($signatory)) {
                    $pejabatPenandatangan[] = [
                        'nama'         => PeopleNameHelper::getFullName(
                            $signatory->first_title,
                            $signatory->first_name,
                            $signatory->last_name,
                            $signatory->last_title
                        ),
                        'nomor_induk'  => $signatory->register_number ?? null,
                        'jabatan'      => $signatory->position->name ?? null,
                        'pangkat'      => $signatory->rank->name ?? null,
                    ];
                }

                // --- daftar_laporan ---
                $daftarLaporan = [];
                if ($accident) {
                    $daftarLaporan[] = [
                        'nomor'               => $accident->no_lp ?? null,
                        'tanggal'             => $accident->report_date
                            ? date('Y-m-d', strtotime($accident->report_date))
                            : null,
                        'kode_satker_penerbit' => $accident->polres->emp_id ?? null,
                    ];
                }

                // --- nomor & tanggal sprindik ---
                $suratPerintahPenyidikan = $document->suratPerintahPenyidikanDocument;
                $nomorSprindik   = $suratPerintahPenyidikan->document_number ?? null;
                $tanggalSprindik = ($suratPerintahPenyidikan && $suratPerintahPenyidikan->document_date)
                    ? date('Y-m-d', strtotime($suratPerintahPenyidikan->document_date))
                    : null;

                // --- daftar_uu_pasal ---
                $daftarUuPasal = [];
                if ($accident && !empty($accident->uu_pasal)) {
                    $rawPasal = is_array($accident->uu_pasal)
                        ? $accident->uu_pasal
                        : json_decode($accident->uu_pasal, true);
                    $daftarUuPasal = $rawPasal ?? [];
                }

                // --- daftar_kejadian_perkara ---
                $daftarKejadianPerkara = [];
                if ($accident) {
                    $daftarKejadianPerkara[] = [
                        'lokasi'       => $accident->accident_location ?? null,
                        'kode_wilayah' => $accident->regency->emp_id ?? ($accident->kode_wilayah ?? null),
                        'waktu'        => $accident->accident_time ?? null,
                        'tahun'        => $accident->accident_year
                            ? intval($accident->accident_year)
                            : null,
                        'bulan'        => $accident->accident_month
                            ? intval($accident->accident_month)
                            : null,
                        'tanggal'      => $accident->accident_date
                            ? intval($accident->accident_date)
                            : null,
                    ];
                }

                // --- daftar_terlapor_atau_tersangka ---
                $daftarTerlapor = [];
                foreach ($document->suspects as $suspect) {
                    $daftarTerlapor[] = [
                        'nama'                    => $suspect->name ?? null,
                        'tempat_lahir'            => $suspect->birth_place ?? null,
                        'tanggal_lahir'           => $suspect->birth_date
                            ? date('Y-m-d', strtotime($suspect->birth_date))
                            : null,
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
                        'status'                  => $suspect->flag === 'TERSANGKA' ? 1 : 2,
                    ];
                }

                // --- daftar_dokumen_digital ---
                $daftarDokumenDigital = [];
                $attachment = $document->suratPemberitahuanDimulainyaPenyidikanDocumentAttachment;
                if ($attachment && isset($attachment->name)) {
                    $filePath = public_path('documents/attachments/' . $attachment->name);
                    $daftarDokumenDigital[] = [
                        'kode_jenis_dokumen' => 'spdp',
                        'mime_type'          => $attachment->mimetype ?? 'application/pdf',
                        'file'               => File::exists($filePath)
                            ? base64_encode(File::get($filePath))
                            : null,
                        'url'                => null,
                    ];
                }

                // --- konten_dokumen ---
                $kontenDokumen = [
                    'daftar_laporan'                => $daftarLaporan,
                    'nomor_sprindik'                => $nomorSprindik,
                    'tanggal_sprindik'              => $tanggalSprindik,
                    'uraian_singkat_perkara'        => $document->description ?? null,
                    'daftar_uu_pasal'               => $daftarUuPasal,
                    'daftar_kejadian_perkara'       => $daftarKejadianPerkara,
                    'daftar_terlapor_atau_tersangka' => !empty($daftarTerlapor) ? $daftarTerlapor : null,
                    'sumber_dana'                   => null,
                    'sumber_informasi'              => null,
                    'pejabat_penandatangan'         => $pejabatPenandatangan,
                    'daftar_dokumen_digital'        => $daftarDokumenDigital,
                ];

                // --- root document ---
                $responseData[$arrayKey] = [
                    'kode_jenis_dokumen' => 'spdp',
                    'identitas_dokumen'  => $identitasDokumen,
                    'konten_dokumen'     => $kontenDokumen,
                    'terenkripsi'        => false,
                    'daftar_kunci_enkripsi' => [],
                    'tanda_tangan_digital'  => null,
                ];

                $docService->putApiSyncMoment(
                    $request,
                    $document,
                    get_class(new SuratPemberitahuanDimulainyaPenyidikanDocument()),
                    $this->tableSchemaName . 'surat_pemberitahuan_dimulainya_penyidikan_documents',
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
