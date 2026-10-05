<?php

namespace App\Http\Controllers\IcellServices\ApiPusiknasBareskrim\Doc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\IcellServices\ApiPusiknasBareskrim\DocService;
use App\Models\Doc\SuratPermintaanIzinPenyitaanDocument\SuratPermintaanIzinPenyitaanDocument;

class SuratPermintaanIzinPenyitaanPusiknasDocumentController extends Controller
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
            $documents = SuratPermintaanIzinPenyitaanDocument::with([
                'accident',
                'suratPerintahPenyidikanDocument' => function($q) {
                    $q->with('attachment');
                },
                'suspects',
                'seizedItems',
                'laws' => function($q) {
                    $q->with('crimeConstitution');
                },
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
                if ($doc->laws && count($doc->laws) > 0) {
                    foreach ($doc->laws as $law) {
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

                // 3. Daftar Tersangka
                $daftarTersangka = [];
                if ($doc->suspects && count($doc->suspects) > 0) {
                    foreach ($doc->suspects as $suspect) {
                        $daftarTersangka[] = [
                            'nama' => $suspect->full_name ?? ($suspect->name ?? '-'),
                            'nomor_identitas' => $suspect->id_card_number ?? ($suspect->identity_number ?? '-')
                        ];
                    }
                }
                
                // Fallback ke Terlapor/Saksi jika opsi "isSuspectExists" false pada form S-12
                if (count($daftarTersangka) === 0 && $doc->reportedPersons && count($doc->reportedPersons) > 0) {
                    foreach ($doc->reportedPersons as $rp) {
                        $daftarTersangka[] = [
                            'nama' => $rp->name ?? '-',
                            'nomor_identitas' => $rp->identity_number ?? '-'
                        ];
                    }
                }

                // 4. Daftar Barang Sitaan
                $daftarBarangSitaan = [];
                if ($doc->seizedItems && count($doc->seizedItems) > 0) {
                    foreach ($doc->seizedItems as $item) {
                        $daftarBarangSitaan[] = [
                            'nama' => $item->nama ?? '-',
                            'jenis' => $item->jenis ?? '-',
                            'jumlah' => $item->jumlah ? (float) $item->jumlah : 0,
                            'satuan' => $item->satuan ?? '-'
                        ];
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

                // 6. Helper Digital Document Base64 & URL
                $getDocDigital = function ($attachment) {
                    $result = [
                        'file' => null,
                        'url'  => ''
                    ];
                    
                    if ($attachment && $attachment->name) {
                        $path = public_path("documents/attachments/{$attachment->name}");
                        $result['url'] = asset("documents/attachments/{$attachment->name}");
                        if (file_exists($path)) {
                            $result['file'] = base64_encode(file_get_contents($path));
                        }
                    }
                    
                    return $result;
                };

                $s12Digital = $getDocDigital($doc->attachment);
                $sprindikDigital = $doc->suratPerintahPenyidikanDocument ? $getDocDigital($doc->suratPerintahPenyidikanDocument->attachment) : $getDocDigital(null);

                $daftarDokumenDigital = [
                    [
                        'kode_jenis_dokumen' => 's12',
                        'mime_type' => 'application/pdf',
                        'file' => $s12Digital['file'],
                        'url' => $s12Digital['url']
                    ],
                    [
                        'kode_jenis_dokumen' => 'sprindik',
                        'mime_type' => 'application/pdf',
                        'file' => $sprindikDigital['file'],
                        'url' => $sprindikDigital['url']
                    ],
                    [
                        'kode_jenis_dokumen' => 'sprin-sita',
                        'mime_type' => 'application/pdf',
                        'file' => null,
                        'url' => ''
                    ],
                    [
                        'kode_jenis_dokumen' => 'resume',
                        'mime_type' => 'application/pdf',
                        'file' => null,
                        'url' => ''
                    ]
                ];


                $responseData[] = [
                    'kode_jenis_dokumen' => 's12',
                    'identitas_dokumen' => $identitasDokumen,
                    'konten_dokumen' => [
                        'nomor_laporan_pengaduan' => $doc->accident->no_lp ?? '-',
                        'tanggal_laporan_pengaduan' => $doc->accident->report_date ? date('Y-m-d', strtotime($doc->accident->report_date)) : null,
                        'nomor_sprindik' => $doc->suratPerintahPenyidikanDocument->document_number ?? $doc->sprindik_number ?? '-',
                        'tanggal_sprindik' => $doc->suratPerintahPenyidikanDocument->document_date ? date('Y-m-d', strtotime($doc->suratPerintahPenyidikanDocument->document_date)) : ($doc->sprindik_date ? date('Y-m-d', strtotime($doc->sprindik_date)) : null),
                        'nomor_surat_perintah_penyitaan' => $doc->surat_perintah_penyitaan_number ?? '-',
                        'tanggal_surat_perintah_penyitaan' => $doc->surat_perintah_penyitaan_date ? date('Y-m-d', strtotime($doc->surat_perintah_penyitaan_date)) : null,
                        'daftar_uu_pasal' => $daftarUUPasal,
                        'daftar_tersangka' => $daftarTersangka,
                        'daftar_barang_sitaan' => $daftarBarangSitaan,
                        'pejabat_penandatangan' => $pejabatPenandatangan
                    ],
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
