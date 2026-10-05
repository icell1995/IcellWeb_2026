<?php

namespace App\Http\Controllers\IcellServices\ApiPusiknasBareskrim\Doc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\IcellServices\ApiPusiknasBareskrim\DocService;
use App\Models\Doc\SuratPemberitahuanDimulainyaPenyidikanPusiknasDocument\SuratPemberitahuanDimulainyaPenyidikanPusiknasDocument;

class SuratPemberitahuanDimulainyaPenyidikanPusiknasDocumentController extends Controller
{
    protected $docService;

    public function __construct(DocService $docService)
    {
        $this->docService = $docService;
    }

    public function index(Request $request)
    {
        $docService = $this->docService;

        // Get request data
        $startDocumentDate = $request->input('start_doc_date');
        $endDocumentDate   = $request->input('end_doc_date');
        $startReleaseDate  = $request->input('start_release_date');
        $endReleaseDate    = $request->input('end_release_date');
        $perPage           = $request->query('perPage', 100);
        $page              = $request->query('page', 1);

        $page = is_numeric($page) ? intval($page) : 1;
        $perPage = is_numeric($perPage) ? intval($perPage) : 100;

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
            $documents = SuratPemberitahuanDimulainyaPenyidikanPusiknasDocument::with([
                'accident',
                'officers',
                'suratPerintahPenyidikanDocument',
                'suratPerintahTugasDocument',
                'prosecutor',
                'court',
                'suspects',
                'reportedPersons',
                'attachments',
            ])
            ->whereHas('accident', function ($query) {
                // Asumsi kecelakaan sudah valid
            })
            // ->whereIn('status_id', $docService->requiredDocumentStatusIds) // Uncomment jika sudah punya status final
            ->orderBy('document_date', 'ASC');

            // Filter
            // $documents = $docService->applyIncludeLegacyDocumentFilter($documents, false);
            $documents = $docService->applyDateRangeFilter($documents, 'released_at', $startReleaseDate, $endReleaseDate);
            $documents = $docService->applyDateRangeFilter($documents, 'document_date', $startDocumentDate, $endDocumentDate);

            $documents = $documents->paginate($perPage, ['*'], 'page', $page);

            $totalData = $documents->total();
            $totalPage = $documents->lastPage();

            $regenciesPath = base_path('master_seeder/regencies-new1.json');
            $allRegencies = file_exists($regenciesPath) ? json_decode(file_get_contents($regenciesPath), true) ?? [] : [];
            $districtsPath = base_path('master_seeder/districts-new1.json');
            $allDistricts = file_exists($districtsPath) ? json_decode(file_get_contents($districtsPath), true) ?? [] : [];
            $resortPolicesPath = base_path('master_seeder/resort_polices-new1.json');
            $allResortPolices = file_exists($resortPolicesPath) ? json_decode(file_get_contents($resortPolicesPath), true) ?? [] : [];
            // Index by id for quick lookup
            $resortPolicesById = [];
            foreach ($allResortPolices as $rp) {
                $resortPolicesById[$rp['id'] ?? ''] = $rp;
            }

            $responseData = [];
            $arrayKey = 0;

            foreach ($documents as $doc) {
                // Fix PostgreSQL 63 chars truncation issue on eager loaded pivot aliases by querying directly
                $docSuspects = $doc->suspects()->get();
                $people = count($docSuspects) > 0 ? $docSuspects : $doc->reportedPersons;
                
                $kodeWilayah = !empty($doc->messages['kode_wilayah']) ? $doc->messages['kode_wilayah'] : '00.00.00';
                if ($kodeWilayah === '00.00.00' && $doc->accident) {
                    $regencyName = $doc->accident->polres->polres_regency ?? '';
                    // Strip prefix 'KABUPATEN'/'KOTA' for matching
                    $regencyNameClean = preg_replace('/^(KABUPATEN|KOTA)\s+/i', '', trim($regencyName));
                    $regencyCode = '';
                    if ($regencyNameClean && !empty($allRegencies)) {
                        foreach ($allRegencies as $reg) {
                            $regNamaClean = preg_replace('/^(KABUPATEN|KOTA)\s+/i', '', strtoupper(trim($reg['Nama'] ?? '')));
                            if ($regNamaClean === strtoupper($regencyNameClean)) {
                                $regencyCode = $reg['KodePuskarda'] ?? '';
                                break;
                            }
                        }
                    }
                    if (!$regencyCode) {
                        $regencyId = $doc->accident->polres->regency_id ?? '';
                        if (strlen($regencyId) === 4) {
                            $regencyCode = substr($regencyId, 0, 2) . '.' . substr($regencyId, 2, 2);
                        }
                    }
                    $districts = [];
                    if (!empty($allDistricts)) {
                        if ($regencyCode) {
                            $districts = array_filter($allDistricts, function($d) use ($regencyCode) {
                                return isset($d['KodePuskarda']) && strpos($d['KodePuskarda'], $regencyCode . '.') === 0;
                            });
                        } else {
                            $districts = $allDistricts;
                        }
                    }
                    $roadName = $doc->accident->road_name ?? '';
                    $extractedDistrict = '';
                    if (preg_match('/(?:KECAMATAN|KEC)\.?\s*([A-Za-z\s]+?)\s*(?:KABUPATEN|KAB\.|KAB|KOTA|,|$)/i', $roadName, $matches)) {
                        $extractedDistrict = trim(preg_replace('/[^a-zA-Z0-9 ]/', '', $matches[1]));
                        $extractedDistrict = preg_replace('/\s+/', ' ', $extractedDistrict);
                    }
                    if ($extractedDistrict && !empty($districts)) {
                        $extractedClean = strtoupper(str_replace(' ', '', $extractedDistrict));
                        foreach ($districts as $d) {
                            if (isset($d['Nama'])) {
                                $dNameClean = strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', $d['Nama']));
                                if ($dNameClean === $extractedClean) {
                                    $kodeWilayah = $d['KodePuskarda'];
                                    break;
                                }
                            }
                        }
                    }
                }

                // --- kode_satker_penerbit from resort_polices seeder ---
                $polresId = $doc->accident->polres_id ?? '';
                $polresSeederData = $resortPolicesById[$polresId] ?? null;
                $kodeSatkerPenerbit = $polresSeederData['work_unit_code'] ?? ($doc->accident->polres->work_unit_code ?? null);

                // Map Suspects / Reported Persons
                $daftarTerlaporTersangka = [];
                foreach ($people as $suspect) {
                    $daftarTerlaporTersangka[] = [
                        'nama' => $suspect->name ?? '-',
                        'tempat_lahir' => $suspect->birth_place ?? '-',
                        'tanggal_lahir' => $suspect->birth_date ? date('Y-m-d', strtotime($suspect->birth_date)) : null,
                        'kode_jenis_kelamin' => ($suspect->gender_id == '1' || $suspect->gender == 'L') ? 1 : 2,
                        'alamat' => $suspect->address ?? '-',
                        'kode_wilayah' => $kodeWilayah, // Sesuai master wilayah Pusiknas
                        'kode_pendidikan' => (int) ($suspect->education_id ?? 1),
                        'kode_pekerjaan' => (int) ($suspect->job_id ?? 1),
                        'nama_ibu' => '-',
                        'kode_agama' => (int) ($suspect->religion_id ?? 1),
                        'kode_status_perkawinan' => (int) ($suspect->marital_status_id ?? 1),
                        'kode_warga_negara' => 'idn',
                        'umur_saat_tindak_pidana' => (function() use ($suspect, $doc) {
                            if ($suspect->birth_date) {
                                $refDate = $doc->accident->accident_date ?? now()->toDateString();
                                return \Carbon\Carbon::parse($suspect->birth_date)->diffInYears(\Carbon\Carbon::parse($refDate));
                            }
                            return $suspect->age ?? null;
                        })(),
                        'status' => 1
                    ];
                }

                // Map Officers
                $pejabat = [];
                foreach ($doc->officers as $officer) {
                    $pejabat[] = [
                        'nama' => trim(($officer->first_title ? $officer->first_title . ' ' : '') . $officer->first_name . ' ' . $officer->last_name . ($officer->last_title ? ' ' . $officer->last_title : '')),
                        'nomor_induk' => $officer->register_number ?? '-',
                        'jabatan' => $officer->position->name ?? $officer->position_id ?? '-',
                        'pangkat' => $officer->rank->name ?? $officer->rank_id ?? '-'
                    ];
                }
                if (count($pejabat) == 0) $pejabat[] = ['nama' => '-', 'nomor_induk' => '-', 'jabatan' => '-', 'pangkat' => '-'];

                // Map Digital Documents
                $daftarDokumenDigital = [];
                // 1. spdp
                foreach ($doc->attachments as $att) {
                    $daftarDokumenDigital[] = [
                        'kode_jenis_dokumen' => 'spdp',
                        'mime_type' => 'application/pdf',
                        'url' => asset("documents/attachments/{$att->name}")
                    ];
                }

                // 2. sprindik
                $sprindikDoc = $doc->suratPerintahPenyidikanDocument;
                if ($sprindikDoc && $sprindikDoc->attachment) {
                    $sprindikAtt = $sprindikDoc->attachment;
                    $daftarDokumenDigital[] = [
                        'kode_jenis_dokumen' => 'sprindik',
                        'mime_type' => 'application/pdf',
                        'url' => asset("documents/attachments/{$sprindikAtt->name}")
                    ];
                } else {
                    $daftarDokumenDigital[] = [
                        'kode_jenis_dokumen' => 'sprindik',
                        'mime_type' => 'application/pdf',
                        'url' => ''
                    ];
                }

                // 3. lp (Laporan Polisi)
                $daftarDokumenDigital[] = [
                    'kode_jenis_dokumen' => 'lp',
                    'mime_type' => 'application/pdf',
                    'url' => ''
                ];

                $identitasDokumen = [
                    'nomor' => $doc->document_number ?? '-',
                    'tanggal' => $doc->document_date ? date('Y-m-d', strtotime($doc->document_date)) : date('Y-m-d'),
                ];

                $kontenDokumen = [
                    'daftar_laporan' => [
                        [
                            'nomor' => $doc->accident->no_lp ?? '-',
                            'tanggal' => $doc->accident->report_date ? date('Y-m-d', strtotime($doc->accident->report_date)) : date('Y-m-d'),
                            'kode_satker_penerbit' => $kodeSatkerPenerbit
                        ]
                    ],
                    'nomor_sprindik' => $doc->suratPerintahPenyidikanDocument->document_number ?? '-',
                    'tanggal_sprindik' => $doc->suratPerintahPenyidikanDocument->document_date ? date('Y-m-d', strtotime($doc->suratPerintahPenyidikanDocument->document_date)) : date('Y-m-d'),
                    'uraian_singkat_perkara' => $doc->messages['uraian_singkat_perkara'] ?? $doc->description ?? $doc->accident->damage_lose_desc ?? 'Laka Lantas',
                    'daftar_uu_pasal' => $doc->messages['daftar_uu_pasal'] ?? ['Pasal 310 UU LLAJ'],
                    'daftar_kejadian_perkara' => [
                        [
                            'lokasi' => !empty($doc->messages['lokasi_kejadian']) ? $doc->messages['lokasi_kejadian'] : ($doc->accident->road_name ?? '-'),
                            'kode_wilayah' => $kodeWilayah,
                            'waktu' => $doc->messages['waktu_kejadian'] ?? $doc->accident->accident_time ?? '-',
                            'tahun' => isset($doc->messages['tahun_kejadian']) ? (int) $doc->messages['tahun_kejadian'] : ($doc->accident->accident_date ? (int) date('Y', strtotime($doc->accident->accident_date)) : (int) date('Y')),
                            'bulan' => isset($doc->messages['bulan_kejadian']) ? (int) $doc->messages['bulan_kejadian'] : ($doc->accident->accident_date ? (int) date('n', strtotime($doc->accident->accident_date)) : (int) date('n')),
                            'tanggal' => isset($doc->messages['tanggal_kejadian']) ? (int) $doc->messages['tanggal_kejadian'] : ($doc->accident->accident_date ? (int) date('j', strtotime($doc->accident->accident_date)) : (int) date('j'))
                        ]
                    ],
                    'daftar_terlapor_atau_tersangka' => count($daftarTerlaporTersangka) > 0 ? $daftarTerlaporTersangka : null,
                    'sumber_dana' => $doc->messages['sumber_dana'] ?? null,
                    'sumber_informasi' => $doc->messages['sumber_informasi'] ?? null,
                    'pejabat_penandatangan' => $pejabat,
                    'daftar_dokumen_digital' => $daftarDokumenDigital
                ];

                // --- root document ---
                $responseData[$arrayKey] = [
                    'kode_jenis_dokumen'    => 'spdp',
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
