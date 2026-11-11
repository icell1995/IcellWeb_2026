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
use App\Models\Doc\SuratPerintahPenyelidikanDocument\SuratPerintahPenyelidikanDocument;
use App\Models\Doc\SuratPerintahPenghentianPenyelidikanDocument\SuratPerintahPenghentianPenyelidikanDocument;
use App\Models\Doc\LaporanHasilGelarPerkaraDocument\LaporanHasilGelarPerkaraDocument;
use App\Models\Doc\SuratKetetapanPenghentianPenyelidikanDocument\SuratKetetapanPenghentianPenyelidikanDocument;
use App\Models\Doc\SuratKetetapanPenghentianPenyelidikanDocument\SuratKetetapanPenghentianPenyelidikanDocumentOfficer;
use App\Models\Doc\SuratKetetapanPenghentianPenyelidikanDocument\SuratKetetapanPenghentianPenyelidikanDocumentAttachment;
use App\Traits\DocsOfficersTraits;

class SuratKetetapanPenghentianPenyelidikanDocumentController extends Controller
{
    protected $docService;

    use DocsOfficersTraits;

    // Master alasan penghentian penyelidikan (sesuai SOP Mindik Polri & Perpol)
    public static $masterAlasan = [
        1  => 'Peristiwa yang dimuat dalam hasil penyelidikan bukan merupakan tindak pidana',
        2  => 'Tidak terdapat cukup alat bukti',
        3  => 'Penyelidikan dihentikan demi hukum',
        4  => 'Terdapat putusan pengadilan yang telah memperoleh kekuatan hukum tetap terhadap terlapor/tersangka atas perkara yang sama',
        5  => 'Kedaluarsa',
        6  => 'Terlapor/tersangka meninggal dunia',
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

        // Dokumen Sprinlidik
        $sprinlidikDocuments = SuratPerintahPenyelidikanDocument::where('accident_id', $accidentId)
            ->orderBy('created_at', 'desc')
            ->get();

        // Dokumen Surat Perintah Penghentian Penyelidikan (Sprint Henti Lidik - 0113)
        $sprintHentiDocuments = SuratPerintahPenghentianPenyelidikanDocument::with(['suspects'])
            ->where('accident_id', $accidentId)
            ->orderBy('created_at', 'desc')
            ->get();

        // Dokumen Laporan Hasil Gelar Perkara (LHGP)
        $lhgpDocuments = LaporanHasilGelarPerkaraDocument::where('accident_id', $accidentId)
            ->orderBy('created_at', 'desc')
            ->get();

        // Pre-fill defaults from latest Sprint Henti Lidik jika ada
        $latestSprintHenti = $sprintHentiDocuments->first();

        $defaultSprintHentiId = $latestSprintHenti ? $latestSprintHenti->id : null;
        $defaultKodeAlasan    = $latestSprintHenti ? (is_array($latestSprintHenti->kode_alasan) ? $latestSprintHenti->kode_alasan : (json_decode($latestSprintHenti->kode_alasan, true) ?? [])) : [1];
        $defaultAlasan        = $latestSprintHenti ? $latestSprintHenti->alasan_penghentian : 'Peristiwa tersebut bukan merupakan tindak pidana';
        $defaultDugaan        = $latestSprintHenti ? ($latestSprintHenti->dugaan_tindak_pidana ?: 'Kecelakaan Lalu Lintas') : 'Kecelakaan Lalu Lintas';
        $defaultPasal         = $latestSprintHenti ? ($latestSprintHenti->pasal_list ?: 'Pasal 310 UU No. 22 Tahun 2009') : 'Pasal 310 UU No. 22 Tahun 2009';
        $defaultSprinlidikId  = $latestSprintHenti ? $latestSprintHenti->surat_perintah_penyelidikan_document_id : ($sprinlidikDocuments->first()->id ?? null);
        $defaultLhgpId        = $latestSprintHenti ? $latestSprintHenti->laporan_hasil_gelar_perkara_document_id : ($lhgpDocuments->first()->id ?? null);
        $defaultSuspectIds    = ($latestSprintHenti && $latestSprintHenti->suspects) ? $latestSprintHenti->suspects->pluck('id')->toArray() : [];
        $defaultSignatoryId   = $latestSprintHenti ? ($latestSprintHenti->messages['signatory_id'] ?? null) : null;

        // Pihak terkait / Terlapor / Pengemudi
        $suspects = Suspect::where('accident_id', $accidentId)->get();

        $documentClassifications = DocumentClassification::where('is_active', true)
            ->orderBy('sort')
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

        $masterAlasan = self::$masterAlasan;

        $defaultReporter = $accident->reporter_name ?? '';
        if (empty($defaultReporter)) {
            $isLpModelA = str_contains(strtoupper($accident->no_lp ?? ''), '/A/') || str_contains(strtoupper($accident->no_lp ?? ''), '-A/');
            if ($isLpModelA && (!empty($accident->officer_first_name) || !empty($accident->officer_last_name))) {
                $defaultReporter = trim(($accident->officer_first_name ?? '') . ' ' . ($accident->officer_last_name ?? ''));
            } else {
                $reporterObj = \App\Models\ReportingPerson::where('accident_id', $accidentId)->first();
                if ($reporterObj && !empty($reporterObj->name)) {
                    $defaultReporter = trim($reporterObj->name);
                }
            }
        }
        if (empty($defaultReporter)) {
            $defaultReporter = 'Pelapor';
        }

        return view('docs.surat-ketetapan-penghentian-penyelidikan-document.create', compact(
            'accidentId',
            'accident',
            'sprinlidikDocuments',
            'sprintHentiDocuments',
            'lhgpDocuments',
            'suspects',
            'documentClassifications',
            'authorizedSignatories',
            'masterAlasan',
            'defaultSprintHentiId',
            'defaultKodeAlasan',
            'defaultAlasan',
            'defaultDugaan',
            'defaultPasal',
            'defaultSprinlidikId',
            'defaultLhgpId',
            'defaultSuspectIds',
            'defaultSignatoryId',
            'defaultReporter'
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
        $effectiveDate  = htmlspecialchars($request->effective_date ?? $documentDate);

        $exists = SuratKetetapanPenghentianPenyelidikanDocument::where('accident_id', $accidentId)
            ->where('document_number', 'ILIKE', $documentNumber)
            ->exists();
        if ($exists) {
            return redirect()->back()
                ->withErrors(['document_number' => 'Nomor ketetapan "' . $documentNumber . '" sudah ada sebelumnya.'])
                ->withInput();
        }

        $kodeAlasan   = $request->kode_alasan ?? [];
        $signatoryId  = htmlspecialchars($request->signatory);
        $suspects     = $request->suspects ?? [];

        // Upload attachment jika ada
        $fileName = null;
        if ($request->hasFile('dokumen_digital')) {
            $file = $request->file('dokumen_digital');
            $fileName = time() . '_sket_henti_lidik_' . str_replace(' ', '_', $file->getClientOriginalName());
            $file->move(public_path('documents/attachments/'), $fileName);
        }

        // Tembusan (Carbon copies)
        $carbonCopies = array_values(array_filter($request->carbon_copies ?? []));

        $sprinlidikId = $request->surat_perintah_penyelidikan_document_id;
        if (empty($sprinlidikId)) {
            $defaultSprinlidik = SuratPerintahPenyelidikanDocument::where('accident_id', $accidentId)->latest()->first();
            $sprinlidikId = $defaultSprinlidik ? $defaultSprinlidik->id : null;
        }

        $sprintHentiId = $request->surat_perintah_penghentian_penyelidikan_document_id;
        if (empty($sprintHentiId)) {
            $defaultSprintHenti = SuratPerintahPenghentianPenyelidikanDocument::where('accident_id', $accidentId)->latest()->first();
            $sprintHentiId = $defaultSprintHenti ? $defaultSprintHenti->id : null;
        }

        $lhgpId = $request->laporan_hasil_gelar_perkara_document_id;
        if (empty($lhgpId)) {
            $defaultLhgp = LaporanHasilGelarPerkaraDocument::where('accident_id', $accidentId)->latest()->first();
            $lhgpId = $defaultLhgp ? $defaultLhgp->id : null;
        }

        DB::beginTransaction();
        try {
            $sketHenti = SuratKetetapanPenghentianPenyelidikanDocument::create([
                'accident_id'                                           => $accidentId,
                'document_number'                                       => $documentNumber,
                'document_date'                                         => $documentDate,
                'effective_date'                                        => $effectiveDate,
                'document_location'                                     => $request->document_location,
                'document_classification_id'                            => $request->document_classification_id,
                'surat_perintah_penyelidikan_document_id'                 => $sprinlidikId,
                'surat_perintah_penghentian_penyelidikan_document_id'     => $sprintHentiId,
                'laporan_hasil_gelar_perkara_document_id'               => $lhgpId,
                'kode_alasan'                                           => $kodeAlasan,
                'alasan_penghentian'                                    => $request->alasan_penghentian,
                'dugaan_tindak_pidana'                                  => $request->dugaan_tindak_pidana,
                'pasal_list'                                            => $request->pasal_list,
                'menimbang'                                             => $request->menimbang,
                'mengingat'                                             => $request->mengingat,
                'memperhatikan'                                         => $request->memperhatikan,
                'memutuskan'                                            => $request->memutuskan,
                'menetapkan'                                            => $request->menetapkan,
                'carbon_copies'                                         => $carbonCopies,
                'status_id'                                             => '2', // Dibuat
                'document_category_id'                                  => '0112',
                'created_by_user_id'                                    => Auth::id(),
                'ip_addresses'                                          => [
                    'created' => $request->ip(),
                ],
                'timestamps'                                            => [
                    'created' => Carbon::now()->toDateTimeString(),
                ],
                'messages'                                              => [
                    'signatory_id' => $signatoryId,
                    'suspect_ids'  => $suspects,
                    'pelapor_name' => $request->pelapor_name,
                ],
            ]);

            // Sync Suspects / Terlapor
            if (!empty($suspects)) {
                $sketHenti->suspects()->sync($suspects);
            }

            // Simpan Attachment
            if ($fileName) {
                $sketHenti->suratKetetapanPenghentianPenyelidikanDocumentAttachment()->create([
                    'surat_ketetapan_penghentian_penyelidikan_document_id' => $sketHenti->id,
                    'name'           => $fileName,
                    'file_path'      => 'documents/attachments/' . $fileName,
                    'file_extension' => pathinfo($fileName, PATHINFO_EXTENSION),
                    'file_size'      => file_exists(public_path('documents/attachments/' . $fileName)) ? filesize(public_path('documents/attachments/' . $fileName)) : 0,
                    'is_active'      => true,
                ]);
            }

            // Simpan Signatory (Penandatangan - Selaku Penyidik)
            $signatory = Officer::find($signatoryId);
            if ($signatory) {
                $sketHenti->suratKetetapanPenghentianPenyelidikanDocumentOfficers()->create([
                    'surat_ketetapan_penghentian_penyelidikan_document_id' => $sketHenti->id,
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

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
            ->with('success', 'Surat Ketetapan Tentang Penghentian Penyelidikan berhasil dibuat.');
    }

    // ─────────────────────────────────────────────
    // SHOW
    // ─────────────────────────────────────────────
    public function show($id)
    {
        $sketHenti = SuratKetetapanPenghentianPenyelidikanDocument::with([
            'accident',
            'accident.polres',
            'accident.polres.polda',
            'classification',
            'suratPerintahPenyelidikanDocument',
            'suratPerintahPenghentianPenyelidikanDocument',
            'laporanHasilGelarPerkaraDocument',
            'suspects',
            'suratKetetapanPenghentianPenyelidikanDocumentOfficers',
            'suratKetetapanPenghentianPenyelidikanDocumentAttachment',
        ])->where('id', $id)->firstOrFail();

        $masterAlasan = self::$masterAlasan;

        return view('docs.surat-ketetapan-penghentian-penyelidikan-document.show', compact('sketHenti', 'masterAlasan'));
    }

    // ─────────────────────────────────────────────
    // EDIT
    // ─────────────────────────────────────────────
    public function edit($id)
    {
        $sketHenti = SuratKetetapanPenghentianPenyelidikanDocument::with([
            'accident',
            'accident.polres',
            'accident.polres.polda',
            'suspects',
            'suratKetetapanPenghentianPenyelidikanDocumentOfficers',
            'suratKetetapanPenghentianPenyelidikanDocumentAttachment',
        ])->where('id', $id)->firstOrFail();

        $accidentId = $sketHenti->accident_id;
        $accident   = $sketHenti->accident;

        $sprinlidikDocuments = SuratPerintahPenyelidikanDocument::where('accident_id', $accidentId)
            ->orderBy('created_at', 'desc')
            ->get();

        $sprintHentiDocuments = SuratPerintahPenghentianPenyelidikanDocument::with(['suspects'])
            ->where('accident_id', $accidentId)
            ->orderBy('created_at', 'desc')
            ->get();

        $lhgpDocuments = LaporanHasilGelarPerkaraDocument::where('accident_id', $accidentId)
            ->orderBy('created_at', 'desc')
            ->get();

        $suspects = Suspect::where('accident_id', $accidentId)->get();

        $documentClassifications = DocumentClassification::where('is_active', true)
            ->orderBy('sort')
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

        $masterAlasan = self::$masterAlasan;

        $selectedKodeAlasan = is_array($sketHenti->kode_alasan) ? $sketHenti->kode_alasan : (json_decode($sketHenti->kode_alasan, true) ?? []);
        $selectedSuspectIds = $sketHenti->suspects->pluck('id')->toArray();
        $signatoryOfficer   = $sketHenti->suratKetetapanPenghentianPenyelidikanDocumentOfficers->firstWhere('class', 'SIGNATORY');
        $selectedSignatoryId= $signatoryOfficer ? ($signatoryOfficer->police_id ? Officer::where('register_number', $signatoryOfficer->register_number)->value('id') : null) : null;

        return view('docs.surat-ketetapan-penghentian-penyelidikan-document.edit', compact(
            'sketHenti',
            'accidentId',
            'accident',
            'sprinlidikDocuments',
            'sprintHentiDocuments',
            'lhgpDocuments',
            'suspects',
            'documentClassifications',
            'authorizedSignatories',
            'masterAlasan',
            'selectedKodeAlasan',
            'selectedSuspectIds',
            'selectedSignatoryId'
        ));
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────
    public function update(Request $request, $id)
    {
        $sketHenti = SuratKetetapanPenghentianPenyelidikanDocument::findOrFail($id);
        $accidentId = $sketHenti->accident_id;

        $validator = $this->validateForm($request, $id);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $documentNumber = htmlspecialchars($request->document_number);
        $documentDate   = htmlspecialchars($request->document_date);
        $effectiveDate  = htmlspecialchars($request->effective_date ?? $documentDate);

        $exists = SuratKetetapanPenghentianPenyelidikanDocument::where('accident_id', $accidentId)
            ->where('document_number', 'ILIKE', $documentNumber)
            ->where('id', '!=', $id)
            ->exists();
        if ($exists) {
            return redirect()->back()
                ->withErrors(['document_number' => 'Nomor ketetapan "' . $documentNumber . '" sudah digunakan dokumen lain.'])
                ->withInput();
        }

        $kodeAlasan   = $request->kode_alasan ?? [];
        $signatoryId  = htmlspecialchars($request->signatory);
        $suspects     = $request->suspects ?? [];
        $carbonCopies = array_values(array_filter($request->carbon_copies ?? []));

        $sprinlidikId = $request->surat_perintah_penyelidikan_document_id;
        if (empty($sprinlidikId)) {
            $defaultSprinlidik = SuratPerintahPenyelidikanDocument::where('accident_id', $accidentId)->latest()->first();
            $sprinlidikId = $defaultSprinlidik ? $defaultSprinlidik->id : null;
        }

        $sprintHentiId = $request->surat_perintah_penghentian_penyelidikan_document_id;
        if (empty($sprintHentiId)) {
            $defaultSprintHenti = SuratPerintahPenghentianPenyelidikanDocument::where('accident_id', $accidentId)->latest()->first();
            $sprintHentiId = $defaultSprintHenti ? $defaultSprintHenti->id : null;
        }

        $lhgpId = $request->laporan_hasil_gelar_perkara_document_id;
        if (empty($lhgpId)) {
            $defaultLhgp = LaporanHasilGelarPerkaraDocument::where('accident_id', $accidentId)->latest()->first();
            $lhgpId = $defaultLhgp ? $defaultLhgp->id : null;
        }

        DB::beginTransaction();
        try {
            $sketHenti->update([
                'document_number'                                       => $documentNumber,
                'document_date'                                         => $documentDate,
                'effective_date'                                        => $effectiveDate,
                'document_location'                                     => $request->document_location,
                'document_classification_id'                            => $request->document_classification_id,
                'surat_perintah_penyelidikan_document_id'                 => $sprinlidikId,
                'surat_perintah_penghentian_penyelidikan_document_id'     => $sprintHentiId,
                'laporan_hasil_gelar_perkara_document_id'               => $lhgpId,
                'kode_alasan'                                           => $kodeAlasan,
                'alasan_penghentian'                                    => $request->alasan_penghentian,
                'dugaan_tindak_pidana'                                  => $request->dugaan_tindak_pidana,
                'pasal_list'                                            => $request->pasal_list,
                'menimbang'                                             => $request->menimbang,
                'mengingat'                                             => $request->mengingat,
                'memperhatikan'                                         => $request->memperhatikan,
                'memutuskan'                                            => $request->memutuskan,
                'menetapkan'                                            => $request->menetapkan,
                'carbon_copies'                                         => $carbonCopies,
                'updated_by_user_id'                                    => Auth::id(),
                'messages'                                              => array_merge($sketHenti->messages ?? [], [
                    'signatory_id' => $signatoryId,
                    'suspect_ids'  => $suspects,
                    'pelapor_name' => $request->pelapor_name,
                ]),
            ]);

            // Sync Suspects
            $sketHenti->suspects()->sync($suspects);

            // Re-sync Signatory
            $sketHenti->suratKetetapanPenghentianPenyelidikanDocumentOfficers()->delete();

            $signatory = Officer::find($signatoryId);
            if ($signatory) {
                $sketHenti->suratKetetapanPenghentianPenyelidikanDocumentOfficers()->create([
                    'surat_ketetapan_penghentian_penyelidikan_document_id' => $sketHenti->id,
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

            // File upload jika ada
            if ($request->hasFile('dokumen_digital')) {
                $file = $request->file('dokumen_digital');
                $fileName = time() . '_sket_henti_lidik_' . str_replace(' ', '_', $file->getClientOriginalName());
                $file->move(public_path('documents/attachments/'), $fileName);

                $sketHenti->suratKetetapanPenghentianPenyelidikanDocumentAttachment()->updateOrCreate(
                    ['surat_ketetapan_penghentian_penyelidikan_document_id' => $sketHenti->id],
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
            ->with('success', 'Surat Ketetapan Tentang Penghentian Penyelidikan berhasil diperbarui.');
    }

    // ─────────────────────────────────────────────
    // DELETE
    // ─────────────────────────────────────────────
    public function delete($id)
    {
        $sketHenti  = SuratKetetapanPenghentianPenyelidikanDocument::findOrFail($id);
        $accidentId = $sketHenti->accident_id;

        $sketHenti->delete();

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
            ->with('success', 'Surat Ketetapan Tentang Penghentian Penyelidikan berhasil dihapus.');
    }

    // ─────────────────────────────────────────────
    // DOWNLOAD WORD
    // ─────────────────────────────────────────────
    public function download($id)
    {
        $sketHenti = SuratKetetapanPenghentianPenyelidikanDocument::with([
            'accident',
            'accident.polres',
            'accident.polres.polda',
            'suratPerintahPenyelidikanDocument',
            'suratPerintahPenghentianPenyelidikanDocument',
            'laporanHasilGelarPerkaraDocument',
            'suspects',
            'suspects.gender',
            'suspects.job',
            'suspects.religion',
            'suspects.country',
            'suratKetetapanPenghentianPenyelidikanDocumentOfficers',
        ])->where('id', $id)->firstOrFail();

        $accident = $sketHenti->accident;
        $polres   = $accident->polres ?? null;
        $polda    = $polres->polda ?? null;

        $daerahPoliceFullName = $polda ? strtoupper($polda->name ?? $polda->full_name) : '-';
        $daerahPoliceFullName = trim(preg_replace('/^(POLDA|KEPOLISIAN DAERAH)\s+/i', '', $daerahPoliceFullName));
        $resorPoliceFullName  = $polres ? strtoupper($polres->name ?? $polres->full_name) : '-';
        $resorPoliceFullName  = trim(preg_replace('/^(RESOR|POLRES|KEPOLISIAN RESOR)\s+/i', '', $resorPoliceFullName));
        $resorPoliceAddress   = $polres ? ucwords($polres->address ?? '') : '-';

        $documentNumber = $sketHenti->document_number ?? '-';
        $accidentNumber = trim(preg_replace('/^Nomor\s*:\s*/i', '', $accident->no_lp ?? '-'));
        $reportDate     = $accident->report_date ? Carbon::parse($accident->report_date)->locale('id')->isoFormat('D MMMM Y') : '-';
        $accidentDate   = $accident->accident_date ? Carbon::parse($accident->accident_date)->locale('id')->isoFormat('D MMMM Y') : '-';
        $roadName       = $accident->road_name ?: '-';
        $reporterName   = $sketHenti->messages['pelapor_name'] ?? ($accident->reporter_name ?: 'Pelapor');

        // Sprinlidik Dasar
        $sprinlidik = $sketHenti->suratPerintahPenyelidikanDocument
            ?: SuratPerintahPenyelidikanDocument::where('accident_id', $accident->id)->latest()->first();
        $suratPerintahPenyelidikanDocumentNumber = $sprinlidik->document_number ?? '-';
        $suratPerintahPenyelidikanDocumentNumber = trim(preg_replace('/^Nomor\s*:\s*/i', '', $suratPerintahPenyelidikanDocumentNumber));
        $rawSprinlidikDate = $sprinlidik ? ($sprinlidik->document_date ?: ($sprinlidik->date ?: null)) : null;
        $suratPerintahPenyelidikanDocumentDate   = $rawSprinlidikDate ? Carbon::parse($rawSprinlidikDate)->locale('id')->isoFormat('D MMMM Y') : '-';

        // Sprint Penghentian Penyelidikan Dasar
        $sprintHenti = $sketHenti->suratPerintahPenghentianPenyelidikanDocument
            ?: SuratPerintahPenghentianPenyelidikanDocument::where('accident_id', $accident->id)->latest()->first();
        $suratPerintahPenghentianPenyelidikanDocumentNumber = $sprintHenti->document_number ?? '-';
        $suratPerintahPenghentianPenyelidikanDocumentNumber = trim(preg_replace('/^Nomor\s*:\s*/i', '', $suratPerintahPenghentianPenyelidikanDocumentNumber));
        $rawSprintHentiDate = $sprintHenti ? ($sprintHenti->document_date ?: ($sprintHenti->date ?: null)) : null;
        $suratPerintahPenghentianPenyelidikanDocumentDate   = $rawSprintHentiDate ? Carbon::parse($rawSprintHentiDate)->locale('id')->isoFormat('D MMMM Y') : '-';

        // LHGP Dasar
        $lhgp = $sketHenti->laporanHasilGelarPerkaraDocument
            ?: ($sprintHenti && $sprintHenti->laporanHasilGelarPerkaraDocument
                ? $sprintHenti->laporanHasilGelarPerkaraDocument
                : LaporanHasilGelarPerkaraDocument::where('accident_id', $accident->id)->latest()->first());
        $rawLhgpDate = $lhgp ? ($lhgp->document_date ?: ($lhgp->date ?: null)) : null;
        $laporanHasilGelarPerkaraDocumentDate = $rawLhgpDate ? Carbon::parse($rawLhgpDate)->locale('id')->isoFormat('D MMMM Y') : '-';

        // Terlapor / Tersangka
        $primarySuspect = $sketHenti->suspects->first() ?? ($accident->suspects ? $accident->suspects->first() : null);
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
        if (strtoupper($suspectNationality) === 'INDONESAI') {
            $suspectNationality = 'INDONESIA';
        }
        $suspectGenderName     = $primarySuspect && $primarySuspect->gender ? $primarySuspect->gender->name : ($primarySuspect && $primarySuspect->gender_id == 1 ? 'Laki-laki' : ($primarySuspect && $primarySuspect->gender_id == 2 ? 'Perempuan' : '-'));
        $suspectBirthPlace     = $primarySuspect ? ($primarySuspect->birth_place ?? '-') : '-';
        $suspectBirthDate      = $primarySuspect && $primarySuspect->birth_date ? Carbon::parse($primarySuspect->birth_date)->locale('id')->isoFormat('D MMMM Y') : '-';
        $suspectJobName        = $primarySuspect && $primarySuspect->job ? $primarySuspect->job->name : '-';
        $suspectReligionName   = $primarySuspect && $primarySuspect->religion ? $primarySuspect->religion->name : '-';
        $suspectFullAddress    = $primarySuspect ? ($primarySuspect->address ?? '-') : '-';

        $dugaanTindakPidana = $sketHenti->dugaan_tindak_pidana ?: 'Kecelakaan Lalu Lintas';
        $pasalList          = $sketHenti->pasal_list ?: ($accident->pasal_names ?? 'Pasal 310 UU No. 22 Tahun 2009');

        // Alasan
        $kodeAlasan = is_array($sketHenti->kode_alasan) ? $sketHenti->kode_alasan : (json_decode($sketHenti->kode_alasan, true) ?? []);
        $alasanList = [];
        foreach ($kodeAlasan as $kd) {
            if (isset(self::$masterAlasan[$kd])) {
                $alasanList[] = self::$masterAlasan[$kd];
            }
        }
        $alasanPenghentian = !empty($alasanList) ? implode('; ', $alasanList) : ($sketHenti->alasan_penghentian ?: 'bukan merupakan tindak pidana');

        $documentLocation = $sketHenti->document_location ?: ($polres->polres_regency ?? ($polres->name ?? '-'));
        $documentDate     = $sketHenti->document_date ? Carbon::parse($sketHenti->document_date)->locale('id')->isoFormat('D MMMM Y') : '-';
        $effectiveDate    = $sketHenti->effective_date ? Carbon::parse($sketHenti->effective_date)->locale('id')->isoFormat('D MMMM Y') : $documentDate;

        // Signatory (Selaku Penyidik)
        $signatory = $sketHenti->suratKetetapanPenghentianPenyelidikanDocumentOfficers->firstWhere('class', 'SIGNATORY');
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
                $signatoryPositionName = 'KEPALA KEPOLISIAN RESOR ' . $polresFullName;
            } elseif ($signatoryPositionDetail->position_cluster_id == '9') {
                $signatoryHeadText     = 'a.n. DIREKTUR LALU LINTAS POLDA ' . $poldaFullName;
                $signatoryPositionName = $signatoryPositionDetail->positionCluster->alias_name ?? $signatoryPositionDetail->name;
            } else {
                $signatoryPositionName = $signatoryPositionDetail->positionCluster->alias_name ?? $signatoryPositionDetail->name;
            }
        }
        if (!$signatoryPositionName) {
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

        // Carbon Copies (Tembusan)
        $carbonCopies = is_array($sketHenti->carbon_copies) ? $sketHenti->carbon_copies : (json_decode($sketHenti->carbon_copies, true) ?? []);
        $blockCarbonCopies = [];
        $ccNo = 1;
        foreach ($carbonCopies as $cc) {
            $blockCarbonCopies[] = [
                'carbon_copy_iteration' => $ccNo++,
                'carbon_copy_name'      => $cc,
            ];
        }

        $templatePath = public_path('word-template/surat_ketetapan_penghentian_penyelidikan.docx');
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($templatePath);

        if (count($blockCarbonCopies) > 0) {
            $templateProcessor->cloneBlock('block_carbon_copies', 0, true, false, $blockCarbonCopies);
            $templateProcessor->setValue('tembusan_heading', 'Tembusan :');
        } else {
            $templateProcessor->cloneBlock('block_carbon_copies', 0);
            $templateProcessor->setValue('tembusan_heading', '');
        }

        $templateProcessor->setValues([
            'daerahPoliceFullName'                                 => $daerahPoliceFullName,
            'resorPoliceFullName'                                  => $resorPoliceFullName,
            'resorPoliceAddress'                                   => $resorPoliceAddress,
            'documentNumber'                                       => $documentNumber,
            'documentLocation'                                     => $documentLocation,
            'accidentNumber'                                       => $accidentNumber,
            'reportDate'                                           => $reportDate,
            'accidentDate'                                         => $accidentDate,
            'roadName'                                             => $roadName,
            'reporterName'                                         => $reporterName,
            'suratPerintahPenyelidikanDocumentNumber'              => $suratPerintahPenyelidikanDocumentNumber,
            'suratPerintahPenyelidikanDocumentDate'                => $suratPerintahPenyelidikanDocumentDate,
            'suratPerintahPenyelidikanDocumentDocumentDate'        => $suratPerintahPenyelidikanDocumentDate,
            'suratPerintahPenghentianPenyelidikanDocumentNumber'   => $suratPerintahPenghentianPenyelidikanDocumentNumber,
            'suratPerintahPenghentianPenyelidikanDocumentDate'     => $suratPerintahPenghentianPenyelidikanDocumentDate,
            'laporanHasilGelarPerkaraDocumentDate'                 => $laporanHasilGelarPerkaraDocumentDate,
            'suspectName'                                          => $suspectName,
            'dugaanTindakPidana'                                   => $dugaanTindakPidana,
            'pasalList'                                            => $pasalList,
            'suspectIdentityNumber'                                => $suspectIdentityNumber,
            'suspectBirthPlace'                                    => $suspectBirthPlace,
            'suspectBirthDate'                                     => $suspectBirthDate,
            'suspectGenderName'                                    => $suspectGenderName,
            'suspectJobName'                                       => $suspectJobName,
            'suspectReligionName'                                  => $suspectReligionName,
            'suspectNationality'                                   => $suspectNationality,
            'suspectFullAddress'                                   => $suspectFullAddress,
            'alasanPenghentian'                                    => $alasanPenghentian,
            'effectiveDate'                                        => $effectiveDate,
            'signatoryHeadText'                                    => $signatoryHeadText,
            'signatoryPositionName'                                => $signatoryPositionName,
            'signatoryPositionHeadText'                            => $signatoryPositionHeadText,
            'signatoryName'                                        => strtoupper($signatoryName),
            'signatoryRankName'                                    => strtoupper($signatoryRankName),
            'signatoryRegisterNumber'                              => $signatoryRegisterNumber,
            'documentDate'                                         => $documentDate,
            'suratPemberitahuanDimulainyaPenyidikanDocumentNumber' => '-',
            'suratPemberitahuanDimulainyaPenyidikanDocumentDate'   => '-',
            'suratPemberitahuanDimulainyaPenyidikanDocumentDocumentDate' => '-',
            'suratKetetapanTentangPenetapanTersangkaDocumentNumber' => '-',
            'suratKetetapanTentangPenetapanTersangkaDocumentDate'   => '-',
            'suratKetetapanTentangPenetapanTersangkaDocumentDocumentDate' => '-',
            'prosecutorName'                                       => '-',
            'courtName'                                            => '-',
        ]);

        $filename = 'generate/' . $sketHenti->id . ' - Surat Ketetapan Penghentian Penyelidikan - ' . ($polres->full_name ?? '');
        $savePath = public_path($filename . '.docx');

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
            'document_number'                                       => 'required|string|max:255',
            'document_date'                                         => 'required|date',
            'signatory'                                             => 'required',
            'kode_alasan'                                           => 'required|array|min:1',
            'surat_perintah_penghentian_penyelidikan_document_id'   => 'required',
        ], [
            'document_number.required'                                     => 'Nomor Surat Ketetapan Penghentian Penyelidikan wajib diisi.',
            'document_date.required'                                       => 'Tanggal Surat Ketetapan Penghentian Penyelidikan wajib diisi.',
            'signatory.required'                                           => 'Pejabat Penandatangan wajib dipilih.',
            'kode_alasan.required'                                         => 'Minimal satu alasan penghentian penyelidikan wajib dipilih.',
            'kode_alasan.min'                                              => 'Minimal satu alasan penghentian penyelidikan wajib dipilih.',
            'surat_perintah_penghentian_penyelidikan_document_id.required' => 'Surat Perintah Penghentian Penyelidikan wajib dipilih.',
        ]);
    }
}
