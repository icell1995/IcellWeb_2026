<?php

namespace App\Http\Controllers\IcellServices\ApiPusiknasBareskrim\Doc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Helpers\PeopleNameHelper;
use App\Services\IcellServices\ApiPusiknasBareskrim\DocService;

use App\Models\Doc\SuratKesepakatanDiversiDocument\SuratKesepakatanDiversiDocument;
use App\Models\Officer;
use App\Models\LaporanPolisi;

class SkdPolDocumentController extends Controller
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
            $documents = SuratKesepakatanDiversiDocument::with([
                'accident',
                'accident.polres',
                'accident.suspects',
                'accident.involvedPeoples',
                'accident.suratPerintahPenyidikanDocuments.attachment',
                'attachment',
                'suratKesepakatanDiversiDocumentOfficers',
                'suspect',
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
                $namaFasilitator = null;
                $officer = $document
                    ->suratKesepakatanDiversiDocumentOfficers
                    ->where('class', 'SIGNATORY')
                    ->first()
                    ?? $document->suratKesepakatanDiversiDocumentOfficers->first();

                if (!empty($officer)) {
                    $namaFasilitator = PeopleNameHelper::getFullName(
                        $officer->first_title,
                        $officer->first_name,
                        $officer->last_name,
                        $officer->last_title
                    );
                } elseif (!empty($payload['facilitatorOfficer'])) {
                    $fOfficer = Officer::where('register_number', $payload['facilitatorOfficer'])
                        ->orWhere('id', $payload['facilitatorOfficer'])
                        ->first();
                    if ($fOfficer) {
                        $namaFasilitator = PeopleNameHelper::getFullName(
                            $fOfficer->first_title,
                            $fOfficer->first_name,
                            $fOfficer->last_name,
                            $fOfficer->last_title
                        );
                    }
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

                if ($suspect || !empty($payload['childName'])) {
                    $birthDate = !empty($payload['childBirthDate'])
                        ? date('Y-m-d', strtotime($payload['childBirthDate']))
                        : ($suspect && $suspect->birth_date ? date('Y-m-d', strtotime($suspect->birth_date)) : null);

                    $age = null;
                    if (!empty($payload['childAgeYear']) && is_numeric($payload['childAgeYear'])) {
                        $age = intval($payload['childAgeYear']);
                    } elseif ($suspect && !empty($suspect->age) && is_numeric($suspect->age)) {
                        $age = intval($suspect->age);
                    } elseif ($birthDate) {
                        try {
                            $target = !empty($document->document_date) ? \Carbon\Carbon::parse($document->document_date) : \Carbon\Carbon::now();
                            $age = \Carbon\Carbon::parse($birthDate)->diffInYears($target);
                        } catch (\Exception $e) {
                            $age = null;
                        }
                    }

                    $desa      = $suspect->village->name ?? ($payload['childAddress'] ?? null);
                    $kecamatan = $suspect->district->name ?? null;
                    $kabupaten = $suspect->regency->name ?? null;
                    $provinsi  = $suspect->province->name ?? null;

                    $kodeAgama = null;
                    if (!empty($payload['childReligion']) && is_numeric($payload['childReligion'])) {
                        $kodeAgama = intval($payload['childReligion']);
                    } elseif ($suspect && isset($suspect->religion->emp_id) && is_numeric($suspect->religion->emp_id)) {
                        $kodeAgama = intval($suspect->religion->emp_id);
                    }

                    $kodeWargaNegara = null;
                    if ($suspect && !empty($suspect->country->code_alpha_3)) {
                        $kodeWargaNegara = strtolower($suspect->country->code_alpha_3);
                    } elseif ($suspect && !empty($suspect->country->emp_id)) {
                        $kodeWargaNegara = $suspect->country->emp_id;
                    } elseif (!empty($payload['childNationality'])) {
                        $nat = \App\Models\Lib\Nationality::find($payload['childNationality']);
                        if ($nat && in_array(strtoupper($nat->emp_id ?? $nat->name ?? ''), ['WNI', 'INDONESIA'])) {
                            $kodeWargaNegara = 'idn';
                        } else {
                            $kodeWargaNegara = $nat->code ?? ($nat->emp_id ?? null);
                        }
                    } elseif ($suspect && !empty($suspect->nationality)) {
                        $kodeWargaNegara = $suspect->nationality;
                    }

                    $daftarAnak[] = [
                        'nama'              => $payload['childName'] ?? ($suspect->name ?? null),
                        'tempat_lahir'      => $payload['childBirthPlace'] ?? ($suspect->birth_place ?? null),
                        'umur'              => $age,
                        'kode_warga_negara' => $kodeWargaNegara,
                        'alamat'            => [
                            'desa'      => $desa,
                            'kecamatan' => $kecamatan,
                            'kabupaten' => $kabupaten,
                            'provinsi'  => $provinsi,
                        ],
                        'kode_agama'        => $kodeAgama,
                    ];
                }

                // --- daftar_pendamping_anak ---
                $daftarPendampingAnak = [];
                $childGuardianName = $payload['childGuardianName'] ?? ($suspect->father_name ?? ($suspect->mother_name ?? null));
                if (!empty($childGuardianName)) {
                    $daftarPendampingAnak[] = [
                        'kode_pendamping' => 1,
                        'nama_pendamping' => $childGuardianName,
                    ];
                }

                // --- daftar_korban ---
                $daftarKorban = [];
                if (!empty($payload['victimName'])) {
                    $vAge = null;
                    if (!empty($payload['victimAgeYear']) && is_numeric($payload['victimAgeYear'])) {
                        $vAge = intval($payload['victimAgeYear']);
                    }

                    $vAgama = null;
                    if (!empty($payload['victimReligion']) && is_numeric($payload['victimReligion'])) {
                        $vAgama = intval($payload['victimReligion']);
                    }

                    $vKodeWargaNegara = 'idn';
                    if (!empty($payload['victimNationality'])) {
                        $vNat = \App\Models\Lib\Nationality::find($payload['victimNationality']);
                        if ($vNat && in_array(strtoupper($vNat->emp_id ?? $vNat->name ?? ''), ['WNI', 'INDONESIA'])) {
                            $vKodeWargaNegara = 'idn';
                        } elseif ($vNat) {
                            $vKodeWargaNegara = $vNat->code ?? ($vNat->emp_id ?? 'idn');
                        }
                    }

                    $daftarKorban[] = [
                        'nama'              => $payload['victimName'],
                        'tempat_lahir'      => $payload['victimBirthPlace'] ?? null,
                        'umur'              => $vAge,
                        'kode_warga_negara' => $vKodeWargaNegara,
                        'alamat'            => [
                            'desa'      => $payload['victimAddress'] ?? null,
                            'kecamatan' => null,
                            'kabupaten' => null,
                            'provinsi'  => null,
                        ],
                        'kode_agama'        => $vAgama,
                    ];
                } elseif ($accident && $accident->involvedPeoples) {
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
                $victimGuardianName = $payload['victimGuardianName'] ?? null;
                if (!empty($victimGuardianName)) {
                    $daftarPendampingKorban[] = [
                        'kode_pendamping' => 1,
                        'nama_pendamping' => $victimGuardianName,
                    ];
                } elseif (!empty($victims) && $victims->isNotEmpty()) {
                    $firstVictim = $victims->first();
                    $namaPendampingKorban = $firstVictim->father_name ?? ($firstVictim->mother_name ?? null);
                    if (!empty($namaPendampingKorban)) {
                        $daftarPendampingKorban[] = [
                            'kode_pendamping' => 1,
                            'nama_pendamping' => $namaPendampingKorban,
                        ];
                    }
                }

                // --- tanggal_musyawarah ---
                $tanggalMusyawarah = !empty($document->diversion_date)
                    ? date('Y-m-d', strtotime($document->diversion_date))
                    : (!empty($payload['diversionDate']) ? date('Y-m-d', strtotime($payload['diversionDate'])) : null);

                // --- daftar_nama_pk (Pembimbing Kemasyarakatan) ---
                $daftarNamaPk = [];
                if (!empty($payload['bapasOfficerName'])) {
                    $daftarNamaPk[] = $payload['bapasOfficerName'];
                }

                // --- daftar_nama_psp (Pekerja Sosial Profesional) ---
                $daftarNamaPsp = [];
                if (!empty($payload['socialWorkerName'])) {
                    $daftarNamaPsp[] = $payload['socialWorkerName'];
                }

                // --- daftar_nama_ph (Penasihat Hukum) ---
                $daftarNamaPh = [];
                $phName = $payload['legalAdvisorName'] ?? ($payload['phName'] ?? ($payload['penasihatHukumName'] ?? null));
                if (!empty($phName)) {
                    $daftarNamaPh[] = $phName;
                }

                // --- daftar_dokumen_digital ---
                $daftarDokumenDigital = [];

                // Dokumen SKD-POL
                $attachment = $document->attachment ?? $document->suratKesepakatanDiversiDocumentAttachment;
                if ($attachment && isset($attachment->name)) {
                    $filePath = public_path('documents/attachments/' . $attachment->name);
                    $daftarDokumenDigital[] = [
                        'kode_jenis_dokumen' => 'skd-pol',
                        'mime_type'          => $attachment->mimetype ?? 'application/pdf',
                        'file'               => File::exists($filePath)
                            ? base64_encode(File::get($filePath))
                            : null,
                        'url'                => url('documents/attachments/' . $attachment->name),
                    ];
                }

                // Dokumen Sprindik
                if ($accident && $accident->suratPerintahPenyidikanDocuments) {
                    $sprindik = $accident->suratPerintahPenyidikanDocuments->sortByDesc('created_at')->first();
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
                    'daftar_anak'             => $daftarAnak,
                    'daftar_pendamping_anak'  => $daftarPendampingAnak,
                    'daftar_korban'           => $daftarKorban,
                    'daftar_pendamping_korban'=> $daftarPendampingKorban,
                    'tanggal_musyawarah'      => $tanggalMusyawarah,
                    'daftar_nama_pk'          => $daftarNamaPk,
                    'daftar_nama_psp'         => $daftarNamaPsp,
                    'daftar_nama_ph'          => $daftarNamaPh,
                    'daftar_dokumen_digital'  => $daftarDokumenDigital,
                ];

                // --- root document ---
                $responseData[$arrayKey] = [
                    'kode_jenis_dokumen'    => 'skd-pol',
                    'identitas_dokumen'     => $identitasDokumen,
                    'konten_dokumen'        => $kontenDokumen,
                    'terenkripsi'           => false,
                    'daftar_kunci_enkripsi' => [],
                    'tanda_tangan_digital'  => null,
                ];

                $docService->putApiSyncMoment(
                    $request,
                    $document,
                    get_class(new SuratKesepakatanDiversiDocument()),
                    $this->tableSchemaName . 'surat_kesepakatan_diversi_documents',
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
