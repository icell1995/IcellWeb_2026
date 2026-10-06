<?php

namespace App\Http\Controllers\Docs;

use App\Http\Controllers\Controller;
use App\Models\Accident;
use App\Models\Doc\SuratKetetapanTentangPenetapanTersangkaDocument\SuratKetetapanTentangPenetapanTersangkaDocument;
use App\Models\Doc\SuratPemberitahuanDimulainyaPenyidikanDocument\SuratPemberitahuanDimulainyaPenyidikanDocument;
use App\Models\Doc\SuratPermintaanPerpanjanganPenahananLanjutanDocument\SuratPermintaanPerpanjanganPenahananLanjutanDocument;
use App\Models\Doc\SuratPermintaanPerpanjanganPenahananLanjutanDocument\SuratPermintaanPerpanjanganPenahananLanjutanDocumentLaw;
use App\Models\Lib\CrimeClass;
use App\Models\Lib\CrimeConstitution;
use App\Models\Lib\CrimeType;
use App\Models\Lib\Prison;
use App\Models\Lib\Prosecutor;
use App\Models\Officer;
use App\Models\Suspect;
use App\Services\Doc\DocService;
use App\Traits\DocsOfficersTraits;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\TemplateProcessor;

class SuratPermintaanPerpanjanganPenahananLanjutanDocumentController extends Controller
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

        $defaultNomorSpdp = $spdpDocument->document_number ?? null;
        $defaultTanggalSpdp = $spdpDocument->document_date ?? null;
        $defaultKejaksaanId = $spdpDocument->prosecutor_id ?? ($accident->polres->prosecutor->first()->id ?? null);
        $defaultNomorSketTersangka = $sketTersangkaDocument->document_number ?? null;
        $defaultTanggalSketTersangka = $sketTersangkaDocument->document_date ?? null;
        $defaultRutanName = 'Rutan '.($accident->polres->full_name ?? '');
        $selectedSuspectIds = $sketTersangkaDocument ? $sketTersangkaDocument->suspect->pluck('id')->toArray() : ($suspects->count() === 1 ? [$suspects->first()->id] : []);

        // Dokumen Pendahulu Surat Perintah Penahanan (S-17)
        $s17Documents = \App\Models\Doc\SuratPerintahPenahananDocument\SuratPerintahPenahananDocument::where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->orderBy('created_at', 'desc')
            ->get();
        $s17Document = $s17Documents->first();
        $defaultS17Id = $s17Document->id ?? null;
        $defaultNomorSprintPenahanan = $s17Document->nomor ?? $s17Document->document_number ?? null;
        $defaultTanggalSprintPenahanan = $s17Document->tanggal ?? $s17Document->document_date ?? null;
        if (!empty($s17Document->tempat_penahanan)) {
            $defaultRutanName = $s17Document->tempat_penahanan;
        }

        // Dokumen Pendahulu Surat Permohonan Perpanjangan Penahanan Kejaksaan (S-21)
        $s21Documents = \App\Models\Doc\SuratPermohonanPerpanjanganPenahananKejaksaanDocument\SuratPermohonanPerpanjanganPenahananKejaksaanDocument::where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->orderBy('created_at', 'desc')
            ->get();
        $s21Document = $s21Documents->first();
        $defaultS21Id = $s21Document->id ?? null;
        $defaultNomorPerpanjanganKejaksaan = $s21Document->nomor ?? $s21Document->document_number ?? null;
        $defaultTanggalPerpanjanganKejaksaan = $s21Document->tanggal ?? $s21Document->document_date ?? null;
        $defaultNamaKejaksaan = $s21Document->nama_kejaksaan ?? ($s21Document->prosecutor->name ?? null);
        $defaultKejaksaanAkhirTanggal = $s21Document->tanggal_akhir_perpanjangan ?? null;
        if ($s21Document && !empty($s21Document->prosecutor_id)) {
            $defaultKejaksaanId = $s21Document->prosecutor_id;
        }

        // Kalkulasi Tanggal Mulai & Akhir S-22 Pertama (30 Hari)
        $defaultTanggalMulai = $defaultKejaksaanAkhirTanggal
            ? Carbon::parse($defaultKejaksaanAkhirTanggal)->addDay()->format('Y-m-d')
            : date('Y-m-d');
        $defaultTanggalAkhir = Carbon::parse($defaultTanggalMulai)->addDays(29)->format('Y-m-d');

        $defaultNamaPengadilanNegeri = null;
        if ($defaultKejaksaanId) {
            $selectedPros = $prosecutors->firstWhere('id', $defaultKejaksaanId);
            if ($selectedPros) {
                $defaultNamaPengadilanNegeri = preg_replace('/kejaksaan/i', 'PENGADILAN', $selectedPros->name);
            }
        }
        if (! $defaultNamaPengadilanNegeri && $accident->polres) {
            $defaultNamaPengadilanNegeri = 'PENGADILAN NEGERI ' . strtoupper($accident->polres->polres_district ?? $accident->polres->name);
        }

        // Master Data untuk Undang-Undang yang Dikenakan
        $crimeTypes = CrimeType::active()->orderBy('sort')->get();
        $crimeClasses = CrimeClass::active()->orderBy('sort')->get();
        $crimeConstitutions = CrimeConstitution::active()->orderBy('sort')->get();

        // Cari Sprin Sidik untuk mengambil undang-undang & pasal yang dipersangkakan
        $sprinSidik = \App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument::with([
            'suratPerintahPenyidikanDocumentLaws.crimeConstitution',
            'suratPerintahPenyidikanDocumentLaws.crimeType',
            'suratPerintahPenyidikanDocumentLaws.crimeClass',
        ])
            ->where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->latest()
            ->first();

        if (! $sprinSidik) {
            $sprinSidik = \App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument::with([
                'suratPerintahPenyidikanDocumentLaws.crimeConstitution',
                'suratPerintahPenyidikanDocumentLaws.crimeType',
                'suratPerintahPenyidikanDocumentLaws.crimeClass',
            ])
                ->where('accident_id', $accidentId)
                ->latest()
                ->first();
        }

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
                } elseif ($law->flag === 'ADDITIONAL' || $law->flag === 'ADDT' || !empty($law->constitution)) {
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

        $pasalList = [];
        if ($sprinSidik && $sprinSidik->suratPerintahPenyidikanDocumentLaws && $sprinSidik->suratPerintahPenyidikanDocumentLaws->isNotEmpty()) {
            foreach ($sprinSidik->suratPerintahPenyidikanDocumentLaws as $law) {
                if ($law->flag === 'MAIN' || empty($law->flag)) {
                    $chapter = trim($law->constitution_chapter ?? '');
                    $cName = $law->crimeConstitution ? trim($law->crimeConstitution->name) : '';
                    $pasalList[] = trim($chapter . ' ' . $cName);
                } else {
                    $pasalList[] = trim($law->constitution ?? '');
                }
            }
        }
        $pasalList = array_values(array_filter($pasalList));
        $defaultPasalDiduga = !empty($pasalList) ? implode(', ', $pasalList) : 'Pasal 310 ayat (4) Undang-Undang Nomor 22 Tahun 2009 tentang Lalu Lintas dan Angkutan Jalan';

        $defaultDugaanTindakPidana = !empty($accident->damage_lose_desc)
            ? $accident->damage_lose_desc
            : 'kecelakaan lalu lintas yang mengakibatkan orang lain meninggal dunia dan/atau luka berat dan/atau kerusakan kendaraan';

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
            'defaultRutanName' => $defaultRutanName,
            'defaultPasalDiduga' => $defaultPasalDiduga,
            'defaultDugaanTindakPidana' => $defaultDugaanTindakPidana,
            'selectedSuspectIds' => $selectedSuspectIds,
            'prisonsGrouped' => Prison::active()->orderBy('province')->orderBy('name')->get()->groupBy('province'),
            's17Documents' => $s17Documents,
            's21Documents' => $s21Documents,
            's17Document' => $s17Document,
            's21Document' => $s21Document,
            'defaultS17Id' => $defaultS17Id,
            'defaultS21Id' => $defaultS21Id,
            'defaultNomorSprintPenahanan' => $defaultNomorSprintPenahanan,
            'defaultTanggalSprintPenahanan' => $defaultTanggalSprintPenahanan,
            'defaultNomorPerpanjanganKejaksaan' => $defaultNomorPerpanjanganKejaksaan,
            'defaultTanggalPerpanjanganKejaksaan' => $defaultTanggalPerpanjanganKejaksaan,
            'defaultNamaKejaksaan' => $defaultNamaKejaksaan,
            'defaultKejaksaanAkhirTanggal' => $defaultKejaksaanAkhirTanggal,
            'defaultTanggalMulai' => $defaultTanggalMulai,
            'defaultTanggalAkhir' => $defaultTanggalAkhir,
        ];

        return view('docs.surat-permintaan-perpanjangan-penahanan-lanjutan-document.create', $viewData);
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

        // Resolusi SPDP otomatis
        $nomorSpdp = $request->nomor_spdp;
        $tanggalSpdp = $request->tanggal_spdp ? Carbon::parse($request->tanggal_spdp)->format('Y-m-d') : null;
        $kejaksaanId = $request->kejaksaan_id;
        if (! $nomorSpdp) {
            $spdp = SuratPemberitahuanDimulainyaPenyidikanDocument::where('accident_id', $accidentId)
                ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
                ->latest()
                ->first();
            if ($spdp) {
                $nomorSpdp = $spdp->document_number;
                $tanggalSpdp = $spdp->document_date ? Carbon::parse($spdp->document_date)->format('Y-m-d') : null;
                if (! $kejaksaanId) {
                    $kejaksaanId = $spdp->prosecutor_id;
                }
            }
        }

        // Resolusi Penetapan Tersangka otomatis
        $nomorSketTersangka = $request->nomor_sket_tersangka;
        $tanggalSketTersangka = $request->tanggal_sket_tersangka ? Carbon::parse($request->tanggal_sket_tersangka)->format('Y-m-d') : null;
        if (! $nomorSketTersangka) {
            $sket = SuratKetetapanTentangPenetapanTersangkaDocument::where('accident_id', $accidentId)
                ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
                ->latest()
                ->first();
            if ($sket) {
                $nomorSketTersangka = $sket->document_number;
                $tanggalSketTersangka = $sket->document_date ? Carbon::parse($sket->document_date)->format('Y-m-d') : null;
            }
        }

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

        DB::beginTransaction();
        try {
            $rawTembusan = $request->tembusan ?? $request->carbonCopies ?? [];
            $filteredTembusan = [];
            if (is_array($rawTembusan)) {
                foreach ($rawTembusan as $t) {
                    if (trim($t)) {
                        $filteredTembusan[] = trim($t);
                    }
                }
            }

            $document = SuratPermintaanPerpanjanganPenahananLanjutanDocument::create([
                'accident_id' => $accidentId,
                'surat_perintah_penahanan_document_id' => $request->surat_perintah_penahanan_document_id ?: null,
                'surat_permohonan_perpanjangan_penahanan_kejaksaan_document_id' => $request->surat_permohonan_perpanjangan_penahanan_kejaksaan_document_id ?: null,
                'document_category_id' => '0606',
                'status_id' => '2', // Dokumen Dibuat
                'nomor_surat' => $request->nomor_surat,
                'tanggal_surat' => $request->tanggal_surat ? Carbon::parse($request->tanggal_surat)->format('Y-m-d') : null,
                'klasifikasi_surat_id' => $request->klasifikasi_surat_id,
                'lampiran_surat' => $lampiranSurat,
                'kejaksaan_id' => $kejaksaanId,
                'nama_pengadilan_negeri' => $request->nama_pengadilan_negeri,
                'nomor_spdp' => $nomorSpdp,
                'tanggal_spdp' => $tanggalSpdp,
                'kode_satker_penerbit_spdp' => $kodeSatkerPenerbitSpdp,
                'nomor_sket_tersangka' => $nomorSketTersangka,
                'tanggal_sket_tersangka' => $tanggalSketTersangka,
                'nomor_surat_perintah_penahanan' => $request->nomor_surat_perintah_penahanan,
                'tanggal_surat_perintah_penahanan' => $request->tanggal_surat_perintah_penahanan ? Carbon::parse($request->tanggal_surat_perintah_penahanan)->format('Y-m-d') : null,
                'nama_kejaksaan_surat_perpanjangan' => $request->nama_kejaksaan_surat_perpanjangan,
                'nomor_surat_perpanjangan_kejaksaan' => $request->nomor_surat_perpanjangan_kejaksaan,
                'tanggal_surat_perpanjangan_kejaksaan' => $request->tanggal_surat_perpanjangan_kejaksaan ? Carbon::parse($request->tanggal_surat_perpanjangan_kejaksaan)->format('Y-m-d') : null,
                'nomor_surat_perintah_perpanjangan_penahanan' => $request->nomor_surat_perintah_perpanjangan_penahanan,
                'tanggal_surat_perintah_perpanjangan_penahanan' => $request->tanggal_surat_perintah_perpanjangan_penahanan ? Carbon::parse($request->tanggal_surat_perintah_perpanjangan_penahanan)->format('Y-m-d') : null,
                'kejaksaan_akhir_tanggal' => $request->kejaksaan_akhir_tanggal ? Carbon::parse($request->kejaksaan_akhir_tanggal)->format('Y-m-d') : null,
                'alasan_perpanjangan' => $request->alasan_perpanjangan,
                'pasal_diduga' => $request->pasal_diduga,
                'dugaan_tindak_pidana' => $request->dugaan_tindak_pidana,
                'waktu_penahanan_hari' => 30,
                'prison_id' => $prisonId,
                'rutan_name' => $rutanName,
                'kode_satker_tempat_penahanan' => $kodeSatkerTempatPenahanan,
                'tanggal_mulai_perpanjangan_penahanan' => $request->tanggal_mulai_perpanjangan_penahanan ? Carbon::parse($request->tanggal_mulai_perpanjangan_penahanan)->format('Y-m-d') : null,
                'tanggal_akhir_perpanjangan_penahanan' => $request->tanggal_akhir_perpanjangan_penahanan ? Carbon::parse($request->tanggal_akhir_perpanjangan_penahanan)->format('Y-m-d') : null,
                'contact_officer_id' => $request->contact_officer_id,
                'tembusan' => !empty($filteredTembusan) ? $filteredTembusan : null,
            ]);

            // Simpan Undang-Undang yang Dikenakan
            $lawCrimeTypeIds = $request->lawCrimeTypeIds ?? [];
            $lawCrimeClassIds = $request->lawCrimeClassIds ?? [];
            $lawCrimeConstitutionIds = $request->lawCrimeConstitutionIds ?? [];
            $lawCrimeConstitutionChapters = $request->lawCrimeConstitutionChapters ?? [];

            if (is_array($lawCrimeTypeIds) && count($lawCrimeTypeIds) > 0) {
                foreach ($lawCrimeTypeIds as $idx => $ctId) {
                    $document->laws()->create([
                        'crime_type_id' => $ctId,
                        'crime_class_id' => $lawCrimeClassIds[$idx] ?? null,
                        'crime_constitution_id' => $lawCrimeConstitutionIds[$idx] ?? null,
                        'constitution_chapter' => $lawCrimeConstitutionChapters[$idx] ?? null,
                        'flag' => 'MAIN',
                    ]);
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

            // Fallback: Jika form tidak mengirimkan data hukum, copy langsung dari Sprindik terkait
            if (empty($lawCrimeTypeIds) && empty($lawAdditionalNames)) {
                $sprinSidik = \App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument::with('suratPerintahPenyidikanDocumentLaws')
                    ->where('accident_id', $accidentId)
                    ->latest()
                    ->first();
                if ($sprinSidik && $sprinSidik->suratPerintahPenyidikanDocumentLaws) {
                    foreach ($sprinSidik->suratPerintahPenyidikanDocumentLaws as $law) {
                        $document->laws()->create([
                            'crime_type_id' => $law->crime_type_id,
                            'crime_class_id' => $law->crime_class_id,
                            'crime_constitution_id' => $law->crime_constitution_id,
                            'constitution_chapter' => $law->constitution_chapter,
                            'constitution' => $law->constitution,
                            'flag' => $law->flag ?? 'MAIN',
                        ]);
                    }
                }
            }

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
                        'phone_number' => $signatory->phone_number,
                        'email' => $signatory->email,
                        'police_id' => $signatory->police_id,
                        'status' => ['PRESENT'],
                        'class' => ['SIGNATORY'],
                        'flag' => ['INTERNAL'],
                    ]);
                } else {
                    DB::rollback();

                    return redirect()->back()->with('error', 'Gagal menyimpan. Data Perwira Penandatangan tidak valid atau tidak ditemukan.');
                }
            }

            if ($request->has('suspects') && is_array($request->suspects)) {
                $document->suspects()->sync($request->suspects);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data: '.$e->getMessage());
        }

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])->with('success', 'Dokumen Surat Permintaan Perpanjangan Penahanan Ke Ketua Pengadilan Negeri (Pertama 30 Hari) berhasil disimpan.');
    }

    public function edit($id)
    {
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $accident = Accident::with(['police', 'polres', 'polres.polda', 'polres.prosecutor'])->where('id', $accidentId)->first();

        if (! $accident) {
            return redirect()->back()->with('error', 'Data Laporan Polisi tidak ditemukan.');
        }

        $document = SuratPermintaanPerpanjanganPenahananLanjutanDocument::with([
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

        // Default Nama Pengadilan Negeri jika kosong
        $defaultNamaPengadilanNegeri = $document->nama_pengadilan_negeri;
        if (empty($defaultNamaPengadilanNegeri)) {
            $defaultProsecutor = null;
            if ($document->kejaksaan_id) {
                $defaultProsecutor = $prosecutors->firstWhere('id', $document->kejaksaan_id);
            }
            if (! $defaultProsecutor && $spdpDocument && $spdpDocument->prosecutor_id) {
                $defaultProsecutor = $prosecutors->firstWhere('id', $spdpDocument->prosecutor_id);
            }
            if (! $defaultProsecutor && $accident->polres && $accident->polres->prosecutor->isNotEmpty()) {
                $defaultProsecutor = $accident->polres->prosecutor->first();
            }
            if ($defaultProsecutor) {
                $rawProsecutorName = $defaultProsecutor->full_name ?? $defaultProsecutor->name;
                $defaultNamaPengadilanNegeri = preg_replace('/kejaksaan/i', 'PENGADILAN', $rawProsecutorName);
            }
        }

        // Default Pasal Diduga & Dugaan Tindak Pidana jika kosong
        $defaultPasalDiduga = $document->pasal_diduga;
        if (empty($defaultPasalDiduga)) {
            $sprinSidik = \App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument::with('suratPerintahPenyidikanDocumentLaws.crimeConstitution')
                ->where('accident_id', $accidentId)
                ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
                ->latest()
                ->first();

            if ($sprinSidik && $sprinSidik->suratPerintahPenyidikanDocumentLaws) {
                $pasalList = $sprinSidik->suratPerintahPenyidikanDocumentLaws
                    ->map(function ($law) {
                        $parts = [];
                        if (! empty($law->constitution_chapter)) {
                            $parts[] = $law->constitution_chapter;
                        }
                        if (! empty($law->constitution)) {
                            $parts[] = $law->constitution;
                        }
                        if (empty($parts) && $law->crimeConstitution) {
                            if (! empty($law->crimeConstitution->chapter)) {
                                $parts[] = $law->crimeConstitution->chapter;
                            }
                            if (! empty($law->crimeConstitution->name)) {
                                $parts[] = $law->crimeConstitution->name;
                            }
                        }

                        return ! empty($parts) ? implode(' ', $parts) : null;
                    })
                    ->filter()
                    ->toArray();
                $defaultPasalDiduga = implode('; ', $pasalList);
            }
            if (empty($defaultPasalDiduga)) {
                $defaultPasalDiduga = 'Pasal 310 ayat (4) Undang-Undang Nomor 22 Tahun 2009 tentang Lalu Lintas dan Angkutan Jalan';
            }
        }

        $defaultDugaanTindakPidana = $document->dugaan_tindak_pidana ?: (!empty($accident->damage_lose_desc) ? $accident->damage_lose_desc : 'kecelakaan lalu lintas yang mengakibatkan orang lain meninggal dunia dan/atau luka berat dan/atau kerusakan kendaraan');

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

        // Dokumen Pendahulu S-17 & S-21 untuk Edit
        $s17Documents = \App\Models\Doc\SuratPerintahPenahananDocument\SuratPerintahPenahananDocument::where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->orderBy('created_at', 'desc')
            ->get();
        $s21Documents = \App\Models\Doc\SuratPermohonanPerpanjanganPenahananKejaksaanDocument\SuratPermohonanPerpanjanganPenahananKejaksaanDocument::where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->orderBy('created_at', 'desc')
            ->get();

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
            's17Documents' => $s17Documents,
            's21Documents' => $s21Documents,
        ];

        return view('docs.surat-permintaan-perpanjangan-penahanan-lanjutan-document.edit', $viewData);
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
            $document = SuratPermintaanPerpanjanganPenahananLanjutanDocument::where('id', $id)->first();
            if (! $document) {
                DB::rollback();

                return redirect()->back()->with('error', 'Data Dokumen S-22 tidak ditemukan.');
            }

            // Resolusi satker penerbit & tempat penahanan otomatis
            $satkerCode = $accident->police->satker_code ?? $accident->polres->satker_code ?? $accident->polres->code ?? '';
            $kodeSatkerPenerbitSpdp = $request->kode_satker_penerbit_spdp ?: ($document->kode_satker_penerbit_spdp ?: $satkerCode);
            $kodeSatkerTempatPenahanan = $request->kode_satker_tempat_penahanan ?: ($document->kode_satker_tempat_penahanan ?: $satkerCode);

            // Resolusi SPDP
            $nomorSpdp = $request->nomor_spdp ?: $document->nomor_spdp;
            $tanggalSpdp = $request->tanggal_spdp ? Carbon::parse($request->tanggal_spdp)->format('Y-m-d') : $document->tanggal_spdp;
            $kejaksaanId = $request->kejaksaan_id ?: $document->kejaksaan_id;

            // Resolusi SKET Tersangka
            $nomorSketTersangka = $request->nomor_sket_tersangka ?: $document->nomor_sket_tersangka;
            $tanggalSketTersangka = $request->tanggal_sket_tersangka ? Carbon::parse($request->tanggal_sket_tersangka)->format('Y-m-d') : $document->tanggal_sket_tersangka;

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
                'surat_perintah_penahanan_document_id' => $request->surat_perintah_penahanan_document_id ?: $document->surat_perintah_penahanan_document_id,
                'surat_permohonan_perpanjangan_penahanan_kejaksaan_document_id' => $request->surat_permohonan_perpanjangan_penahanan_kejaksaan_document_id ?: $document->surat_permohonan_perpanjangan_penahanan_kejaksaan_document_id,
                'nomor_surat' => $request->nomor_surat,
                'tanggal_surat' => $request->tanggal_surat ? Carbon::parse($request->tanggal_surat)->format('Y-m-d') : null,
                'klasifikasi_surat_id' => $request->klasifikasi_surat_id,
                'lampiran_surat' => $lampiranSurat,
                'kejaksaan_id' => $kejaksaanId,
                'nama_pengadilan_negeri' => $request->has('nama_pengadilan_negeri') ? $request->nama_pengadilan_negeri : $document->nama_pengadilan_negeri,
                'nomor_spdp' => $nomorSpdp,
                'tanggal_spdp' => $tanggalSpdp,
                'kode_satker_penerbit_spdp' => $kodeSatkerPenerbitSpdp,
                'nomor_sket_tersangka' => $nomorSketTersangka,
                'tanggal_sket_tersangka' => $tanggalSketTersangka,
                'nomor_surat_perintah_penahanan' => $request->nomor_surat_perintah_penahanan,
                'tanggal_surat_perintah_penahanan' => $request->tanggal_surat_perintah_penahanan ? Carbon::parse($request->tanggal_surat_perintah_penahanan)->format('Y-m-d') : null,
                'nama_kejaksaan_surat_perpanjangan' => $request->nama_kejaksaan_surat_perpanjangan,
                'nomor_surat_perpanjangan_kejaksaan' => $request->nomor_surat_perpanjangan_kejaksaan,
                'tanggal_surat_perpanjangan_kejaksaan' => $request->tanggal_surat_perpanjangan_kejaksaan ? Carbon::parse($request->tanggal_surat_perpanjangan_kejaksaan)->format('Y-m-d') : null,
                'nomor_surat_perintah_perpanjangan_penahanan' => $request->nomor_surat_perintah_perpanjangan_penahanan,
                'tanggal_surat_perintah_perpanjangan_penahanan' => $request->tanggal_surat_perintah_perpanjangan_penahanan ? Carbon::parse($request->tanggal_surat_perintah_perpanjangan_penahanan)->format('Y-m-d') : null,
                'kejaksaan_akhir_tanggal' => $request->kejaksaan_akhir_tanggal ? Carbon::parse($request->kejaksaan_akhir_tanggal)->format('Y-m-d') : null,
                'alasan_perpanjangan' => $request->alasan_perpanjangan,
                'pasal_diduga' => $request->has('pasal_diduga') ? $request->pasal_diduga : $document->pasal_diduga,
                'dugaan_tindak_pidana' => $request->has('dugaan_tindak_pidana') ? $request->dugaan_tindak_pidana : $document->dugaan_tindak_pidana,
                'waktu_penahanan_hari' => 30,
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
                        'phone_number' => $signatory->phone_number,
                        'email' => $signatory->email,
                        'police_id' => $signatory->police_id,
                        'status' => ['PRESENT'],
                        'class' => ['SIGNATORY'],
                        'flag' => ['INTERNAL'],
                    ]);
                }
            }

            if ($request->has('suspects') && is_array($request->suspects)) {
                $document->suspects()->sync($request->suspects);
            } else {
                $document->suspects()->sync([]);
            }

            // Sync Undang-Undang yang Dikenakan (selalu hapus data lama dan masukkan data terbaru)
            $document->laws()->forceDelete();

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

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])->with('success', 'Dokumen Surat Permintaan Perpanjangan Penahanan Ke Ketua Pengadilan Negeri (Pertama 30 Hari) berhasil diperbarui.');
    }

    public function delete($id)
    {
        $accidentId = htmlspecialchars(request()->query('accident_id'));

        DB::beginTransaction();
        try {
            $document = SuratPermintaanPerpanjanganPenahananLanjutanDocument::where('id', $id)->first();

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

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])->with('success', 'Dokumen Surat Permintaan Perpanjangan Penahanan Ke Ketua Pengadilan Negeri (Pertama 30 Hari) berhasil dihapus.');
    }

    public function download($id)
    {
        $document = SuratPermintaanPerpanjanganPenahananLanjutanDocument::with([
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
            $templateFile = file_exists(public_path('word-template/surat_permintaan_perpanjangan_penahanan_pertama_30_hari.docx'))
                ? public_path('word-template/surat_permintaan_perpanjangan_penahanan_pertama_30_hari.docx')
                : public_path('word-template/surat_permintaan_perpanjangan_penahanan.docx');

            $templateProcessor = new TemplateProcessor($templateFile);

            $resorPoliceFullName = (in_array($accident->polres->id ?? '', ['1114'])) ? 'DIREKTORAT LALU LINTAS' : 'RESOR '.strtoupper($accident->polres->full_name ?? '');
            $poldaFullName = $accident->polres->polda->name ?? ($accident->polres->polda->full_name ?? '');
            $resorPoliceAddress = ucwords(strtolower(($accident->polres->address ?? '').', '.($accident->polres->polres_district ?? '').', '.($accident->polres->polres_zipcode ?? '')));
            $documentLocation = 'S-22';
            $documentCity = ucwords(strtolower($accident->polres->name ?? $accident->polres->polres_district ?? ''));
            if (empty($documentCity) && !empty($accident->polres->polres_regency)) {
                $cleanReg = preg_replace('/^(KABUPATEN|KOTA)\s+/i', '', trim($accident->polres->polres_regency));
                $documentCity = ucwords(strtolower($cleanReg));
            }
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
            $templateProcessor->setValue('documentCity', $documentCity);
            $templateProcessor->setValue('dikeluarkanDi', $documentCity);
            $templateProcessor->setValue('kota', $documentCity);
            $templateProcessor->setValue('documentDate', $docDateFormatted);
            $templateProcessor->setValue('documentNumber', $document->nomor_surat ?? '-');
            $templateProcessor->setValue('documentClassificationName', $document->klasifikasi_surat_id ?? 'Biasa');
            $templateProcessor->setValue('appendix', $appendix);

            // 2. Tujuan Surat (Pengadilan Negeri / Kejaksaan)
            $targetPN = $document->nama_pengadilan_negeri ?: ('Pengadilan Negeri '.ucwords(strtolower($accident->polres->name ?? $accident->polres->polres_district ?? '')));

            // Resolusi Kota/Kabupaten Tujuan (prosecutorLocation)
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

            $templateProcessor->setValue('rujukan_undang_undang', $rujukanUndangUndangText);
            $templateProcessor->setValue('lpNumber', $accident->no_lp ?? '-');
            $templateProcessor->setValue('lpDate', $lpDateFormatted);
            $templateProcessor->setValue('spdpNumber', $document->nomor_spdp ?? '-');
            $templateProcessor->setValue('spdpDate', $document->tanggal_spdp ? Carbon::parse($document->tanggal_spdp)->locale('id')->translatedFormat('d F Y') : '-');
            $templateProcessor->setValue('sketNumber', $document->nomor_sket_tersangka ?? '-');
            $templateProcessor->setValue('sketDate', $document->tanggal_sket_tersangka ? Carbon::parse($document->tanggal_sket_tersangka)->locale('id')->translatedFormat('d F Y') : '-');
            $templateProcessor->setValue('sketSuspectName', $suspectNamesStr ?: '-');

            $templateProcessor->setValue('sphNumber', $document->nomor_surat_perintah_penahanan ?? '-');
            $templateProcessor->setValue('sphDate', $document->tanggal_surat_perintah_penahanan ? Carbon::parse($document->tanggal_surat_perintah_penahanan)->locale('id')->translatedFormat('d F Y') : '-');
            $templateProcessor->setValue('sphSuspectName', $suspectNamesStr ?: '-');

            $namaKejaksaan = $document->nama_kejaksaan_surat_perpanjangan ?: ($document->kejaksaan->name ?? ($accident->polres->prosecutor->first()->name ?? '-'));
            $templateProcessor->setValue('nama_kejaksaan', $namaKejaksaan);
            $templateProcessor->setValue('kejaksaanExtensionNumber', $document->nomor_surat_perpanjangan_kejaksaan ?? '-');
            $templateProcessor->setValue('kejaksaanExtensionDate', $document->tanggal_surat_perpanjangan_kejaksaan ? Carbon::parse($document->tanggal_surat_perpanjangan_kejaksaan)->locale('id')->translatedFormat('d F Y') : '-');
            $templateProcessor->setValue('kejaksaanExtensionSuspectName', $suspectNamesStr ?: '-');

            $templateProcessor->setValue('perpanjanganOrderNumber', $document->nomor_surat_perintah_perpanjangan_penahanan ?? '-');
            $templateProcessor->setValue('perpanjanganOrderDate', $document->tanggal_surat_perintah_perpanjangan_penahanan ? Carbon::parse($document->tanggal_surat_perintah_perpanjangan_penahanan)->locale('id')->translatedFormat('d F Y') : '-');
            $templateProcessor->setValue('perpanjanganOrderSuspectName', $suspectNamesStr ?: '-');

            // 4. Perkara & Identitas Tersangka (Poin 2)
            $pasalDiduga = $document->pasal_diduga;
            if (empty($pasalDiduga) && $document->laws && $document->laws->count() > 0) {
                $pasalChapters = $document->laws->pluck('constitution_chapter')->filter()->implode(', ');
                if (!empty($pasalChapters)) {
                    $pasalDiduga = $pasalChapters;
                }
            }

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
            $tglAkhirJaksa = $document->kejaksaan_akhir_tanggal ? Carbon::parse($document->kejaksaan_akhir_tanggal)->locale('id')->translatedFormat('d F Y') : '-';
            $templateProcessor->setValue('detentionEndDate', $tglAkhirJaksa);

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
            $templateProcessor->setValue('kejaksaan_akhir_tanggal', $tglAkhirJaksa);
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

            $filename = 'generate/'.Str::uuid().'_S-22_Surat_Permintaan_Perpanjangan_Pertama_30_Hari.docx';
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
            'surat_perintah_penahanan_document_id' => 'nullable',
            'surat_permohonan_perpanjangan_penahanan_kejaksaan_document_id' => 'nullable',
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
            'kejaksaan_akhir_tanggal' => 'required|date',

            // 4. Detail Masa Perpanjangan
            'waktu_penahanan_hari' => 'nullable|numeric',
            'prison_id' => 'nullable',
            'rutan_name' => 'required|max:255',
            'tanggal_mulai_perpanjangan_penahanan' => 'required|date',
            'tanggal_akhir_perpanjangan_penahanan' => 'required|date',
            'alasan_perpanjangan' => 'required',

            // 5. Perkara & Dasar Hukum
            'lawCrimeTypeIds' => 'nullable|array',
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
            'kejaksaan_akhir_tanggal.required' => 'Tanggal Akhir Penahanan Kejaksaan harus diisi',
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
}
