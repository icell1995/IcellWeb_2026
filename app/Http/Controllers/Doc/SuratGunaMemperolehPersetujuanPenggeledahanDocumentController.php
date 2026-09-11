<?php

namespace App\Http\Controllers\Doc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Helpers\PeopleNameHelper;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Services\Doc\DocService;
use App\Models\SP3;
use App\Models\Doc\SuratPemberitahuanDimulainyaPenyidikanDocument\SuratPemberitahuanDimulainyaPenyidikanDocument;
use App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument;
use App\Models\Doc\SuratPermintaanPenggeledahanDocument\SuratPermintaanPenggeledahanDocument;
use App\Models\Doc\SuratGunaMemperolehPersetujuanPenggeledahanDocument\SuratGunaMemperolehPersetujuanPenggeledahanDocument;
use App\Models\Accident;
use App\Models\Officer;
use App\Models\Suspect;
use App\Models\Lib\Ref;
use App\Models\Opt\Status;

use App\Traits\DocsOfficersTraits;

class SuratGunaMemperolehPersetujuanPenggeledahanDocumentController extends Controller
{
    protected $docService;

    use DocsOfficersTraits;

    public function __construct(DocService $docService)
    {
        $this->docService = $docService;
    }

     public function create()
    {
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $accident   = Accident::with(['polres', 'polres.polda'])->where('id', $accidentId)->first();

        // Ambil SPDP yang sudah diterbitkan
        $spdpDocuments = SuratPemberitahuanDimulainyaPenyidikanDocument::where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->get();

        // Ambil SPRINDIK yang sudah diterbitkan
        $sprindikDocuments = SuratPerintahPenyidikanDocument::with('suratPerintahPenyidikanDocumentLaws.crimeConstitution', 'suratPerintahPenyidikanDocumentLaws.crimeType')
            ->where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->get();

        foreach ($sprindikDocuments as $sprindik) {
            $pasalParts = [];
            $tindakPidanaParts = [];
            if ($sprindik->suratPerintahPenyidikanDocumentLaws->isNotEmpty()) {
                foreach ($sprindik->suratPerintahPenyidikanDocumentLaws as $law) {
                    $chapter = trim($law->constitution_chapter ?? '');
                    $constitutionName = $law->crimeConstitution ? trim($law->crimeConstitution->name) : '';
                    $pasalParts[] = implode(' ', array_filter([$chapter, $constitutionName]));

                    if ($law->crimeType) {
                        $tindakPidanaParts[] = trim($law->crimeType->name);
                    }
                }
            }
            $sprindik->pasal_formatted = implode(', ', array_unique(array_filter($pasalParts)));
            $sprindik->tindak_pidana_formatted = implode(', ', array_unique(array_filter($tindakPidanaParts)));
        }

        // Ambil Surat Permintaan Penggeledahan yang sudah diterbitkan
        $suratPermintaanPenggeledahanDocuments = SuratPermintaanPenggeledahanDocument::where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->get();

        if ($suratPermintaanPenggeledahanDocuments->isEmpty()) {
            return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
                ->with('error', 'Dokumen Permintaan Penggeledahan belum dibuat, mohon untuk buat Surat Permintaan Penggeledahan terlebih dahulu.');
        }

        // Tersangka di perkara ini
        $suspects = Suspect::where('accident_id', $accidentId)
            ->where('flag', Suspect::getEnumOption('flag', 'TERSANGKA'))
            ->get();

        $getOldNewPolresIds = $this->getOldNewPolresIds($accident->polres_id);
        $authorizedSignatories = Officer::withRelated()
            ->selectFullName()
            ->whereIn('police_id', $getOldNewPolresIds)
            ->whereHasUserActive()
            ->hasDataComplete()
            ->signatory()->active()->valid()
            ->orderBy('first_name')->get();

        return view('docs.surat-guna-memperoleh-persetujuan-penggeledahan-document.create', compact(
            'accidentId',
            'accident',
            'spdpDocuments',
            'sprindikDocuments',
            'suratPermintaanPenggeledahanDocuments',
            'suspects',
            'authorizedSignatories'
        ));
    }

