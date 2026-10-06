<?php

namespace App\Http\Controllers\Docs;

use App\Helpers\PeopleNameHelper;
use App\Http\Controllers\Controller;
use App\Models\Accident;
use App\Models\Doc\SuratKetetapanTentangPenetapanTersangkaDocument\SuratKetetapanTentangPenetapanTersangkaDocument;
use App\Models\Doc\SuratPemberitahuanDimulainyaPenyidikanDocument\SuratPemberitahuanDimulainyaPenyidikanDocument;
use App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument;
use App\Models\Doc\SuratPerintahPembantaranPenahananDocument\SuratPerintahPembantaranPenahananDocument;
use App\Models\Doc\SuratPerintahPembantaranPenahananDocument\SuratPerintahPembantaranPenahananDocumentLaw;
use App\Models\Doc\SuratPerintahPembantaranPenahananDocument\SuratPerintahPembantaranPenahananDocumentOfficer;
use App\Models\Doc\SuratPerintahPembantaranPenahananDocument\SuratPerintahPembantaranPenahananDocumentSuspect;
use App\Models\Lib\CrimeClass;
use App\Models\Lib\CrimeConstitution;
use App\Models\Lib\CrimeType;
use App\Models\Officer;
use App\Models\Suspect;
use App\Services\Doc\DocService;
use App\Traits\DocsOfficersTraits;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class SuratPerintahPembantaranPenahananDocumentController extends Controller
{
    use DocsOfficersTraits;

    protected $docService;

    public function __construct(DocService $docService)
    {
        $this->docService = $docService;
    }

    public function index()
    {
        return $this->create();
    }

    public function create()
    {
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $accident = Accident::with(['police', 'polres', 'polres.polda'])->where('id', $accidentId)->first();

        if (! $accident) {
            return redirect()->back()->with('error', 'Data Laporan Polisi tidak ditemukan.');
        }

        $getOldNewPolresIds = $this->getOldNewPolresIds($accident->polres_id);

        $authorizedSignatories = Officer::withRelated()
            ->selectFullName()
            ->whereHasUserActive()
            ->hasDataComplete()
            ->whereIn('police_id', $getOldNewPolresIds)
            ->signatory()
            ->active()
            ->valid()
            ->orderBy('first_name')
            ->get();

        $memberOfficers = Officer::withRelated()
            ->selectFullName()
            ->whereHasUserActive()
            ->hasDataComplete()
            ->whereIn('police_id', $getOldNewPolresIds)
            ->member()
            ->active()
            ->valid()
            ->orderBy('first_name')
            ->get();

        $suspects = Suspect::where('accident_id', $accidentId)->get();

        // 1. Auto-resolusi Dokumen SPDP
        $spdpDocument = SuratPemberitahuanDimulainyaPenyidikanDocument::where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->latest()
            ->first();

        // 2. Auto-resolusi Dokumen Sprindik
        $sprinSidik = SuratPerintahPenyidikanDocument::with([
            'suratPerintahPenyidikanDocumentLaws.crimeConstitution',
            'suratPerintahPenyidikanDocumentLaws.crimeType',
            'suratPerintahPenyidikanDocumentLaws.crimeClass',
        ])
            ->where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->latest()
            ->first();

        // 3. Auto-resolusi Dokumen Penetapan Tersangka
        $sketTersangkaDocument = SuratKetetapanTentangPenetapanTersangkaDocument::with(['suspect'])
            ->where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->latest()
            ->first();

        $defaultNomorSurat = '';

        $defaultPertimbangan = 'bahwa dalam rangka penyidikan, melihat kondisi kesehatan tersangka yang ditahan menderita sakit dan dirawat di rumah sakit, memerlukan rawat inap (opname) di luar rumah tahanan negara, dipandang perlu mengeluarkan surat perintah.';

        $defaultDikeluarkanDi = ucwords(strtolower($accident->polres->name ?? $accident->polres->polres_district ?? ''));
        $defaultKotaRumahSakit = $defaultDikeluarkanDi;

        // Auto-resolve Dasar Hukum (Pasal yang disangkakan)
        $crimeTypes = CrimeType::with(['crimeClass', 'crimeConstitution'])->where('is_active', true)->orderBy('name')->get();
        $crimeClasses = CrimeClass::where('is_active', true)->orderBy('name')->get();
        $crimeConstitutions = CrimeConstitution::where('is_active', true)->orderBy('name')->get();

        $initialMainLaws = [];
        $initialAdditionalLaws = [];
        if ($sprinSidik && $sprinSidik->suratPerintahPenyidikanDocumentLaws) {
            foreach ($sprinSidik->suratPerintahPenyidikanDocumentLaws as $law) {
                if ($law->flag === 'MAIN' || empty($law->flag)) {
                    $initialMainLaws[] = [
                        'crime_type_id' => $law->crime_type_id,
                        'crime_type_name' => $law->crimeType->name ?? '',
                        'crime_class_id' => $law->crime_class_id,
                        'crime_class_name' => $law->crimeClass->name ?? '',
                        'crime_constitution_id' => $law->crime_constitution_id,
                        'crime_constitution_name' => $law->crimeConstitution->name ?? '',
                        'constitution_chapter' => $law->constitution_chapter ?? '',
                    ];
                } elseif ($law->flag === 'ADDITIONAL' || !empty($law->constitution)) {
                    $initialAdditionalLaws[] = [
                        'constitution' => $law->constitution ?? '',
                    ];
                }
            }
        }

        if (empty($initialMainLaws) && $crimeTypes->count() > 0) {
            $defaultCt = $crimeTypes->firstWhere('id', 4) ?? $crimeTypes->first();
            if ($defaultCt) {
                $initialMainLaws[] = [
                    'crime_type_id' => $defaultCt->id,
                    'crime_type_name' => $defaultCt->name,
                    'crime_class_id' => $defaultCt->crimeClass->id ?? '',
                    'crime_class_name' => $defaultCt->crimeClass->name ?? '',
                    'crime_constitution_id' => $defaultCt->crimeConstitution->id ?? '',
                    'crime_constitution_name' => $defaultCt->crimeConstitution->name ?? '',
                    'constitution_chapter' => 'Pasal 310 Ayat (4)',
                ];
            }
        }

        $satkerName = $accident->police->full_name ?? $accident->polres->full_name ?? $accident->polres->name ?? 'Satker';
        $satkerCode = $accident->police->satker_code ?? $accident->polres->satker_code ?? $accident->polres->code ?? '';

        // 4. Auto-resolusi Dokumen Surat Perintah Penahanan (S-17)
        $s17Documents = \App\Models\Doc\SuratPerintahPenahananDocument\SuratPerintahPenahananDocument::with(['suspects'])
            ->where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->orderBy('created_at', 'desc')
            ->get();
        $defaultS17 = $s17Documents->first();
        $defaultS17Id = $defaultS17->id ?? null;
        $defaultNomorSuratPerintahPenahanan = $defaultS17 ? ($defaultS17->nomor ?? $defaultS17->document_number ?? null) : null;
        $defaultTanggalSuratPerintahPenahanan = $defaultS17 ? ($defaultS17->tanggal ?? $defaultS17->document_date ?? null) : null;
        $defaultSuspectId = null;
        if ($defaultS17 && $defaultS17->suspects && $defaultS17->suspects->isNotEmpty()) {
            $defaultSuspectId = $defaultS17->suspects->first()->id ?? $defaultS17->suspects->first()->suspect_id ?? null;
        }

        $viewData = [
            'satkerName' => $satkerName,
            'satkerCode' => $satkerCode,
            'authorizedSignatories' => $authorizedSignatories,
            'memberOfficers' => $memberOfficers,
            'accidentId' => $accidentId,
            'accident' => $accident,
            'resortPoliceId' => $accident->polres_id,
            'suspects' => $suspects,
            'spdpDocument' => $spdpDocument,
            'sprinSidik' => $sprinSidik,
            'sketTersangkaDocument' => $sketTersangkaDocument,
            's17Documents' => $s17Documents,
            'defaultS17Id' => $defaultS17Id,
            'defaultNomorSuratPerintahPenahanan' => $defaultNomorSuratPerintahPenahanan,
            'defaultTanggalSuratPerintahPenahanan' => $defaultTanggalSuratPerintahPenahanan,
            'defaultSuspectId' => $defaultSuspectId,
            'crimeTypes' => $crimeTypes,
            'crimeClasses' => $crimeClasses,
            'crimeConstitutions' => $crimeConstitutions,
            'initialMainLaws' => $initialMainLaws,
            'initialAdditionalLaws' => $initialAdditionalLaws,
            'defaultNomorSurat' => $defaultNomorSurat,
            'defaultPertimbangan' => $defaultPertimbangan,
            'defaultDikeluarkanDi' => $defaultDikeluarkanDi,
            'defaultKotaRumahSakit' => $defaultKotaRumahSakit,
            'defaultHariPenyerahan' => Carbon::now()->locale('id')->translatedFormat('l'),
            'defaultTanggalPenyerahan' => Carbon::now()->format('Y-m-d'),
        ];

        return view('docs.surat-perintah-pembantaran-penahanan-document.create', $viewData);
    }

    public function store(Request $request)
    {
        $validator = $this->validateForm($request);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $accidentId = htmlspecialchars($request->accident_id);
        $accident = Accident::with(['police', 'polres'])->where('id', $accidentId)->first();

        if (! $accident) {
            return redirect()->back()->with('error', 'Gagal menyimpan. Data Laporan Polisi tidak ditemukan.');
        }

        // Resolusi kode satker penerbit SPDP
        $satkerCode = $accident->police->satker_code ?? $accident->polres->satker_code ?? $accident->polres->code ?? '';
        $kodeSatkerPenerbitSpdp = $request->kode_satker_penerbit_spdp ?: $satkerCode;

        DB::beginTransaction();
        try {
            $document = new SuratPerintahPembantaranPenahananDocument();
            $document->accident_id = $accidentId;
            $document->document_category_id = '0608';
            $document->status_id = '2'; // Dokumen Dibuat
            $document->nomor_surat = $request->nomor_surat;
            $document->tanggal_surat = $request->tanggal_surat ? Carbon::parse($request->tanggal_surat)->format('Y-m-d') : null;
            $document->dikeluarkan_di = $request->dikeluarkan_di ?: ($accident->polres->name ?? '');
            $document->pertimbangan = $request->pertimbangan ?: null;

            // Data SPDP & Satker
            $document->nomor_spdp = $request->nomor_spdp ?: null;
            $document->tanggal_spdp = $request->tanggal_spdp ? Carbon::parse($request->tanggal_spdp)->format('Y-m-d') : null;
            $document->kode_satker_penerbit_spdp = $kodeSatkerPenerbitSpdp;

            // Rujukan Dasar Perkara
            $document->nomor_surat_perintah_penyidikan = $request->nomor_surat_perintah_penyidikan ?: null;
            $document->tanggal_surat_perintah_penyidikan = $request->tanggal_surat_perintah_penyidikan ? Carbon::parse($request->tanggal_surat_perintah_penyidikan)->format('Y-m-d') : null;
            $document->nomor_sket_tersangka = $request->nomor_sket_tersangka ?: null;
            $document->tanggal_sket_tersangka = $request->tanggal_sket_tersangka ? Carbon::parse($request->tanggal_sket_tersangka)->format('Y-m-d') : null;
            $document->surat_perintah_penahanan_document_id = $request->surat_perintah_penahanan_document_id ?: null;
            $document->nomor_surat_perintah_penahanan = $request->nomor_surat_perintah_penahanan ?: null;
            $document->tanggal_surat_perintah_penahanan = $request->tanggal_surat_perintah_penahanan ? Carbon::parse($request->tanggal_surat_perintah_penahanan)->format('Y-m-d') : null;

            // Data Dokter & Rumah Sakit (SPPT-TI)
            $document->nama_dokter = $request->nama_dokter;
            $document->nomor_surat_dokter = $request->nomor_surat_dokter ?: null;
            $document->tanggal_surat_dokter = $request->tanggal_surat_dokter ? Carbon::parse($request->tanggal_surat_dokter)->format('Y-m-d') : null;
            $document->tempat_rawat_inap = $request->tempat_rawat_inap;
            $document->kota_rumah_sakit = $request->kota_rumah_sakit ?: ($accident->polres->name ?? '');
            $document->tanggal_mulai_rawat_inap = $request->tanggal_mulai_rawat_inap ? Carbon::parse($request->tanggal_mulai_rawat_inap)->format('Y-m-d') : null;

            // Lembar Penyerahan
            $document->hari_penyerahan = $request->hari_penyerahan ?: Carbon::now()->locale('id')->translatedFormat('l');
            $document->tanggal_penyerahan = $request->tanggal_penyerahan ? Carbon::parse($request->tanggal_penyerahan)->format('Y-m-d') : Carbon::now()->format('Y-m-d');
            $document->nama_penerima_keluarga = $request->nama_penerima_keluarga ?: null;
            $document->hubungan_penerima = $request->hubungan_penerima ?: 'Tersangka';

            $document->tembusan = null;
            $document->created_by_user_id = Auth::id();

            $document->timestamps = [
                'created_at' => now(),
            ];
            $document->ip_addresses = [
                'created_ip' => $request->ip(),
            ];

            $document->save();

            // 1. Simpan Pejabat Penandatangan (SIGNATORY / Selaku Penyidik)
            $signatoryId = $request->signatory_officer_id ?: $request->signatory;
            if ($signatoryId) {
                $signatoryOfficer = Officer::with('rank')->find($signatoryId);
                if ($signatoryOfficer) {
                    SuratPerintahPembantaranPenahananDocumentOfficer::create([
                        'document_id' => $document->id,
                        'officer_id' => $signatoryOfficer->id,
                        'register_number' => $signatoryOfficer->register_number ?? $signatoryOfficer->identifier_number,
                        'first_title' => $signatoryOfficer->first_title,
                        'first_name' => $signatoryOfficer->first_name ?: $signatoryOfficer->name,
                        'last_name' => $signatoryOfficer->last_name,
                        'last_title' => $signatoryOfficer->last_title,
                        'rank_id' => $signatoryOfficer->rank_id,
                        'position_id' => $signatoryOfficer->position_id,
                        'rank' => $signatoryOfficer->rank->name ?? null,
                        'position' => $signatoryOfficer->position,
                        'police_id' => $signatoryOfficer->police_id,
                        'class' => ['SIGNATORY'],
                        'type' => 'penandatangan',
                    ]);
                }
            }

            // 2. Simpan Petugas Penerima Perintah (RECEIVER) jika dipilih
            if ($request->receiver_officer_id) {
                $receiverOfficer = Officer::with('rank')->find($request->receiver_officer_id);
                if ($receiverOfficer) {
                    SuratPerintahPembantaranPenahananDocumentOfficer::create([
                        'document_id' => $document->id,
                        'officer_id' => $receiverOfficer->id,
                        'register_number' => $receiverOfficer->register_number ?? $receiverOfficer->identifier_number,
                        'first_title' => $receiverOfficer->first_title,
                        'first_name' => $receiverOfficer->first_name ?: $receiverOfficer->name,
                        'last_name' => $receiverOfficer->last_name,
                        'last_title' => $receiverOfficer->last_title,
                        'rank_id' => $receiverOfficer->rank_id,
                        'position_id' => $receiverOfficer->position_id,
                        'rank' => $receiverOfficer->rank->name ?? null,
                        'position' => $receiverOfficer->position,
                        'police_id' => $receiverOfficer->police_id,
                        'class' => ['RECEIVER'],
                        'type' => 'penerima_perintah',
                    ]);
                }
            }

            // 3. Simpan Petugas Penyerah Surat (DELIVERER) jika dipilih
            if ($request->deliverer_officer_id) {
                $delivererOfficer = Officer::with('rank')->find($request->deliverer_officer_id);
                if ($delivererOfficer) {
                    SuratPerintahPembantaranPenahananDocumentOfficer::create([
                        'document_id' => $document->id,
                        'officer_id' => $delivererOfficer->id,
                        'register_number' => $delivererOfficer->register_number ?? $delivererOfficer->identifier_number,
                        'first_title' => $delivererOfficer->first_title,
                        'first_name' => $delivererOfficer->first_name ?: $delivererOfficer->name,
                        'last_name' => $delivererOfficer->last_name,
                        'last_title' => $delivererOfficer->last_title,
                        'rank_id' => $delivererOfficer->rank_id,
                        'position_id' => $delivererOfficer->position_id,
                        'rank' => $delivererOfficer->rank->name ?? null,
                        'position' => $delivererOfficer->position,
                        'police_id' => $delivererOfficer->police_id,
                        'class' => ['DELIVERER'],
                        'type' => 'penyerah',
                    ]);
                }
            }

            // 4. Simpan Tersangka
            $suspectInput = $request->suspects ?: $request->suspect_id;
            $suspectIds = is_array($suspectInput) ? $suspectInput : [$suspectInput];
            foreach ($suspectIds as $sId) {
                if ($sId) {
                    SuratPerintahPembantaranPenahananDocumentSuspect::create([
                        'document_id' => $document->id,
                        'suspect_id' => $sId,
                    ]);
                }
            }

            // 5. Simpan Pasal Pidana (Dasar Hukum)
            $lawCrimeTypeIds = $request->lawCrimeTypeIds ?? [];
            $lawCrimeClassIds = $request->lawCrimeClassIds ?? [];
            $lawCrimeConstitutionIds = $request->lawCrimeConstitutionIds ?? [];
            $lawCrimeConstitutionChapters = $request->lawCrimeConstitutionChapters ?? [];

            if (is_array($lawCrimeTypeIds) && count($lawCrimeTypeIds) > 0) {
                foreach ($lawCrimeTypeIds as $idx => $ctId) {
                    if ($ctId) {
                        SuratPerintahPembantaranPenahananDocumentLaw::create([
                            'document_id' => $document->id,
                            'crime_type_id' => $ctId,
                            'crime_class_id' => $lawCrimeClassIds[$idx] ?? null,
                            'crime_constitution_id' => $lawCrimeConstitutionIds[$idx] ?? null,
                            'constitution_chapter' => $lawCrimeConstitutionChapters[$idx] ?? null,
                            'flag' => 'MAIN',
                        ]);
                    }
                }
            } elseif ($request->has('main_laws')) {
                $mainLaws = $request->input('main_laws', []);
                if (is_array($mainLaws)) {
                    foreach ($mainLaws as $lawData) {
                        if (!empty($lawData['crime_type_id'])) {
                            SuratPerintahPembantaranPenahananDocumentLaw::create([
                                'document_id' => $document->id,
                                'crime_type_id' => $lawData['crime_type_id'],
                                'crime_class_id' => $lawData['crime_class_id'] ?? null,
                                'crime_constitution_id' => $lawData['crime_constitution_id'] ?? null,
                                'constitution_chapter' => $lawData['constitution_chapter'] ?? null,
                                'flag' => 'MAIN',
                            ]);
                        }
                    }
                }
            }

            $lawAdditionalNames = $request->lawAdditionalNames ?? [];
            if (is_array($lawAdditionalNames) && count($lawAdditionalNames) > 0) {
                foreach ($lawAdditionalNames as $addLaw) {
                    if (trim($addLaw)) {
                        SuratPerintahPembantaranPenahananDocumentLaw::create([
                            'document_id' => $document->id,
                            'constitution' => trim($addLaw),
                            'flag' => 'ADDITIONAL',
                        ]);
                    }
                }
            } elseif ($request->has('additional_laws')) {
                $additionalLaws = $request->input('additional_laws', []);
                if (is_array($additionalLaws)) {
                    foreach ($additionalLaws as $lawData) {
                        if (!empty($lawData['constitution'])) {
                            SuratPerintahPembantaranPenahananDocumentLaw::create([
                                'document_id' => $document->id,
                                'constitution' => $lawData['constitution'],
                                'flag' => 'ADDITIONAL',
                            ]);
                        }
                    }
                }
            }

            DB::commit();

            return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
                ->with('success', 'Surat Perintah Pembantaran Penahanan (S-23) berhasil dibuat dan diajukan ke Admin Satker.');
        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()->with('error', 'Gagal menyimpan Dokumen S-23: '.$e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        $document = SuratPerintahPembantaranPenahananDocument::with([
            'officers',
            'suspects',
            'laws.crimeType',
            'laws.crimeClass',
            'laws.crimeConstitution',
            'accident.polres.polda',
        ])->where('id', $id)->first();

        if (! $document) {
            return redirect()->back()->with('error', 'Data Dokumen S-23 tidak ditemukan.');
        }

        $accidentId = $document->accident_id;
        $accident = $document->accident;

        $getOldNewPolresIds = $this->getOldNewPolresIds($accident->polres_id);

        $authorizedSignatories = Officer::withRelated()
            ->selectFullName()
            ->whereHasUserActive()
            ->hasDataComplete()
            ->whereIn('police_id', $getOldNewPolresIds)
            ->signatory()
            ->active()
            ->valid()
            ->orderBy('first_name')
            ->get();

        $memberOfficers = Officer::withRelated()
            ->selectFullName()
            ->whereHasUserActive()
            ->hasDataComplete()
            ->whereIn('police_id', $getOldNewPolresIds)
            ->member()
            ->active()
            ->valid()
            ->orderBy('first_name')
            ->get();

        $suspects = Suspect::where('accident_id', $accidentId)->get();

        $crimeTypes = CrimeType::with(['crimeClass', 'crimeConstitution'])->where('is_active', true)->orderBy('name')->get();
        $crimeClasses = CrimeClass::where('is_active', true)->orderBy('name')->get();
        $crimeConstitutions = CrimeConstitution::where('is_active', true)->orderBy('name')->get();

        $existingMainLaws = [];
        $existingAdditionalLaws = [];
        if ($document->laws) {
            foreach ($document->laws as $law) {
                if ($law->flag === 'MAIN' || empty($law->flag)) {
                    $existingMainLaws[] = [
                        'crime_type_id' => $law->crime_type_id,
                        'crime_type_name' => $law->crimeType->name ?? '',
                        'crime_class_id' => $law->crime_class_id,
                        'crime_class_name' => $law->crimeClass->name ?? '',
                        'crime_constitution_id' => $law->crime_constitution_id,
                        'crime_constitution_name' => $law->crimeConstitution->name ?? '',
                        'constitution_chapter' => $law->constitution_chapter ?? '',
                    ];
                } elseif ($law->flag === 'ADDITIONAL' || !empty($law->constitution)) {
                    $existingAdditionalLaws[] = [
                        'constitution' => $law->constitution ?? '',
                    ];
                }
            }
        }

        $signatoryOfficer = $document->officers->first(function ($o) {
            $class = is_array($o->class) ? $o->class : json_decode($o->class, true);
            return in_array('SIGNATORY', (array) $class) || $o->type === 'penandatangan';
        });

        $receiverOfficer = $document->officers->first(function ($o) {
            $class = is_array($o->class) ? $o->class : json_decode($o->class, true);
            return in_array('RECEIVER', (array) $class) || $o->type === 'penerima_perintah' || $o->type === 'penerima';
        });

        $delivererOfficer = $document->officers->first(function ($o) {
            $class = is_array($o->class) ? $o->class : json_decode($o->class, true);
            return in_array('DELIVERER', (array) $class) || $o->type === 'penyerah_surat' || $o->type === 'penyerah';
        });

        $satkerName = $accident->police->full_name ?? $accident->polres->full_name ?? $accident->polres->name ?? 'Satker';
        $satkerCode = $document->kode_satker_penerbit_spdp ?: ($accident->police->satker_code ?? $accident->polres->satker_code ?? $accident->polres->code ?? '');

        $selectedSuspectIds = $document->suspects->pluck('id')->filter()->values()->toArray();
        if (empty($selectedSuspectIds)) {
            $selectedSuspectIds = $document->suspects->pluck('suspect_id')->filter()->values()->toArray();
        }

        $s17Documents = \App\Models\Doc\SuratPerintahPenahananDocument\SuratPerintahPenahananDocument::with(['suspects'])
            ->where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->orderBy('created_at', 'desc')
            ->get();

        $viewData = [
            'document' => $document,
            'satkerName' => $satkerName,
            'satkerCode' => $satkerCode,
            'authorizedSignatories' => $authorizedSignatories,
            'memberOfficers' => $memberOfficers,
            'accidentId' => $accidentId,
            'accident' => $accident,
            'resortPoliceId' => $accident->polres_id,
            'suspects' => $suspects,
            's17Documents' => $s17Documents,
            'selectedSuspectId' => $selectedSuspectIds[0] ?? null,
            'selectedSuspectIds' => $selectedSuspectIds,
            'selectedSignatoryId' => $signatoryOfficer->officer_id ?? null,
            'selectedReceiverId' => $receiverOfficer->officer_id ?? null,
            'selectedDelivererId' => $delivererOfficer->officer_id ?? null,
            'crimeTypes' => $crimeTypes,
            'crimeClasses' => $crimeClasses,
            'crimeConstitutions' => $crimeConstitutions,
            'existingMainLaws' => $existingMainLaws,
            'existingAdditionalLaws' => $existingAdditionalLaws,
        ];

        return view('docs.surat-perintah-pembantaran-penahanan-document.edit', $viewData);
    }

    public function update(Request $request, $id)
    {
        $validator = $this->validateForm($request);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $document = SuratPerintahPembantaranPenahananDocument::where('id', $id)->first();
        if (! $document) {
            return redirect()->back()->with('error', 'Data Dokumen S-23 tidak ditemukan.');
        }

        $accidentId = $document->accident_id;
        $accident = Accident::with(['police', 'polres'])->where('id', $accidentId)->first();

        // Resolusi kode satker penerbit SPDP
        $satkerCode = $accident->police->satker_code ?? $accident->polres->satker_code ?? $accident->polres->code ?? '';
        $kodeSatkerPenerbitSpdp = $request->kode_satker_penerbit_spdp ?: ($document->kode_satker_penerbit_spdp ?: $satkerCode);

        DB::beginTransaction();
        try {
            $document->nomor_surat = $request->nomor_surat ?: $document->nomor_surat;
            $document->tanggal_surat = $request->tanggal_surat ? Carbon::parse($request->tanggal_surat)->format('Y-m-d') : $document->tanggal_surat;
            $document->dikeluarkan_di = $request->dikeluarkan_di ?: $document->dikeluarkan_di;
            $document->pertimbangan = $request->pertimbangan ?: $document->pertimbangan;

            // SPDP
            $document->nomor_spdp = $request->nomor_spdp ?: $document->nomor_spdp;
            $document->tanggal_spdp = $request->tanggal_spdp ? Carbon::parse($request->tanggal_spdp)->format('Y-m-d') : $document->tanggal_spdp;
            $document->kode_satker_penerbit_spdp = $kodeSatkerPenerbitSpdp;

            // Dasar Perkara
            $document->nomor_surat_perintah_penyidikan = $request->nomor_surat_perintah_penyidikan ?: $document->nomor_surat_perintah_penyidikan;
            $document->tanggal_surat_perintah_penyidikan = $request->tanggal_surat_perintah_penyidikan ? Carbon::parse($request->tanggal_surat_perintah_penyidikan)->format('Y-m-d') : $document->tanggal_surat_perintah_penyidikan;
            $document->nomor_sket_tersangka = $request->nomor_sket_tersangka ?: $document->nomor_sket_tersangka;
            $document->tanggal_sket_tersangka = $request->tanggal_sket_tersangka ? Carbon::parse($request->tanggal_sket_tersangka)->format('Y-m-d') : $document->tanggal_sket_tersangka;
            $document->surat_perintah_penahanan_document_id = $request->surat_perintah_penahanan_document_id ?: $document->surat_perintah_penahanan_document_id;
            $document->nomor_surat_perintah_penahanan = $request->nomor_surat_perintah_penahanan ?: $document->nomor_surat_perintah_penahanan;
            $document->tanggal_surat_perintah_penahanan = $request->tanggal_surat_perintah_penahanan ? Carbon::parse($request->tanggal_surat_perintah_penahanan)->format('Y-m-d') : $document->tanggal_surat_perintah_penahanan;

            // Data Dokter & Rumah Sakit
            $document->nama_dokter = $request->nama_dokter ?: $document->nama_dokter;
            $document->nomor_surat_dokter = $request->nomor_surat_dokter ?: $document->nomor_surat_dokter;
            $document->tanggal_surat_dokter = $request->tanggal_surat_dokter ? Carbon::parse($request->tanggal_surat_dokter)->format('Y-m-d') : $document->tanggal_surat_dokter;
            $document->tempat_rawat_inap = $request->tempat_rawat_inap ?: $document->tempat_rawat_inap;
            $document->kota_rumah_sakit = $request->kota_rumah_sakit ?: $document->kota_rumah_sakit;
            $document->tanggal_mulai_rawat_inap = $request->tanggal_mulai_rawat_inap ? Carbon::parse($request->tanggal_mulai_rawat_inap)->format('Y-m-d') : $document->tanggal_mulai_rawat_inap;

            // Lembar Penyerahan
            $document->hari_penyerahan = $request->hari_penyerahan ?: $document->hari_penyerahan;
            $document->tanggal_penyerahan = $request->tanggal_penyerahan ? Carbon::parse($request->tanggal_penyerahan)->format('Y-m-d') : $document->tanggal_penyerahan;
            $document->nama_penerima_keluarga = $request->nama_penerima_keluarga ?: $document->nama_penerima_keluarga;
            $document->hubungan_penerima = $request->hubungan_penerima ?: $document->hubungan_penerima;

            // Jika dokumen sedang dalam status '4' (Dikembalikan), otomatis kembalikan ke '3' (Diajukan Ulang)
            if ($document->status_id == '4') {
                $document->status_id = '3';
            }

            $document->tembusan = null;
            $document->updated_by_user_id = Auth::id();

            $document->save();

            // Re-sync Officers
            SuratPerintahPembantaranPenahananDocumentOfficer::where('document_id', $document->id)->forceDelete();

            $signatoryId = $request->signatory_officer_id ?: $request->signatory;
            if ($signatoryId) {
                $signatoryOfficer = Officer::with('rank')->find($signatoryId);
                if ($signatoryOfficer) {
                    SuratPerintahPembantaranPenahananDocumentOfficer::create([
                        'document_id' => $document->id,
                        'officer_id' => $signatoryOfficer->id,
                        'register_number' => $signatoryOfficer->register_number ?? $signatoryOfficer->identifier_number,
                        'first_title' => $signatoryOfficer->first_title,
                        'first_name' => $signatoryOfficer->first_name ?: $signatoryOfficer->name,
                        'last_name' => $signatoryOfficer->last_name,
                        'last_title' => $signatoryOfficer->last_title,
                        'rank_id' => $signatoryOfficer->rank_id,
                        'position_id' => $signatoryOfficer->position_id,
                        'rank' => $signatoryOfficer->rank->name ?? null,
                        'position' => $signatoryOfficer->position,
                        'police_id' => $signatoryOfficer->police_id,
                        'class' => ['SIGNATORY'],
                        'type' => 'penandatangan',
                    ]);
                }
            }

            if ($request->receiver_officer_id) {
                $receiverOfficer = Officer::with('rank')->find($request->receiver_officer_id);
                if ($receiverOfficer) {
                    SuratPerintahPembantaranPenahananDocumentOfficer::create([
                        'document_id' => $document->id,
                        'officer_id' => $receiverOfficer->id,
                        'register_number' => $receiverOfficer->register_number ?? $receiverOfficer->identifier_number,
                        'first_title' => $receiverOfficer->first_title,
                        'first_name' => $receiverOfficer->first_name ?: $receiverOfficer->name,
                        'last_name' => $receiverOfficer->last_name,
                        'last_title' => $receiverOfficer->last_title,
                        'rank_id' => $receiverOfficer->rank_id,
                        'position_id' => $receiverOfficer->position_id,
                        'rank' => $receiverOfficer->rank->name ?? null,
                        'position' => $receiverOfficer->position,
                        'police_id' => $receiverOfficer->police_id,
                        'class' => ['RECEIVER'],
                        'type' => 'penerima_perintah',
                    ]);
                }
            }

            if ($request->deliverer_officer_id) {
                $delivererOfficer = Officer::with('rank')->find($request->deliverer_officer_id);
                if ($delivererOfficer) {
                    SuratPerintahPembantaranPenahananDocumentOfficer::create([
                        'document_id' => $document->id,
                        'officer_id' => $delivererOfficer->id,
                        'register_number' => $delivererOfficer->register_number ?? $delivererOfficer->identifier_number,
                        'first_title' => $delivererOfficer->first_title,
                        'first_name' => $delivererOfficer->first_name ?: $delivererOfficer->name,
                        'last_name' => $delivererOfficer->last_name,
                        'last_title' => $delivererOfficer->last_title,
                        'rank_id' => $delivererOfficer->rank_id,
                        'position_id' => $delivererOfficer->position_id,
                        'rank' => $delivererOfficer->rank->name ?? null,
                        'position' => $delivererOfficer->position,
                        'police_id' => $delivererOfficer->police_id,
                        'class' => ['DELIVERER'],
                        'type' => 'penyerah',
                    ]);
                }
            }

            // Re-sync Suspects
            SuratPerintahPembantaranPenahananDocumentSuspect::where('document_id', $document->id)->delete();
            $suspectInput = $request->suspects ?: $request->suspect_id;
            $suspectIds = is_array($suspectInput) ? $suspectInput : [$suspectInput];
            foreach ($suspectIds as $sId) {
                if ($sId) {
                    SuratPerintahPembantaranPenahananDocumentSuspect::create([
                        'document_id' => $document->id,
                        'suspect_id' => $sId,
                    ]);
                }
            }

            // Re-sync Laws
            SuratPerintahPembantaranPenahananDocumentLaw::where('document_id', $document->id)->forceDelete();
            $lawCrimeTypeIds = $request->lawCrimeTypeIds ?? [];
            $lawCrimeClassIds = $request->lawCrimeClassIds ?? [];
            $lawCrimeConstitutionIds = $request->lawCrimeConstitutionIds ?? [];
            $lawCrimeConstitutionChapters = $request->lawCrimeConstitutionChapters ?? [];

            if (is_array($lawCrimeTypeIds) && count($lawCrimeTypeIds) > 0) {
                foreach ($lawCrimeTypeIds as $idx => $ctId) {
                    if ($ctId) {
                        SuratPerintahPembantaranPenahananDocumentLaw::create([
                            'document_id' => $document->id,
                            'crime_type_id' => $ctId,
                            'crime_class_id' => $lawCrimeClassIds[$idx] ?? null,
                            'crime_constitution_id' => $lawCrimeConstitutionIds[$idx] ?? null,
                            'constitution_chapter' => $lawCrimeConstitutionChapters[$idx] ?? null,
                            'flag' => 'MAIN',
                        ]);
                    }
                }
            } elseif ($request->has('main_laws')) {
                $mainLaws = $request->input('main_laws', []);
                if (is_array($mainLaws)) {
                    foreach ($mainLaws as $lawData) {
                        if (!empty($lawData['crime_type_id'])) {
                            SuratPerintahPembantaranPenahananDocumentLaw::create([
                                'document_id' => $document->id,
                                'crime_type_id' => $lawData['crime_type_id'],
                                'crime_class_id' => $lawData['crime_class_id'] ?? null,
                                'crime_constitution_id' => $lawData['crime_constitution_id'] ?? null,
                                'constitution_chapter' => $lawData['constitution_chapter'] ?? null,
                                'flag' => 'MAIN',
                            ]);
                        }
                    }
                }
            }

            $lawAdditionalNames = $request->lawAdditionalNames ?? [];
            if (is_array($lawAdditionalNames) && count($lawAdditionalNames) > 0) {
                foreach ($lawAdditionalNames as $addLaw) {
                    if (trim($addLaw)) {
                        SuratPerintahPembantaranPenahananDocumentLaw::create([
                            'document_id' => $document->id,
                            'constitution' => trim($addLaw),
                            'flag' => 'ADDITIONAL',
                        ]);
                    }
                }
            } elseif ($request->has('additional_laws')) {
                $additionalLaws = $request->input('additional_laws', []);
                if (is_array($additionalLaws)) {
                    foreach ($additionalLaws as $lawData) {
                        if (!empty($lawData['constitution'])) {
                            SuratPerintahPembantaranPenahananDocumentLaw::create([
                                'document_id' => $document->id,
                                'constitution' => $lawData['constitution'],
                                'flag' => 'ADDITIONAL',
                            ]);
                        }
                    }
                }
            }

            DB::commit();

            return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
                ->with('success', 'Surat Perintah Pembantaran Penahanan (S-23) berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()->with('error', 'Gagal memperbarui Dokumen S-23: '.$e->getMessage())->withInput();
        }
    }

    public function delete($id)
    {
        $document = SuratPerintahPembantaranPenahananDocument::where('id', $id)->first();
        if (! $document) {
            return redirect()->back()->with('error', 'Data Dokumen S-23 tidak ditemukan.');
        }

        $accidentId = $document->accident_id;
        $document->delete();

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
            ->with('success', 'Surat Perintah Pembantaran Penahanan (S-23) berhasil dihapus.');
    }

    public function validateRequestForm(Request $request)
    {
        $validator = $this->validateForm($request);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Validasi berhasil.',
        ]);
    }

    protected function validateForm(Request $request)
    {
        if (!$request->has('signatory_officer_id') && $request->has('signatory')) {
            $request->merge(['signatory_officer_id' => $request->signatory]);
        }
        if (!$request->has('suspect_id') && $request->has('suspects')) {
            $request->merge(['suspect_id' => $request->suspects]);
        }
        $rules = [
            'surat_perintah_penahanan_document_id' => 'nullable',
            'nomor_surat' => 'required|string|max:255',
            'tanggal_surat' => 'required|date',
            'dikeluarkan_di' => 'required|max:255',
            'nama_dokter' => 'required|string|max:255',
            'tempat_rawat_inap' => 'required|string|max:255',
            'tanggal_mulai_rawat_inap' => 'required|date',
            'suspect_id' => 'required',
            'signatory_officer_id' => 'required',
        ];

        $messages = [
            'nomor_surat.required' => 'Nomor Surat Perintah Pembantaran Penahanan wajib diisi.',
            'nomor_surat.max' => 'Nomor Surat maksimal 255 karakter.',
            'tanggal_surat.required' => 'Tanggal Surat wajib diisi.',
            'tanggal_surat.date' => 'Format Tanggal Surat tidak valid.',
            'dikeluarkan_di.required' => 'Kota penerbitan (Dikeluarkan di) wajib diisi.',
            'nama_dokter.required' => 'Nama Dokter yang merawat/memeriksa wajib diisi.',
            'tempat_rawat_inap.required' => 'Nama Rumah Sakit tempat rawat inap (opname) wajib diisi.',
            'tanggal_mulai_rawat_inap.required' => 'Tanggal mulai rawat inap wajib diisi.',
            'suspect_id.required' => 'Tersangka yang dibantarkan wajib dipilih.',
            'signatory_officer_id.required' => 'Penyidik penandatangan surat perintah wajib dipilih.',
        ];

        return Validator::make($request->all(), $rules, $messages);
    }

    public function download($id)
    {
        $document = SuratPerintahPembantaranPenahananDocument::with([
            'attachment',
            'officers.officer.position.positionCluster',
            'officers.officer.rank',
            'suspects.gender',
            'suspects.job',
            'suspects.religion',
            'laws.crimeType',
            'laws.crimeClass',
            'laws.crimeConstitution',
            'accident.polres.polda',
            'accident.police',
        ])->where('id', $id)->first();

        if (! $document) {
            return redirect()->back()->with('error', 'Data Dokumen Surat Perintah Pembantaran Penahanan (S-23) tidak ditemukan.');
        }

        // Jika ada attachment fisik dan tidak minta template, deliver attachment
        $isTemplateDownload = request()->has('template') || !request()->has('attachment');
        if (! $isTemplateDownload && $document->attachment && file_exists(public_path('documents/attachments/'.$document->attachment->name))) {
            return response()->download(public_path('documents/attachments/'.$document->attachment->name), $document->attachment->original_name ?? $document->attachment->name);
        }

        $accidentId = htmlspecialchars(request()->query('accident_id') ?: (request()->input('accident_id') ?: $document->accident_id));
        $accident = Accident::with(['polres', 'polres.polda'])->where('id', $accidentId)->first();

        if (! $accident) {
            return redirect()->back()->with('error', 'Gagal mendownload dokumen. Data Laporan Polisi (Accident) tidak ditemukan.');
        }

        try {
            $templateFile = file_exists(public_path('word-template/surat_perintah_pembantaran_penahanan_s23.docx'))
                ? public_path('word-template/surat_perintah_pembantaran_penahanan_s23.docx')
                : public_path('word-template/surat_perintah_pembantaran_penahanan.docx');

            if (! file_exists($templateFile)) {
                return redirect()->back()->with('error', 'File template dokumen word S-23 tidak ditemukan di server.');
            }

            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($templateFile);

            // 1. Kop Surat & Identitas Dokumen
            $resorPoliceFullName = (in_array($accident->polres->id ?? '', ['1114'])) ? 'DIREKTORAT LALU LINTAS' : 'RESOR '.strtoupper($accident->polres->full_name ?? '');
            $poldaFullName = $accident->polres->polda->full_name ?? ($accident->polres->polda->name ?? '');
            $resorPoliceAddress = ucwords(strtolower(($accident->polres->address ?? '').', '.($accident->polres->polres_district ?? '').', '.($accident->polres->polres_zipcode ?? '')));
            $documentLocation = 'S-23';
            $docDateFormatted = $document->tanggal_surat ? Carbon::parse($document->tanggal_surat)->locale('id')->translatedFormat('d F Y') : '-';
            $dikeluarkanDi = $document->dikeluarkan_di ?: ucwords(strtolower($accident->polres->polres_district ?? ($accident->polres->name ?? 'Tempat')));

            $templateProcessor->setValue('daerahPoliceFullName', $poldaFullName);
            $templateProcessor->setValue('resorPoliceFullName', $resorPoliceFullName);
            $templateProcessor->setValue('resorPoliceAddress', $resorPoliceAddress);
            $templateProcessor->setValue('documentLocation', $documentLocation);
            $templateProcessor->setValue('documentNumber', $document->nomor_surat ?? '-');
            $templateProcessor->setValue('documentDate', $docDateFormatted);
            $templateProcessor->setValue('dikeluarkanDi', $dikeluarkanDi);

            // 2. Pertimbangan
            $pertimbangan = $document->pertimbangan ?: 'bahwa dalam rangka penyidikan, melihat kondisi kesehatan tersangka yang ditahan menderita sakit dan dirawat di rumah sakit, memerlukan rawat inap (opname) di luar rumah tahanan negara, dipandang perlu mengeluarkan surat perintah.';
            $templateProcessor->setValue('pertimbangan', $pertimbangan);

            // 3. Dasar Poin 1 s.d. 9
            $mainLawTitles = [];
            $additionalLawTitles = [];
            if ($document->laws && $document->laws->count() > 0) {
                foreach ($document->laws as $law) {
                    if ($law->flag === 'MAIN' || empty($law->flag)) {
                        $constName = $law->crimeConstitution->name ?? '';
                        if (!empty($constName) && !in_array($constName, $mainLawTitles)) {
                            $mainLawTitles[] = $constName;
                        }
                    } elseif ($law->flag === 'ADDITIONAL' || !empty($law->constitution)) {
                        $addName = trim($law->constitution);
                        if (!empty($addName) && !in_array($addName, $additionalLawTitles)) {
                            $additionalLawTitles[] = $addName;
                        }
                    }
                }
            }
            if (empty($mainLawTitles) && empty($additionalLawTitles)) {
                $mainLawTitles[] = 'Undang-Undang Nomor 22 Tahun 2009 tentang Lalu Lintas dan Angkutan Jalan';
            }
            $allLaws = array_merge($mainLawTitles, $additionalLawTitles);
            $undangUndangLain = implode(' dan ', $allLaws);

            $lpDateFormatted = $accident->report_date ? Carbon::parse($accident->report_date)->locale('id')->translatedFormat('d F Y') : ($accident->date ? Carbon::parse($accident->date)->locale('id')->translatedFormat('d F Y') : '-');

            $templateProcessor->setValue('undangUndangLain', $undangUndangLain);
            $templateProcessor->setValue('accidentNumber', $accident->no_lp ?? '-');
            $templateProcessor->setValue('accidentDate', $lpDateFormatted);
            $templateProcessor->setValue('suratPerintahPenyidikanDocumentNumber', $document->nomor_surat_perintah_penyidikan ?? '-');
            $templateProcessor->setValue('suratPerintahPenyidikanDocumentDate', $document->tanggal_surat_perintah_penyidikan ? Carbon::parse($document->tanggal_surat_perintah_penyidikan)->locale('id')->translatedFormat('d F Y') : '-');
            $templateProcessor->setValue('suratKetetapanPenetapanTersangkaDocumentNumber', $document->nomor_sket_tersangka ?? '-');
            $templateProcessor->setValue('suratKetetapanPenetapanTersangkaDocumentDate', $document->tanggal_sket_tersangka ? Carbon::parse($document->tanggal_sket_tersangka)->locale('id')->translatedFormat('d F Y') : '-');
            $templateProcessor->setValue('suratPerintahPenahananDocumentNumber', $document->nomor_surat_perintah_penahanan ?? '-');
            $templateProcessor->setValue('suratPerintahPenahananDocumentDate', $document->tanggal_surat_perintah_penahanan ? Carbon::parse($document->tanggal_surat_perintah_penahanan)->locale('id')->translatedFormat('d F Y') : '-');
            $templateProcessor->setValue('namaDokter', $document->nama_dokter ?? '-');
            $templateProcessor->setValue('nomorSuratDokter', $document->nomor_surat_dokter ?? '-');
            $templateProcessor->setValue('tanggalSuratDokter', $document->tanggal_surat_dokter ? Carbon::parse($document->tanggal_surat_dokter)->locale('id')->translatedFormat('d F Y') : '-');

            // 4. Identitas Tersangka
            $primarySuspect = $document->suspects->first();
            $templateProcessor->setValue('suspectName', $primarySuspect->name ?? '-');
            $templateProcessor->setValue('suspectIdentityNumber', $primarySuspect->identity_number ?? '-');
            $templateProcessor->setValue('suspectGenderName', $primarySuspect->gender->name ?? ($primarySuspect->gender ?? '-'));
            $templateProcessor->setValue('suspectBirthPlace', $primarySuspect->birth_place ?? '-');
            $templateProcessor->setValue('suspectBirthDate', ($primarySuspect && $primarySuspect->birth_date) ? Carbon::parse($primarySuspect->birth_date)->locale('id')->translatedFormat('d F Y') : '-');
            $templateProcessor->setValue('suspectReligionName', $primarySuspect->religion->name ?? ($primarySuspect->religion ?? '-'));
            $templateProcessor->setValue('suspectJobName', $primarySuspect->job->name ?? ($primarySuspect->job ?? '-'));
            $templateProcessor->setValue('suspectNationality', $primarySuspect->citizenship ?? 'Indonesia');
            $templateProcessor->setValue('suspectFullAddress', $primarySuspect->address ?? '-');

            // 5. Tempat & Tanggal Rawat Inap (Opname)
            $templateProcessor->setValue('tempatRawatInap', $document->tempat_rawat_inap ?? '-');
            $templateProcessor->setValue('kotaRumahSakit', $document->kota_rumah_sakit ?: ($dikeluarkanDi ?: 'Tempat'));
            $templateProcessor->setValue('tanggalMulaiRawatInap', $document->tanggal_mulai_rawat_inap ? Carbon::parse($document->tanggal_mulai_rawat_inap)->locale('id')->translatedFormat('d F Y') : '-');

            // 6. Petugas Penandatangan (SIGNATORY)
            $signatoryDocOfficer = $document->officers->first(function ($o) {
                $class = is_array($o->class) ? $o->class : json_decode($o->class, true);
                return in_array('SIGNATORY', (array) $class) || $o->type === 'penandatangan';
            });
            $sigName = '-';
            $sigRank = '-';
            $sigNrp = '-';
            if ($signatoryDocOfficer) {
                $sigModel = $signatoryDocOfficer->officer;
                $sigFirstTitle = $signatoryDocOfficer->first_title;
                $sigFirstName = $signatoryDocOfficer->first_name;
                $sigLastName = $signatoryDocOfficer->last_name;
                $sigLastTitle = $signatoryDocOfficer->last_title;
                $sigName = trim(($sigFirstTitle ? $sigFirstTitle.' ' : '').$sigFirstName.($sigLastName ? ' '.$sigLastName : '').($sigLastTitle ? ', '.$sigLastTitle : ''));
                $sigRank = '';
                if (is_array($signatoryDocOfficer->rank)) {
                    $sigRank = $signatoryDocOfficer->rank['name'] ?? ($signatoryDocOfficer->rank['full_name'] ?? '');
                } elseif (is_string($signatoryDocOfficer->rank)) {
                    $sigRank = $signatoryDocOfficer->rank;
                }
                if (empty($sigRank) && isset($sigModel->rank)) {
                    $sigRank = $sigModel->rank->name ?? '';
                }
                $sigNrp = is_string($signatoryDocOfficer->register_number) ? $signatoryDocOfficer->register_number : ($sigModel->register_number ?? '-');

                $clusterId = $sigModel->position->position_cluster_id ?? ($sigModel->position->positionCluster->id ?? null);
                $aliasName = $sigModel->position->positionCluster->alias_name ?? ($sigModel->position->name ?? 'KASAT LANTAS');

                if ($clusterId == '1') {
                    $signatoryHeadText = 'KEPALA KEPOLISIAN RESOR '.strtoupper($accident->polres->full_name ?? '');
                    $signatoryPositionHeadText = '';
                } elseif ($clusterId == '9') {
                    $signatoryHeadText = 'a.n. DIREKTUR LALU LINTAS POLDA '.strtoupper($poldaFullName);
                    $signatoryPositionHeadText = strtoupper($aliasName);
                } else {
                    $signatoryHeadText = 'a.n. KEPALA KEPOLISIAN RESOR '.strtoupper($accident->polres->full_name ?? '');
                    $signatoryPositionHeadText = strtoupper($aliasName);
                }

                $templateProcessor->setValue('signatoryHeadText', $signatoryHeadText);
                $templateProcessor->setValue('signatoryPositionHeadText', $signatoryPositionHeadText);
                $templateProcessor->setValue('signatoryName', $sigName);
                $templateProcessor->setValue('signatoryRankName', strtoupper($sigRank));
                $templateProcessor->setValue('signatoryRegisterNumber', $sigNrp);
            } else {
                $templateProcessor->setValue('signatoryHeadText', 'a.n. KEPALA KEPOLISIAN RESOR '.strtoupper($accident->polres->full_name ?? ''));
                $templateProcessor->setValue('signatoryPositionHeadText', 'KASAT LANTAS');
                $templateProcessor->setValue('signatoryName', '-');
                $templateProcessor->setValue('signatoryRankName', '-');
                $templateProcessor->setValue('signatoryRegisterNumber', '-');
            }

            // 7. Petugas Penerima Perintah (RECEIVER)
            $receiverDocOfficer = $document->officers->first(function ($o) {
                $class = is_array($o->class) ? $o->class : json_decode($o->class, true);
                return in_array('RECEIVER', (array) $class) || $o->type === 'penerima_perintah';
            });
            $recName = '-';
            $recRank = '-';
            $recNrp = '-';
            if ($receiverDocOfficer) {
                $recModel = $receiverDocOfficer->officer;
                $recFirstTitle = $receiverDocOfficer->first_title;
                $recFirstName = $receiverDocOfficer->first_name;
                $recLastName = $receiverDocOfficer->last_name;
                $recLastTitle = $receiverDocOfficer->last_title;
                $recName = trim(($recFirstTitle ? $recFirstTitle.' ' : '').$recFirstName.($recLastName ? ' '.$recLastName : '').($recLastTitle ? ', '.$recLastTitle : ''));
                $recRank = $receiverDocOfficer->rank ?: ($recModel->rank->name ?? '');
                $recNrp = $receiverDocOfficer->register_number ?: ($recModel->register_number ?? '-');

                $templateProcessor->setValue('recipientOfficerName', $recName);
                $templateProcessor->setValue('recipientOfficerRankName', strtoupper($recRank));
                $templateProcessor->setValue('recipientOfficerRegisterNumber', $recNrp);
            } else {
                $templateProcessor->setValue('recipientOfficerName', '-');
                $templateProcessor->setValue('recipientOfficerRankName', '-');
                $templateProcessor->setValue('recipientOfficerRegisterNumber', '-');
            }

            // 8. Petugas Penyerah Surat (DELIVERER) & Tanda Terima
            $delivererDocOfficer = $document->officers->first(function ($o) {
                $class = is_array($o->class) ? $o->class : json_decode($o->class, true);
                return in_array('DELIVERER', (array) $class) || $o->type === 'penyerah';
            });
            $delName = '-';
            $delRank = '-';
            $delNrp = '-';
            if ($delivererDocOfficer) {
                $delModel = $delivererDocOfficer->officer;
                $delFirstTitle = $delivererDocOfficer->first_title;
                $delFirstName = $delivererDocOfficer->first_name;
                $delLastName = $delivererDocOfficer->last_name;
                $delLastTitle = $delivererDocOfficer->last_title;
                $delName = trim(($delFirstTitle ? $delFirstTitle.' ' : '').$delFirstName.($delLastName ? ' '.$delLastName : '').($delLastTitle ? ', '.$delLastTitle : ''));
                $delRank = $delivererDocOfficer->rank ?: ($delModel->rank->name ?? '');
                $delNrp = $delivererDocOfficer->register_number ?: ($delModel->register_number ?? '-');

                $templateProcessor->setValue('delivererOfficerName', $delName);
                $templateProcessor->setValue('delivererOfficerRankName', strtoupper($delRank));
                $templateProcessor->setValue('delivererOfficerRegisterNumber', $delNrp);
            } else {
                $delName = $recName !== '-' ? $recName : $sigName;
                $delRank = $recRank !== '-' ? $recRank : $sigRank;
                $delNrp = $recNrp !== '-' ? $recNrp : $sigNrp;

                $templateProcessor->setValue('delivererOfficerName', $delName);
                $templateProcessor->setValue('delivererOfficerRankName', strtoupper($delRank));
                $templateProcessor->setValue('delivererOfficerRegisterNumber', $delNrp);
            }

            $templateProcessor->setValue('hariPenyerahan', $document->tanggal_surat ? Carbon::parse($document->tanggal_surat)->locale('id')->translatedFormat('l') : '.............');
            $templateProcessor->setValue('tanggalPenyerahan', $docDateFormatted);
            $templateProcessor->setValue('namaPenerimaKeluarga', $document->nama_penerima_keluarga ?: '..........................................');



            // 10. Lampiran (Hanya jika template masih menyediakan variabel officer_no)
            if (in_array('officer_no', $templateProcessor->getVariables())) {
                $blockOfficers = [];
                $no = 1;
                if ($receiverDocOfficer) {
                    $posName = 'Penyidik Pembantu';
                    if ($receiverDocOfficer->position) {
                        $posName = is_string($receiverDocOfficer->position) ? $receiverDocOfficer->position : ($receiverDocOfficer->position['name'] ?? ($receiverDocOfficer->position->name ?? 'Penyidik Pembantu'));
                    }
                    $blockOfficers[] = [
                        'officer_no' => $no++,
                        'officer_name' => $recName ?? '-',
                        'officer_rank' => strtoupper($recRank ?? '-'),
                        'officer_nrp' => $recNrp ?? '-',
                        'officer_position' => $posName,
                        'officer_role' => 'Penerima Perintah / Pelaksana',
                    ];
                }
                if ($delivererDocOfficer && $delivererDocOfficer->officer_id !== ($receiverDocOfficer->officer_id ?? null)) {
                    $posName = 'Penyidik Pembantu';
                    if ($delivererDocOfficer->position) {
                        $posName = is_string($delivererDocOfficer->position) ? $delivererDocOfficer->position : ($delivererDocOfficer->position['name'] ?? ($delivererDocOfficer->position->name ?? 'Penyidik Pembantu'));
                    }
                    $blockOfficers[] = [
                        'officer_no' => $no++,
                        'officer_name' => $delName ?? '-',
                        'officer_rank' => strtoupper($delRank ?? '-'),
                        'officer_nrp' => $delNrp ?? '-',
                        'officer_position' => $posName,
                        'officer_role' => 'Petugas Penyerah',
                    ];
                }
                if (empty($blockOfficers)) {
                    $blockOfficers[] = [
                        'officer_no' => 1,
                        'officer_name' => '-',
                        'officer_rank' => '-',
                        'officer_nrp' => '-',
                        'officer_position' => '-',
                        'officer_role' => 'Pelaksana',
                    ];
                }
                $templateProcessor->cloneRowAndSetValues('officer_no', $blockOfficers);
            }

            $filename = 'generate/'.Str::uuid().'_S-23_Surat_Perintah_Pembantaran_Penahanan.docx';
            $templateProcessor->saveAs(public_path($filename));

            return response()->download(public_path($filename))->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat membuat file dokumen (Template Error): '.$e->getMessage());
        }
    }

    public function show($id)
    {
        return $this->download($id);
    }
}
