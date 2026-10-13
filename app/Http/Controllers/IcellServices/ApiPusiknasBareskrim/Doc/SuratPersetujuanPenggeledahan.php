<?php


namespace App\Http\Controllers\IcellServices\ApiPusiknasBareskrim\Doc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Doc\SuratGunaMemperolehPersetujuanPenggeledahanDocument\SuratGunaMemperolehPersetujuanPenggeledahanDocument;
use App\Helpers\PeopleNameHelper;
use Illuminate\Support\Facades\File;
use App\Services\IcellServices\ApiPusiknasBareskrim\DocService;

class SuratPersetujuanPenggeledahan extends Controller
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

        $no_lp          = $request->input('no_lp');
        $kode_jenis_doc = $request->input('kode_jenis_doc');
           if (empty($no_lp)) {
            return response()->json([
                "code"    => "400",
                "status"  => "BAD_REQUEST",
                "message" => "Parameter no_lp wajib diisi.",
                "data"    => [],
            ], 400);
        }
         DB::beginTransaction();
        try {
             $documents = SuratGunaMemperolehPersetujuanPenggeledahanDocument::with([
                'accident',
                'accident.polres',
                'suratPemberitahuanDimulainyaPenyidikanDocument',
                'suratPerintahPenyidikanDocument.suratPerintahPenyidikanDocumentLaws.crimeConstitution',
                'suratPerintahPenyidikanDocument.suratPerintahPenyidikanDocumentLaws.crimeType',
                'suratPerintahPenyidikanDocument.attachment',
                'suratPerintahPenyidikanDocument.suratPerintahPenyidikanDocumentAttachment',
                'suspects',
                'officers.rank',
                'officers.position',
                'authorizedSignatory.rank',
                'authorizedSignatory.position',
                'attachment'
            ])
            ->whereHas('accident', function ($q) use ($no_lp) {
                $q->where('no_lp', trim($no_lp));
            })
            ->get();
           //dd($documents);
            $responseData = [];
            foreach($documents as $item){
                $accident = $item->accident;
                $spdp     = $item->suratPemberitahuanDimulainyaPenyidikanDocument;
                $sprindik = $item->suratPerintahPenyidikanDocument;
                $daftarUuPasal = [];
                $daftarJenisTindakPidana = [];

                if ($sprindik && $sprindik->suratPerintahPenyidikanDocumentLaws) {
                    foreach ($sprindik->suratPerintahPenyidikanDocumentLaws as $law) {
                        $chapter = trim($law->constitution_chapter ?? '');
                        $constitutionName = $law->crimeConstitution ? trim($law->crimeConstitution->name) : '';
                        $pasal = implode(' ', array_filter([$chapter, $constitutionName]));
                        if (!empty($pasal)) {
                            $daftarUuPasal[] = $pasal;
                        }

                        if ($law->crimeType && !empty($law->crimeType->name)) {
                            $daftarJenisTindakPidana[] = trim($law->crimeType->name);
                        }
                    }
                }

                if (empty($daftarUuPasal)) {
                    $daftarUuPasal = ["Pasal 111 ayat (1) UU RI No. 35 Tahun 2009"];
                }
                if (empty($daftarJenisTindakPidana)) {
                    $daftarJenisTindakPidana = ["pencurian"];
                }
                  // --- Daftar Tersangka ---
                $daftarTersangka = [];
                if ($item->suspects && $item->suspects->isNotEmpty()) {
                    foreach ($item->suspects as $suspect) {
                        $daftarTersangka[] = [
                            "nama"         => $suspect->first_name ?? $suspect->name ?? '-',
                            "alamat"       => $suspect->address ?? '-',
                            "kode_wilayah" => $suspect->subdistrict_id ?? $suspect->district_id ?? '11.01.17',
                        ];
                    }
                } else {
                    $daftarTersangka[] = [
                        "nama"         => null,
                        "alamat"       => null,
                        "kode_wilayah" => null
                    ];
                }

                // --- Daftar Lokasi Penggeledahan ---
                $daftarLokasi = [];
                if (!empty($item->alamat_penggeledahan)) {
                    $daftarLokasi[] = [
                        "kode_jenis_lokasi" => 1,
                        "alamat"            => $item->alamat_penggeledahan,
                    ];
                } else {
                    $daftarLokasi[] = [
                        "kode_jenis_lokasi" => null,
                        "alamat"            => null
                    ];
                }

                // --- Pejabat Penandatangan ---
                $pejabatPenandatangan = [];
                $signatoryOfficer = $item->officers->where('class', 'SIGNATORY')->first() ?? $item->authorizedSignatory;
                if ($signatoryOfficer) {
                    $pejabatPenandatangan[] = [
                        "nama"        => PeopleNameHelper::getFullName(
                            $signatoryOfficer->first_title,
                            $signatoryOfficer->first_name,
                            $signatoryOfficer->last_name,
                            $signatoryOfficer->last_title
                        ),
                        "nomor_induk" => $signatoryOfficer->register_number ?? null,
                        "jabatan"     => $signatoryOfficer->position->name ?? ($signatoryOfficer->position_id ?? null),
                        "pangkat"     => $signatoryOfficer->rank->name ?? ($signatoryOfficer->rank_id ?? null),
                    ];
                } else {
                    $pejabatPenandatangan[] = [
                        "nama"        => null,
                        "nomor_induk" => null,
                        "jabatan"     => null,
                        "pangkat"     => null
                    ];
                }

                // --- Daftar Dokumen Digital ---
                $attachment = $item->attachment;
                $fileUrl    = null;
                $fileBase64 = null;

                if ($attachment && !empty($attachment->name)) {
                    $filePath = public_path('documents/attachments/' . $attachment->name);
                    $fileUrl  = url('documents/attachments/' . $attachment->name);
                  //  dd($fileUrl);

                    if (File::exists($filePath)) {
                        $fileBase64 = base64_encode(File::get($filePath));
                    }
                }

                // --- Attachment SPRINDIK ---
                $sprindikAttachment = $sprindik ? ($sprindik->attachment ?? $sprindik->suratPerintahPenyidikanDocumentAttachment) : null;
                $sprindikFileUrl    = ($sprindikAttachment && !empty($sprindikAttachment->name))
                    ? url('documents/attachments/' . $sprindikAttachment->name)
                    : null;

                $daftarDokumenDigital = [
                    [
                        "kode_jenis_dokumen" => "s11",
                        "mime_type"          => $attachment->mimetype ?? "application/pdf",
                        "file"               => "U3dhZ2dlciByb2Nrcw==",
                        "url"                => $fileUrl ?? null
                    ],
                    [
                        "kode_jenis_dokumen" => "sprindik",
                        "mime_type"          => $sprindikAttachment->mimetype ?? "application/pdf",
                      //  "file"               => "U3dhZ2dlciByb2Nrcw==",
                        "url"                => $sprindikFileUrl ?? null
                    ],
                    [
                        "kode_jenis_dokumen" => "sprin-dah",
                        "mime_type"          => "application/pdf",
                      //  "file"               => "U3dhZ2dlciByb2Nrcw==",
                        "url"                => null
                    ],
                    [
                        "kode_jenis_dokumen" => "resume",
                        "mime_type"          => "application/pdf",
                     //   "file"               => "U3dhZ2dlciByb2Nrcw==",
                        "url"                => null
                    ]
                ];

                $responseData[] = [
                    "kode_jenis_dokumen" => "s11",
                    "identitas_dokumen" => [
                        "nomor"   => $item->document_number ?? '-',
                        "tanggal" => $item->document_date ? date('Y-m-d', strtotime($item->document_date)) : null,
                    ],
                    "konten_dokumen" => [
                        "nomor_laporan_pengaduan"              => $accident->no_lp ?? null,
                        "tanggal_laporan_pengaduan"            => $accident ? ($accident->report_date ? date('Y-m-d', strtotime($accident->report_date)) : ($accident->created_at ? $accident->created_at->format('Y-m-d') : null)) : null,
                        "nomor_sprindik"                       => $sprindik->document_number ?? null,
                        "tanggal_sprindik"                     => ($sprindik && $sprindik->document_date) ? date('Y-m-d', strtotime($sprindik->document_date)) : null,
                        "nomor_spdp"                           => $spdp->document_number ?? $item->no_spdp ?? null,
                        "tanggal_spdp"                         => ($spdp && $spdp->document_date) ? date('Y-m-d', strtotime($spdp->document_date)) : null,
                        "nomor_surat_perintah_penggeledahan"   => $item->document_number ?? null,
                        "tanggal_surat_perintah_penggeledahan" => $item->document_date ? date('Y-m-d', strtotime($item->document_date)) : null,
                        "daftar_uu_pasal"                      => array_values(array_unique($daftarUuPasal)),
                        "daftar_jenis_tindak_pidana"           => array_values(array_unique($daftarJenisTindakPidana)),
                        "daftar_tersangka"                     => $daftarTersangka,
                        "daftar_lokasi_penggeledahan"          => $daftarLokasi,
                        "pejabat_penandatangan"                => $pejabatPenandatangan,
                    ],
                    "daftar_dokumen_digital" => $daftarDokumenDigital
                ];

                
            }
            DB::commit();

            return response()->json([
                "code"       => "200",
                "status"     => "OK",
                "message"    => "Success",
                "data"       => $responseData,
            ], 200);

        }
        catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                "code"       => "500",
                "status"     => "INTERNAL_SERVER_ERROR",
                "message"    => "An error occurred while processing your request: " . $e->getMessage(),
            ], 500);
        }
    }
}