      public function store()
    {
        $accidentId = htmlspecialchars(request()->input('accident_id'));
        $data       = request()->all();
       // dd($data);
        $validator = Validator::make($data, [
            'accident_id'                                           =>'required|string|max:255',
            'documentDate'                                          => 'required|string|max:255',
            'documentNumber'                                        => 'required|string|max:255', 
            'nomorSuratPerintah'                                    => 'required|string|max:255',
            'tglSuratPerintah'                                      => 'required|string|max:255',
            'nomorPengaduan'                                        => 'required|string|max:255',
            'tglPengaduan'                                          => 'required|string|max:255',
            'no_sprindik'                                           => 'required|string|max:255',
            'nomorSuratPermintaanPenggeledahan'                     => 'required|string|max:255',
            'no_spdp'                                               => 'required|string|max:255',
            'suspects_id'                                           => 'required|array',
            'signatory_id'                                          => 'required',

        ], [
            'required' => 'Bagian ini harus diisi.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validasi gagal.',
                'errors'  => $validator->errors(),
            ], 400);
        }

        try {
            DB::beginTransaction();

            $spdpId = $data['no_spdp'] ?? $data['surat_pemberitahuan_dimulainya_penyidikan_document_id'] ?? null;
            $spdp = null;
            if ($spdpId) {
                $spdp = SuratPemberitahuanDimulainyaPenyidikanDocument::find($spdpId);
                if (!$spdp) {
                    $spdp = SuratPemberitahuanDimulainyaPenyidikanDocument::where('document_number', $spdpId)
                        ->where('accident_id', $accidentId)
                        ->first();
                }
            }

            $spdpDocumentId = $spdp ? $spdp->id : $spdpId;
            $noSpdp = $spdp ? $spdp->document_number : $data['no_spdp'];

            $daftarPenggeledahan = $data['daftar_penggeledahan'] ?? [];
            if (is_string($daftarPenggeledahan)) {
                $daftarPenggeledahan = array_values(array_filter(array_map('trim', explode(',', $daftarPenggeledahan))));
            }

            // Create document
            $document = SuratGunaMemperolehPersetujuanPenggeledahanDocument::create([
                'accident_id'                                           => $accidentId,
                'surat_pemberitahuan_dimulainya_penyidikan_document_id' => $spdpDocumentId,
                'document_number'                                       => $data['documentNumber'],
                'document_date'                                         => $data['documentDate'],
                'no_permintaan_penggeledahan_document_id'               => $data['nomorSuratPermintaanPenggeledahan'],
                'no_pengaduan'                                          => $data['nomorPengaduan'],
                'tgl_pengaduan'                                         => $data['tglPengaduan'],
                'no_sprindik'                                           => $data['no_sprindik'],
                'no_spdp'                                               => $noSpdp,
                'no_surat_perintah_penggeledahan'                       => $data['nomorSuratPerintah'],
                'tgl_surat_perintah_penggeledahan'                      => $data['tglSuratPerintah'],
                'suspect_id'                                            => $data['suspect_id'] ?? ($data['suspects_id'][0] ?? null),
                'signatory_id'                                          => $data['signatory_id'],
            ]);

            // Sync suspects
            $document->suspects()->sync($data['suspects_id'] ?? []);

            // Create signatory officer
            $signatory = Officer::where('id', $data['signatory_id'])->first();
            if ($signatory) {
                $document->officers()->create([
                    'register_number' => $signatory->register_number,
                    'first_title'     => $signatory->first_title,
                    'first_name'      => $signatory->first_name,
                    'last_name'       => $signatory->last_name,
                    'last_title'      => $signatory->last_title,
                    'rank_id'         => $signatory->rank_id,
                    'position_id'     => $signatory->position_id,
                    'phone_number'    => $signatory->phone_number,
                    'email'           => $signatory->email,
                    'police_id'       => $signatory->police_id,
                    'status'          => 'PRESENT',
                    'class'           => 'SIGNATORY',
                    'flag'            => 'INTERNAL',
                    'insert_method'   => 'IMPORT',
                ]);
            }

            DB::commit();      
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan pada saat menyimpan data: ' . $e->getMessage());
        }
         return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId]);
    }

    public function show($id)
    {
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $document = SuratGunaMemperolehPersetujuanPenggeledahanDocument::with([
            'authorizedSignatory' => function ($query) {
                $query->with('rank', 'position', 'user');
            },
            'officers' => function ($query) {
                $query->with('rank', 'position', 'police');
            },
            'suratPemberitahuanDimulainyaPenyidikanDocument',
            'suratPerintahPenyidikanDocument.suratPerintahPenyidikanDocumentLaws.crimeConstitution',
            'suratPerintahPenyidikanDocument.suratPerintahPenyidikanDocumentLaws.crimeType',
            'suspects',
        ])->where('id', $id)->first();
 
        if (!$document) {
            if (request()->expectsJson() || request()->wantsJson()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Dokumen tidak ditemukan.',
                ], 404);
            }
            abort(404, 'Dokumen tidak ditemukan.');
        }

        if ($document->suratPerintahPenyidikanDocument) {
            $sprindik = $document->suratPerintahPenyidikanDocument;
            $pasalParts = [];
            $tindakPidanaParts = [];
            if ($sprindik->suratPerintahPenyidikanDocumentLaws && $sprindik->suratPerintahPenyidikanDocumentLaws->isNotEmpty()) {
                foreach ($sprindik->suratPerintahPenyidikanDocumentLaws as $law) {
                    $chapter = trim($law->constitution_chapter ?? '');
                    $constitutionName = $law->crimeConstitution ? trim($law->crimeConstitution->name) : '';
                    $pasalParts[] = implode(' ', array_filter([$chapter, $constitutionName]));

                    if ($law->crimeType) {
                        $tindakPidanaParts[] = trim($law->crimeType->name);
                    }
                }
            }
            $sprindik->pasal_formatted = implode(', ', array_unique(array_filter($pasalParts)));
            $sprindik->tindak_pidana_formatted = implode(', ', array_unique(array_filter($tindakPidanaParts)));
        }
 
        if (request()->expectsJson() || request()->wantsJson()) {
            // Get investigators (penyidik)
            $investigators = $document->officers->where('class', 'MEMBER')->values();
 
            // Get drafting officers (penyusun)
            $draftingOfficers = $document->officers->where('class', 'LEADER')->values();
 
            return response()->json([
                'status'  => 'success',
                'data'    => $document,
                'investigators' => $investigators,
                'draftingOfficers' => $draftingOfficers,
            ], 200);
        }

        $accident = Accident::with(['polres', 'polres.polda'])->where('id', $accidentId ?? $document->accident_id)->first();

        return view('docs.surat-guna-memperoleh-persetujuan-penggeledahan-document.show', compact(
            'accidentId',
            'accident',
            'document'
        ));
    }

    public function delete($id)
    {
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $document   = SuratGunaMemperolehPersetujuanPenggeledahanDocument::where('id', $id)->firstOrFail();

        DB::beginTransaction();
        try {
            $document->suspects()->detach();
            $document->officers()->delete();
            $document->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal menghapus dokumen: ' . $e->getMessage());
        }

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId]);
    }

    public function download($id)
    {
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $document = SuratGunaMemperolehPersetujuanPenggeledahanDocument::with([
            'authorizedSignatory' => function ($query) {
                $query->with('rank', 'position', 'user');
            },
            'officers' => function ($query) {
                $query->with('rank', 'position', 'police');
            },
            'suratPemberitahuanDimulainyaPenyidikanDocument',
            'suratPerintahPenyidikanDocument.suratPerintahPenyidikanDocumentLaws.crimeConstitution',
            'suratPerintahPenyidikanDocument.suratPerintahPenyidikanDocumentLaws.crimeType',
            'suspects' => function ($query) {
                $query->with('gender', 'job', 'religion');
            },
        ])->where('id', $id)->firstOrFail();

        $accident = Accident::with(['polres', 'polres.polda', 'police'])->where('id', $accidentId ?? $document->accident_id)->first();

        $signatory = $document->authorizedSignatory;
        $investigator = $document->officers->where('class', 'MEMBER')->first() 
            ?? $document->officers->where('class', 'LEADER')->first() 
            ?? $signatory;

        $sprindik = $document->suratPerintahPenyidikanDocument;
        $accidentNumber = $accident->no_lp ?? '-';
        $reportDate = $accident->report_date ? Carbon::parse($accident->report_date)->locale('id')->translatedFormat('d F Y') : ($accident->created_at ? Carbon::parse($accident->created_at)->locale('id')->translatedFormat('d F Y') : '-');
        $accidentDate = $accident->accident_date ? Carbon::parse($accident->accident_date)->locale('id')->translatedFormat('d F Y') : '-';
        $accidentDay = $accident->accident_date ? Carbon::parse($accident->accident_date)->locale('id')->translatedFormat('l') : '-';
        $accidentTime = $accident->accident_time ? Carbon::parse($accident->accident_time)->format('H:i') : '-';
        $accidentRoad = $accident->road_name ?? ($document->alamat_penggeledahan ?? '-');

        $suratPerintahPenyidikanDocumentNumber = $sprindik->document_number ?? '-';
        $suratPerintahPenyidikanDocumentDocumentDay = $sprindik && $sprindik->document_date ? Carbon::parse($sprindik->document_date)->locale('id')->translatedFormat('l') : '-';
        $suratPerintahPenyidikanDocumentDocumentDate = $sprindik && $sprindik->document_date ? Carbon::parse($sprindik->document_date)->locale('id')->translatedFormat('d F Y') : '-';

        $pasalParts = [];
        $tindakPidanaParts = [];
        if ($sprindik && $sprindik->suratPerintahPenyidikanDocumentLaws && $sprindik->suratPerintahPenyidikanDocumentLaws->isNotEmpty()) {
            foreach ($sprindik->suratPerintahPenyidikanDocumentLaws as $law) {
                $chapter = trim($law->constitution_chapter ?? '');
                $constitutionName = $law->crimeConstitution ? trim($law->crimeConstitution->name) : '';
                $pasalParts[] = implode(' ', array_filter([$chapter, $constitutionName]));

                if ($law->crimeType) {
                    $tindakPidanaParts[] = trim($law->crimeType->name);
                }
            }
        }
        $pasalText = implode(', ', array_unique(array_filter($pasalParts))) ?: 'Undang-Undang terkait tindak pidana yang dipersangkakan';
        $tindakPidanaText = implode(', ', array_unique(array_filter($tindakPidanaParts))) ?: 'tindak pidana yang dipersangkakan';

        $daerahPoliceFullName = strtoupper($accident->polres->polda->full_name ?? '');
        $resorPoliceFullName = (in_array($accident->polres_id, ['1114'])) ? 'DIREKTORAT LALU LINTAS' : 'RESOR ' . strtoupper($accident->polres->full_name ?? '');
        $resorPoliceAddress = ucwords(($accident->polres->address ?? '') . ', ' . ($accident->polres->polres_zipcode ?? ''));
        $documentLocation = ucwords(strtolower($accident->polres->polres_district ?? ($accident->polres->full_name ?? 'Kota')));
        $documentDate = $document->document_date ? Carbon::parse($document->document_date)->locale('id')->translatedFormat('d F Y') : date('d F Y');
        $documentNumber = $document->document_number ?? '-';
        $documentClassificationName = 'Biasa';
        $appendix = 'satu berkas';

        $workUnitName = '';
        if (!empty($accident->police)) {
            if ($accident->police->class == 'DAERAH') {
                $workUnitName = 'Dit Lantas ' . ucwords(strtolower($accident->police->full_name));
            } else if ($accident->police->class == 'RESOR') {
                $workUnitName = 'Sat Lantas ' . ucwords(strtolower($accident->police->full_name));
            }
        } else {
            $workUnitName = 'Sat Lantas ' . ucwords(strtolower($accident->polres->full_name ?? ''));
        }

        $prosecutorName = 'KETUA PENGADILAN NEGERI ' . strtoupper($accident->polres->polres_district ?? ($accident->polres->full_name ?? '......'));
        $prosecutorLocation = ucwords(strtolower($accident->polres->polres_district ?? ($accident->polres->full_name ?? 'Kota/Kab')));

        $suspects = $document->suspects ?? collect();
        $isSuspectExist = $suspects->isNotEmpty();
        $suspectExistText = $isSuspectExist ? 'dengan identitas sebagai berikut:' : '';
        $suspectNames = $suspects->pluck('name')->implode(', ') ?: '....................................';

        $blockSuspects = [];
        foreach ($suspects as $suspect) {
            $genderName = $suspect->gender->name ?? ($suspect->gender ?? '-');
            $birthPlace = $suspect->birth_place ?? '-';
            $birthDate = $suspect->birth_date ? Carbon::parse($suspect->birth_date)->locale('id')->translatedFormat('d F Y') : '-';
            $jobName = $suspect->job->name ?? ($suspect->job ?? ($suspect->occupation->name ?? '-'));
            $religionName = $suspect->religion->name ?? ($suspect->religion ?? '-');
            $fullAddress = $suspect->address ?? ($suspect->full_address ?? '-');

            $blockSuspects[] = [
                'suspectName' => $suspect->name ?? '',
                'suspectIdentityNumber' => $suspect->nik ?? ($suspect->identity_number ?? '-'),
                'suspectNationality' => $suspect->citizenship ?? 'WNI',
                'suspectGenderName' => $genderName,
                'suspectBirthPlace' => $birthPlace,
                'suspectBirthDate' => $birthDate,
                'suspectJobName' => $jobName,
                'suspectReligionName' => $religionName,
                'suspectFullAddress' => $fullAddress,
            ];
        }

        if (empty($blockSuspects)) {
            $blockSuspects[] = [
                'suspectName' => '...........................................................................',
                'suspectIdentityNumber' => '...........................................................................',
                'suspectNationality' => 'WNI',
                'suspectGenderName' => '...........................................................................',
                'suspectBirthPlace' => '...........................................................................',
                'suspectBirthDate' => '...........................................................................',
                'suspectJobName' => '...........................................................................',
                'suspectReligionName' => '...........................................................................',
                'suspectFullAddress' => '...........................................................................',
            ];
        }

        $references = [
            [
                'reference_iteration' => 'a.',
                'reference_name' => 'Undang-Undang Nomor 2 Tahun 2002 tentang Kepolisian Negara Republik Indonesia;'
            ],
            [
                'reference_iteration' => 'b.',
                'reference_name' => 'Pasal 3 dan Pasal 618 Undang-Undang Nomor 1 Tahun 2023 tentang Kitab Undang-Undang Hukum Pidana;'
            ],
            [
                'reference_iteration' => 'c.',
                'reference_name' => 'Pasal 1 angka 14 dan angka 34, Pasal 5 ayat (2) huruf a, Pasal 7 ayat (1) huruf f, Pasal 41, Pasal 42, Pasal 43, Pasal 47, Pasal 89 huruf d, Pasal 112 huruf a, Pasal 113 ayat (4), ayat (5), ayat (6), ayat (7), ayat (8) dan ayat (9), Pasal 114, Pasal 115, Pasal 116, Pasal 156 ayat (1) huruf d dan Pasal 361 Undang-Undang Nomor 20 Tahun 2025 tentang Kitab Undang-Undang Hukum Acara Pidana;'
            ],
            [
                'reference_iteration' => 'd.',
                'reference_name' => $pasalText . ';'
            ],
            [
                'reference_iteration' => 'e.',
                'reference_name' => 'Laporan Polisi Nomor : ' . $accidentNumber . ', tanggal ' . $reportDate . ';'
            ],
            [
                'reference_iteration' => 'f.',
                'reference_name' => 'Surat Perintah Penyidikan Nomor : ' . $suratPerintahPenyidikanDocumentNumber . ', tanggal ' . $suratPerintahPenyidikanDocumentDocumentDate . ';'
            ],
            [
                'reference_iteration' => 'g.',
                'reference_name' => 'Laporan kemajuan singkat tanggal ' . $documentDate . '.'
            ]
        ];

        $blockCarbonCopies = [
            [
                'carbon_copy_iteration' => '1',
                'carbon_copy_name' => 'Kepala Kepolisian Daerah ' . ($accident->polres->polda->full_name ?? '..................') . ';'
            ],
            [
                'carbon_copy_iteration' => '2',
                'carbon_copy_name' => 'Kepala Kepolisian Resor ' . ($accident->polres->full_name ?? '..................') . ';'
            ],
            [
                'carbon_copy_iteration' => '3',
                'carbon_copy_name' => 'Pengawas Penyidikan / Arsip.'
            ]
        ];

        // Signatory details
        $signatoryFullName = $signatory ? ($signatory->full_name ?? PeopleNameHelper::getFullName($signatory->first_title, $signatory->first_name, $signatory->last_name, $signatory->last_title)) : '................................................';
        $signatoryRankName = $signatory->rank->full_name ?? ($signatory->rank->name ?? '');
        $signatoryRegisterNumber = $signatory->register_number ?? '....................';
        $signatoryPositionName = strtoupper($signatory->position->name ?? 'KASAT LANTAS / SELAKU PENYIDIK');
        $signatoryHeadText = '';

        // QR Code Generation
        $tempQrCodePath = storage_path('images/qrcode-signature-' . $document->id . '.png');
        if (!file_exists(storage_path('images'))) {
            mkdir(storage_path('images'), 0777, true);
        }
        try {
            QrCode::format('png')
                ->size(300)
                ->errorCorrection('H')
                ->merge(public_path('images/logo2x.png'), .2, true)
                ->generate('https://dokumen-tte.bareskrim.polri.go.id/DocumentInfo/Icell?id=' . $document->id, $tempQrCodePath);
        } catch (\Exception $e) {
            QrCode::format('png')
                ->size(300)
                ->errorCorrection('H')
                ->generate('https://dokumen-tte.bareskrim.polri.go.id/DocumentInfo/Icell?id=' . $document->id, $tempQrCodePath);
        }

        $templatePath = public_path('word-template/surat_laporan_guna_memperoleh_persetujuan_penggeledahan.docx');
        if (!file_exists($templatePath)) {
            $templatePath = base_path('public/word-template/surat_laporan_guna_memperoleh_persetujuan_penggeledahan.docx');
        }

        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($templatePath);

        // Process block & row clones if present
        $templateVariables = $templateProcessor->getVariables();
        
        if (in_array('block_suspects', $templateVariables) || in_array('/block_suspects', $templateVariables)) {
            $templateProcessor->cloneBlock('block_suspects', 0, true, false, $blockSuspects);
        }
        if (in_array('reference_iteration', $templateVariables)) {
            $templateProcessor->cloneRowAndSetValues('reference_iteration', $references);
        }
        if (in_array('carbon_copy_iteration', $templateVariables)) {
            $templateProcessor->cloneRowAndSetValues('carbon_copy_iteration', $blockCarbonCopies);
        }
        if (in_array('QRCodeImage', $templateVariables) && file_exists($tempQrCodePath)) {
            $templateProcessor->setImageValue('QRCodeImage', [
                'path' => $tempQrCodePath,
                'width' => 100,
                'height' => 100,
            ]);
        }

        // Set all values
        $templateProcessor->setValue('daerahPoliceFullName', $daerahPoliceFullName);
        $templateProcessor->setValue('resorPoliceFullName', $resorPoliceFullName);
        $templateProcessor->setValue('resorPoliceAddress', $resorPoliceAddress);
        $templateProcessor->setValue('documentLocation', $documentLocation);
        $templateProcessor->setValue('documentDate', $documentDate);
        $templateProcessor->setValue('documentNumber', $documentNumber);
        $templateProcessor->setValue('documentClassificationName', $documentClassificationName);
        $templateProcessor->setValue('appendix', $appendix);
        $templateProcessor->setValue('prosecutorName', $prosecutorName);
        $templateProcessor->setValue('prosecutorLocation', $prosecutorLocation);

        $templateProcessor->setValue('workUnitName', $workUnitName);
        $templateProcessor->setValue('suratPerintahPenyidikanDocumentDocumentDay', $suratPerintahPenyidikanDocumentDocumentDay);
        $templateProcessor->setValue('suratPerintahPenyidikanDocumentDocumentDate', $suratPerintahPenyidikanDocumentDocumentDate);
        $templateProcessor->setValue('suratPerintahPenyidikanDocumentNumber', $suratPerintahPenyidikanDocumentNumber);
        $templateProcessor->setValue('crimeClass', 'Kejahatan Lalu Lintas');
        $templateProcessor->setValue('crimeConstitution', $pasalText);
        $templateProcessor->setValue('accidentRoad', $accidentRoad);
        $templateProcessor->setValue('accidentDate', $accidentDate);
        $templateProcessor->setValue('suspectExistText', $suspectExistText);

        $templateProcessor->setValue('signatoryHeadText', $signatoryHeadText);
        $templateProcessor->setValue('signatoryPositionName', $signatoryPositionName);
        $templateProcessor->setValue('signatoryName', strtoupper($signatoryFullName));
        $templateProcessor->setValue('signatoryRankName', strtoupper($signatoryRankName));
        $templateProcessor->setValue('signatoryRegisterNumber', $signatoryRegisterNumber);

        // Additional fallback variables
        $templateProcessor->setValue('pasalText', $pasalText);
        $templateProcessor->setValue('noLp', $accidentNumber);
        $templateProcessor->setValue('tglLp', $reportDate);
        $templateProcessor->setValue('noSprindik', $suratPerintahPenyidikanDocumentNumber);
        $templateProcessor->setValue('tglSprindik', $suratPerintahPenyidikanDocumentDocumentDate);
        $templateProcessor->setValue('tglLapju', $documentDate);
        $templateProcessor->setValue('satkerName', $accident->polres->full_name ?? 'Kepolisian');
        $templateProcessor->setValue('tindakPidanaText', $tindakPidanaText);
        $templateProcessor->setValue('hariKejadian', $accidentDay);
        $templateProcessor->setValue('tglKejadian', $accidentDate);
        $daftarPenggeledahanText = is_array($document->daftar_penggeledahan)
            ? implode(', ', $document->daftar_penggeledahan)
            : ($document->daftar_penggeledahan ?? ($document->jenis_penggeledahan ?? 'rumah/tempat tertutup lainnya atau alat angkut'));
        $templateProcessor->setValue('daftar_penggeledahan', strtolower($daftarPenggeledahanText));
        $templateProcessor->setValue('jenisPenggeledahan', strtolower($daftarPenggeledahanText));
        $templateProcessor->setValue('suspectNames', $suspectNames);
        $templateProcessor->setValue('alamatPenggeledahan', $document->alamat_penggeledahan ?? '-');

        $firstSuspect = $suspects->first();
        $templateProcessor->setValue('suspectName', $firstSuspect->name ?? '...........................................................................');
        $templateProcessor->setValue('suspectBirth', ($firstSuspect ? (($firstSuspect->birth_place ?? '-') . ', ' . ($firstSuspect->birth_date ? Carbon::parse($firstSuspect->birth_date)->locale('id')->translatedFormat('d F Y') : '-')) : '...........................................................................'));
        $templateProcessor->setValue('suspectIdentityNumber', $firstSuspect->nik ?? ($firstSuspect->identity_number ?? '...........................................................................'));
        $templateProcessor->setValue('suspectGender', $firstSuspect->gender->name ?? ($firstSuspect->gender ?? '...........................................................................'));
        $templateProcessor->setValue('suspectJob', $firstSuspect->job->name ?? ($firstSuspect->job ?? ($firstSuspect->occupation->name ?? '...........................................................................')));
        $templateProcessor->setValue('suspectCitizenship', $firstSuspect->citizenship ?? 'WNI');
        $templateProcessor->setValue('suspectReligion', $firstSuspect->religion->name ?? ($firstSuspect->religion ?? '...........................................................................'));
        $templateProcessor->setValue('suspectAddress', $firstSuspect->address ?? ($firstSuspect->full_address ?? '...........................................................................'));

        $investigatorFullName = $investigator ? ($investigator->full_name ?? PeopleNameHelper::getFullName($investigator->first_title, $investigator->first_name, $investigator->last_name, $investigator->last_title)) : $signatoryFullName;
        $investigatorRankName = $investigator ? ($investigator->rank->name ?? '') : ($signatory ? ($signatory->rank->name ?? '') : '....................');
        $investigatorPhone = $investigator->phone_number ?? ($signatory->phone_number ?? '....................');

        $templateProcessor->setValue('investigatorName', $investigatorFullName);
        $templateProcessor->setValue('investigatorRank', $investigatorRankName);
        $templateProcessor->setValue('investigatorPhone', $investigatorPhone);



        if (!file_exists(public_path('generate'))) {
            mkdir(public_path('generate'), 0777, true);
        }

        $filename = public_path('generate/' . $document->id . ' - Surat Guna Memperoleh Persetujuan Penggeledahan - ' . ($accident->polres->full_name ?? 'Polres') . '.docx');
        $templateProcessor->saveAs($filename);

        return response()->download($filename)->deleteFileAfterSend(true);
    }

    public function edit($id){
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $accident   = Accident::where('id', $accidentId)->first();

        $suratPerintahPenyidikanDocuments = SuratPerintahPenyidikanDocument::with('suratPerintahPenyidikanDocumentLaws.crimeConstitution', 'suratPerintahPenyidikanDocumentLaws.crimeType')
            ->where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->get();

        foreach ($suratPerintahPenyidikanDocuments as $sprindik) {
            $pasalParts = [];
            if ($sprindik->suratPerintahPenyidikanDocumentLaws->isNotEmpty()) {
                foreach ($sprindik->suratPerintahPenyidikanDocumentLaws as $law) {
                    $chapter = trim($law->constitution_chapter ?? '');
                    $constitutionName = $law->crimeConstitution ? trim($law->crimeConstitution->name) : '';
                    $pasalParts[] = implode(' ', array_filter([$chapter, $constitutionName]));
                }
            }
            $sprindik->pasal_formatted = implode(', ', $pasalParts);
        }

        $suratPerintahTugasDocuments = SuratPerintahTugasDocument::where('accident_id', $accidentId)
            ->whereHasMorph('related', get_class(new SuratPerintahPenyidikanDocument()))
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->get();

        $getOldNewPolresIds = $this->getOldNewPolresIds($accident->polres_id);

        $authorizedSignatories = Officer::withRelated()
            ->selectFullName()
            ->whereIn('police_id', $getOldNewPolresIds)
            ->whereHasUserActive()
            ->hasDataComplete()
            ->signatory()
            ->active()
            ->valid()
            ->orderBy('first_name')
            ->get();

        $suspects = Suspect::where('accident_id', $accidentId)
            ->where('flag', Suspect::getEnumOption('flag', 'TERSANGKA'))
            ->where('class', Suspect::getEnumOption('class', 'DETERMINATION'))
            ->whereHas('suratKetetapanTentangPenetapanTersangkaDocument')
            ->get();

        $reportedPersons = ReportedPerson::where('accident_id', $accidentId)->get();

        $prosecutors = Prosecutor::where('is_active', true)->orderBy('sort')->get();
        $courts      = Court::where('is_active', true)->orderBy('sort')->get();
        $documentClassifications = DocumentClassification::where('group', 'SURAT_PEMBERITAHUAN_DIMULAINYA_PENYIDIKAN')
            ->where('is_active', true)->orderBy('sort')->get();


        return view('docs.spdp-pusiknas-document.edit', compact(
            'accidentId',
            'accident',
            'suratPerintahPenyidikanDocuments',
            'suratPerintahTugasDocuments',
            'authorizedSignatories',
            'suspects',
            'reportedPersons',
            'prosecutors',
            'courts',
            'documentClassifications',

        ));
    }

    public function update(Request $request){
        $documentId = htmlspecialchars($request->document_id);

        $document = SuratPemberitahuanDimulainyaPenyidikanDocument::find($documentId);
        if (!$document) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Dokumen tidak ditemukan.',
            ], 404);
        }

        $validator = $this->validateForm($request, $document);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $documentNumber                   = htmlspecialchars($request->documentNumber);
        $documentDate                     = htmlspecialchars($request->documentDate);
        $documentClassificationId         = htmlspecialchars($request->documentClassification);
        $suratPerintahPenyidikanDocumentId = htmlspecialchars($request->suratPerintahPenyidikanDocument);
        $suratPerintahTugasDocumentId     = htmlspecialchars($request->suratPerintahTugasDocument);
        $isSuspectExists                  = ($request->isSuspectExists == 'true') ? true : false;
        $prosecutorId                     = htmlspecialchars($request->prosecutor);
        $courtId                          = htmlspecialchars($request->court);
        $appendix                         = $request->appendix;
        $kodeWilayah                      = $request->kode_wilayah;
        $signatoryId                      = htmlspecialchars($request->signatory);

        $accident = Accident::find($accidentId);
        $accidentDateObj = \Carbon\Carbon::parse($accident->accident_date);
        $waktuKejadian   = 'Sekitar pukul ' . \Carbon\Carbon::parse($accident->accident_time)->format('H:i') . ' WIB';
        $tanggalKejadian = intval($accidentDateObj->format('d'));
        $bulanKejadian   = intval($accidentDateObj->format('m'));


    }   
    

    public function validateRequestForm()
    {
        $data       = request()->all();
       
        $validator = Validator::make($data, [
            'accident_id'                        => 'required|string|max:255',
            'no_sprindik'                        => 'required|string|max:255',
            'nomorSuratPermintaanPenggeledahan'  => 'required|string|max:255',
            'nomorSuratPerintah'                 => 'required|string|max:255',
            'tglSuratPerintah'                   => 'required|string|max:255',
            'documentNumber'                     => 'required|string|max:255',
            'documentDate'                       => 'required|string|max:255',
            'no_spdp'                            => 'required|string|max:255',
            'nomorPengaduan'                     => 'required|string|max:255',
            'tglPengaduan'                       => 'required|string|max:255',
            'daftar_penggeledahan'               => 'required',
            'suspects_id'                        => 'required|array',
            'signatory_id'                       => 'required',
        ], [
            'required' => 'Bagian ini harus diisi.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => 422,
                'errors' => $validator->errors()
            ], 422);
        }

        return response()->json([
            'success' => true,
            'code' => 200,
            'message' => 'Validasi berhasil. Data siap disimpan.',
        ], 200);
    }

}
