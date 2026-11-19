<?php

namespace App\Http\Controllers\IcellServices\ApiPusiknasBareskrim\Doc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\IcellServices\ApiPusiknasBareskrim\DocService;
use App\Models\Doc\SuratLaporanPersetujuanPenyitaanDocument\SuratLaporanPersetujuanPenyitaanDocument;

class SuratLaporanPersetujuanPenyitaanPusiknasDocumentController extends Controller
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
            $documents = SuratLaporanPersetujuanPenyitaanDocument::with([
                'accident',
                'suratPerintahPenyidikanDocument' => function($q) {
                    $q->with(['attachment', 'suratPerintahPenyidikanDocumentLaws.crimeConstitution']);
                },
                'persons.suspect',
                'persons.witness',
                'persons.reportedPerson',
                'persons.seizedItems',
                'signatories' => function($q) {
                    $q->with(['position', 'rank']);
                },
                'attachment'
            ])
            ->whereHas('accident', function ($query) {
                // Asumsi kecelakaan sudah valid
            })
            ->where('status_id', '11')
            ->orderBy('document_date', 'ASC');

            $documents = $docService->applyDateRangeFilter($documents, 'released_at', $startReleaseDate, $endReleaseDate);
            $documents = $docService->applyDateRangeFilter($documents, 'document_date', $startDocumentDate, $endDocumentDate);

            $documents = $documents->paginate($perPage, ['*'], 'page', $page);

            $responseData = [];

            foreach ($documents as $doc) {
                // 1. Identitas Dokumen
                $identitasDokumen = [
                    'nomor' => $doc->document_number ?? '-',
                    'tanggal' => $doc->document_date ? date('Y-m-d', strtotime($doc->document_date)) : null
                ];

                // 2. Daftar UU Pasal
                $daftarUUPasal = [];
                $sprindikLaws = $doc->suratPerintahPenyidikanDocument?->suratPerintahPenyidikanDocumentLaws;
                if ($sprindikLaws && count($sprindikLaws) > 0) {
                    foreach ($sprindikLaws as $law) {
                        if ($law->flag === 'ADDITIONAL' || $law->flag === 'ADDT') {
                            if (!empty($law->constitution)) {
                                $daftarUUPasal[] = $law->constitution;
                            }
                        } else {
                            $part = '';
                            if (!empty($law->constitution_chapter)) {
                                $part .= 'Pasal ' . ltrim($law->constitution_chapter, 'Pasal ') . ' ';
                            }
                            if (!empty($law->crimeConstitution) && !empty($law->crimeConstitution->name)) {
                                $part .= $law->crimeConstitution->name;
                            }
                            if (!empty($part)) {
                                $daftarUUPasal[] = trim($part);
                            }
                        }
                    }
                }

                // 3. Daftar Tersangka & Barang Sitaan (Dari S-13 persons pivot)
                $daftarTersangka = [];
                $daftarBarangSitaan = [];
                
                if ($doc->persons && count($doc->persons) > 0) {
                    foreach ($doc->persons as $person) {
                        $s = $person->suspect;
                        $w = $person->witness;
                        $rp = $person->reportedPerson;
                        
                        if ($s) {
                            $daftarTersangka[] = [
                                'nama' => $s->full_name ?? ($s->name ?? '-'),
                                'nomor_identitas' => $s->id_card_number ?? ($s->identity_number ?? '-')
                            ];
                        } elseif ($rp) {
                            $daftarTersangka[] = [
                                'nama' => $rp->name ?? '-',
                                'nomor_identitas' => $rp->identity_number ?? '-'
                            ];
                        } elseif ($w) {
                            $daftarTersangka[] = [
                                'nama' => $w->full_name ?? ($w->name ?? '-'),
                                'nomor_identitas' => $w->id_card_number ?? ($w->identity_number ?? '-')
                            ];
                        }
                        
                        // Barang Sitaan dari tiap person
                        if ($person->seizedItems && count($person->seizedItems) > 0) {
                            foreach ($person->seizedItems as $item) {
                                $daftarBarangSitaan[] = [
                                    'nama' => $item->name ?? '-',
                                    'jumlah' => $item->quantity ? (float) $item->quantity : 0,
                                    'satuan' => $item->unit ?? '-'
                                ];
                            }
                        }
                    }
                }

                // 5. Pejabat Penandatangan
                $pejabatPenandatangan = [];
                if ($doc->signatories && count($doc->signatories) > 0) {
                    foreach ($doc->signatories as $officer) {
                        $fullName = trim(($officer->first_title ? $officer->first_title . ' ' : '') . 
                            $officer->first_name . ' ' . $officer->last_name . 
                            ($officer->last_title ? ' ' . $officer->last_title : ''));
                        
                        $pejabatPenandatangan[] = [
                            'nama' => $fullName !== '' ? $fullName : '-',
                            'nomor_induk' => $officer->register_number ?? '-',
                            'jabatan' => $officer->position->name ?? $officer->position_id ?? '-',
                            'pangkat' => $officer->rank->name ?? $officer->rank_id ?? '-'
                        ];
                    }
                }

                // 6. Helper Digital Document URL
                $getDocDigital = function ($attachment) {
                    $result = [
                        'url'  => ''
                    ];
                    
                    if ($attachment && $attachment->name) {
                        $result['url'] = asset("documents/attachments/{$attachment->name}");
                    }
                    
                    return $result;
                };

                $s13Digital = $getDocDigital($doc->attachment);
                $sprindikDigital = $doc->suratPerintahPenyidikanDocument ? $getDocDigital($doc->suratPerintahPenyidikanDocument->attachment) : $getDocDigital(null);

                // Sprin Sita
                $sprinsitaDigital = null;
                if (!empty($doc->surat_perintah_penyitaan_file)) {
                    $sprinsitaDigital = [
                        'kode_jenis_dokumen' => 'sprin-sita',
                        'mime_type' => 'application/pdf',
                        'url' => asset(
                            'file/penyitaan/surat-perintah-penyitaan/' .
                            $doc->surat_perintah_penyitaan_file
                        )
                    ];
                }

                // BA Sita
                $baSitaUrls = [];
                if ($doc->persons && count($doc->persons) > 0) {
                    foreach ($doc->persons as $person) {
                        if (!empty($person->bap_file)) {
                            $baSitaUrls[] = asset('file/penyitaan/berita-acara-penyitaan/' . $person->bap_file);
                        }
                    }
                }
                $baSitaDigital = null;
                if (!empty($baSitaUrls)) {
                    $baSitaDigital = [
                        'kode_jenis_dokumen' => 'ba-sita',
                        'mime_type' => 'application/pdf',
                        'url' => count($baSitaUrls) > 1 ? array_values(array_unique($baSitaUrls)) : $baSitaUrls[0]
                    ];
                }

                $daftarDokumenDigital = [
                    [
                        'kode_jenis_dokumen' => 's13',
                        'mime_type' => 'application/pdf',
                        'url' => $s13Digital['url']
                    ],
                    [
                        'kode_jenis_dokumen' => 'sprindik',
                        'mime_type' => 'application/pdf',
                        'url' => $sprindikDigital['url']
                    ]
                ];

                if ($sprinsitaDigital) {
                    $daftarDokumenDigital[] = $sprinsitaDigital;
                }

                if ($baSitaDigital) {
                    $daftarDokumenDigital[] = $baSitaDigital;
                }

                $kontenDokumen = [
                    'nomor_laporan_pengaduan' => $doc->accident->no_lp ?? '-',
                    'tanggal_laporan_pengaduan' => $doc->accident->report_date ? date('Y-m-d', strtotime($doc->accident->report_date)) : null,
                    'nomor_sprindik' => $doc->suratPerintahPenyidikanDocument->document_number ?? $doc->sprindik_number ?? '-',
                    'tanggal_sprindik' => $doc->suratPerintahPenyidikanDocument->document_date ? date('Y-m-d', strtotime($doc->suratPerintahPenyidikanDocument->document_date)) : ($doc->sprindik_date ? date('Y-m-d', strtotime($doc->sprindik_date)) : null),
                ];

                if (!empty($doc->spdp_number)) {
                    $kontenDokumen['nomor_spdp'] = $doc->spdp_number;
                }

                if (!empty($doc->spdp_date)) {
                    $kontenDokumen['tanggal_spdp'] = date('Y-m-d', strtotime($doc->spdp_date));
                }

                if ($sprinsitaDigital) {
                    $kontenDokumen['nomor_surat_perintah_penyitaan'] =
                        $doc->surat_perintah_penyitaan_number ?? '-';

                    $kontenDokumen['tanggal_surat_perintah_penyitaan'] =
                        $doc->surat_perintah_penyitaan_date
                            ? date('Y-m-d', strtotime($doc->surat_perintah_penyitaan_date))
                            : null;
                }

                $kontenDokumen = array_merge($kontenDokumen, [
                    'daftar_uu_pasal' => $daftarUUPasal,
                    'daftar_tersangka' => $daftarTersangka,
                    'daftar_barang_sitaan' => $daftarBarangSitaan,
                    'pejabat_penandatangan' => $pejabatPenandatangan
                ]);

                $responseData[] = [
                    'kode_jenis_dokumen' => 's13',
                    'identitas_dokumen' => $identitasDokumen,
                    'konten_dokumen' => $kontenDokumen,
                    'daftar_dokumen_digital' => $daftarDokumenDigital
                ];

            }

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Data ditemukan.',
                'total'   => count($responseData),
                'page'    => $page,
                'data'    => $responseData
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }
}
