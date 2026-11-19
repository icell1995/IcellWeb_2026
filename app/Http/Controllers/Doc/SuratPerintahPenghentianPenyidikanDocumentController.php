<?php

namespace App\Http\Controllers\Doc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

use App\Services\Doc\DocService;
use App\Models\Accident;
use App\Models\Officer;
use App\Models\Suspect;
use App\Models\Lib\Rank;
use App\Models\Lib\Position;
use App\Models\Lib\DocumentClassification;
use App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument;
use App\Models\Doc\LaporanHasilGelarPerkaraDocument\LaporanHasilGelarPerkaraDocument;
use App\Models\Doc\SuratPemberitahuanDimulainyaPenyidikanDocument\SuratPemberitahuanDimulainyaPenyidikanDocument;
use App\Models\Doc\SuratKetetapanTentangPenetapanTersangkaDocument\SuratKetetapanTentangPenetapanTersangkaDocument;
use App\Models\Doc\SuratKetetapanPenghentianPenyidikanDocument\SuratKetetapanPenghentianPenyidikanDocument;
use App\Models\Doc\SuratPerintahPenghentianPenyidikanDocument\SuratPerintahPenghentianPenyidikanDocument;
use App\Models\Doc\SuratPerintahPenghentianPenyidikanDocument\SuratPerintahPenghentianPenyidikanDocumentOfficer;
use App\Models\Doc\SuratPerintahPenghentianPenyidikanDocument\SuratPerintahPenghentianPenyidikanDocumentAttachment;
use App\Traits\DocsOfficersTraits;

class SuratPerintahPenghentianPenyidikanDocumentController extends Controller
{
    protected $docService;

    use DocsOfficersTraits;

    // Master alasan penghentian perkara (sesuai referensi SPPT-TI & KUHAP)
    public static $masterAlasan = [
        1  => 'Tidak terdapat cukup alat bukti',
        2  => 'Peristiwa tersebut bukan merupakan tindak pidana',
        3  => 'Penyidikan dihentikan demi hukum',
        4  => 'Terdapat putusan pengadilan yang telah memperoleh kekuatan hukum tetap terhadap tersangka atas perkara yang sama',
        5  => 'Kedaluarsa',
        6  => 'Tersangka meninggal dunia',
        7  => 'Ditariknya pengaduan pada tindak pidana aduan',
        8  => 'Tercapainya penyelesaian perkara melalui mekanisme keadilan restoratif',
        9  => 'Tersangka membayar maksimum pidana denda atas tindak pidana yang hanya diancam dengan pidana denda paling banyak kategori II',
        10 => 'Tersangka membayar maksimum pidana denda kategori IV atas tindak pidana yang diancam dengan pidana paling lama 1 (satu) tahun atau pidana denda paling banyak kategori III',
    ];

    public function __construct(DocService $docService)
    {
        $this->docService = $docService;
    }

