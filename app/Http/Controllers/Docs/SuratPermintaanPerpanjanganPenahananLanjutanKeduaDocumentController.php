<?php

namespace App\Http\Controllers\Docs;

use App\Http\Controllers\Controller;
use App\Models\Accident;
use App\Models\Doc\SuratKetetapanTentangPenetapanTersangkaDocument\SuratKetetapanTentangPenetapanTersangkaDocument;
use App\Models\Doc\SuratPemberitahuanDimulainyaPenyidikanDocument\SuratPemberitahuanDimulainyaPenyidikanDocument;
use App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument;
use App\Models\Doc\SuratPermintaanPerpanjanganPenahananLanjutanDocument\SuratPermintaanPerpanjanganPenahananLanjutanDocument;
use App\Models\Doc\SuratPermintaanPerpanjanganPenahananLanjutanKeduaDocument\SuratPermintaanPerpanjanganPenahananLanjutanKeduaDocument;
use App\Models\Doc\SuratPermintaanPerpanjanganPenahananLanjutanKeduaDocument\SuratPermintaanPerpanjanganPenahananLanjutanKeduaDocumentLaw;
use App\Models\Lib\CrimeClass;
use App\Models\Lib\CrimeConstitution;
use App\Models\Lib\CrimeType;
use App\Models\Lib\Prison;
use App\Models\Lib\Prosecutor;
use App\Models\Officer;
use App\Models\Polres;
use App\Models\Suspect;
use App\Services\Doc\DocService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\TemplateProcessor;
use Webpatser\Uuid\Uuid;

class SuratPermintaanPerpanjanganPenahananLanjutanKeduaDocumentController extends Controller
{
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
        $accident = Accident::with(['police', 'polres', 'polres.polda', 'polres.prosecutor'])->where('id', $accidentId)->first();

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
        $selectedSuspectIds = $suspects->pluck('id')->toArray();

        $prosecutors = Prosecutor::where('is_active', true)->orderBy('name')->get();
        if ($prosecutors->isEmpty()) {
            $prosecutors = Prosecutor::all();
        }

        // Cari dokumen pendahulu SPDP
        $spdpDocument = SuratPemberitahuanDimulainyaPenyidikanDocument::where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->latest()
            ->first();

        // Cari dokumen pendahulu SKET Penetapan Tersangka
        $sketTersangkaDocument = SuratKetetapanTentangPenetapanTersangkaDocument::with(['suspect'])
            ->where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->latest()
            ->first();

        // Cari dokumen pendahulu Sprint Sidik (1.f)
        $sprintSidikDocument = SuratPerintahPenyidikanDocument::with([
            'suratPerintahPenyidikanDocumentLaws.crimeConstitution',
            'suratPerintahPenyidikanDocumentLaws.crimeType',
            'suratPerintahPenyidikanDocumentLaws.crimeClass',
        ])
            ->where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->latest()
            ->first();

        // Cari dokumen pendahulu S-22 (Perpanjangan Pertama 30 Hari) jika ada
        $firstExtensionDoc = SuratPermintaanPerpanjanganPenahananLanjutanDocument::with(['laws.crimeType', 'laws.crimeClass', 'laws.crimeConstitution'])
            ->where('accident_id', $accidentId)
            ->latest()
            ->first();

        // Default Nilai SPDP
        $defaultNomorSpdp = $firstExtensionDoc->nomor_spdp ?? ($spdpDocument->document_number ?? null);
        $defaultTanggalSpdp = $firstExtensionDoc->tanggal_spdp ?? ($spdpDocument && $spdpDocument->document_date ? Carbon::parse($spdpDocument->document_date)->format('Y-m-d') : null);

        // Default Kejaksaan & Pengadilan Negeri
        $defaultKejaksaanId = $firstExtensionDoc->kejaksaan_id ?? ($spdpDocument->prosecutor_id ?? null);
        $defaultNamaPengadilanNegeri = $firstExtensionDoc->nama_pengadilan_negeri ?? null;
        if (empty($defaultNamaPengadilanNegeri)) {
            if ($defaultKejaksaanId) {
                $selectedPros = $prosecutors->where('id', $defaultKejaksaanId)->first();
                if ($selectedPros) {
                    $defaultNamaPengadilanNegeri = preg_replace('/kejaksaan/i', 'PENGADILAN', $selectedPros->name);
                }
            }
            if (! $defaultNamaPengadilanNegeri && $accident->polres) {
                $polresDistrict = $accident->polres->polres_district ?? $accident->polres->name ?? '';
                $cleanedDistrict = preg_replace('/^(POLRES\s+|KEPOLISIAN\s+RESOR\s+)/i', '', trim($polresDistrict));
                $defaultNamaPengadilanNegeri = 'PENGADILAN NEGERI ' . strtoupper($cleanedDistrict);
            }
        }

        // Default SKET Tersangka
        $defaultNomorSketTersangka = $firstExtensionDoc->nomor_sket_tersangka ?? ($sketTersangkaDocument->document_number ?? null);
        $defaultTanggalSketTersangka = $firstExtensionDoc->tanggal_sket_tersangka ?? ($sketTersangkaDocument && $sketTersangkaDocument->document_date ? Carbon::parse($sketTersangkaDocument->document_date)->format('Y-m-d') : null);

        // Default Sprint Sidik (1.f)
        $defaultNomorSprintSidik = $sprintSidikDocument ? $sprintSidikDocument->document_number : null;
        $defaultTanggalSprintSidik = $sprintSidikDocument && $sprintSidikDocument->document_date ? Carbon::parse($sprintSidikDocument->document_date)->format('Y-m-d') : null;

        // Default Dokumen Penahanan S-17 & Kejaksaan
        $defaultNomorSprintPenahanan = $firstExtensionDoc->nomor_surat_perintah_penahanan ?? null;
        $defaultTanggalSprintPenahanan = $firstExtensionDoc->tanggal_surat_perintah_penahanan ?? null;
        $defaultNomorPerpanjanganKejaksaan = $firstExtensionDoc->nomor_surat_perpanjangan_kejaksaan ?? null;
        $defaultTanggalPerpanjanganKejaksaan = $firstExtensionDoc->tanggal_surat_perpanjangan_kejaksaan ?? null;
        $defaultNomorSprintPerpanjanganJpu = $firstExtensionDoc->nomor_surat_perintah_perpanjangan_penahanan ?? null;
        $defaultTanggalSprintPerpanjanganJpu = $firstExtensionDoc->tanggal_surat_perintah_perpanjangan_penahanan ?? null;

        // Default KPN Pertama (1.l & 1.m)
        $defaultNomorSketKpn1 = null;
        $defaultTanggalSketKpn1 = null;
        $defaultNomorSprintKpn1 = null;
        $defaultTanggalSprintKpn1 = null;

        // Default Batas Akhir PN (Poin 3) & Mulai Perpanjangan Kedua
        $defaultPengadilanNegeriAkhirTanggal = $firstExtensionDoc->tanggal_akhir_perpanjangan_penahanan ?? null;
        $defaultTanggalMulai = $defaultPengadilanNegeriAkhirTanggal ? Carbon::parse($defaultPengadilanNegeriAkhirTanggal)->addDay()->format('Y-m-d') : null;
        $defaultTanggalAkhir = $defaultTanggalMulai ? Carbon::parse($defaultTanggalMulai)->addDays(29)->format('Y-m-d') : null;

        // Default Rutan
        $defaultRutanName = $firstExtensionDoc->rutan_name ?? ('Rutan '.($accident->polres->full_name ?? ''));
        $defaultPrisonId = $firstExtensionDoc->prison_id ?? null;

        // Default Pasal Diduga & Dugaan Tindak Pidana
        $defaultPasalDiduga = $firstExtensionDoc->pasal_diduga ?? null;
        if (empty($defaultPasalDiduga) && $sprintSidikDocument && $sprintSidikDocument->suratPerintahPenyidikanDocumentLaws) {
            $pasalList = $sprintSidikDocument->suratPerintahPenyidikanDocumentLaws
                ->map(function ($law) {
                    return trim(($law->crimeConstitution->name ?? '').' '.($law->constitution_chapter ?? ''));
                })
                ->filter()
                ->toArray();
            $defaultPasalDiduga = implode('; ', $pasalList);
        }
        if (empty($defaultPasalDiduga)) {
            $defaultPasalDiduga = 'Pasal 310 ayat (4) Undang-Undang Nomor 22 Tahun 2009 tentang Lalu Lintas dan Angkutan Jalan';
        }

