<?php

namespace App\Http\Controllers\IcellServices\ApiPusiknasBareskrim\Doc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\IcellServices\ApiPusiknasBareskrim\DocService;
use App\Models\Doc\Tahap2Document\Tahap2Document as Tahap2PusiknasDocument;

class Tahap2PusiknasDocumentController extends Controller
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
            $documents = Tahap2PusiknasDocument::with([
                'accident',
                'officers',
                'suratPemberitahuanDimulainyaPenyidikan',
                'suspects',
                'attachments'
            ])
            ->whereHas('accident', function ($query) {
                // Asumsi kecelakaan sudah valid
            })
            // ->whereIn('status_id', $docService->requiredDocumentStatusIds)
            ->orderBy('document_date', 'ASC');

            $documents = $docService->applyDateRangeFilter($documents, 'released_at', $startReleaseDate, $endReleaseDate);
            $documents = $docService->applyDateRangeFilter($documents, 'document_date', $startDocumentDate, $endDocumentDate);

            $documents = $documents->paginate($perPage, ['*'], 'page', $page);

            $totalData = $documents->total();
            $totalPage = $documents->lastPage();

            $regenciesPath = base_path('master_seeder/regencies-new1.json');
            $allRegencies = file_exists($regenciesPath) ? json_decode(file_get_contents($regenciesPath), true) ?? [] : [];
            $districtsPath = base_path('master_seeder/districts-new1.json');
            $allDistricts = file_exists($districtsPath) ? json_decode(file_get_contents($districtsPath), true) ?? [] : [];

            $responseData = [];
            $arrayKey = 0;

            foreach ($documents as $doc) {
                $messages = $doc->messages ?? [];
                
                $kodeWilayah = !empty($messages['kode_wilayah']) ? $messages['kode_wilayah'] : '00.00.00';
                if ($kodeWilayah === '00.00.00' && $doc->accident) {
                    $regencyName = $doc->accident->polres->polres_regency ?? '';
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

                // Map Suspects
                $daftarTersangka = [];
                $people = $doc->suspects;
                foreach ($people as $suspect) {
                    $daftarTersangka[] = [
                        'nama' => $suspect->name ?? '-',
                        'tempat_lahir' => $suspect->birth_place ?? '-',
                        'kode_jenis_kelamin' => ($suspect->gender_id == '1' || $suspect->gender == 'L') ? 1 : 2,
                        'alamat' => $suspect->address ?? '-',
                        'kode_wilayah' => $kodeWilayah,
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
                        'daftar_uu_pasal' => ['Pasal 310 UU LLAJ']
                    ];
                }
                if (count($daftarTersangka) == 0) {
                    $daftarTersangka[] = [
                        'nama' => '-', 'tempat_lahir' => '-', 'kode_jenis_kelamin' => 1, 'alamat' => '-', 
                        'kode_wilayah' => '00.00.00', 'kode_pendidikan' => 1, 'kode_pekerjaan' => 1, 
                        'nama_ibu' => '-', 'kode_agama' => 1, 'kode_status_perkawinan' => 1, 
                        'kode_warga_negara' => 'idn', 'umur_saat_tindak_pidana' => 20, 'daftar_uu_pasal' => ['-']
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

                // Map Barang Bukti
                $daftarBarangBukti = [];
                if (is_array($doc->barang_bukti) && count($doc->barang_bukti) > 0) {
                    foreach ($doc->barang_bukti as $bb) {
                        $daftarBarangBukti[] = [
                            'nama' => $bb['nama_barang'] ?? $bb['nama'] ?? '-',
                            'jumlah' => (int) ($bb['jumlah_barang'] ?? $bb['jumlah'] ?? 1),
                            'satuan' => $bb['satuan'] ?? 'unit',
                            'keterangan' => $bb['keterangan'] ?? null
                        ];
                    }
                } elseif (!empty($messages['daftar_barang_bukti']) && is_array($messages['daftar_barang_bukti'])) {
                    foreach ($messages['daftar_barang_bukti'] as $b) {
                        $daftarBarangBukti[] = [
                            'nama' => $b['nama'] ?? '-',
                            'jumlah' => (int) ($b['jumlah'] ?? 1),
                            'satuan' => $b['satuan'] ?? 'unit',
                            'keterangan' => $b['keterangan'] ?? null
                        ];
                    }
                } else {
                    if (isset($doc->accident) && isset($doc->accident->evidence) && count($doc->accident->evidence) > 0) {
                        foreach ($doc->accident->evidence as $evidence) {
                            $daftarBarangBukti[] = [
                                'nama' => $evidence->evidence_name ?? '-',
                                'jumlah' => (int) ($evidence->total ?? 1),
                                'satuan' => 'unit',
                                'keterangan' => null
                            ];
                        }
                    }
                }
                if (empty($daftarBarangBukti)) {
                    $daftarBarangBukti[] = ['nama' => '-', 'jumlah' => 1, 'satuan' => 'unit'];
                }

                // Map Saksi
                $daftarSaksi = [];
                $messages = $doc->messages ?? [];

                if (!empty($messages['daftar_saksi']) && is_array($messages['daftar_saksi'])) {
                    foreach ($messages['daftar_saksi'] as $s) {
                        $daftarSaksi[] = [
                            'nama' => $s['nama'] ?? '-',
                            'tempat_lahir' => $s['tempat_lahir'] ?? '-',
                            'kode_jenis_kelamin' => (int) ($s['kode_jenis_kelamin'] ?? 1),
                            'alamat' => $s['alamat'] ?? '-',
                            'kode_wilayah' => !empty($s['kode_wilayah']) ? $s['kode_wilayah'] : '00.00.00',
                            'kode_pendidikan' => (int) ($s['kode_pendidikan'] ?? 1),
                            'kode_pekerjaan' => (int) ($s['kode_pekerjaan'] ?? 1),
                            'nama_ibu' => $s['nama_ibu'] ?? '-',
                            'kode_agama' => (int) ($s['kode_agama'] ?? 1),
                            'kode_status_perkawinan' => (int) ($s['kode_status_perkawinan'] ?? 1),
                            'kode_warga_negara' => $s['kode_warga_negara'] ?? 'idn'
                        ];
                    }
                } else {
                    $witnesses = \App\Models\Witness::where('accident_id', $doc->accident_id)->where('group', 'TAHAP_I')->get();
                    foreach ($witnesses as $witness) {
                        $daftarSaksi[] = [
                            'nama' => $witness->name ?? '-',
                            'tempat_lahir' => $witness->birth_place ?? '-',
                            'kode_jenis_kelamin' => $witness->gender_id == '1' ? 1 : 2,
                            'alamat' => $witness->address ?? '-',
                            'kode_wilayah' => '00.00.00',
                            'kode_pendidikan' => 1,
                            'kode_pekerjaan' => 1,
                            'nama_ibu' => '-',
                            'kode_agama' => 1,
                            'kode_status_perkawinan' => 1,
                            'kode_warga_negara' => 'idn'
                        ];
                    }
                }

                if (empty($daftarSaksi)) {
                    $daftarSaksi[] = [
                        'nama' => '-', 'tempat_lahir' => '-', 'kode_jenis_kelamin' => 1, 'alamat' => '-',
                        'kode_wilayah' => '00.00.00', 'kode_pendidikan' => 1, 'kode_pekerjaan' => 1,
                        'nama_ibu' => '-', 'kode_agama' => 1, 'kode_status_perkawinan' => 1, 'kode_warga_negara' => 'idn'
                    ];
                }

                // Map Digital Documents
                $daftarDokumenDigital = [];
                // 1. bpt2 (Tahap II)
                foreach ($doc->attachments as $att) {
                    $daftarDokumenDigital[] = [
                        'kode_jenis_dokumen' => 'bpt2',
                        'mime_type' => 'application/pdf',
                        'url' => asset("documents/attachments/{$att->name}")
                    ];
                }

                // 2. sprindik
                $sprindikDoc = $doc->suratPerintahPenyidikan;
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
                
                $messages = $doc->messages ?? [];

                $identitasDokumen = [
                    'nomor_surat_pengantar' => $doc->document_number ?? '-',
                    'tanggal_surat_pengantar' => $doc->document_date ? date('Y-m-d', strtotime($doc->document_date)) : date('Y-m-d'),
                    'nomor_berkas_perkara' => $doc->berkas_perkara_number ?? '-',
                    'nomor_spdp' => $doc->no_spdp ?? '-',
                    'tanggal_terima_p21' => $doc->tanggal_terima_p21 ? date('Y-m-d', strtotime($doc->tanggal_terima_p21)) : date('Y-m-d')
                ];

                $kontenDokumen = [
                    'uraian_singkat_perkara' => $messages['uraian_singkat_perkara'] ?? $doc->description ?? $doc->accident->damage_lose_desc ?? 'Laka Lantas',
                    'tempat_kejadian_perkara' => [
                        'lokasi' => !empty($messages['lokasi_kejadian']) ? $messages['lokasi_kejadian'] : ($doc->accident->road_name ?? '-'),
                        'kode_wilayah' => $kodeWilayah
                    ],
                    'waktu_kejadian' => $messages['waktu_kejadian'] ?? $doc->accident->accident_time ?? '-',
                    'tahun_kejadian' => $doc->accident->accident_date ? (int) date('Y', strtotime($doc->accident->accident_date)) : (int) date('Y'),
                    'bulan_kejadian' => $doc->accident->accident_date ? (int) date('n', strtotime($doc->accident->accident_date)) : (int) date('n'),
                    'tanggal_kejadian' => $doc->accident->accident_date ? (int) date('j', strtotime($doc->accident->accident_date)) : (int) date('j'),
                    'daftar_tersangka' => $daftarTersangka,
                    'daftar_barang_bukti' => $daftarBarangBukti,
                    'daftar_saksi' => $daftarSaksi,
                    'pejabat_penandatangan' => $pejabat,
                    'daftar_dokumen_digital' => $daftarDokumenDigital
                ];

                // --- root document ---
                $responseData[$arrayKey] = [
                    'kode_jenis_dokumen'    => 'bpt2',
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
