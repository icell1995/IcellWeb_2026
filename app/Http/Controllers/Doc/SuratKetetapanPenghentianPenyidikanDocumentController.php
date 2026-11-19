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
use App\Models\Lib\Prosecutor;
use App\Models\Lib\Court;
use App\Models\Lib\DocumentClassification;
use App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument;
use App\Models\Doc\SuratPemberitahuanDimulainyaPenyidikanDocument\SuratPemberitahuanDimulainyaPenyidikanDocument;
use App\Models\Doc\SuratKetetapanTentangPenetapanTersangkaDocument\SuratKetetapanTentangPenetapanTersangkaDocument;
use App\Models\Doc\SuratPerintahPenghentianPenyidikanDocument\SuratPerintahPenghentianPenyidikanDocument;
use App\Models\Doc\LaporanHasilGelarPerkaraDocument\LaporanHasilGelarPerkaraDocument;
use App\Models\Doc\SuratKetetapanPenghentianPenyidikanDocument\SuratKetetapanPenghentianPenyidikanDocument;
use App\Models\Doc\SuratKetetapanPenghentianPenyidikanDocument\SuratKetetapanPenghentianPenyidikanDocumentOfficer;
use App\Models\Doc\SuratKetetapanPenghentianPenyidikanDocument\SuratKetetapanPenghentianPenyidikanDocumentAttachment;
use App\Traits\DocsOfficersTraits;