        $defaultDugaanTindakPidana = $firstExtensionDoc->dugaan_tindak_pidana ?? 'kecelakaan lalu lintas yang mengakibatkan orang lain meninggal dunia dan/atau luka berat dan/atau kerusakan kendaraan';
        $defaultAlasan = $firstExtensionDoc->alasan_perpanjangan ?? 'Pemeriksaan terhadap tersangka dan saksi-saksi tambahan belum selesai serta masih memerlukan kelengkapan berkas perkara.';

        // Master Data untuk Undang-Undang yang Dikenakan
        $crimeTypes = CrimeType::active()->orderBy('sort')->get();
        $crimeClasses = CrimeClass::active()->orderBy('sort')->get();
        $crimeConstitutions = CrimeConstitution::active()->orderBy('sort')->get();

        // Initial Laws
        $initialMainLaws = [];
        $initialAdditionalLaws = [];

        if ($firstExtensionDoc && $firstExtensionDoc->laws && $firstExtensionDoc->laws->isNotEmpty()) {
            foreach ($firstExtensionDoc->laws as $law) {
                if ($law->flag === 'MAIN') {
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
        } elseif ($sprintSidikDocument && $sprintSidikDocument->suratPerintahPenyidikanDocumentLaws && $sprintSidikDocument->suratPerintahPenyidikanDocumentLaws->isNotEmpty()) {
            foreach ($sprintSidikDocument->suratPerintahPenyidikanDocumentLaws as $law) {
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
                } elseif ($law->flag === 'ADDITIONAL' || $law->flag === 'ADDT' || !empty($law->constitution)) {
                    $initialAdditionalLaws[] = [
                        'constitution' => $law->constitution ?? '',
                    ];
                }
            }
        }

        if (empty($initialMainLaws)) {
            $defaultConstitution = $crimeConstitutions->firstWhere('name', 'Undang-Undang Nomor 22 Tahun 2009');
            $defaultType = $crimeTypes->firstWhere('id', $defaultConstitution->crime_type_id ?? null);
            $defaultClass = $crimeClasses->firstWhere('id', $defaultType->crime_class_id ?? null);

            $initialMainLaws[] = [
                'crime_type_id' => $defaultType->id ?? ($crimeTypes->first()->id ?? 1),
                'crime_type_name' => $defaultType->name ?? ($crimeTypes->first()->name ?? 'Kejahatan'),
                'crime_class_id' => $defaultClass->id ?? ($crimeClasses->first()->id ?? 1),
                'crime_class_name' => $defaultClass->name ?? ($crimeClasses->first()->name ?? 'Kejahatan Lalu Lintas'),
                'crime_constitution_id' => $defaultConstitution->id ?? ($crimeConstitutions->first()->id ?? 1),
                'crime_constitution_name' => $defaultConstitution->name ?? ($crimeConstitutions->first()->name ?? 'Undang-Undang Nomor 22 Tahun 2009'),
                'constitution_chapter' => 'Pasal 310 ayat (4)',
            ];
        }

        $viewData = [
            'authorizedSignatories' => $authorizedSignatories,
            'memberOfficers' => $memberOfficers,
            'accidentId' => $accidentId,
            'accident' => $accident,
            'resortPoliceId' => $accident->polres_id,
            'suspects' => $suspects,
            'prosecutors' => $prosecutors,
            'spdpDocument' => $spdpDocument,
            'sketTersangkaDocument' => $sketTersangkaDocument,
            'sprintSidikDocument' => $sprintSidikDocument,
            'firstExtensionDoc' => $firstExtensionDoc,
            'crimeTypes' => $crimeTypes,
            'crimeClasses' => $crimeClasses,
            'crimeConstitutions' => $crimeConstitutions,
            'initialMainLaws' => $initialMainLaws,
            'initialAdditionalLaws' => $initialAdditionalLaws,
            'defaultNomorSpdp' => $defaultNomorSpdp,
            'defaultTanggalSpdp' => $defaultTanggalSpdp,
            'defaultKejaksaanId' => $defaultKejaksaanId,
            'defaultNamaPengadilanNegeri' => $defaultNamaPengadilanNegeri,
            'defaultNomorSketTersangka' => $defaultNomorSketTersangka,
            'defaultTanggalSketTersangka' => $defaultTanggalSketTersangka,
            'defaultNomorSprintSidik' => $defaultNomorSprintSidik,
            'defaultTanggalSprintSidik' => $defaultTanggalSprintSidik,
            'defaultNomorSprintPenahanan' => $defaultNomorSprintPenahanan,
            'defaultTanggalSprintPenahanan' => $defaultTanggalSprintPenahanan,
            'defaultNomorPerpanjanganKejaksaan' => $defaultNomorPerpanjanganKejaksaan,
            'defaultTanggalPerpanjanganKejaksaan' => $defaultTanggalPerpanjanganKejaksaan,
            'defaultNomorSprintPerpanjanganJpu' => $defaultNomorSprintPerpanjanganJpu,
            'defaultTanggalSprintPerpanjanganJpu' => $defaultTanggalSprintPerpanjanganJpu,
            'defaultNomorSketKpn1' => $defaultNomorSketKpn1,
            'defaultTanggalSketKpn1' => $defaultTanggalSketKpn1,
            'defaultNomorSprintKpn1' => $defaultNomorSprintKpn1,
            'defaultTanggalSprintKpn1' => $defaultTanggalSprintKpn1,
            'defaultPengadilanNegeriAkhirTanggal' => $defaultPengadilanNegeriAkhirTanggal,
            'defaultTanggalMulai' => $defaultTanggalMulai,
            'defaultTanggalAkhir' => $defaultTanggalAkhir,
            'defaultRutanName' => $defaultRutanName,
            'defaultPrisonId' => $defaultPrisonId,
            'defaultPasalDiduga' => $defaultPasalDiduga,
            'defaultDugaanTindakPidana' => $defaultDugaanTindakPidana,
            'defaultAlasan' => $defaultAlasan,
            'selectedSuspectIds' => $selectedSuspectIds,
            'prisonsGrouped' => Prison::active()->orderBy('province')->orderBy('name')->get()->groupBy('province'),
        ];

        return view('docs.surat-permintaan-perpanjangan-penahanan-lanjutan-kedua-document.create', $viewData);
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

        // Resolusi satker penerbit & tempat penahanan otomatis
        $satkerCode = $accident->police->satker_code ?? $accident->polres->satker_code ?? $accident->polres->code ?? '';
        $kodeSatkerPenerbitSpdp = $request->kode_satker_penerbit_spdp ?: $satkerCode;
        $kodeSatkerTempatPenahanan = $request->kode_satker_tempat_penahanan ?: $satkerCode;

        // Resolusi Tempat Penahanan / Rutan
        $prisonId = null;
        if ($request->filled('prison_id') && is_numeric($request->prison_id)) {
            $prison = Prison::find($request->prison_id);
            if ($prison) {
                $prisonId = $prison->id;
                $rutanName = $prison->name;
                if (!empty($prison->spptti_id)) {
                    $kodeSatkerTempatPenahanan = $prison->spptti_id;
                }
            }
        }
        if (empty($rutanName)) {
            $rutanName = $request->rutan_name ?: ('Rutan '.($accident->polres->full_name ?? ''));
        }
        $lampiranSurat = $request->lampiran_surat ?: null;

        $rawTembusan = $request->tembusan ?? $request->carbonCopies ?? [];
        $filteredTembusan = [];
        if (is_array($rawTembusan)) {
            foreach ($rawTembusan as $t) {
                if (trim($t)) {
                    $filteredTembusan[] = trim($t);
                }
            }
        }

        DB::beginTransaction();
        try {
            $document = SuratPermintaanPerpanjanganPenahananLanjutanKeduaDocument::create([
                'accident_id' => $accidentId,
                'document_category_id' => '0604',
                'status_id' => '2', // Dokumen Dibuat
                'nomor_surat' => $request->nomor_surat,
                'tanggal_surat' => $request->tanggal_surat ? Carbon::parse($request->tanggal_surat)->format('Y-m-d') : null,
                'klasifikasi_surat_id' => $request->klasifikasi_surat_id,
                'lampiran_surat' => $lampiranSurat,
                'kejaksaan_id' => $request->kejaksaan_id,
                'nama_pengadilan_negeri' => $request->nama_pengadilan_negeri,

                // Rujukan
                'nomor_surat_perintah_penyidikan' => $request->nomor_surat_perintah_penyidikan,
                'tanggal_surat_perintah_penyidikan' => $request->tanggal_surat_perintah_penyidikan ? Carbon::parse($request->tanggal_surat_perintah_penyidikan)->format('Y-m-d') : null,
                'nomor_spdp' => $request->nomor_spdp,
                'tanggal_spdp' => $request->tanggal_spdp ? Carbon::parse($request->tanggal_spdp)->format('Y-m-d') : null,
                'kode_satker_penerbit_spdp' => $kodeSatkerPenerbitSpdp,
                'nomor_sket_tersangka' => $request->nomor_sket_tersangka,
                'tanggal_sket_tersangka' => $request->tanggal_sket_tersangka ? Carbon::parse($request->tanggal_sket_tersangka)->format('Y-m-d') : null,
                'nomor_surat_perintah_penahanan' => $request->nomor_surat_perintah_penahanan,
                'tanggal_surat_perintah_penahanan' => $request->tanggal_surat_perintah_penahanan ? Carbon::parse($request->tanggal_surat_perintah_penahanan)->format('Y-m-d') : null,
                'nama_kejaksaan_surat_perpanjangan' => $request->nama_kejaksaan_surat_perpanjangan,
                'nomor_surat_perpanjangan_kejaksaan' => $request->nomor_surat_perpanjangan_kejaksaan,
                'tanggal_surat_perpanjangan_kejaksaan' => $request->tanggal_surat_perpanjangan_kejaksaan ? Carbon::parse($request->tanggal_surat_perpanjangan_kejaksaan)->format('Y-m-d') : null,
                'nomor_surat_perintah_perpanjangan_penahanan' => $request->nomor_surat_perintah_perpanjangan_penahanan,
                'tanggal_surat_perintah_perpanjangan_penahanan' => $request->tanggal_surat_perintah_perpanjangan_penahanan ? Carbon::parse($request->tanggal_surat_perintah_perpanjangan_penahanan)->format('Y-m-d') : null,
                'nomor_sket_perpanjangan_kpn_pertama' => $request->nomor_sket_perpanjangan_kpn_pertama,
                'tanggal_sket_perpanjangan_kpn_pertama' => $request->tanggal_sket_perpanjangan_kpn_pertama ? Carbon::parse($request->tanggal_sket_perpanjangan_kpn_pertama)->format('Y-m-d') : null,
                'nomor_surat_perintah_perpanjangan_penahanan_kpn_pertama' => $request->nomor_surat_perintah_perpanjangan_penahanan_kpn_pertama,
                'tanggal_surat_perintah_perpanjangan_penahanan_kpn_pertama' => $request->tanggal_surat_perintah_perpanjangan_penahanan_kpn_pertama ? Carbon::parse($request->tanggal_surat_perintah_perpanjangan_penahanan_kpn_pertama)->format('Y-m-d') : null,

                // Poin 3
                'pengadilan_negeri_akhir_tanggal' => $request->pengadilan_negeri_akhir_tanggal ? Carbon::parse($request->pengadilan_negeri_akhir_tanggal)->format('Y-m-d') : null,
                'kejaksaan_akhir_tanggal' => $request->kejaksaan_akhir_tanggal ? Carbon::parse($request->kejaksaan_akhir_tanggal)->format('Y-m-d') : null,
                'alasan_perpanjangan' => $request->alasan_perpanjangan,
                'pasal_diduga' => $request->pasal_diduga,
                'dugaan_tindak_pidana' => $request->dugaan_tindak_pidana,
                'waktu_penahanan_hari' => $request->waktu_penahanan_hari ?: 30,
                'prison_id' => $prisonId,
                'rutan_name' => $rutanName,
                'kode_satker_tempat_penahanan' => $kodeSatkerTempatPenahanan,
                'tanggal_mulai_perpanjangan_penahanan' => $request->tanggal_mulai_perpanjangan_penahanan ? Carbon::parse($request->tanggal_mulai_perpanjangan_penahanan)->format('Y-m-d') : null,
                'tanggal_akhir_perpanjangan_penahanan' => $request->tanggal_akhir_perpanjangan_penahanan ? Carbon::parse($request->tanggal_akhir_perpanjangan_penahanan)->format('Y-m-d') : null,
                'contact_officer_id' => $request->contact_officer_id,
                'tembusan' => !empty($filteredTembusan) ? $filteredTembusan : null,
            ]);

            // Simpan Penandatangan
            $signatoryId = htmlspecialchars($request->signatory);
            if ($signatoryId) {
                $signatory = Officer::where('id', $signatoryId)->first();
                if ($signatory) {
                    $document->officers()->create([
                        'officer_id' => $signatory->id,
                        'register_number' => $signatory->register_number,
                        'first_title' => $signatory->first_title,
                        'first_name' => $signatory->first_name,
                        'last_name' => $signatory->last_name,
                        'last_title' => $signatory->last_title,
                        'rank_id' => $signatory->rank_id,
                        'position_id' => $signatory->position_id,
                        'rank' => $signatory->rank,
                        'position' => $signatory->position,
                        'role' => $signatory->role,
                        'phone_number' => $signatory->phone_number,
                        'email' => $signatory->email,
                        'police_id' => $signatory->police_id,
                        'type' => 'penandatangan',
                        'status' => $signatory->status,
                        'class' => $signatory->class,
                        'flag' => $signatory->flag,
                    ]);
                }
            }

            // Simpan Tersangka
            $suspectIds = $request->suspects ?? [];
            if (!empty($suspectIds) && is_array($suspectIds)) {
                $document->suspects()->sync($suspectIds);
            }

            // Simpan Undang-Undang
            $lawCrimeTypeIds = $request->lawCrimeTypeIds ?? [];
            $lawCrimeClassIds = $request->lawCrimeClassIds ?? [];
            $lawCrimeConstitutionIds = $request->lawCrimeConstitutionIds ?? [];
            $lawCrimeConstitutionChapters = $request->lawCrimeConstitutionChapters ?? [];

            if (is_array($lawCrimeTypeIds) && count($lawCrimeTypeIds) > 0) {
                foreach ($lawCrimeTypeIds as $idx => $ctId) {
                    if ($ctId) {
                        $document->laws()->create([
                            'crime_type_id' => $ctId,
                            'crime_class_id' => $lawCrimeClassIds[$idx] ?? null,
                            'crime_constitution_id' => $lawCrimeConstitutionIds[$idx] ?? null,
                            'constitution_chapter' => $lawCrimeConstitutionChapters[$idx] ?? null,
                            'flag' => 'MAIN',
                        ]);
                    }
                }
            }

            $lawAdditionalNames = $request->lawAdditionalNames ?? [];
            if (is_array($lawAdditionalNames) && count($lawAdditionalNames) > 0) {
                foreach ($lawAdditionalNames as $addLaw) {
                    if (trim($addLaw)) {
                        $document->laws()->create([
                            'constitution' => trim($addLaw),
                            'flag' => 'ADDITIONAL',
                        ]);
                    }
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data: '.$e->getMessage());
        }

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])->with('success', 'Dokumen Surat Permintaan Perpanjangan Penahanan Ke Ketua Pengadilan Negeri (Kedua 30 Hari) berhasil disimpan.');
    }

