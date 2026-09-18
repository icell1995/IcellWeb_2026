<?php

namespace App\Http\Controllers\IcellServices\ApiPusiknasBareskrim\Doc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Helpers\PeopleNameHelper;
use App\Services\IcellServices\ApiPusiknasBareskrim\DocService;

use App\Models\Doc\SuratPemberitahuanUpayaDiversiDocument\SuratPemberitahuanUpayaDiversiDocument;
use App\Models\LaporanPolisi;

class SpudDocumentController extends Controller
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
            $documents = SuratPemberitahuanUpayaDiversiDocument::with([
                'accident',
                'accident.polres',
                'accident.suspects',
                'accident.involvedPeoples',
                'suratPemberitahuanUpayaDiversiDocumentAttachment',
                'suratPemberitahuanUpayaDiversiDocumentOfficers',
                'suratPerintahPenyidikanDocument.suratPerintahPenyidikanDocumentAttachment',
                'suratPemberitahuanDimulainyaPenyidikanDocument',
                'prosecutor',
                'court',
                'suspect',
            ])
            ->whereHas('accident.suratPemberitahuanUpayaDiversiDocuments', function ($query) {
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
                $officer = $document
                    ->suratPemberitahuanUpayaDiversiDocumentOfficers
                    ->where('class', 'SIGNATORY')
                    ->first()
                    ?? $document->suratPemberitahuanUpayaDiversiDocumentOfficers->first();

                $namaFasilitator = null;
                if (!empty($officer)) {
                    $namaFasilitator = PeopleNameHelper::getFullName(
                        $officer->first_title,
                        $officer->first_name,
                        $officer->last_name,
                        $officer->last_title
                    );
                }

                $identitasDokumen = [
                    'nomor'            => $document->document_number,
                    'tanggal'          => $document->document_date
                        ? date('Y-m-d', strtotime($document->document_date))
                        : null,
                    'nama_fasilitator' => $namaFasilitator,
                ];

                // --- daftar_anak ---
                $daftarAnak = [];
                $suspect = $document->suspect;

                if ($suspect) {
                    $birthDate = $suspect->birth_date ? date('Y-m-d', strtotime($suspect->birth_date)) : null;
                    $age = null;
                    if (!empty($document->messages['suspect_age']) && is_numeric($document->messages['suspect_age'])) {
                        $age = intval($document->messages['suspect_age']);
                    } elseif (!empty($suspect->age) && is_numeric($suspect->age)) {
                        $age = intval($suspect->age);
                    } elseif ($birthDate) {
                        try {
                            $target = !empty($document->document_date) ? \Carbon\Carbon::parse($document->document_date) : \Carbon\Carbon::now();
                            $age = \Carbon\Carbon::parse($birthDate)->diffInYears($target);
                        } catch (\Exception $e) {
                            $age = null;
                        }
                    }

                    $daftarAnak[] = [
                        'nama'               => $suspect->name ?? null,
                        'tempat_lahir'       => $suspect->birth_place ?? null,
                        'tanggal_lahir'      => $birthDate,
                        'umur'               => $age,
                        'kode_jenis_kelamin' => isset($suspect->gender->emp_id) ? intval($suspect->gender->emp_id) : null,
                        'kode_warga_negara'  => !empty($suspect->country->code_alpha_3)
                            ? strtolower($suspect->country->code_alpha_3)
                            : ($suspect->country->emp_id ?? ($suspect->nationality ?? null)),
                        'alamat'             => [
                            'desa'      => $suspect->village->name ?? null,
                            'kecamatan' => $suspect->district->name ?? null,
                            'kabupaten' => $suspect->regency->name ?? null,
                            'provinsi'  => $suspect->province->name ?? null,
                        ],
                        'kode_agama'         => isset($suspect->religion->emp_id) ? intval($suspect->religion->emp_id) : null,
                        'kode_pekerjaan'     => isset($suspect->job->emp_id) ? intval($suspect->job->emp_id) : null,
                        'kode_pendidikan'    => isset($suspect->education->emp_id) ? intval($suspect->education->emp_id) : null,
                    ];
                }

                // --- daftar_pendamping_anak ---
                $daftarPendampingAnak = [];
                if ($suspect) {
                    $namaPendamping = $suspect->father_name ?? ($suspect->mother_name ?? null);
                    if (!empty($namaPendamping)) {
                        $daftarPendampingAnak[] = [
                            'kode_pendamping' => 1,
                            'nama_pendamping' => $namaPendamping,
                        ];
                    }
                }

                // --- daftar_korban ---
                $daftarKorban = [];
                $victims = collect();
                if ($accident && $accident->involvedPeoples) {
                    $victims = $accident->involvedPeoples->filter(function ($item) {
                        return in_array(strtoupper($item->flag ?? ''), ['KORBAN', 'VICTIM']) ||
                               in_array(strtoupper($item->class ?? ''), ['VICTIM', 'KORBAN']);
                    });

                    foreach ($victims as $victim) {
                        $daftarKorban[] = [
                            'nama'              => $victim->name ?? null,
                            'tempat_lahir'      => $victim->birth_place ?? null,
                            'umur'              => $victim->age ? intval($victim->age) : null,
                            'kode_warga_negara' => !empty($victim->country->code_alpha_3)
                                ? strtolower($victim->country->code_alpha_3)
                                : ($victim->country->emp_id ?? ($victim->nationality ?? null)),
                            'alamat'            => [
                                'desa'      => $victim->village->name ?? null,
                                'kecamatan' => $victim->district->name ?? null,
                                'kabupaten' => $victim->regency->name ?? null,
                                'provinsi'  => $victim->province->name ?? null,
                            ],
                            'kode_agama'        => isset($victim->religion->emp_id) ? intval($victim->religion->emp_id) : null,
                        ];
                    }
                }

                // --- daftar_pendamping_korban ---
                $daftarPendampingKorban = [];
                if (!empty($victims) && $victims->isNotEmpty()) {
                    $firstVictim = $victims->first();
                    $namaPendampingKorban = $firstVictim->father_name ?? ($firstVictim->mother_name ?? null);
                    if (!empty($namaPendampingKorban)) {
                        $daftarPendampingKorban[] = [
                            'kode_pendamping' => 1,
                            'nama_pendamping' => $namaPendampingKorban,
                        ];
                    }
                }

                // --- persetujuan_untuk_musyawarah & tanggal_musyawarah ---
                $persetujuanUntukMusyawarah = [
                    'persetujuan_anak'   => !empty($document->messages['persetujuan_anak']) ? intval($document->messages['persetujuan_anak']) : 1,
                    'persetujuan_korban' => !empty($document->messages['persetujuan_korban']) ? intval($document->messages['persetujuan_korban']) : 1,
                ];

                $tanggalMusyawarah = !empty($document->messages['tanggal_musyawarah'])
                    ? date('Y-m-d', strtotime($document->messages['tanggal_musyawarah']))
                    : null;

                // --- daftar_dokumen_digital ---
                $daftarDokumenDigital = [];

                // Dokumen SPUD
                $attachment = $document->suratPemberitahuanUpayaDiversiDocumentAttachment;
                if ($attachment && isset($attachment->name)) {
                    $filePath = public_path('documents/attachments/' . $attachment->name);
                    $daftarDokumenDigital[] = [
                        'kode_jenis_dokumen' => 'spud',
                        'mime_type'          => $attachment->mimetype ?? 'application/pdf',
                        'file'               => File::exists($filePath)
                            ? base64_encode(File::get($filePath))
                            : null,
                        'url'                => url('documents/attachments/' . $attachment->name),
                    ];
                }

                // Dokumen Sprindik
                $sprindik = $document->suratPerintahPenyidikanDocument;
                $sprindikAttachment = $sprindik ? $sprindik->suratPerintahPenyidikanDocumentAttachment : null;
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
                    'daftar_anak'                 => $daftarAnak,
                    'daftar_pendamping_anak'      => $daftarPendampingAnak,
                    'daftar_korban'               => $daftarKorban,
                    'daftar_pendamping_korban'    => $daftarPendampingKorban,
                    'persetujuan_untuk_musyawarah'=> $persetujuanUntukMusyawarah,
                    'tanggal_musyawarah'          => $tanggalMusyawarah,
                    'daftar_dokumen_digital'      => $daftarDokumenDigital,
                ];

                // --- root document ---
                $responseData[$arrayKey] = [
                    'kode_jenis_dokumen'    => 'spud',
                    'identitas_dokumen'     => $identitasDokumen,
                    'konten_dokumen'        => $kontenDokumen,
                    'terenkripsi'           => false,
                    'daftar_kunci_enkripsi' => [],
                    'tanda_tangan_digital'  => null,
                ];

                $docService->putApiSyncMoment(
                    $request,
                    $document,
                    get_class(new SuratPemberitahuanUpayaDiversiDocument()),
                    $this->tableSchemaName . 'surat_pemberitahuan_upaya_diversi_documents',
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