class SuratKetetapanPenghentianPenyidikanDocumentController extends Controller
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

        // Sprindik
        $sprindikDocuments = SuratPerintahPenyidikanDocument::where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->orderBy('created_at', 'desc')
            ->get();

        // SPDP
        $spdpDocuments = SuratPemberitahuanDimulainyaPenyidikanDocument::where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->orderBy('created_at', 'desc')
            ->get()
            ->merge(
                \App\Models\Doc\SuratPemberitahuanDimulainyaPenyidikanPusiknasDocument\SuratPemberitahuanDimulainyaPenyidikanPusiknasDocument::where('accident_id', $accidentId)
                    ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
                    ->get()
            );

        // SK Penetapan Tersangka
        $skpptDocuments = SuratKetetapanTentangPenetapanTersangkaDocument::where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->orderBy('created_at', 'desc')
            ->get();

        // LHGP
        $lhgpDocuments = LaporanHasilGelarPerkaraDocument::where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->orderBy('created_at', 'desc')
            ->get();

        // Surat Perintah Penghentian Penyidikan
        $sprintHentiDocuments = SuratPerintahPenghentianPenyidikanDocument::with(['suspects'])
            ->where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->orderBy('created_at', 'desc')
            ->get();

        $latestSprintHenti = $sprintHentiDocuments->first();

        $defaultSprintHentiId = $latestSprintHenti ? $latestSprintHenti->id : null;
        $defaultKodeAlasan    = $latestSprintHenti ? (is_array($latestSprintHenti->kode_alasan) ? $latestSprintHenti->kode_alasan : (json_decode($latestSprintHenti->kode_alasan, true) ?? [])) : [];
        $defaultAlasan        = $latestSprintHenti ? $latestSprintHenti->alasan_penghentian : '';
        $defaultDugaan        = $latestSprintHenti ? ($latestSprintHenti->dugaan_tindak_pidana ?: 'Kecelakaan Lalu Lintas') : 'Kecelakaan Lalu Lintas';
        $defaultPasal         = $latestSprintHenti ? ($latestSprintHenti->pasal_list ?: 'Pasal 310 UU No. 22 Tahun 2009') : 'Pasal 310 UU No. 22 Tahun 2009';
        $defaultSprindikId    = $latestSprintHenti ? $latestSprintHenti->surat_perintah_penyidikan_document_id : null;
        $defaultSpdpId        = $latestSprintHenti ? $latestSprintHenti->surat_pemberitahuan_dimulainya_penyidikan_document_id : null;
        $defaultSkpptId       = $latestSprintHenti ? $latestSprintHenti->surat_ketetapan_tentang_penetapan_tersangka_document_id : null;
        $defaultLhgpId        = $latestSprintHenti ? $latestSprintHenti->laporan_hasil_gelar_perkara_document_id : null;
        $defaultSuspectIds    = ($latestSprintHenti && $latestSprintHenti->suspects) ? $latestSprintHenti->suspects->pluck('id')->toArray() : [];
        $defaultSignatoryId   = $latestSprintHenti ? ($latestSprintHenti->messages['signatory_id'] ?? null) : null;

        $suspects = Suspect::where('accident_id', $accidentId)->get();

        $documentClassifications = DocumentClassification::where('is_active', true)->orderBy('sort')->get();
        $prosecutors = Prosecutor::where('is_active', true)->orderBy('name')->get();
        $courts = Court::where('is_active', true)->orderBy('name')->get();

        $getOldNewPolresIds = $this->getOldNewPolresIds($accident->polres_id);

        $authorizedSignatories = Officer::withRelated()
            ->selectFullName()
            ->whereIn('police_id', $getOldNewPolresIds)
            ->whereHasUserActive()
            ->hasDataComplete()
            ->signatory()->active()->valid()
            ->orderBy('first_name')->get();

        $masterAlasan = self::$masterAlasan;

        return view('docs.surat-ketetapan-penghentian-penyidikan-document.create', compact(
            'accidentId',
            'accident',
            'sprindikDocuments',
            'spdpDocuments',
            'skpptDocuments',
            'sprintHentiDocuments',
            'lhgpDocuments',
            'suspects',
            'prosecutors',
            'courts',
            'documentClassifications',
            'authorizedSignatories',
            'masterAlasan',
            'defaultSprintHentiId',
            'defaultKodeAlasan',
            'defaultAlasan',
            'defaultDugaan',
            'defaultPasal',
            'defaultSprindikId',
            'defaultSpdpId',
            'defaultSkpptId',
            'defaultLhgpId',
            'defaultSuspectIds',
            'defaultSignatoryId'
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

        $exists = SuratKetetapanPenghentianPenyidikanDocument::where('accident_id', $accidentId)
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
            $fileName = time() . '_sket_henti_' . str_replace(' ', '_', $file->getClientOriginalName());
            $file->move(public_path('documents/attachments/'), $fileName);
        }

        DB::beginTransaction();
        try {
            $sketHenti = SuratKetetapanPenghentianPenyidikanDocument::create([
                'accident_id'                                           => $accidentId,
                'document_number'                                       => $documentNumber,
                'document_date'                                         => $documentDate,
                'effective_date'                                        => $effectiveDate,
                'document_location'                                     => $request->document_location,
                'document_classification_id'                            => $request->document_classification_id,
                'surat_perintah_penyidikan_document_id'                 => $request->surat_perintah_penyidikan_document_id,
                'surat_pemberitahuan_dimulainya_penyidikan_document_id' => $request->surat_pemberitahuan_dimulainya_penyidikan_document_id,
                'surat_ketetapan_tentang_penetapan_tersangka_document_id'=> $request->surat_ketetapan_tentang_penetapan_tersangka_document_id,
                'surat_perintah_penghentian_penyidikan_document_id'     => $request->surat_perintah_penghentian_penyidikan_document_id,
                'laporan_hasil_gelar_perkara_document_id'               => $request->laporan_hasil_gelar_perkara_document_id,
                'prosecutor_id'                                         => $request->prosecutor_id,
                'court_id'                                              => $request->court_id,
                'kode_alasan'                                           => $kodeAlasan,
                'alasan_penghentian'                                    => $request->alasan_penghentian,
                'dugaan_tindak_pidana'                                  => $request->dugaan_tindak_pidana,
                'pasal_list'                                            => $request->pasal_list,
                'status_id'                                             => '2', // Dibuat
                'document_category_id'                                  => '0206',
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
                ],
            ]);

            // Sync Suspects
            if (!empty($suspects)) {
                $sketHenti->suspects()->sync($suspects);
            }

            // Simpan Attachment
            if ($fileName) {
                $sketHenti->suratKetetapanPenghentianPenyidikanDocumentAttachment()->create([
                    'surat_ketetapan_penghentian_penyidikan_document_id' => $sketHenti->id,
                    'name'           => $fileName,
                    'file_path'      => 'documents/attachments/' . $fileName,
                    'file_extension' => pathinfo($fileName, PATHINFO_EXTENSION),
                    'file_size'      => file_exists(public_path('documents/attachments/' . $fileName)) ? filesize(public_path('documents/attachments/' . $fileName)) : 0,
                    'is_active'      => true,
                ]);
            }

            // Simpan Signatory
            $signatory = Officer::find($signatoryId);
            if ($signatory) {
                $sketHenti->suratKetetapanPenghentianPenyidikanDocumentOfficers()->create([
                    'surat_ketetapan_penghentian_penyidikan_document_id' => $sketHenti->id,
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
            ->with('success', 'Surat Ketetapan Penghentian Penyidikan berhasil dibuat.');
    }

    // ─────────────────────────────────────────────
    // SHOW
    // ─────────────────────────────────────────────
    public function show($id)
    {
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $sketHenti  = SuratKetetapanPenghentianPenyidikanDocument::with([
            'accident',
            'accident.polres',
            'accident.polres.polda',
            'accident.suspects',
            'suratPerintahPenyidikanDocument',
            'suratPemberitahuanDimulainyaPenyidikanDocument',
            'suratKetetapanTentangPenetapanTersangkaDocument',
            'suratPerintahPenghentianPenyidikanDocument',
            'laporanHasilGelarPerkaraDocument',
            'court',
            'suratKetetapanPenghentianPenyidikanDocumentOfficers',
            'suratKetetapanPenghentianPenyidikanDocumentAttachment',
            'documentClassification',
            'status',
        ])->where('id', $id)->firstOrFail();

        $accident = Accident::with(['polres', 'polres.polda'])->where('id', $sketHenti->accident_id)->first();
        $masterAlasan = self::$masterAlasan;

        $kodeAlasan = is_array($sketHenti->kode_alasan) ? $sketHenti->kode_alasan : (json_decode($sketHenti->kode_alasan, true) ?? []);
        $signatoryOfficer = $sketHenti->suratKetetapanPenghentianPenyidikanDocumentOfficers->firstWhere('class', 'SIGNATORY');

        return view('docs.surat-ketetapan-penghentian-penyidikan-document.show', compact(
            'accidentId',
            'accident',
            'sketHenti',
            'masterAlasan',
            'kodeAlasan',
            'signatoryOfficer'
        ));
    }

    // ─────────────────────────────────────────────
    // EDIT
    // ─────────────────────────────────────────────
    public function edit($id)
    {
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $sketHenti  = SuratKetetapanPenghentianPenyidikanDocument::with([
            'suspects',
            'suratKetetapanPenghentianPenyidikanDocumentOfficers',
            'suratKetetapanPenghentianPenyidikanDocumentAttachment',
        ])->where('id', $id)->firstOrFail();

        $accident = Accident::with(['polres', 'polres.polda'])->where('id', $sketHenti->accident_id)->firstOrFail();

        $sprindikDocuments = SuratPerintahPenyidikanDocument::where('accident_id', $sketHenti->accident_id)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->orderBy('created_at', 'desc')->get();

        $spdpDocuments = SuratPemberitahuanDimulainyaPenyidikanDocument::where('accident_id', $sketHenti->accident_id)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->orderBy('created_at', 'desc')->get()
            ->merge(
                \App\Models\Doc\SuratPemberitahuanDimulainyaPenyidikanPusiknasDocument\SuratPemberitahuanDimulainyaPenyidikanPusiknasDocument::where('accident_id', $sketHenti->accident_id)
                    ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
                    ->get()
            );

        $skpptDocuments = SuratKetetapanTentangPenetapanTersangkaDocument::where('accident_id', $sketHenti->accident_id)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->orderBy('created_at', 'desc')->get();

        $lhgpDocuments = LaporanHasilGelarPerkaraDocument::where('accident_id', $sketHenti->accident_id)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->orderBy('created_at', 'desc')->get();

        $sprintHentiDocuments = SuratPerintahPenghentianPenyidikanDocument::where('accident_id', $sketHenti->accident_id)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->orderBy('created_at', 'desc')->get();

        $suspects = Suspect::where('accident_id', $sketHenti->accident_id)->get();
        $prosecutors = Prosecutor::where('is_active', true)->orderBy('name')->get();
        $courts = Court::where('is_active', true)->orderBy('name')->get();
        $documentClassifications = DocumentClassification::where('is_active', true)->orderBy('sort')->get();

        $getOldNewPolresIds = $this->getOldNewPolresIds($accident->polres_id);

        $authorizedSignatories = Officer::withRelated()
            ->selectFullName()
            ->whereIn('police_id', $getOldNewPolresIds)
            ->whereHasUserActive()
            ->hasDataComplete()
            ->signatory()->active()->valid()
            ->orderBy('first_name')->get();

        $masterAlasan = self::$masterAlasan;
        $kodeAlasan   = is_array($sketHenti->kode_alasan) ? $sketHenti->kode_alasan : (json_decode($sketHenti->kode_alasan, true) ?? []);

        $signatoryDocOfficer = $sketHenti->suratKetetapanPenghentianPenyidikanDocumentOfficers->firstWhere('class', 'SIGNATORY');
        $currentSignatoryId = $sketHenti->messages['signatory_id'] ?? null;
        if (!$currentSignatoryId && $signatoryDocOfficer) {
            $currentSignatoryId = optional(Officer::where('register_number', $signatoryDocOfficer->register_number)->first())->id;
        }

        $currentSuspectIds  = $sketHenti->suspects->pluck('id')->toArray();
        if (empty($currentSuspectIds) && !empty($sketHenti->messages['suspect_ids'])) {
            $currentSuspectIds = $sketHenti->messages['suspect_ids'];
        }

        $savedKodeAlasan  = $kodeAlasan;
        $savedSignatoryId = $currentSignatoryId;
        $savedSuspectIds  = $currentSuspectIds;

        return view('docs.surat-ketetapan-penghentian-penyidikan-document.edit', compact(
            'accidentId',
            'accident',
            'sketHenti',
            'sprindikDocuments',
            'spdpDocuments',
            'skpptDocuments',
            'sprintHentiDocuments',
            'lhgpDocuments',
            'suspects',
            'prosecutors',
            'courts',
            'documentClassifications',
            'authorizedSignatories',
            'masterAlasan',
            'kodeAlasan',
            'currentSignatoryId',
            'currentSuspectIds',
            'savedKodeAlasan',
            'savedSignatoryId',
            'savedSuspectIds'
        ));
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────
    public function update(Request $request, $id)
    {
        $sketHenti  = SuratKetetapanPenghentianPenyidikanDocument::findOrFail($id);
        $accidentId = $sketHenti->accident_id;

        $validator = $this->validateForm($request, $id);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $documentNumber = htmlspecialchars($request->document_number);
        $documentDate   = htmlspecialchars($request->document_date);
        $effectiveDate  = htmlspecialchars($request->effective_date ?? $documentDate);

        $kodeAlasan   = $request->kode_alasan ?? [];
        $signatoryId  = htmlspecialchars($request->signatory);
        $suspects     = $request->suspects ?? [];

        DB::beginTransaction();
        try {
            $sketHenti->update([
                'document_number'                                       => $documentNumber,
                'document_date'                                         => $documentDate,
                'effective_date'                                        => $effectiveDate,
                'document_location'                                     => $request->document_location,
                'document_classification_id'                            => $request->document_classification_id,
                'surat_perintah_penyidikan_document_id'                 => $request->surat_perintah_penyidikan_document_id,
                'surat_pemberitahuan_dimulainya_penyidikan_document_id' => $request->surat_pemberitahuan_dimulainya_penyidikan_document_id,
                'surat_ketetapan_tentang_penetapan_tersangka_document_id'=> $request->surat_ketetapan_tentang_penetapan_tersangka_document_id,
                'surat_perintah_penghentian_penyidikan_document_id'     => $request->surat_perintah_penghentian_penyidikan_document_id,
                'laporan_hasil_gelar_perkara_document_id'               => $request->laporan_hasil_gelar_perkara_document_id,
                'prosecutor_id'                                         => $request->prosecutor_id,
                'court_id'                                              => $request->court_id,
                'kode_alasan'                                           => $kodeAlasan,
                'alasan_penghentian'                                    => $request->alasan_penghentian,
                'dugaan_tindak_pidana'                                  => $request->dugaan_tindak_pidana,
                'pasal_list'                                            => $request->pasal_list,
                'updated_by_user_id'                                    => Auth::id(),
            ]);

            // Sync Suspects
            $sketHenti->suspects()->sync($suspects);

            // Re-sync Signatory
            $sketHenti->suratKetetapanPenghentianPenyidikanDocumentOfficers()->delete();

            $signatory = Officer::find($signatoryId);
            if ($signatory) {
                $sketHenti->suratKetetapanPenghentianPenyidikanDocumentOfficers()->create([
                    'surat_ketetapan_penghentian_penyidikan_document_id' => $sketHenti->id,
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
                $fileName = time() . '_sket_henti_' . str_replace(' ', '_', $file->getClientOriginalName());
                $file->move(public_path('documents/attachments/'), $fileName);

                $sketHenti->suratKetetapanPenghentianPenyidikanDocumentAttachment()->updateOrCreate(
                    ['surat_ketetapan_penghentian_penyidikan_document_id' => $sketHenti->id],
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
            ->with('success', 'Surat Ketetapan Penghentian Penyidikan berhasil diperbarui.');
    }

    // ─────────────────────────────────────────────
    // DELETE
    // ─────────────────────────────────────────────
    public function delete($id)
    {
        $sketHenti  = SuratKetetapanPenghentianPenyidikanDocument::findOrFail($id);
        $accidentId = $sketHenti->accident_id;

        $sketHenti->delete();

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
            ->with('success', 'Surat Ketetapan Penghentian Penyidikan berhasil dihapus.');
    }

    // ─────────────────────────────────────────────
    // DOWNLOAD WORD
    // ─────────────────────────────────────────────
    public function download($id)
    {
        $sketHenti = SuratKetetapanPenghentianPenyidikanDocument::with([
            'accident',
            'accident.polres',
            'accident.polres.polda',
            'suratPerintahPenyidikanDocument',
            'suratPemberitahuanDimulainyaPenyidikanDocument',
            'suratKetetapanTentangPenetapanTersangkaDocument',
            'suratPerintahPenghentianPenyidikanDocument',
            'laporanHasilGelarPerkaraDocument',
            'prosecutor',
            'court',
            'suspects',
            'suspects.gender',
            'suspects.job',
            'suspects.religion',
            'suspects.country',
            'suratKetetapanPenghentianPenyidikanDocumentOfficers',
        ])->where('id', $id)->firstOrFail();

        $accident = $sketHenti->accident;
        $polres   = $accident->polres ?? null;
        $polda    = $polres->polda ?? null;

        $daerahPoliceFullName = $polda ? strtoupper($polda->name ?? $polda->full_name) : '-';
        $resorPoliceFullName  = $polres ? strtoupper($polres->name ?? $polres->full_name) : '-';
        $resorPoliceAddress   = $polres ? ucwords($polres->address ?? '') : '-';

        $documentNumber = $sketHenti->document_number ?? '-';
        $accidentNumber = $accident->no_lp ?? '-';
        $reportDate     = $accident->report_date ? Carbon::parse($accident->report_date)->locale('id')->isoFormat('D MMMM Y') : '-';
        $accidentDate   = $accident->accident_date ? Carbon::parse($accident->accident_date)->locale('id')->isoFormat('D MMMM Y') : '-';
        $roadName       = $accident->road_name ?: '-';

        // Sprindik Dasar
        $sprindik       = $sketHenti->suratPerintahPenyidikanDocument;
        $suratPerintahPenyidikanDocumentNumber = $sprindik->document_number ?? '-';
        $suratPerintahPenyidikanDocumentDocumentDate = $sprindik && $sprindik->document_date ? Carbon::parse($sprindik->document_date)->locale('id')->isoFormat('D MMMM Y') : '-';

        // SPDP Dasar
        $spdp           = $sketHenti->suratPemberitahuanDimulainyaPenyidikanDocument;
        $suratPemberitahuanDimulainyaPenyidikanDocumentNumber = $spdp->document_number ?? '-';
        $suratPemberitahuanDimulainyaPenyidikanDocumentDocumentDate = $spdp && $spdp->document_date ? Carbon::parse($spdp->document_date)->locale('id')->isoFormat('D MMMM Y') : '-';

        // SK Penetapan Tersangka Dasar
        $skppt          = $sketHenti->suratKetetapanTentangPenetapanTersangkaDocument;
        $suratKetetapanTentangPenetapanTersangkaDocumentNumber = $skppt->document_number ?? '-';
        $suratKetetapanTentangPenetapanTersangkaDocumentDocumentDate = $skppt && $skppt->document_date ? Carbon::parse($skppt->document_date)->locale('id')->isoFormat('D MMMM Y') : '-';

        // Sprint Penghentian Penyidikan Dasar
        $sprintHenti    = $sketHenti->suratPerintahPenghentianPenyidikanDocument ?: SuratPerintahPenghentianPenyidikanDocument::where('accident_id', $accident->id)->latest()->first();
        $suratPerintahPenghentianPenyidikanDocumentNumber = $sprintHenti->document_number ?? '-';
        $suratPerintahPenghentianPenyidikanDocumentDate   = $sprintHenti && $sprintHenti->document_date ? Carbon::parse($sprintHenti->document_date)->locale('id')->isoFormat('D MMMM Y') : '-';

        // LHGP Dasar
        $lhgp           = $sketHenti->laporanHasilGelarPerkaraDocument;
        $laporanHasilGelarPerkaraDocumentDate = $lhgp && $lhgp->document_date ? Carbon::parse($lhgp->document_date)->locale('id')->isoFormat('D MMMM Y') : '-';

        // Tersangka utama
        $primarySuspect = $sketHenti->suspects->first();
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
        $suspectGenderName     = $primarySuspect && $primarySuspect->gender ? $primarySuspect->gender->name : '-';
        $suspectBirthPlace     = $primarySuspect ? ($primarySuspect->birth_place ?? '-') : '-';
        $suspectBirthDate      = $primarySuspect && $primarySuspect->birth_date ? Carbon::parse($primarySuspect->birth_date)->locale('id')->isoFormat('D MMMM Y') : '-';
        $suspectJobName        = $primarySuspect && $primarySuspect->job ? $primarySuspect->job->name : '-';
        $suspectReligionName   = $primarySuspect && $primarySuspect->religion ? $primarySuspect->religion->name : '-';
        $suspectFullAddress    = $primarySuspect ? ($primarySuspect->address ?? '-') : '-';

        $dugaanTindakPidana = $sketHenti->dugaan_tindak_pidana ?: 'Kecelakaan Lalu Lintas';
        $pasalList          = $sketHenti->pasal_list ?: 'Pasal 310 UU No. 22 Tahun 2009';

        $effectiveDate = $sketHenti->effective_date ? Carbon::parse($sketHenti->effective_date)->locale('id')->isoFormat('D MMMM Y') : ($sketHenti->document_date ? Carbon::parse($sketHenti->document_date)->locale('id')->isoFormat('D MMMM Y') : '-');

        // Alasan
        $kodeAlasan = is_array($sketHenti->kode_alasan) ? $sketHenti->kode_alasan : (json_decode($sketHenti->kode_alasan, true) ?? []);
        $alasanList = [];
        foreach ($kodeAlasan as $kd) {
            if (isset(self::$masterAlasan[$kd])) {
                $alasanList[] = self::$masterAlasan[$kd];
            }
        }
        $alasanPenghentian = !empty($alasanList) ? implode('; ', $alasanList) : ($sketHenti->alasan_penghentian ?: 'Keadilan Restoratif');

        $prosecutorName = $sketHenti->prosecutor ? $sketHenti->prosecutor->name : 'Kejaksaan Negeri';
        $courtName      = $sketHenti->court ? $sketHenti->court->name : 'Pengadilan Negeri';

        $documentLocation = $sketHenti->document_location ?: ($polres->polres_regency ?? ($polres->name ?? 'Di tempat'));
        $documentDate     = $sketHenti->document_date ? Carbon::parse($sketHenti->document_date)->locale('id')->isoFormat('D MMMM Y') : '-';

        // Signatory
        $signatory = $sketHenti->suratKetetapanPenghentianPenyidikanDocumentOfficers->firstWhere('class', 'SIGNATORY');
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

        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor(public_path('word-template/surat_ketetapan_penghentian_penyidikan.docx'));

        $templateProcessor->setValues([
            'daerahPoliceFullName'                                 => $daerahPoliceFullName,
            'resorPoliceFullName'                                  => $resorPoliceFullName,
            'resorPoliceAddress'                                   => $resorPoliceAddress,
            'documentNumber'                                       => $documentNumber,
            'accidentNumber'                                       => $accidentNumber,
            'reportDate'                                           => $reportDate,
            'suratPerintahPenyidikanDocumentNumber'                => $suratPerintahPenyidikanDocumentNumber,
            'suratPerintahPenyidikanDocumentDocumentDate'          => $suratPerintahPenyidikanDocumentDocumentDate,
            'suratPemberitahuanDimulainyaPenyidikanDocumentNumber' => $suratPemberitahuanDimulainyaPenyidikanDocumentNumber,
            'suratPemberitahuanDimulainyaPenyidikanDocumentDocumentDate' => $suratPemberitahuanDimulainyaPenyidikanDocumentDocumentDate,
            'suratKetetapanTentangPenetapanTersangkaDocumentNumber'=> $suratKetetapanTentangPenetapanTersangkaDocumentNumber,
            'suratKetetapanTentangPenetapanTersangkaDocumentDocumentDate' => $suratKetetapanTentangPenetapanTersangkaDocumentDocumentDate,
            'suratPerintahPenghentianPenyidikanDocumentNumber'     => $suratPerintahPenghentianPenyidikanDocumentNumber,
            'suratPerintahPenghentianPenyidikanDocumentDate'       => $suratPerintahPenghentianPenyidikanDocumentDate,
            'suspectName'                                          => $suspectName,
            'dugaanTindakPidana'                                   => $dugaanTindakPidana,
            'pasalList'                                            => $pasalList,
            'accidentDate'                                         => $accidentDate,
            'roadName'                                             => $roadName,
            'laporanHasilGelarPerkaraDocumentDate'                 => $laporanHasilGelarPerkaraDocumentDate,
            'suspectIdentityNumber'                                => $suspectIdentityNumber,
            'suspectNationality'                                   => $suspectNationality,
            'suspectGenderName'                                    => $suspectGenderName,
            'suspectBirthPlace'                                    => $suspectBirthPlace,
            'suspectBirthDate'                                     => $suspectBirthDate,
            'suspectJobName'                                       => $suspectJobName,
            'suspectReligionName'                                  => $suspectReligionName,
            'suspectFullAddress'                                   => $suspectFullAddress,
            'effectiveDate'                                        => $effectiveDate,
            'alasanPenghentian'                                    => $alasanPenghentian,
            'prosecutorName'                                       => $prosecutorName,
            'courtName'                                            => $courtName,
            'documentLocation'                                     => $documentLocation,
            'documentDate'                                         => $documentDate,
            'signatoryHeadText'                                    => $signatoryHeadText,
            'signatoryPositionName'                                => $signatoryPositionName,
            'signatoryPositionHeadText'                            => $signatoryPositionName,
            'signatoryName'                                        => strtoupper($signatoryName),
            'signatoryRankName'                                    => strtoupper($signatoryRankName),
            'signatoryRegisterNumber'                              => $signatoryRegisterNumber,
        ]);

        $filename = 'generate/' . $sketHenti->id . ' - Surat Ketetapan Penghentian Penyidikan - ' . ($polres->full_name ?? '');
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
            'document_number' => 'required|string|max:255',
            'document_date'   => 'required|date',
            'signatory'       => 'required',
            'kode_alasan'     => 'required|array|min:1',
        ], [
            'document_number.required' => 'Nomor surat ketetapan wajib diisi.',
            'document_date.required'   => 'Tanggal surat ketetapan wajib diisi.',
            'signatory.required'       => 'Pejabat penandatangan wajib dipilih.',
            'kode_alasan.required'     => 'Alasan penghentian penyidikan wajib dipilih minimal 1.',
        ]);
    }
}