    public function edit($id)
    {
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $accident = Accident::with(['police', 'polres', 'polres.polda', 'polres.prosecutor'])->where('id', $accidentId)->first();

        if (! $accident) {
            return redirect()->back()->with('error', 'Data Laporan Polisi tidak ditemukan.');
        }

        $document = SuratPermintaanPerpanjanganPenahananLanjutanKeduaDocument::with([
            'officers',
            'suspects',
            'laws.crimeType',
            'laws.crimeClass',
            'laws.crimeConstitution',
        ])->where('id', $id)->first();

        if (! $document) {
            return redirect()->back()->with('error', 'Data Dokumen S-22 tidak ditemukan.');
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
        $prosecutors = Prosecutor::where('is_active', true)->orderBy('name')->get();
        if ($prosecutors->isEmpty()) {
            $prosecutors = Prosecutor::all();
        }

        // Cari dokumen pendahulu SPDP & SKET Tersangka
        $spdpDocument = SuratPemberitahuanDimulainyaPenyidikanDocument::where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->latest()
            ->first();

        $sketTersangkaDocument = SuratKetetapanTentangPenetapanTersangkaDocument::with(['suspect'])
            ->where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->latest()
            ->first();

        $sprintSidikDocument = SuratPerintahPenyidikanDocument::where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->latest()
            ->first();

        $defaultNamaPengadilanNegeri = $document->nama_pengadilan_negeri;
        $defaultPasalDiduga = $document->pasal_diduga ?: 'Pasal 310 ayat (4) Undang-Undang Nomor 22 Tahun 2009 tentang Lalu Lintas dan Angkutan Jalan';
        $defaultDugaanTindakPidana = $document->dugaan_tindak_pidana ?: 'kecelakaan lalu lintas yang mengakibatkan orang lain meninggal dunia dan/atau luka berat dan/atau kerusakan kendaraan';

        // Master Data untuk Undang-Undang yang Dikenakan
        $crimeTypes = CrimeType::active()->orderBy('sort')->get();
        $crimeClasses = CrimeClass::active()->orderBy('sort')->get();
        $crimeConstitutions = CrimeConstitution::active()->orderBy('sort')->get();

        $existingMainLaws = [];
        $existingAdditionalLaws = [];
        if ($document->laws) {
            foreach ($document->laws as $law) {
                if ($law->flag === 'MAIN') {
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

        $viewData = [
            'document' => $document,
            'authorizedSignatories' => $authorizedSignatories,
            'memberOfficers' => $memberOfficers,
            'accidentId' => $accidentId,
            'accident' => $accident,
            'resortPoliceId' => $accident->polres_id,
            'suspects' => $suspects,
            'prosecutors' => $prosecutors,
            'spdpDocument' => $spdpDocument,
            'sketTersangkaDocument' => $sketTersangkaDocument,
            'sprintSidikDocument' => $sprintSidikDocument,
            'crimeTypes' => $crimeTypes,
            'crimeClasses' => $crimeClasses,
            'crimeConstitutions' => $crimeConstitutions,
            'existingMainLaws' => $existingMainLaws,
            'existingAdditionalLaws' => $existingAdditionalLaws,
            'defaultNamaPengadilanNegeri' => $defaultNamaPengadilanNegeri,
            'defaultPasalDiduga' => $defaultPasalDiduga,
            'defaultDugaanTindakPidana' => $defaultDugaanTindakPidana,
            'defaultRutanName' => 'Rutan '.($accident->polres->full_name ?? ''),
            'prisonsGrouped' => Prison::active()->orderBy('province')->orderBy('name')->get()->groupBy('province'),
        ];

        return view('docs.surat-permintaan-perpanjangan-penahanan-lanjutan-kedua-document.edit', $viewData);
    }

    public function update(Request $request, $id)
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

        DB::beginTransaction();
        try {
            $document = SuratPermintaanPerpanjanganPenahananLanjutanKeduaDocument::where('id', $id)->first();
            if (! $document) {
                DB::rollback();

                return redirect()->back()->with('error', 'Data Dokumen S-22 tidak ditemukan.');
            }

            $satkerCode = $accident->police->satker_code ?? $accident->polres->satker_code ?? $accident->polres->code ?? '';
            $kodeSatkerPenerbitSpdp = $request->kode_satker_penerbit_spdp ?: ($document->kode_satker_penerbit_spdp ?: $satkerCode);
            $kodeSatkerTempatPenahanan = $request->kode_satker_tempat_penahanan ?: ($document->kode_satker_tempat_penahanan ?: $satkerCode);

            // Resolusi Tempat Penahanan / Rutan
            $prisonId = null;
            if ($request->filled('prison_id') && is_numeric($request->prison_id)) {
                $prison = Prison::find($request->prison_id);
                if ($prison) {
                    $prisonId = $prison->id;
                    $rutanName = $prison->name;
                    if (!empty($prison->spptti_id)) {
                        $kodeSatkerTempatPenahanan = $prison->spptti_id;
                    }
                }
            } elseif ($request->prison_id === 'polres') {
                $prisonId = null;
                $rutanName = 'Rutan '.($accident->polres->full_name ?? '');
                $kodeSatkerTempatPenahanan = $satkerCode;
            } else {
                $prisonId = $document->prison_id;
                $rutanName = $request->rutan_name ?: ($document->rutan_name ?: ('Rutan '.($accident->polres->full_name ?? '')));
            }
            $lampiranSurat = $request->has('lampiran_surat') && $request->lampiran_surat ? $request->lampiran_surat : ($document->lampiran_surat ?: null);

            $rawTembusan = $request->tembusan ?? $request->carbonCopies ?? [];
            $filteredTembusan = [];
            if (is_array($rawTembusan)) {
                foreach ($rawTembusan as $t) {
                    if (trim($t)) {
                        $filteredTembusan[] = trim($t);
                    }
                }
            }

            $document->update([
                'nomor_surat' => $request->nomor_surat,
                'tanggal_surat' => $request->tanggal_surat ? Carbon::parse($request->tanggal_surat)->format('Y-m-d') : null,
                'klasifikasi_surat_id' => $request->klasifikasi_surat_id,
                'lampiran_surat' => $lampiranSurat,
                'kejaksaan_id' => $request->kejaksaan_id ?: $document->kejaksaan_id,
                'nama_pengadilan_negeri' => $request->nama_pengadilan_negeri ?: $document->nama_pengadilan_negeri,

                // Rujukan
                'nomor_surat_perintah_penyidikan' => $request->nomor_surat_perintah_penyidikan,
                'tanggal_surat_perintah_penyidikan' => $request->tanggal_surat_perintah_penyidikan ? Carbon::parse($request->tanggal_surat_perintah_penyidikan)->format('Y-m-d') : null,
                'nomor_spdp' => $request->nomor_spdp,
                'tanggal_spdp' => $request->tanggal_spdp ? Carbon::parse($request->tanggal_spdp)->format('Y-m-d') : null,
                'kode_satker_penerbit_spdp' => $kodeSatkerPenerbitSpdp,
                'nomor_sket_tersangka' => $request->nomor_sket_tersangka,
                'tanggal_sket_tersangka' => $request->tanggal_sket_tersangka ? Carbon::parse($request->tanggal_sket_tersangka)->format('Y-m-d') : null,
                'nomor_surat_perintah_penahanan' => $request->nomor_surat_perintah_penahanan,
                'tanggal_surat_perintah_penahanan' => $request->tanggal_surat_perintah_penahanan ? Carbon::parse($request->tanggal_surat_perintah_penahanan)->format('Y-m-d') : null,
                'nama_kejaksaan_surat_perpanjangan' => $request->nama_kejaksaan_surat_perpanjangan,
                'nomor_surat_perpanjangan_kejaksaan' => $request->nomor_surat_perpanjangan_kejaksaan,
                'tanggal_surat_perpanjangan_kejaksaan' => $request->tanggal_surat_perpanjangan_kejaksaan ? Carbon::parse($request->tanggal_surat_perpanjangan_kejaksaan)->format('Y-m-d') : null,
                'nomor_surat_perintah_perpanjangan_penahanan' => $request->nomor_surat_perintah_perpanjangan_penahanan,
                'tanggal_surat_perintah_perpanjangan_penahanan' => $request->tanggal_surat_perintah_perpanjangan_penahanan ? Carbon::parse($request->tanggal_surat_perintah_perpanjangan_penahanan)->format('Y-m-d') : null,
                'nomor_sket_perpanjangan_kpn_pertama' => $request->nomor_sket_perpanjangan_kpn_pertama,
                'tanggal_sket_perpanjangan_kpn_pertama' => $request->tanggal_sket_perpanjangan_kpn_pertama ? Carbon::parse($request->tanggal_sket_perpanjangan_kpn_pertama)->format('Y-m-d') : null,
                'nomor_surat_perintah_perpanjangan_penahanan_kpn_pertama' => $request->nomor_surat_perintah_perpanjangan_penahanan_kpn_pertama,
                'tanggal_surat_perintah_perpanjangan_penahanan_kpn_pertama' => $request->tanggal_surat_perintah_perpanjangan_penahanan_kpn_pertama ? Carbon::parse($request->tanggal_surat_perintah_perpanjangan_penahanan_kpn_pertama)->format('Y-m-d') : null,

                // Poin 3
                'pengadilan_negeri_akhir_tanggal' => $request->pengadilan_negeri_akhir_tanggal ? Carbon::parse($request->pengadilan_negeri_akhir_tanggal)->format('Y-m-d') : null,
                'kejaksaan_akhir_tanggal' => $request->kejaksaan_akhir_tanggal ? Carbon::parse($request->kejaksaan_akhir_tanggal)->format('Y-m-d') : null,
                'alasan_perpanjangan' => $request->alasan_perpanjangan,
                'pasal_diduga' => $request->pasal_diduga,
                'dugaan_tindak_pidana' => $request->dugaan_tindak_pidana,
                'waktu_penahanan_hari' => $request->waktu_penahanan_hari,
                'prison_id' => $prisonId,
                'rutan_name' => $rutanName,
                'kode_satker_tempat_penahanan' => $kodeSatkerTempatPenahanan,
                'tanggal_mulai_perpanjangan_penahanan' => $request->tanggal_mulai_perpanjangan_penahanan ? Carbon::parse($request->tanggal_mulai_perpanjangan_penahanan)->format('Y-m-d') : null,
                'tanggal_akhir_perpanjangan_penahanan' => $request->tanggal_akhir_perpanjangan_penahanan ? Carbon::parse($request->tanggal_akhir_perpanjangan_penahanan)->format('Y-m-d') : null,
                'contact_officer_id' => $request->contact_officer_id,
                'tembusan' => !empty($filteredTembusan) ? $filteredTembusan : null,
            ]);

            $signatoryId = htmlspecialchars($request->signatory);
            if ($signatoryId) {
                $signatory = Officer::where('id', $signatoryId)->first();
                if ($signatory) {
                    $document->officers()->delete();
                    $document->officers()->create([
                        'officer_id' => $signatory->id,
                        'register_number' => $signatory->register_number,
                        'first_title' => $signatory->first_title,
                        'first_name' => $signatory->first_name,
                        'last_name' => $signatory->last_name,
                        'last_title' => $signatory->last_title,
                        'rank_id' => $signatory->rank_id,
                        'position_id' => $signatory->position_id,
                        'rank' => $signatory->rank,
                        'position' => $signatory->position,
                        'role' => $signatory->role,
                        'phone_number' => $signatory->phone_number,
                        'email' => $signatory->email,
                        'police_id' => $signatory->police_id,
                        'type' => 'penandatangan',
                        'status' => $signatory->status,
                        'class' => $signatory->class,
                        'flag' => $signatory->flag,
                    ]);
                }
            }

            $suspectIds = $request->suspects ?? [];
            if (is_array($suspectIds)) {
                $document->suspects()->sync($suspectIds);
            }

            $document->laws()->delete();

            $lawCrimeTypeIds = $request->lawCrimeTypeIds ?? [];
            $lawCrimeClassIds = $request->lawCrimeClassIds ?? [];
            $lawCrimeConstitutionIds = $request->lawCrimeConstitutionIds ?? [];
            $lawCrimeConstitutionChapters = $request->lawCrimeConstitutionChapters ?? [];

            if (is_array($lawCrimeTypeIds) && count($lawCrimeTypeIds) > 0) {
                foreach ($lawCrimeTypeIds as $idx => $ctId) {
                    if ($ctId) {
                        $document->laws()->create([
                            'crime_type_id' => $ctId,
                            'crime_class_id' => $lawCrimeClassIds[$idx] ?? null,
                            'crime_constitution_id' => $lawCrimeConstitutionIds[$idx] ?? null,
                            'constitution_chapter' => $lawCrimeConstitutionChapters[$idx] ?? null,
                            'flag' => 'MAIN',
                        ]);
                    }
                }
            }

            $lawAdditionalNames = $request->lawAdditionalNames ?? [];
            if (is_array($lawAdditionalNames) && count($lawAdditionalNames) > 0) {
                foreach ($lawAdditionalNames as $addLaw) {
                    if (trim($addLaw)) {
                        $document->laws()->create([
                            'constitution' => trim($addLaw),
                            'flag' => 'ADDITIONAL',
                        ]);
                    }
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui data: '.$e->getMessage());
        }

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])->with('success', 'Dokumen Surat Permintaan Perpanjangan Penahanan Ke Ketua Pengadilan Negeri (Kedua 30 Hari) berhasil diperbarui.');
    }

    public function delete($id)
    {
        $accidentId = htmlspecialchars(request()->query('accident_id'));

        DB::beginTransaction();
        try {
            $document = SuratPermintaanPerpanjanganPenahananLanjutanKeduaDocument::where('id', $id)->first();

            if (! $document) {
                DB::rollback();

                return redirect()->back()->with('error', 'Dokumen tidak ditemukan atau sudah dihapus sebelumnya.');
            }

            $document->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()->with('error', 'Terjadi kesalahan pada saat menghapus data: '.$e->getMessage());
        }

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])->with('success', 'Dokumen Surat Permintaan Perpanjangan Penahanan Ke Ketua Pengadilan Negeri (Kedua 30 Hari) berhasil dihapus.');
    }

    public function show($id)
    {
        return $this->edit($id);
    }

    public function download($id)
    {
        $document = SuratPermintaanPerpanjanganPenahananLanjutanKeduaDocument::with([
            'officers',
            'suspects.gender',
            'suspects.job',
            'suspects.religion',
            'contactOfficer.rank',
            'kejaksaan.regency',
            'laws.crimeConstitution',
            'laws.crimeType',
            'laws.crimeClass',
        ])->where('id', $id)->first();

        if (! $document) {
            return redirect()->back()->with('error', 'Gagal mendownload dokumen. Data Surat Perpanjangan Penahanan tidak ditemukan.');
        }

        $accidentId = htmlspecialchars(request()->query('accident_id') ?: (request()->input('accident_id') ?: $document->accident_id));
        $accident = Accident::with(['polres', 'polres.polda', 'polres.prosecutor'])->where('id', $accidentId)->first();

        if (! $accident) {
            return redirect()->back()->with('error', 'Gagal mendownload dokumen. Data Laporan Polisi (Accident) tidak ditemukan.');
        }

        $signatory = $document->officers->first();
        $primarySuspect = $document->suspects->first();
        $suspectNamesStr = $document->suspects->pluck('name')->implode(', ');

        try {
            $templateFile = public_path('word-template/surat_permintaan_perpanjangan_penahanan_kedua_30_hari.docx');

            if (!file_exists($templateFile)) {
                return redirect()->back()->with('error', 'Template Word surat_permintaan_perpanjangan_penahanan_kedua_30_hari.docx tidak ditemukan.');
            }

            $templateProcessor = new TemplateProcessor($templateFile);

            $resorPoliceFullName = (in_array($accident->polres->id ?? '', ['1114'])) ? 'DIREKTORAT LALU LINTAS' : 'RESOR '.strtoupper($accident->polres->full_name ?? '');
            $poldaFullName = $accident->polres->polda->name ?? ($accident->polres->polda->full_name ?? '');
            $resorPoliceAddress = ucwords(strtolower(($accident->polres->address ?? '').', '.($accident->polres->polres_district ?? '').', '.($accident->polres->polres_zipcode ?? '')));
            $documentLocation = 'S-22';
            $docDateFormatted = $document->tanggal_surat ? Carbon::parse($document->tanggal_surat)->locale('id')->translatedFormat('d F Y') : '-';
            $lpDateFormatted = $accident->report_date ? Carbon::parse($accident->report_date)->locale('id')->translatedFormat('d F Y') : ($accident->date ? Carbon::parse($accident->date)->locale('id')->translatedFormat('d F Y') : '-');

            // Lampiran: jika kosong/default, deteksi otomatis jumlah halaman (<Pages>) dari template docx
            $appendix = trim($document->lampiran_surat ?? '');
            $terbilangMap = [
                1 => 'satu', 2 => 'dua', 3 => 'tiga', 4 => 'empat', 5 => 'lima',
                6 => 'enam', 7 => 'tujuh', 8 => 'delapan', 9 => 'sembilan', 10 => 'sepuluh',
            ];

            if (empty($appendix) || $appendix === '-') {
                $pageCount = 2; // Default template S-22
                $templatePath = $templateFile;
                if (file_exists($templatePath)) {
                    $zip = new \ZipArchive();
                    if ($zip->open($templatePath) === true) {
                        $appXml = $zip->getFromName('docProps/app.xml');
                        if ($appXml && preg_match('/<Pages>(\d+)<\/Pages>/', $appXml, $matches)) {
                            $pageCount = (int)$matches[1];
                        }
                        $zip->close();
                    }
                }
                $terbilang = $terbilangMap[$pageCount] ?? (string)$pageCount;
                $appendix = "{$pageCount} ({$terbilang}) berkas";
            } elseif (is_numeric($appendix)) {
                $num = (int)$appendix;
                $terbilang = $terbilangMap[$num] ?? (string)$num;
                $appendix = "{$num} ({$terbilang}) berkas";
            }

            // 1. Kop & Header Surat
            $templateProcessor->setValue('daerahPoliceFullName', $poldaFullName);
            $templateProcessor->setValue('resorPoliceFullName', $resorPoliceFullName);
            $templateProcessor->setValue('resorPoliceAddress', $resorPoliceAddress);
            $templateProcessor->setValue('documentLocation', $documentLocation);
            $templateProcessor->setValue('documentDate', $docDateFormatted);
            $templateProcessor->setValue('documentNumber', $document->nomor_surat ?? '-');
            $templateProcessor->setValue('documentClassificationName', $document->klasifikasi_surat_id ?? 'Biasa');
            $templateProcessor->setValue('appendix', $appendix);

            // 2. Tujuan Surat (Ketua Pengadilan Negeri)
            $targetPN = $document->nama_pengadilan_negeri ?: ('Pengadilan Negeri '.ucwords(strtolower($accident->polres->name ?? $accident->polres->polres_district ?? '')));

            $prosecutorRegency = '';
            if ($document->kejaksaan && $document->kejaksaan->regency) {
                $prosecutorRegency = $document->kejaksaan->regency->name ?? '';
            }
            if (empty($prosecutorRegency) && $accident->polres) {
                $prosecutorRegency = $accident->polres->polres_regency ?? ($accident->polres->name ?? '');
            }
            $cleanRegency = preg_replace('/^(KABUPATEN|KOTA)\s+/i', '', trim($prosecutorRegency));
            $targetLocation = !empty($cleanRegency) ? strtoupper($cleanRegency) : strtoupper($prosecutorRegency);

            $templateProcessor->setValue('prosecutorName', strtoupper($targetPN));
            $templateProcessor->setValue('prosecutorLocation', $targetLocation ?: 'DI TEMPAT');

            // 3. Rujukan Dokumen Terkait & Undang-Undang
            $mainLawTitles = [];
            $additionalLawTitles = [];
            if ($document->laws && $document->laws->count() > 0) {
                foreach ($document->laws as $law) {
                    if ($law->flag === 'MAIN') {
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
                $mainLawTitles[] = 'Undang-undang Republik Indonesia Nomor 22 Tahun 2009 tentang Lalu Lintas dan Angkutan Jalan';
            }
            $allLaws = array_merge($mainLawTitles, $additionalLawTitles);
            $rujukanUndangUndangText = implode(' dan ', $allLaws);

            // Poin 1.a s.d 1.e
            $templateProcessor->setValue('rujukan_undang_undang', $rujukanUndangUndangText);
            $templateProcessor->setValue('lpNumber', $accident->no_lp ?? '-');
            $templateProcessor->setValue('lpDate', $lpDateFormatted);

            // Poin 1.f (Sprint Sidik)
            $templateProcessor->setValue('sprintSidikNumber', $document->nomor_surat_perintah_penyidikan ?? '-');
            $templateProcessor->setValue('sprintSidikDate', $document->tanggal_surat_perintah_penyidikan ? Carbon::parse($document->tanggal_surat_perintah_penyidikan)->locale('id')->translatedFormat('d F Y') : '-');

            // Poin 1.g (SPDP)
            $templateProcessor->setValue('spdpNumber', $document->nomor_spdp ?? '-');
            $templateProcessor->setValue('spdpDate', $document->tanggal_spdp ? Carbon::parse($document->tanggal_spdp)->locale('id')->translatedFormat('d F Y') : '-');

            // Poin 1.h (SKET Tersangka)
            $templateProcessor->setValue('sketNumber', $document->nomor_sket_tersangka ?? '-');
            $templateProcessor->setValue('sketDate', $document->tanggal_sket_tersangka ? Carbon::parse($document->tanggal_sket_tersangka)->locale('id')->translatedFormat('d F Y') : '-');
            $templateProcessor->setValue('sketSuspectName', $suspectNamesStr ?: '-');

            // Poin 1.i (Sprint Penahanan Penyidik S-17)
            $templateProcessor->setValue('sphNumber', $document->nomor_surat_perintah_penahanan ?? '-');
            $templateProcessor->setValue('sphDate', $document->tanggal_surat_perintah_penahanan ? Carbon::parse($document->tanggal_surat_perintah_penahanan)->locale('id')->translatedFormat('d F Y') : '-');
            $templateProcessor->setValue('sphSuspectName', $suspectNamesStr ?: '-');

            // Poin 1.j (Surat Perpanjangan Penahanan dari Kejaksaan)
            $namaKejaksaan = $document->nama_kejaksaan_surat_perpanjangan ?: ($document->kejaksaan->name ?? ($accident->polres->prosecutor->first()->name ?? '-'));
            $templateProcessor->setValue('nama_kejaksaan', $namaKejaksaan);
            $templateProcessor->setValue('kejaksaanExtensionNumber', $document->nomor_surat_perpanjangan_kejaksaan ?? '-');
            $templateProcessor->setValue('kejaksaanExtensionDate', $document->tanggal_surat_perpanjangan_kejaksaan ? Carbon::parse($document->tanggal_surat_perpanjangan_kejaksaan)->locale('id')->translatedFormat('d F Y') : '-');
            $templateProcessor->setValue('kejaksaanExtensionSuspectName', $suspectNamesStr ?: '-');

            // Poin 1.k (Sprint Perpanjangan Penahanan JPU)
            $templateProcessor->setValue('perpanjanganOrderNumber', $document->nomor_surat_perintah_perpanjangan_penahanan ?? '-');
            $templateProcessor->setValue('perpanjanganOrderDate', $document->tanggal_surat_perintah_perpanjangan_penahanan ? Carbon::parse($document->tanggal_surat_perintah_perpanjangan_penahanan)->locale('id')->translatedFormat('d F Y') : '-');
            $templateProcessor->setValue('perpanjanganOrderSuspectName', $suspectNamesStr ?: '-');

            // Poin 1.l (S.Ket Perpanjangan KPN Pertama / KPN1)
            $templateProcessor->setValue('nama_pengadilan_negeri_kpn1', strtoupper($document->nama_pengadilan_negeri ?: $targetPN));
            $templateProcessor->setValue('sketKpn1Number', $document->nomor_sket_perpanjangan_kpn_pertama ?? '-');
            $templateProcessor->setValue('sketKpn1Date', $document->tanggal_sket_perpanjangan_kpn_pertama ? Carbon::parse($document->tanggal_sket_perpanjangan_kpn_pertama)->locale('id')->translatedFormat('d F Y') : '-');
            $templateProcessor->setValue('sketKpn1SuspectName', $suspectNamesStr ?: '-');

            // Poin 1.m (Sprint Perpanjangan Penahanan KPN Pertama / KPN1)
            $templateProcessor->setValue('sprintKpn1Number', $document->nomor_surat_perintah_perpanjangan_penahanan_kpn_pertama ?? '-');
            $templateProcessor->setValue('sprintKpn1Date', $document->tanggal_surat_perintah_perpanjangan_penahanan_kpn_pertama ? Carbon::parse($document->tanggal_surat_perintah_perpanjangan_penahanan_kpn_pertama)->locale('id')->translatedFormat('d F Y') : '-');
            $templateProcessor->setValue('sprintKpn1SuspectName', $suspectNamesStr ?: '-');

            // 4. Perkara & Identitas Tersangka (Poin 2)
            $pasalDiduga = $document->pasal_diduga;
            if (empty($pasalDiduga) && $document->laws && $document->laws->count() > 0) {
                $pasalChapters = $document->laws->pluck('constitution_chapter')->filter()->implode(', ');
                if (!empty($pasalChapters)) {
                    $pasalDiduga = $pasalChapters;
                }
            }

            $tglAkhirPn = $document->pengadilan_negeri_akhir_tanggal
                ? Carbon::parse($document->pengadilan_negeri_akhir_tanggal)->locale('id')->translatedFormat('d F Y')
                : ($document->kejaksaan_akhir_tanggal ? Carbon::parse($document->kejaksaan_akhir_tanggal)->locale('id')->translatedFormat('d F Y') : '-');

            $templateProcessor->setValue('satker_penyidik', 'Satuan Lalu Lintas '.($accident->polres->full_name ?? ''));
            $templateProcessor->setValue('dugaan_tindak_pidana', $document->dugaan_tindak_pidana ?: 'kecelakaan lalu lintas');
            $templateProcessor->setValue('pasal_diduga', $pasalDiduga ?: 'Pasal 310 ayat (4) UU No. 22 Tahun 2009');
            $templateProcessor->setValue('tempat_kejadian', $accident->place ?: ($accident->road_name ?? '-'));

            $accidentDateFormatted = '-';
            if (!empty($accident->accident_date)) {
                $accidentDateFormatted = Carbon::parse($accident->accident_date)->locale('id')->translatedFormat('l, d F Y');
                if (!empty($accident->accident_time)) {
                    $accidentTimeFormatted = Carbon::parse($accident->accident_time)->format('H.i');
                    $accidentDateFormatted .= ' sekira pukul ' . $accidentTimeFormatted . ' WIB';
                }
            } elseif (!empty($accident->report_date)) {
                $accidentDateFormatted = Carbon::parse($accident->report_date)->locale('id')->translatedFormat('l, d F Y');
            }
            $templateProcessor->setValue('kurun_waktu', $accidentDateFormatted);
            $templateProcessor->setValue('detentionEndDate', $tglAkhirPn);

            // Identitas Tersangka
            $templateProcessor->setValue('suspectName', $primarySuspect->name ?? '-');
            $templateProcessor->setValue('suspectIdentityNumber', $primarySuspect->identity_number ?? '-');
            $templateProcessor->setValue('suspectGenderName', $primarySuspect->gender->name ?? ($primarySuspect->gender ?? '-'));
            $templateProcessor->setValue('suspectBirthPlace', $primarySuspect->birth_place ?? '-');
            $templateProcessor->setValue('suspectBirthDate', ($primarySuspect && $primarySuspect->birth_date) ? Carbon::parse($primarySuspect->birth_date)->locale('id')->translatedFormat('d F Y') : '-');
            $templateProcessor->setValue('suspectJobName', $primarySuspect->job->name ?? ($primarySuspect->job ?? '-'));
            $templateProcessor->setValue('suspectReligionName', $primarySuspect->religion->name ?? ($primarySuspect->religion ?? '-'));
            $templateProcessor->setValue('suspectNationality', $primarySuspect->citizenship ?? 'Indonesia');
            $templateProcessor->setValue('suspectFullAddress', $primarySuspect->address ?? '-');

            // 5. Permohonan PN (Poin 3)
            $templateProcessor->setValue('pengadilan_negeri_akhir_tanggal', $tglAkhirPn);
            $templateProcessor->setValue('kejaksaan_akhir_tanggal', $tglAkhirPn); // fallback jika diperlukan
            $templateProcessor->setValue('requestedExtensionDays', $document->waktu_penahanan_hari ?? 30);
            $templateProcessor->setValue('rutan_name', $document->rutan_name ?? '-');
            $templateProcessor->setValue('extensionStartDate', $document->tanggal_mulai_perpanjangan_penahanan ? Carbon::parse($document->tanggal_mulai_perpanjangan_penahanan)->locale('id')->translatedFormat('d F Y') : '-');
            $templateProcessor->setValue('extensionEndDate', $document->tanggal_akhir_perpanjangan_penahanan ? Carbon::parse($document->tanggal_akhir_perpanjangan_penahanan)->locale('id')->translatedFormat('d F Y') : '-');

            // Kontak Penyidik Pembantu
            $contactOfficer = $document->contactOfficer;
            if ($contactOfficer) {
                $contactRank = $contactOfficer->rank->name ?? '';
                $templateProcessor->setValue('contactOfficerName', trim($contactRank.' '.$contactOfficer->full_name));
                $templateProcessor->setValue('contactOfficerPhone', $contactOfficer->phone_number ?? '-');
            } else {
                $templateProcessor->setValue('contactOfficerName', '-');
                $templateProcessor->setValue('contactOfficerPhone', '-');
            }

            // Tembusan
            $carbonCopies = $document->tembusan ?? [];
            if (empty($carbonCopies) || !is_array($carbonCopies)) {
                $defaultCc = 'Kepala Kepolisian Resor '.($accident->polres->full_name ?? '');
                $carbonCopies = [$defaultCc];
            }

            $blockCarbonCopies = [];
            $iter = 1;
            foreach ($carbonCopies as $ccVal) {
                if (trim($ccVal)) {
                    $blockCarbonCopies[] = [
                        'carbon_copy_iteration' => $iter++,
                        'carbon_copy_name' => trim($ccVal),
                    ];
                }
            }
            if (empty($blockCarbonCopies)) {
                $blockCarbonCopies[] = [
                    'carbon_copy_iteration' => 1,
                    'carbon_copy_name' => 'Kepala Kepolisian Resor '.($accident->polres->full_name ?? ''),
                ];
            }

            $templateProcessor->cloneRowAndSetValues('carbon_copy_iteration', $blockCarbonCopies);

            // Penandatangan
            if ($signatory) {
                $sigFirstTitle = $signatory->first_title;
                $sigFirstName = $signatory->first_name;
                $sigLastName = $signatory->last_name;
                $sigLastTitle = $signatory->last_title;
                $nama = trim(($sigFirstTitle ? $sigFirstTitle.' ' : '').$sigFirstName.($sigLastName ? ' '.$sigLastName : '').($sigLastTitle ? ', '.$sigLastTitle : ''));

                $sigModel = $signatory->officer ?? Officer::with(['position.positionCluster', 'rank'])->find($signatory->officer_id);
                $sigRank = '';
                if (is_array($signatory->rank)) {
                    $sigRank = $signatory->rank['name'] ?? ($signatory->rank['full_name'] ?? '');
                } elseif (is_string($signatory->rank)) {
                    $sigRank = $signatory->rank;
                }
                if (empty($sigRank) && isset($sigModel->rank)) {
                    $sigRank = $sigModel->rank->name ?? '';
                }
                $sigNrp = is_string($signatory->register_number) ? $signatory->register_number : ($sigModel->register_number ?? '-');

                $clusterId = $sigModel->position->position_cluster_id ?? ($sigModel->position->positionCluster->id ?? null);
                $aliasName = $sigModel->position->positionCluster->alias_name ?? ($sigModel->position->name ?? 'KASAT LANTAS');

                if ($clusterId == '1') {
                    $signatoryHeadText = 'KEPALA KEPOLISIAN RESOR '.strtoupper($accident->polres->full_name ?? '');
                    $signatoryPositionName = '';
                } elseif ($clusterId == '9') {
                    $signatoryHeadText = 'a.n. DIREKTUR LALU LINTAS POLDA '.strtoupper($poldaFullName);
                    $signatoryPositionName = strtoupper($aliasName);
                } else {
                    $signatoryHeadText = 'a.n. KEPALA KEPOLISIAN RESOR '.strtoupper($accident->polres->full_name ?? '');
                    $signatoryPositionName = strtoupper($aliasName);
                }

                $templateProcessor->setValue('signatoryHeadText', $signatoryHeadText);
                $templateProcessor->setValue('signatoryPositionName', $signatoryPositionName);
                $templateProcessor->setValue('signatoryName', $nama ?: '-');
                $templateProcessor->setValue('signatoryRankName', strtoupper($sigRank));
                $templateProcessor->setValue('signatoryRegisterNumber', $sigNrp);
            } else {
                $templateProcessor->setValue('signatoryHeadText', 'a.n. KEPALA KEPOLISIAN RESOR '.strtoupper($accident->polres->full_name ?? ''));
                $templateProcessor->setValue('signatoryPositionName', 'KASAT LANTAS');
                $templateProcessor->setValue('signatoryName', '-');
                $templateProcessor->setValue('signatoryRankName', '-');
                $templateProcessor->setValue('signatoryRegisterNumber', '-');
            }

            // Fallback tags lama jika template dimodifikasi
            $templateProcessor->setValue('nomor_surat', $document->nomor_surat ?? '-');
            $templateProcessor->setValue('tanggal_surat', $docDateFormatted);
            $templateProcessor->setValue('lampiran_surat', $appendix);
            $templateProcessor->setValue('nomor_spdp', $document->nomor_spdp ?? '-');
            $templateProcessor->setValue('tanggal_spdp', $document->tanggal_spdp ? Carbon::parse($document->tanggal_spdp)->locale('id')->translatedFormat('d F Y') : '-');
            $templateProcessor->setValue('alasan_perpanjangan', $document->alasan_perpanjangan ?? '-');
            $templateProcessor->setValue('waktu_penahanan_hari', $document->waktu_penahanan_hari ?? '-');
            $templateProcessor->setValue('polda_full_name', $poldaFullName);
            $templateProcessor->setValue('polres_full_name', $resorPoliceFullName);
            $templateProcessor->setValue('polres_alamat', $resorPoliceAddress);
            $templateProcessor->setValue('officer_signature_name', $signatory ? $nama : '-');
            $templateProcessor->setValue('officer_signature_nrp', $signatory ? ($signatory->register_number ?? '-') : '-');

            $genDir = public_path('generate');
            if (!file_exists($genDir)) {
                mkdir($genDir, 0755, true);
            }

            $filename = 'generate/'.Str::uuid().'_S-22_Surat_Permintaan_Perpanjangan_Kedua_30_Hari.docx';
            $templateProcessor->saveAs(public_path($filename));

            return response()->download(public_path($filename))->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat membuat file dokumen (Template Error): '.$e->getMessage());
        }
    }

    public function validateRequestForm(Request $request)
    {
        try {
            $validator = $this->validateForm($request);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'code' => 422,
                    'errors' => $validator->errors(),
                ], 422);
            }

            return response()->json([
                'success' => true,
                'code' => 200,
                'message' => 'Silahkan menunggu proses simpan data',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'errors' => 'Terjadi kesalahan pada sistem: '.$e->getMessage(),
                'code' => 500,
            ], 500);
        }
    }