    public function index()
    {
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        if ($accidentId) {
            return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId]);
        }
        return redirect()->route('accident.index');
    }

    // ─────────────────────────────────────────────
    // CREATE
    // ─────────────────────────────────────────────
    public function create()
    {
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $accident   = Accident::with(['polres', 'polres.polda'])->where('id', $accidentId)->firstOrFail();

        // Sprindik yang ada pada perkara ini beserta laws
        $sprindikDocuments = SuratPerintahPenyidikanDocument::with([
            'suratPerintahPenyidikanDocumentLaws.crimeConstitution',
            'suratPerintahPenyidikanDocumentLaws.crimeType',
            'suratPerintahPenyidikanDocumentLaws.crimeClass',
        ])
            ->where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($sprindikDocuments as $sprindik) {
            $pasalParts = [];
            $crimeTypeParts = [];
            if ($sprindik->suratPerintahPenyidikanDocumentLaws && $sprindik->suratPerintahPenyidikanDocumentLaws->isNotEmpty()) {
                foreach ($sprindik->suratPerintahPenyidikanDocumentLaws as $law) {
                    $chapter = trim($law->constitution_chapter ?? '');
                    $constitutionName = $law->crimeConstitution ? trim($law->crimeConstitution->name) : '';
                    $pasalParts[] = implode(' ', array_filter([$chapter, $constitutionName]));
                    if ($law->crimeType) {
                        $crimeTypeParts[] = trim($law->crimeType->name);
                    }
                }
            }
            $sprindik->pasal_formatted = implode(', ', array_filter(array_unique($pasalParts)));
            $sprindik->dugaan_formatted = implode(', ', array_filter(array_unique($crimeTypeParts)));
        }

        // Laporan Hasil Gelar Perkara yang ada pada perkara ini
        $lhgpDocuments = LaporanHasilGelarPerkaraDocument::where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->orderBy('created_at', 'desc')
            ->get();

        // SPDP pada perkara ini
        $spdpDocuments = SuratPemberitahuanDimulainyaPenyidikanDocument::where('accident_id', $accidentId)
            ->orderBy('created_at', 'desc')
            ->get();

        // Surat Ketetapan Penetapan Tersangka pada perkara ini
        $skpptDocuments = SuratKetetapanTentangPenetapanTersangkaDocument::where('accident_id', $accidentId)
            ->orderBy('created_at', 'desc')
            ->get();

        // Tersangka di perkara ini
        $suspects = Suspect::where('accident_id', $accidentId)->get();

        $documentClassifications = DocumentClassification::where('is_active', true)
            ->orderBy('sort')
            ->get();

        $getOldNewPolresIds = $this->getOldNewPolresIds($accident->polres_id);

        // Pejabat Penandatangan / Pemberi Perintah
        $authorizedSignatories = Officer::withRelated()
            ->selectFullName()
            ->whereIn('police_id', $getOldNewPolresIds)
            ->whereHasUserActive()
            ->hasDataComplete()
            ->signatory()->active()->valid()
            ->orderBy('first_name')->get();

        // Daftar Petugas Penyidik yang dapat diperintahkan
        $teamOfficers = Officer::withRelated()
            ->selectFullName()
            ->whereIn('police_id', $getOldNewPolresIds)
            ->whereHasUserActive()
            ->hasDataComplete()
            ->member()->active()->valid()
            ->orderBy('first_name')->get();

        $ranks = Rank::where('is_active', true)->orderBy('sort')->get();
        $positions = Position::where('is_active', true)->orderBy('sort')->get();

        $masterAlasan = self::$masterAlasan;

        // Default dugaan & pasal dari sprindik pertama (jika ada)
        $firstSprindik = $sprindikDocuments->first();
        $defaultPasal  = ($firstSprindik && $firstSprindik->pasal_formatted) ? $firstSprindik->pasal_formatted : ($accident->pasal_names ?? 'Pasal 310 UU No. 22 Tahun 2009');
        $defaultDugaan = ($firstSprindik && $firstSprindik->dugaan_formatted) ? $firstSprindik->dugaan_formatted : 'Kecelakaan Lalu Lintas';

        return view('docs.surat-perintah-penghentian-penyidikan-document.create', compact(
            'accidentId',
            'accident',
            'sprindikDocuments',
            'spdpDocuments',
            'skpptDocuments',
            'lhgpDocuments',
            'suspects',
            'documentClassifications',
            'authorizedSignatories',
            'teamOfficers',
            'ranks',
            'positions',
            'masterAlasan',
            'defaultPasal',
            'defaultDugaan'
        ));
    }

    // ─────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────
    public function store(Request $request)
    {
        $accidentId = htmlspecialchars($request->accident_id);

        $validator = $this->validateForm($request);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $documentNumber = htmlspecialchars($request->document_number);
        $documentDate   = htmlspecialchars($request->document_date);

        $exists = SuratPerintahPenghentianPenyidikanDocument::where('accident_id', $accidentId)
            ->where('document_number', 'ILIKE', $documentNumber)
            ->exists();
        if ($exists) {
            return redirect()->back()
                ->withErrors(['document_number' => 'Nomor dokumen "' . $documentNumber . '" sudah ada sebelumnya. Gunakan nomor yang berbeda.'])
                ->withInput();
        }

        $kodeAlasan   = $request->kode_alasan ?? [];
        $signatoryId  = htmlspecialchars($request->signatory);
        $suspects     = $request->suspects ?? [];
        $officerIds   = $request->officers ?? [];
        $carbonCopies = array_values(array_filter($request->carbonCopies ?? [], fn($v) => !empty(trim($v))));

        // Upload attachment jika ada
        $fileName = null;
        if ($request->hasFile('dokumen_digital')) {
            $file = $request->file('dokumen_digital');
            $fileName = time() . '_sprint_henti_' . str_replace(' ', '_', $file->getClientOriginalName());
            $file->move(public_path('documents/attachments/'), $fileName);
        }

        DB::beginTransaction();
        try {
            $sprintHenti = SuratPerintahPenghentianPenyidikanDocument::create([
                'accident_id'                                             => $accidentId,
                'document_number'                                         => $documentNumber,
                'document_date'                                           => $documentDate,
                'document_location'                                       => $request->document_location,
                'document_classification_id'                              => $request->document_classification_id,
                'surat_perintah_penyidikan_document_id'                   => $request->surat_perintah_penyidikan_document_id,
                'surat_pemberitahuan_dimulainya_penyidikan_document_id'   => $request->surat_pemberitahuan_dimulainya_penyidikan_document_id,
                'surat_ketetapan_tentang_penetapan_tersangka_document_id' => $request->surat_ketetapan_tentang_penetapan_tersangka_document_id,
                'laporan_hasil_gelar_perkara_document_id'                  => $request->laporan_hasil_gelar_perkara_document_id,
                'dugaan_tindak_pidana'                                    => $request->dugaan_tindak_pidana,
                'pasal_list'                                              => $request->pasal_list,
                'kode_alasan'                                             => $kodeAlasan,
                'alasan_penghentian'                                      => $request->alasan_penghentian,
                'pertimbangan'                                            => $request->pertimbangan,
                'dasar'                                                   => $request->dasar,
                'untuk'                                                   => $request->untuk,
                'carbon_copies'                                           => $carbonCopies,
                'status_id'                                               => '2', // Dibuat
                'document_category_id'                                    => '0205',
                'created_by_user_id'                                      => Auth::id(),
                'ip_addresses'                                            => [
                    'created' => $request->ip(),
                ],
                'timestamps'                                              => [
                    'created' => Carbon::now()->toDateTimeString(),
                ],
                'messages'                              => [
                    'signatory_id' => $signatoryId,
                    'suspect_ids'  => $suspects,
                    'officer_ids'  => $officerIds,
                ],
            ]);

            // Sync Suspects
            if (!empty($suspects)) {
                $sprintHenti->suspects()->sync($suspects);
            }

            // Simpan Attachment jika ada file
            if ($fileName) {
                $sprintHenti->suratPerintahPenghentianPenyidikanDocumentAttachment()->create([
                    'surat_perintah_penghentian_penyidikan_document_id' => $sprintHenti->id,
                    'name'           => $fileName,
                    'file_path'      => 'documents/attachments/' . $fileName,
                    'file_extension' => pathinfo($fileName, PATHINFO_EXTENSION),
                    'file_size'      => file_exists(public_path('documents/attachments/' . $fileName)) ? filesize(public_path('documents/attachments/' . $fileName)) : 0,
                    'is_active'      => true,
                ]);
            }

            // Simpan Pejabat Penandatangan (SIGNATORY)
            $signatory = Officer::find($signatoryId);
            if ($signatory) {
                $sprintHenti->suratPerintahPenghentianPenyidikanDocumentOfficers()->create([
                    'surat_perintah_penghentian_penyidikan_document_id' => $sprintHenti->id,
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
                    'sort'            => 0,
                ]);
            }

            // Simpan Petugas yang diperintahkan (MEMBERS)
            if (!empty($officerIds)) {
                $sort = 1;
                foreach ($officerIds as $offId) {
                    $officer = Officer::find($offId);
                    if ($officer) {
                        $sprintHenti->suratPerintahPenghentianPenyidikanDocumentOfficers()->create([
                            'surat_perintah_penghentian_penyidikan_document_id' => $sprintHenti->id,
                            'register_number' => $officer->register_number,
                            'first_title'     => $officer->first_title,
                            'first_name'      => $officer->first_name,
                            'last_name'       => $officer->last_name,
                            'last_title'      => $officer->last_title,
                            'rank_id'         => $officer->rank_id,
                            'position_id'     => $officer->position_id,
                            'phone_number'    => $officer->phone_number,
                            'email'           => $officer->email,
                            'police_id'       => $officer->police_id,
                            'status'          => 'PRESENT',
                            'class'           => 'MEMBER',
                            'flag'            => 'INTERNAL',
                            'insert_method'   => 'IMPORT',
                            'sort'            => $sort++,
                        ]);
                    }
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
            ->with('success', 'Surat Perintah Penghentian Penyidikan berhasil dibuat.');
    }

    // ─────────────────────────────────────────────
    // SHOW
    // ─────────────────────────────────────────────
    public function show($id)
    {
        $accidentId   = htmlspecialchars(request()->query('accident_id'));
        $sprintHenti  = SuratPerintahPenghentianPenyidikanDocument::with([
            'accident',
            'accident.polres',
            'accident.polres.polda',
            'accident.suspects',
            'suratPerintahPenyidikanDocument',
            'laporanHasilGelarPerkaraDocument',
            'suratPerintahPenghentianPenyidikanDocumentOfficers',
            'suratPerintahPenghentianPenyidikanDocumentAttachment',
            'documentClassification',
            'status',
        ])->where('id', $id)->firstOrFail();

        $accident = Accident::with(['polres', 'polres.polda'])->where('id', $sprintHenti->accident_id)->first();
        $masterAlasan = self::$masterAlasan;

        $kodeAlasan = is_array($sprintHenti->kode_alasan) ? $sprintHenti->kode_alasan : (json_decode($sprintHenti->kode_alasan, true) ?? []);
        $signatoryOfficer = $sprintHenti->suratPerintahPenghentianPenyidikanDocumentOfficers->firstWhere('class', 'SIGNATORY');
        $memberOfficers   = $sprintHenti->suratPerintahPenghentianPenyidikanDocumentOfficers->where('class', 'MEMBER')->sortBy('sort');

        return view('docs.surat-perintah-penghentian-penyidikan-document.show', compact(
            'accidentId',
            'accident',
            'sprintHenti',
            'masterAlasan',
            'kodeAlasan',
            'signatoryOfficer',
            'memberOfficers'
        ));
    }

    // ─────────────────────────────────────────────
    // EDIT
    // ─────────────────────────────────────────────
    public function edit($id)
    {
        $accidentId   = htmlspecialchars(request()->query('accident_id'));
        $sprintHenti  = SuratPerintahPenghentianPenyidikanDocument::with([
            'suspects',
            'suratPerintahPenghentianPenyidikanDocumentOfficers',
            'suratPerintahPenghentianPenyidikanDocumentAttachment',
        ])->where('id', $id)->firstOrFail();

        $accident = Accident::with(['polres', 'polres.polda'])->where('id', $sprintHenti->accident_id)->firstOrFail();

        $sprindikDocuments = SuratPerintahPenyidikanDocument::with([
            'suratPerintahPenyidikanDocumentLaws.crimeConstitution',
            'suratPerintahPenyidikanDocumentLaws.crimeType',
            'suratPerintahPenyidikanDocumentLaws.crimeClass',
        ])
            ->where('accident_id', $sprintHenti->accident_id)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($sprindikDocuments as $sprindik) {
            $pasalParts = [];
            $crimeTypeParts = [];
            if ($sprindik->suratPerintahPenyidikanDocumentLaws && $sprindik->suratPerintahPenyidikanDocumentLaws->isNotEmpty()) {
                foreach ($sprindik->suratPerintahPenyidikanDocumentLaws as $law) {
                    $chapter = trim($law->constitution_chapter ?? '');
                    $constitutionName = $law->crimeConstitution ? trim($law->crimeConstitution->name) : '';
                    $pasalParts[] = implode(' ', array_filter([$chapter, $constitutionName]));
                    if ($law->crimeType) {
                        $crimeTypeParts[] = trim($law->crimeType->name);
                    }
                }
            }
            $sprindik->pasal_formatted = implode(', ', array_filter(array_unique($pasalParts)));
            $sprindik->dugaan_formatted = implode(', ', array_filter(array_unique($crimeTypeParts)));
        }

        $spdpDocuments = SuratPemberitahuanDimulainyaPenyidikanDocument::where('accident_id', $sprintHenti->accident_id)
            ->orderBy('created_at', 'desc')
            ->get();

        $skpptDocuments = SuratKetetapanTentangPenetapanTersangkaDocument::where('accident_id', $sprintHenti->accident_id)
            ->orderBy('created_at', 'desc')
            ->get();

        $lhgpDocuments = LaporanHasilGelarPerkaraDocument::where('accident_id', $sprintHenti->accident_id)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->orderBy('created_at', 'desc')
            ->get();

        $suspects = Suspect::where('accident_id', $sprintHenti->accident_id)->get();

        $documentClassifications = DocumentClassification::where('is_active', true)
            ->orderBy('sort')
            ->get();

        $getOldNewPolresIds = $this->getOldNewPolresIds($accident->polres_id);

        $authorizedSignatories = Officer::withRelated()
            ->selectFullName()
            ->whereIn('police_id', $getOldNewPolresIds)
            ->whereHasUserActive()
            ->hasDataComplete()
            ->signatory()->active()->valid()
            ->orderBy('first_name')->get();

        $teamOfficers = Officer::withRelated()
            ->selectFullName()
            ->whereIn('police_id', $getOldNewPolresIds)
            ->whereHasUserActive()
            ->hasDataComplete()
            ->member()->active()->valid()
            ->orderBy('first_name')->get();

        $ranks = Rank::where('is_active', true)->orderBy('sort')->get();
        $positions = Position::where('is_active', true)->orderBy('sort')->get();

        $masterAlasan = self::$masterAlasan;
        $kodeAlasan   = is_array($sprintHenti->kode_alasan) ? $sprintHenti->kode_alasan : (json_decode($sprintHenti->kode_alasan, true) ?? []);

        $savedCarbonCopies = is_array($sprintHenti->carbon_copies) ? $sprintHenti->carbon_copies : (json_decode($sprintHenti->carbon_copies, true) ?? []);

        $signatoryDocOfficer = $sprintHenti->suratPerintahPenghentianPenyidikanDocumentOfficers->firstWhere('class', 'SIGNATORY');
        $currentSignatoryId = $sprintHenti->messages['signatory_id'] ?? null;
        if (!$currentSignatoryId && $signatoryDocOfficer) {
            $currentSignatoryId = optional(Officer::where('register_number', $signatoryDocOfficer->register_number)->first())->id;
        }

        $currentOfficerIds = $sprintHenti->messages['officer_ids'] ?? [];
        if (empty($currentOfficerIds)) {
            $regNumbers = $sprintHenti->suratPerintahPenghentianPenyidikanDocumentOfficers->where('class', 'MEMBER')->pluck('register_number')->toArray();
            $currentOfficerIds = Officer::whereIn('register_number', $regNumbers)->pluck('id')->toArray();
        }

        $currentSuspectIds = $sprintHenti->suspects->pluck('id')->toArray();
        if (empty($currentSuspectIds) && !empty($sprintHenti->messages['suspect_ids'])) {
            $currentSuspectIds = $sprintHenti->messages['suspect_ids'];
        }

        $savedKodeAlasan       = $kodeAlasan;
        $savedSignatoryId      = $currentSignatoryId;
        $savedMemberOfficerIds = $currentOfficerIds;
        $savedSuspectIds       = $currentSuspectIds;

        return view('docs.surat-perintah-penghentian-penyidikan-document.edit', compact(
            'accidentId',
            'accident',
            'sprintHenti',
            'sprindikDocuments',
            'spdpDocuments',
            'skpptDocuments',
            'lhgpDocuments',
            'suspects',
            'documentClassifications',
            'authorizedSignatories',
            'teamOfficers',
            'ranks',
            'positions',
            'masterAlasan',
            'kodeAlasan',
            'savedCarbonCopies',
            'currentSignatoryId',
            'currentOfficerIds',
            'currentSuspectIds',
            'savedKodeAlasan',
            'savedSignatoryId',
            'savedMemberOfficerIds',
            'savedSuspectIds'
        ));
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────
    public function update(Request $request, $id)
    {
        $sprintHenti = SuratPerintahPenghentianPenyidikanDocument::findOrFail($id);
        $accidentId  = $sprintHenti->accident_id;

        $validator = $this->validateForm($request, $id);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $documentNumber = htmlspecialchars($request->document_number);
        $documentDate   = htmlspecialchars($request->document_date);

        $kodeAlasan   = $request->kode_alasan ?? [];
        $signatoryId  = htmlspecialchars($request->signatory);
        $suspects     = $request->suspects ?? [];
        $officerIds   = $request->officers ?? [];
        $carbonCopies = array_values(array_filter($request->carbonCopies ?? [], fn($v) => !empty(trim($v))));

        DB::beginTransaction();
        try {
            $sprintHenti->update([
                'document_number'                                         => $documentNumber,
                'document_date'                                           => $documentDate,
                'document_location'                                       => $request->document_location,
                'document_classification_id'                              => $request->document_classification_id,
                'surat_perintah_penyidikan_document_id'                   => $request->surat_perintah_penyidikan_document_id,
                'surat_pemberitahuan_dimulainya_penyidikan_document_id'   => $request->surat_pemberitahuan_dimulainya_penyidikan_document_id,
                'surat_ketetapan_tentang_penetapan_tersangka_document_id' => $request->surat_ketetapan_tentang_penetapan_tersangka_document_id,
                'laporan_hasil_gelar_perkara_document_id'                  => $request->laporan_hasil_gelar_perkara_document_id,
                'dugaan_tindak_pidana'                                    => $request->dugaan_tindak_pidana,
                'pasal_list'                                              => $request->pasal_list,
                'kode_alasan'                                             => $kodeAlasan,
                'alasan_penghentian'                                      => $request->alasan_penghentian,
                'pertimbangan'                                            => $request->pertimbangan,
                'dasar'                                                   => $request->dasar,
                'untuk'                                                   => $request->untuk,
                'carbon_copies'                                           => $carbonCopies,
                'updated_by_user_id'                                      => Auth::id(),
                'messages'                                                => array_merge($sprintHenti->messages ?? [], [
                    'signatory_id' => $signatoryId,
                    'suspect_ids'  => $suspects,
                    'officer_ids'  => $officerIds,
                ]),
            ]);

            // Sync Suspects
            $sprintHenti->suspects()->sync($suspects);

            // Re-sync Officers
            $sprintHenti->suratPerintahPenghentianPenyidikanDocumentOfficers()->delete();

            $signatory = Officer::find($signatoryId);
            if ($signatory) {
                $sprintHenti->suratPerintahPenghentianPenyidikanDocumentOfficers()->create([
                    'surat_perintah_penghentian_penyidikan_document_id' => $sprintHenti->id,
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
                    'sort'            => 0,
                ]);
            }

            if (!empty($officerIds)) {
                $sort = 1;
                foreach ($officerIds as $offId) {
                    $officer = Officer::find($offId);
                    if ($officer) {
                        $sprintHenti->suratPerintahPenghentianPenyidikanDocumentOfficers()->create([
                            'surat_perintah_penghentian_penyidikan_document_id' => $sprintHenti->id,
                            'register_number' => $officer->register_number,
                            'first_title'     => $officer->first_title,
                            'first_name'      => $officer->first_name,
                            'last_name'       => $officer->last_name,
                            'last_title'      => $officer->last_title,
                            'rank_id'         => $officer->rank_id,
                            'position_id'     => $officer->position_id,
                            'phone_number'    => $officer->phone_number,
                            'email'           => $officer->email,
                            'police_id'       => $officer->police_id,
                            'status'          => 'PRESENT',
                            'class'           => 'MEMBER',
                            'flag'            => 'INTERNAL',
                            'insert_method'   => 'IMPORT',
                            'sort'            => $sort++,
                        ]);
                    }
                }
            }

            // File upload jika ada
            if ($request->hasFile('dokumen_digital')) {
                $file = $request->file('dokumen_digital');
                $fileName = time() . '_sprint_henti_' . str_replace(' ', '_', $file->getClientOriginalName());
                $file->move(public_path('documents/attachments/'), $fileName);

                $sprintHenti->suratPerintahPenghentianPenyidikanDocumentAttachment()->updateOrCreate(
                    ['surat_perintah_penghentian_penyidikan_document_id' => $sprintHenti->id],
                    [
                        'name'           => $fileName,
                        'file_path'      => 'documents/attachments/' . $fileName,
                        'file_extension' => pathinfo($fileName, PATHINFO_EXTENSION),
                        'file_size'      => file_exists(public_path('documents/attachments/' . $fileName)) ? filesize(public_path('documents/attachments/' . $fileName)) : 0,
                        'is_active'      => true,
                    ]
                );
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
            ->with('success', 'Surat Perintah Penghentian Penyidikan berhasil diperbarui.');
    }

    // ─────────────────────────────────────────────
    // DELETE
    // ─────────────────────────────────────────────
    public function delete($id)
    {
        $sprintHenti = SuratPerintahPenghentianPenyidikanDocument::findOrFail($id);
        $accidentId  = $sprintHenti->accident_id;

        $sprintHenti->delete();

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
            ->with('success', 'Surat Perintah Penghentian Penyidikan berhasil dihapus.');
    }

    // ─────────────────────────────────────────────
    // DOWNLOAD WORD
    // ─────────────────────────────────────────────
    public function download($id)
    {
        $sprintHenti = SuratPerintahPenghentianPenyidikanDocument::with([
            'accident',
            'accident.polres',
            'accident.polres.polda',
            'accident.suspects',
            'accident.suspects.gender',
            'accident.suspects.job',
            'accident.suspects.country',
            'suratPerintahPenyidikanDocument',
            'suratPemberitahuanDimulainyaPenyidikanDocument',
            'suratKetetapanTentangPenetapanTersangkaDocument',
            'laporanHasilGelarPerkaraDocument',
            'suspects',
            'suspects.gender',
            'suspects.job',
            'suspects.country',
            'suratPerintahPenghentianPenyidikanDocumentOfficers',
        ])->where('id', $id)->firstOrFail();

        $accident = $sprintHenti->accident;
        $polres   = $accident->polres ?? null;
        $polda    = $polres->polda ?? null;

        $daerahPoliceFullName = $polda ? strtoupper($polda->name ?? $polda->full_name) : '-';
        $resorPoliceFullName  = $polres ? strtoupper($polres->name ?? $polres->full_name) : '-';
        $resorPoliceAddress   = $polres ? ucwords($polres->address ?? '') : '-';

        $documentNumber   = $sprintHenti->document_number ?? '-';
        $documentLocation = $sprintHenti->document_location ?: ($polres->polres_regency ?? ($polres->name ?? '-'));
        $accidentNumber   = $accident->no_lp ?? '-';
        $accidentDate     = $accident->accident_date ? Carbon::parse($accident->accident_date)->locale('id')->isoFormat('D MMMM Y') : '-';

        // Sprindik Dasar
        $sprindik = $sprintHenti->suratPerintahPenyidikanDocument;
        $suratPerintahPenyidikanDocumentNumber = $sprindik->document_number ?? '-';
        $suratPerintahPenyidikanDocumentDate   = $sprindik && $sprindik->document_date ? Carbon::parse($sprindik->document_date)->locale('id')->isoFormat('D MMMM Y') : '-';

        // SPDP Dasar
        $spdp = $sprintHenti->suratPemberitahuanDimulainyaPenyidikanDocument ?: SuratPemberitahuanDimulainyaPenyidikanDocument::where('accident_id', $accident->id)->latest()->first();
        $suratPemberitahuanDimulainyaPenyidikanDocumentNumber = $spdp->document_number ?? ($sprintHenti->no_spdp ?? '-');
        $suratPemberitahuanDimulainyaPenyidikanDocumentDate   = $spdp && $spdp->document_date ? Carbon::parse($spdp->document_date)->locale('id')->isoFormat('D MMMM Y') : '-';

        // SK Penetapan Tersangka Dasar
        $skppt = $sprintHenti->suratKetetapanTentangPenetapanTersangkaDocument ?: SuratKetetapanTentangPenetapanTersangkaDocument::where('accident_id', $accident->id)->latest()->first();
        $suratKetetapanPenetapanTersangkaDocumentNumber = $skppt->document_number ?? '-';
        $suratKetetapanPenetapanTersangkaDocumentDate   = $skppt && $skppt->document_date ? Carbon::parse($skppt->document_date)->locale('id')->isoFormat('D MMMM Y') : '-';

        // Suspect
        $primarySuspect = $sprintHenti->suspects->first() ?? ($accident->suspects ? $accident->suspects->first() : null);
        $suspectName           = $primarySuspect ? $primarySuspect->name : '-';
        $suspectIdentityNumber = $primarySuspect ? ($primarySuspect->nik ?? '-') : '-';
        $suspectNationality    = 'Indonesia';
        if ($primarySuspect) {
            if (is_object($primarySuspect->nationality)) {
                $suspectNationality = $primarySuspect->nationality->name ?? 'Indonesia';
            } elseif (!empty($primarySuspect->nationality)) {
                $suspectNationality = $primarySuspect->nationality;
            } elseif ($primarySuspect->country) {
                $suspectNationality = $primarySuspect->country->name ?? 'Indonesia';
            }
        }
        $suspectGenderName     = $primarySuspect && $primarySuspect->gender ? $primarySuspect->gender->name : ($primarySuspect && $primarySuspect->gender_id == 1 ? 'Laki-laki' : ($primarySuspect && $primarySuspect->gender_id == 2 ? 'Perempuan' : '-'));
        $suspectBirthPlace     = $primarySuspect ? ($primarySuspect->birth_place ?? '-') : '-';
        $suspectBirthDate      = $primarySuspect && $primarySuspect->birth_date ? Carbon::parse($primarySuspect->birth_date)->locale('id')->isoFormat('D MMMM Y') : '-';
        $suspectJobName        = $primarySuspect && $primarySuspect->job ? $primarySuspect->job->name : '-';
        $suspectFullAddress    = $primarySuspect ? ($primarySuspect->address ?? '-') : '-';

        $dugaanTindakPidana = $sprintHenti->dugaan_tindak_pidana ?: 'Kecelakaan Lalu Lintas';
        $pasalList          = $sprintHenti->pasal_list ?: ($accident->pasal_names ?? 'Pasal 310 UU No. 22 Tahun 2009');

        // Alasan
        $kodeAlasan = is_array($sprintHenti->kode_alasan) ? $sprintHenti->kode_alasan : (json_decode($sprintHenti->kode_alasan, true) ?? []);
        $alasanList = [];
        foreach ($kodeAlasan as $kd) {
            if (isset(self::$masterAlasan[$kd])) {
                $alasanList[] = self::$masterAlasan[$kd];
            }
        }
        $alasanPenghentian = !empty($alasanList) ? implode('; ', $alasanList) : ($sprintHenti->alasan_penghentian ?: 'Keadilan Restoratif');

        $documentDate = $sprintHenti->document_date ? Carbon::parse($sprintHenti->document_date)->locale('id')->isoFormat('D MMMM Y') : '-';

        // Signatory (Yang Memberi Perintah)
        $signatory = $sprintHenti->suratPerintahPenghentianPenyidikanDocumentOfficers->firstWhere('class', 'SIGNATORY');
        $signatoryPositionId = $signatory ? (is_array($signatory->position) ? ($signatory->position['id'] ?? null) : $signatory->position_id) : null;
        $signatoryPositionDetail = $signatoryPositionId
            ? \App\Models\Lib\Position::with('positionCluster')->find($signatoryPositionId)
            : null;

        $polresFullName = $polres->full_name ?? ($accident->polres->full_name ?? '');
        $poldaFullName  = $accident->polres->polda->full_name ?? ($polres->polda->full_name ?? '');
        $signatoryHeadText     = 'a.n. KEPALA KEPOLISIAN RESOR ' . $polresFullName;
        $signatoryPositionName = '';
        if ($signatoryPositionDetail) {
            if ($signatoryPositionDetail->position_cluster_id == '1') {
                $signatoryHeadText     = 'KEPALA KEPOLISIAN RESOR ' . $polresFullName;
                $signatoryPositionName = '';
            } elseif ($signatoryPositionDetail->position_cluster_id == '9') {
                $signatoryHeadText     = 'a.n. DIREKTUR LALU LINTAS POLDA ' . $poldaFullName;
                $signatoryPositionName = $signatoryPositionDetail->positionCluster->alias_name ?? $signatoryPositionDetail->name;
            } else {
                $signatoryPositionName = $signatoryPositionDetail->positionCluster->alias_name ?? $signatoryPositionDetail->name;
            }
        }
        if (!$signatoryPositionName && $signatoryPositionDetail && $signatoryPositionDetail->position_cluster_id != '1') {
            $signatoryPositionName = 'KASAT LANTAS';
        }

        $signatoryName = trim(implode(' ', array_filter([
            $signatory->first_title ?? '',
            $signatory->first_name ?? '',
            $signatory->last_name ?? '',
            $signatory->last_title ?? ''
        ])));
        $signatoryName = $signatoryName ?: '-';

        $signatoryRank = $signatory && $signatory->rank_id ? Rank::find($signatory->rank_id) : ($signatory->rank ?? null);
        $signatoryRankName = $signatoryRank->full_name ?? ($signatoryRank->name ?? '');
        $signatoryRegisterNumber = $signatory->register_number ?? '-';

        $signatoryPositionHeadText = $signatoryPositionName;

        // Officers (MEMBER)
        $members = $sprintHenti->suratPerintahPenghentianPenyidikanDocumentOfficers
            ->where('class', 'MEMBER')
            ->sortBy('sort')
            ->values();

        // Yang Menerima Perintah (Petugas Penyidik Pertama)
        $firstMember = $members->first();
        if ($firstMember) {
            $memRank = $firstMember->rank_id ? Rank::find($firstMember->rank_id) : ($firstMember->rank ?? null);
            $rankName = $memRank->full_name ?? ($memRank->name ?? ($firstMember->rank_id ?? '-'));
            $receiverOfficerName           = trim(implode(' ', array_filter([
                $firstMember->first_title ?? '',
                $firstMember->first_name ?? '',
                $firstMember->last_name ?? '',
                $firstMember->last_title ?? ''
            ]))) ?: '-';
            $receiverOfficerRankName       = strtoupper($rankName);
            $receiverOfficerRegisterNumber = $firstMember->register_number ?? '-';
        } else {
            $receiverOfficerName           = '-';
            $receiverOfficerRankName       = '-';
            $receiverOfficerRegisterNumber = '-';
        }

        $blockOfficers = [];
        $i = 0;
        foreach ($members as $mem) {
            $i++;
            $memRankObj = $mem->rank_id ? Rank::find($mem->rank_id) : ($mem->rank ?? null);
            $rankName   = $memRankObj->name ?? ($memRankObj->full_name ?? ($mem->rank_id ?? '-'));
            $posObj     = $mem->position_id ? Position::find($mem->position_id) : ($mem->position ?? null);
            $posName    = $posObj->name ?? ($mem->position_id ?? '-');
            $blockOfficers[] = [
                'number'     => $i,
                'first_name' => ($mem->first_title ? $mem->first_title . ' ' : '') . ($mem->first_name ?? ''),
                'last_name'  => ($mem->last_name ?? '') . ($mem->last_title ? ', ' . $mem->last_title : ''),
                'rank_id'    => $rankName,
                'officer_id' => $mem->register_number ?? '-',
                'position'   => $posName,
            ];
        }

        if (empty($blockOfficers)) {
            $blockOfficers[] = [
                'number'     => 1,
                'first_name' => '-',
                'last_name'  => '',
                'rank_id'    => '-',
                'officer_id' => '-',
                'position'   => '-',
            ];
        }

        // Carbon Copies (Tembusan)
        $carbonCopies = is_array($sprintHenti->carbon_copies) ? $sprintHenti->carbon_copies : (json_decode($sprintHenti->carbon_copies, true) ?? []);
        $blockCarbonCopies = [];
        $ccNo = 1;
        foreach ($carbonCopies as $cc) {
            $blockCarbonCopies[] = [
                'carbon_copy_iteration' => $ccNo++,
                'carbon_copy_name'      => $cc,
            ];
        }

        $templatePath = file_exists(public_path('word-template/surat_perintah_penghentian_penyidikan_v2.docx'))
            ? public_path('word-template/surat_perintah_penghentian_penyidikan_v2.docx')
            : public_path('word-template/surat_perintah_penghentian_penyidikan.docx');

        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($templatePath);

        if (count($blockOfficers) > 0) {
            $templateProcessor->cloneBlock('block_officers', 0, true, false, $blockOfficers);
        } else {
            $templateProcessor->cloneBlock('block_officers', 0);
        }

        if (count($blockCarbonCopies) > 0) {
            $templateProcessor->cloneBlock('block_carbon_copies', 0, true, false, $blockCarbonCopies);
        } else {
            $templateProcessor->cloneBlock('block_carbon_copies', 0);
        }

        $templateProcessor->setValues([
            'daerahPoliceFullName'                                 => $daerahPoliceFullName,
            'resorPoliceFullName'                                  => $resorPoliceFullName,
            'resorPoliceAddress'                                   => $resorPoliceAddress,
            'documentNumber'                                       => $documentNumber,
            'documentLocation'                                     => $documentLocation,
            'accidentNumber'                                       => $accidentNumber,
            'accidentDate'                                         => $accidentDate,
            'suratPerintahPenyidikanDocumentNumber'                => $suratPerintahPenyidikanDocumentNumber,
            'suratPerintahPenyidikanDocumentDate'                  => $suratPerintahPenyidikanDocumentDate,
            'suratPemberitahuanDimulainyaPenyidikanDocumentNumber' => $suratPemberitahuanDimulainyaPenyidikanDocumentNumber,
            'suratPemberitahuanDimulainyaPenyidikanDocumentDate'   => $suratPemberitahuanDimulainyaPenyidikanDocumentDate,
            'suratKetetapanPenetapanTersangkaDocumentNumber'       => $suratKetetapanPenetapanTersangkaDocumentNumber,
            'suratKetetapanPenetapanTersangkaDocumentDate'         => $suratKetetapanPenetapanTersangkaDocumentDate,
            'suspectName'                                          => $suspectName,
            'dugaanTindakPidana'                                   => $dugaanTindakPidana,
            'pasalList'                                            => $pasalList,
            'suspectIdentityNumber'                                => $suspectIdentityNumber,
            'suspectBirthPlace'                                    => $suspectBirthPlace,
            'suspectBirthDate'                                     => $suspectBirthDate,
            'suspectGenderName'                                    => $suspectGenderName,
            'suspectJobName'                                       => $suspectJobName,
            'suspectNationality'                                   => $suspectNationality,
            'suspectFullAddress'                                   => $suspectFullAddress,
            'alasanPenghentian'                                    => $alasanPenghentian,
            'signatoryHeadText'                                    => $signatoryHeadText,
            'signatoryPositionName'                                => $signatoryPositionName,
            'signatoryPositionHeadText'                            => $signatoryPositionHeadText,
            'signatoryName'                                        => strtoupper($signatoryName),
            'signatoryRankName'                                    => strtoupper($signatoryRankName),
            'signatoryRegisterNumber'                              => $signatoryRegisterNumber,
            'receiverOfficerName'                                  => strtoupper($receiverOfficerName),
            'receiverOfficerRankName'                              => $receiverOfficerRankName,
            'receiverOfficerRegisterNumber'                        => $receiverOfficerRegisterNumber,
            'documentDate'                                         => $documentDate,
        ]);

        $filename = 'generate/' . $sprintHenti->id . ' - Surat Perintah Penghentian Penyidikan - ' . ($polres->full_name ?? '');
        $savePath = public_path($filename . '.docx');

        // Pastikan folder generate ada
        if (!file_exists(public_path('generate'))) {
            mkdir(public_path('generate'), 0777, true);
        }

        $templateProcessor->saveAs($savePath);
        return response()->download($savePath)->deleteFileAfterSend(true);
    }

    // ─────────────────────────────────────────────
    // VALIDATE REQUEST FORM (AJAX)
    // ─────────────────────────────────────────────
    public function validateRequestForm(Request $request)
    {
        $validator = $this->validateForm($request, $request->id);
        if ($validator->fails()) {
            return response()->json([
                'code'    => '422',
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }
        return response()->json(['success' => true, 'message' => 'Data valid, dokumen siap disimpan.']);
    }

    private function validateForm(Request $request, $id = null)
    {
        return Validator::make($request->all(), [
            'document_number' => 'required|string|max:255',
            'document_date'   => 'required|date',
            'signatory'       => 'required',
            'kode_alasan'     => 'required|array|min:1',
        ], [
            'document_number.required' => 'Nomor surat perintah wajib diisi.',
            'document_date.required'   => 'Tanggal surat perintah wajib diisi.',
            'signatory.required'       => 'Pejabat penandatangan / pemberi perintah wajib dipilih.',
            'kode_alasan.required'     => 'Alasan penghentian penyidikan wajib dipilih minimal 1.',
        ]);
    }
}