    private function validateForm(Request $request)
    {
        return Validator::make($request->all(), [
            // 1. Identitas Surat
            'nomor_surat' => 'required|max:255',
            'tanggal_surat' => 'required|date',
            'klasifikasi_surat_id' => 'required',
            'lampiran_surat' => 'nullable|max:255',

            // 2. Instansi Tujuan
            'kejaksaan_id' => 'required',
            'nama_pengadilan_negeri' => 'required|max:255',

            // 3. Rujukan Dokumen Pendahulu
            'nomor_surat_perintah_penyidikan' => 'required|max:255',
            'tanggal_surat_perintah_penyidikan' => 'required|date',
            'nomor_spdp' => 'required|max:255',
            'tanggal_spdp' => 'required|date',
            'nomor_sket_tersangka' => 'required|max:255',
            'tanggal_sket_tersangka' => 'required|date',
            'nomor_surat_perintah_penahanan' => 'required|max:255',
            'tanggal_surat_perintah_penahanan' => 'required|date',
            'nomor_surat_perpanjangan_kejaksaan' => 'required|max:255',
            'tanggal_surat_perpanjangan_kejaksaan' => 'required|date',
            'nomor_surat_perintah_perpanjangan_penahanan' => 'required|max:255',
            'tanggal_surat_perintah_perpanjangan_penahanan' => 'required|date',
            'nomor_sket_perpanjangan_kpn_pertama' => 'required|max:255',
            'tanggal_sket_perpanjangan_kpn_pertama' => 'required|date',
            'nomor_surat_perintah_perpanjangan_penahanan_kpn_pertama' => 'required|max:255',
            'tanggal_surat_perintah_perpanjangan_penahanan_kpn_pertama' => 'required|date',

            // 4. Detail Masa Perpanjangan (Poin 3)
            'pengadilan_negeri_akhir_tanggal' => 'required|date',
            'waktu_penahanan_hari' => 'required|numeric|min:1|max:60',
            'prison_id' => 'nullable',
            'rutan_name' => 'required|max:255',
            'tanggal_mulai_perpanjangan_penahanan' => 'required|date',
            'tanggal_akhir_perpanjangan_penahanan' => 'required|date',
            'alasan_perpanjangan' => 'required',

            // 5. Perkara & Dasar Hukum
            'lawCrimeTypeIds' => 'required|array|min:1',
            'pasal_diduga' => 'required',
            'dugaan_tindak_pidana' => 'required',

            // 6. Tersangka, Petugas, Tembusan & Penandatangan
            'suspects' => 'required|array|min:1',
            'contact_officer_id' => 'required',
            'tembusan' => 'required|array|min:1',
            'tembusan.0' => 'required',
            'signatory' => 'required',
        ], [
            'nomor_surat.required' => 'Nomor Surat harus diisi',
            'nomor_surat.max' => 'Nomor Surat maksimal 255 karakter',
            'tanggal_surat.required' => 'Tanggal Surat harus diisi',
            'klasifikasi_surat_id.required' => 'Klasifikasi Surat harus dipilih',
            'kejaksaan_id.required' => 'Kejaksaan Negeri Tujuan harus dipilih',
            'nama_pengadilan_negeri.required' => 'Pengadilan Negeri Tujuan harus diisi',
            'nomor_surat_perintah_penyidikan.required' => 'Nomor Surat Perintah Penyidikan harus diisi',
            'tanggal_surat_perintah_penyidikan.required' => 'Tanggal Surat Perintah Penyidikan harus diisi',
            'nomor_spdp.required' => 'Nomor SPDP harus diisi',
            'tanggal_spdp.required' => 'Tanggal SPDP harus diisi',
            'nomor_sket_tersangka.required' => 'Nomor SKET Penetapan Tersangka harus diisi',
            'tanggal_sket_tersangka.required' => 'Tanggal SKET Penetapan Tersangka harus diisi',
            'nomor_surat_perintah_penahanan.required' => 'Nomor Surat Perintah Penahanan Penyidik harus diisi',
            'tanggal_surat_perintah_penahanan.required' => 'Tanggal Surat Perintah Penahanan Penyidik harus diisi',
            'nomor_surat_perpanjangan_kejaksaan.required' => 'Nomor Surat Perpanjangan Kejaksaan harus diisi',
            'tanggal_surat_perpanjangan_kejaksaan.required' => 'Tanggal Surat Perpanjangan Kejaksaan harus diisi',
            'nomor_surat_perintah_perpanjangan_penahanan.required' => 'Nomor Surat Perintah Perpanjangan Penahanan Penyidik harus diisi',
            'tanggal_surat_perintah_perpanjangan_penahanan.required' => 'Tanggal Surat Perintah Perpanjangan Penahanan Penyidik harus diisi',
            'nomor_sket_perpanjangan_kpn_pertama.required' => 'Nomor S.Ket Perpanjangan KPN1 harus diisi',
            'tanggal_sket_perpanjangan_kpn_pertama.required' => 'Tanggal S.Ket Perpanjangan KPN1 harus diisi',
            'nomor_surat_perintah_perpanjangan_penahanan_kpn_pertama.required' => 'Nomor Sprint Perpanjangan Penahanan (KPN1) harus diisi',
            'tanggal_surat_perintah_perpanjangan_penahanan_kpn_pertama.required' => 'Tanggal Sprint Perpanjangan Penahanan (KPN1) harus diisi',
            'pengadilan_negeri_akhir_tanggal.required' => 'Tanggal Akhir Penahanan Pengadilan Negeri harus diisi',
            'waktu_penahanan_hari.required' => 'Lama Perpanjangan (Hari) harus diisi',
            'waktu_penahanan_hari.numeric' => 'Lama Perpanjangan (Hari) harus berupa angka',
            'waktu_penahanan_hari.min' => 'Lama Perpanjangan (Hari) minimal 1 hari',
            'waktu_penahanan_hari.max' => 'Lama Perpanjangan (Hari) maksimal 60 hari',
            'rutan_name.required' => 'Tempat Penahanan / Rutan harus diisi',
            'tanggal_mulai_perpanjangan_penahanan.required' => 'Tanggal Mulai Perpanjangan harus diisi',
            'tanggal_akhir_perpanjangan_penahanan.required' => 'Tanggal Akhir Perpanjangan harus diisi',
            'alasan_perpanjangan.required' => 'Alasan Perpanjangan harus diisi',
            'lawCrimeTypeIds.required' => 'Minimal 1 Undang-Undang yang Dikenakan harus ditambahkan ke tabel',
            'lawCrimeTypeIds.min' => 'Minimal 1 Undang-Undang yang Dikenakan harus ditambahkan ke tabel',
            'pasal_diduga.required' => 'Pasal yang Dipersangkakan harus diisi',
            'dugaan_tindak_pidana.required' => 'Dugaan Tindak Pidana harus diisi',
            'suspects.required' => 'Minimal 1 Tersangka harus dipilih',
            'contact_officer_id.required' => 'Penyidik Pembantu / Petugas Penghubung harus dipilih',
            'tembusan.required' => 'Tembusan Surat harus diisi',
            'tembusan.0.required' => 'Tembusan Surat minimal harus memiliki 1 tujuan',
            'signatory.required' => 'Pejabat Penandatangan harus dipilih',
        ]);
    }

    private function getOldNewPolresIds($polresId)
    {
        $polres = Polres::where('id', $polresId)->first();
        if (! $polres) {
            return [$polresId];
        }

        $ids = [$polres->id];
        if (! empty($polres->old_id)) {
            $ids[] = $polres->old_id;
        }

        return array_unique(array_filter($ids));
    }
}
