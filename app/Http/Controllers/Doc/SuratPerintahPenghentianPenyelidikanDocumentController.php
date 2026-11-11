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
use App\Models\Doc\SuratKetetapanPenghentianPenyelidikanDocument\SuratKetetapanPenghentianPenyelidikanDocument;
use App\Models\Doc\LaporanHasilGelarPerkaraDocument\LaporanHasilGelarPerkaraDocument;
use App\Models\Doc\SuratPerintahPenghentianPenyelidikanDocument\SuratPerintahPenghentianPenyelidikanDocument;
use App\Models\Doc\SuratPerintahPenghentianPenyelidikanDocument\SuratPerintahPenghentianPenyelidikanDocumentOfficer;
use App\Models\Doc\SuratPerintahPenghentianPenyelidikanDocument\SuratPerintahPenghentianPenyelidikanDocumentAttachment;
use App\Traits\DocsOfficersTraits;

class SuratPerintahPenghentianPenyelidikanDocumentController extends Controller
{
    protected $docService;

    use DocsOfficersTraits;

    // Master alasan penghentian perkara (sesuai referensi SPPT-TI & SOP Mindik Polri)
    public static $masterAlasan = [
        1  => 'Peristiwa tersebut bukan merupakan tindak pidana',
        2  => 'Tidak terdapat cukup alat bukti',
        3  => 'Penyelidikan dihentikan demi hukum',
        4  => 'Terdapat putusan pengadilan yang telah memperoleh kekuatan hukum tetap terhadap tersangka/pelaku atas perkara yang sama',
        5  => 'Kedaluarsa',
        6  => 'Tersangka/Pelaku meninggal dunia',
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

        // Sprinlidik yang ada pada perkara ini
        $sprinlidikDocuments = SuratPerintahPenyelidikanDocument::where('accident_id', $accidentId)
            ->orderBy('created_at', 'desc')
            ->get();

        // Laporan Hasil Gelar Perkara yang ada pada perkara ini
        $lhgpDocuments = LaporanHasilGelarPerkaraDocument::where('accident_id', $accidentId)
            ->orderBy('created_at', 'desc')
            ->get();

        // Pihak terkait / Tersangka / Pengemudi di perkara ini
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

        $teamOfficers = Officer::withRelated()
            ->selectFullName()
            ->whereIn('police_id', $getOldNewPolresIds)
            ->active()
            ->valid()
            ->orderBy('first_name')
            ->get();

        $defaultDugaan = 'Kecelakaan Lalu Lintas';
        $defaultPasal  = $accident->pasal_names ?? 'Pasal 310 UU No. 22 Tahun 2009';

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

        $defaultLocation = $accident->crime_scene_district
            ? ($accident->crime_scene_district . ', ' . ($accident->polres->polres_regency ?? ($accident->polres->name ?? '')))
            : ($accident->road_name ?: ($accident->polres->polres_regency ?? ''));

        $defaultTime = $accident->accident_date
            ? (Carbon::parse($accident->accident_date)->locale('id')->isoFormat('D MMMM Y') . ($accident->accident_time ? ' sekira pukul ' . $accident->accident_time . ' WIB' : ''))
            : '';

        $viewData = [
            'accidentId'              => $accidentId,
            'accident'                => $accident,
            'sprinlidikDocuments'     => $sprinlidikDocuments,
            'lhgpDocuments'           => $lhgpDocuments,
            'suspects'                => $suspects,
            'documentClassifications' => $documentClassifications,
            'authorizedSignatories'   => $authorizedSignatories,
            'teamOfficers'            => $teamOfficers,
            'masterAlasan'            => self::$masterAlasan,
            'defaultDugaan'           => $defaultDugaan,
            'defaultPasal'            => $defaultPasal,
            'defaultReporter'         => $defaultReporter,
            'defaultLocation'         => $defaultLocation,
            'defaultTime'             => $defaultTime,
            'defaultLhgpId'           => $lhgpDocuments->first()->id ?? null,
        ];

        return view('docs.surat-perintah-penghentian-penyelidikan-document.create', $viewData);
    }

    // ─────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────
    public function store(Request $request)
    {
        $accidentId = htmlspecialchars($request->accident_id ?? $request->query('accident_id'));

        $validator = $this->validateForm($request);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $documentNumber = htmlspecialchars($request->document_number);
        $documentDate   = htmlspecialchars($request->document_date);

        $kodeAlasan   = $request->kode_alasan ?? [];
        $signatoryId  = htmlspecialchars($request->signatory);
        $suspects     = $request->suspects ?? [];
        $officerIds   = $request->officers ?? [];
        $carbonCopies = array_values(array_filter($request->carbon_copies ?? ($request->carbonCopies ?? []), fn($v) => !empty(trim($v))));

        $sprinlidikId = $request->surat_perintah_penyelidikan_document_id;
        if (empty($sprinlidikId)) {
            $defaultSprinlidik = SuratPerintahPenyelidikanDocument::where('accident_id', $accidentId)->latest()->first();
            $sprinlidikId = $defaultSprinlidik ? $defaultSprinlidik->id : null;
        }

        $lhgpId = $request->laporan_hasil_gelar_perkara_document_id;
        if (empty($lhgpId)) {
            $defaultLhgp = LaporanHasilGelarPerkaraDocument::where('accident_id', $accidentId)->latest()->first();
            $lhgpId = $defaultLhgp ? $defaultLhgp->id : null;
        }

        DB::beginTransaction();
        try {
            $sprintHenti = SuratPerintahPenghentianPenyelidikanDocument::create([
                'accident_id'                           => $accidentId,
                'document_number'                       => $documentNumber,
                'document_date'                         => $documentDate,
                'document_location'                     => $request->document_location,
                'document_classification_id'            => $request->document_classification_id,
                'surat_perintah_penyelidikan_document_id'=> $sprinlidikId,
                'laporan_hasil_gelar_perkara_document_id'=> $lhgpId,
                'dugaan_tindak_pidana'                  => $request->dugaan_tindak_pidana,
                'pasal_list'                            => $request->pasal_list,
                'kode_alasan'                           => $kodeAlasan,
                'alasan_penghentian'                    => $request->alasan_penghentian,
                'pertimbangan'                          => $request->pertimbangan,
                'dasar'                                 => $request->dasar,
                'untuk'                                 => $request->untuk,
                'carbon_copies'                         => $carbonCopies,
                'status_id'                             => '2',
                'document_category_id'                  => '0113',
                'created_by_user_id'                    => Auth::id(),
                'messages'                              => [
                    'signatory_id'         => $signatoryId,
                    'suspect_ids'          => $suspects,
                    'officer_ids'          => $officerIds,
                    'pelapor_name'         => $request->pelapor_name,
                    'crime_scene_location' => $request->crime_scene_location,
                    'incident_time'        => $request->incident_time,
                ],
            ]);

            // Sync Suspects (Terlapor/pihak terkait)
            if (!empty($suspects)) {
                $sprintHenti->suspects()->sync($suspects);
            }

            // Simpan Signatory (Pemberi Perintah)
            $signatory = Officer::find($signatoryId);
            if ($signatory) {
                $sprintHenti->suratPerintahPenghentianPenyelidikanDocumentOfficers()->create([
                    'surat_perintah_penghentian_penyelidikan_document_id' => $sprintHenti->id,
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

            // Simpan Penyelidik Penerima Perintah (Members)
            if (!empty($officerIds)) {
                $sort = 1;
                foreach ($officerIds as $offId) {
                    $officer = Officer::find($offId);
                    if ($officer) {
                        $sprintHenti->suratPerintahPenghentianPenyelidikanDocumentOfficers()->create([
                            'surat_perintah_penghentian_penyelidikan_document_id' => $sprintHenti->id,
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
                $fileName = time() . '_sprint_henti_lidik_' . str_replace(' ', '_', $file->getClientOriginalName());
                $file->move(public_path('documents/attachments/'), $fileName);

                $sprintHenti->suratPerintahPenghentianPenyelidikanDocumentAttachment()->create([
                    'name'           => $fileName,
                    'file_path'      => 'documents/attachments/' . $fileName,
                    'file_extension' => pathinfo($fileName, PATHINFO_EXTENSION),
                    'file_size'      => file_exists(public_path('documents/attachments/' . $fileName)) ? filesize(public_path('documents/attachments/' . $fileName)) : 0,
                    'is_active'      => true,
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
            ->with('success', 'Surat Perintah Penghentian Penyelidikan berhasil dibuat.');
    }

    // ─────────────────────────────────────────────
    // SHOW
    // ─────────────────────────────────────────────
    public function show($id)
    {
        $sprintHenti = SuratPerintahPenghentianPenyelidikanDocument::with([
            'accident',
            'accident.polres',
            'suratPerintahPenyelidikanDocument',
            'laporanHasilGelarPerkaraDocument',
            'suspects',
            'suratPerintahPenghentianPenyelidikanDocumentAttachment',
            'suratPerintahPenghentianPenyelidikanDocumentOfficers',
            'documentClassification',
            'status',
        ])->where('id', $id)->firstOrFail();

        $accidentId = $sprintHenti->accident_id;
        $accident   = $sprintHenti->accident;

        $viewData = [
            'sprintHenti'  => $sprintHenti,
            'accidentId'   => $accidentId,
            'accident'     => $accident,
            'masterAlasan' => self::$masterAlasan,
        ];

        return view('docs.surat-perintah-penghentian-penyelidikan-document.show', $viewData);
    }

    // ─────────────────────────────────────────────
    // EDIT
    // ─────────────────────────────────────────────
    public function edit($id)
    {
        $sprintHenti = SuratPerintahPenghentianPenyelidikanDocument::with([
            'suspects',
            'suratPerintahPenghentianPenyelidikanDocumentOfficers',
            'suratPerintahPenghentianPenyelidikanDocumentAttachment',
        ])->where('id', $id)->firstOrFail();

        $accidentId = $sprintHenti->accident_id;
        $accident   = Accident::with(['polres', 'polres.polda'])->where('id', $accidentId)->firstOrFail();

        $sprinlidikDocuments = SuratPerintahPenyelidikanDocument::where('accident_id', $accidentId)
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

        $teamOfficers = Officer::withRelated()
            ->selectFullName()
            ->whereIn('police_id', $getOldNewPolresIds)
            ->active()
            ->valid()
            ->orderBy('first_name')
            ->get();

        $selectedSignatoryId = optional($sprintHenti->suratPerintahPenghentianPenyelidikanDocumentOfficers->firstWhere('class', 'SIGNATORY'))->register_number
            ? Officer::where('register_number', optional($sprintHenti->suratPerintahPenghentianPenyelidikanDocumentOfficers->firstWhere('class', 'SIGNATORY'))->register_number)->value('id')
            : null;

        $selectedOfficerIds = [];
        $memberOfficers = $sprintHenti->suratPerintahPenghentianPenyelidikanDocumentOfficers->where('class', 'MEMBER');
        foreach ($memberOfficers as $m) {
            $offId = Officer::where('register_number', $m->register_number)->value('id');
            if ($offId) {
                $selectedOfficerIds[] = $offId;
            }
        }

        $viewData = [
            'sprintHenti'            => $sprintHenti,
            'accidentId'             => $accidentId,
            'accident'               => $accident,
            'sprinlidikDocuments'    => $sprinlidikDocuments,
            'lhgpDocuments'          => $lhgpDocuments,
            'suspects'               => $suspects,
            'documentClassifications'=> $documentClassifications,
            'authorizedSignatories' => $authorizedSignatories,
            'teamOfficers'           => $teamOfficers,
            'masterAlasan'           => self::$masterAlasan,
            'selectedSignatoryId'    => $selectedSignatoryId,
            'selectedOfficerIds'     => $selectedOfficerIds,
        ];

        return view('docs.surat-perintah-penghentian-penyelidikan-document.edit', $viewData);
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────
    public function update(Request $request, $id)
    {
        $sprintHenti = SuratPerintahPenghentianPenyelidikanDocument::findOrFail($id);
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
        $carbonCopies = array_values(array_filter($request->carbon_copies ?? ($request->carbonCopies ?? []), fn($v) => !empty(trim($v))));

        $sprinlidikId = $request->surat_perintah_penyelidikan_document_id;
        if (empty($sprinlidikId)) {
            $defaultSprinlidik = SuratPerintahPenyelidikanDocument::where('accident_id', $accidentId)->latest()->first();
            $sprinlidikId = $defaultSprinlidik ? $defaultSprinlidik->id : null;
        }

        $lhgpId = $request->laporan_hasil_gelar_perkara_document_id;
        if (empty($lhgpId)) {
            $defaultLhgp = LaporanHasilGelarPerkaraDocument::where('accident_id', $accidentId)->latest()->first();
            $lhgpId = $defaultLhgp ? $defaultLhgp->id : null;
        }

        DB::beginTransaction();
        try {
            $sprintHenti->update([
                'document_number'                       => $documentNumber,
                'document_date'                         => $documentDate,
                'document_location'                     => $request->document_location,
                'document_classification_id'            => $request->document_classification_id,
                'surat_perintah_penyelidikan_document_id'=> $sprinlidikId,
                'laporan_hasil_gelar_perkara_document_id'=> $lhgpId,
                'dugaan_tindak_pidana'                  => $request->dugaan_tindak_pidana,
                'pasal_list'                            => $request->pasal_list,
                'kode_alasan'                           => $kodeAlasan,
                'alasan_penghentian'                    => $request->alasan_penghentian,
                'pertimbangan'                          => $request->pertimbangan,
                'dasar'                                 => $request->dasar,
                'untuk'                                 => $request->untuk,
                'carbon_copies'                         => $carbonCopies,
                'updated_by_user_id'                    => Auth::id(),
                'messages'                              => array_merge($sprintHenti->messages ?? [], [
                    'signatory_id'         => $signatoryId,
                    'suspect_ids'          => $suspects,
                    'officer_ids'          => $officerIds,
                    'pelapor_name'         => $request->pelapor_name,
                    'crime_scene_location' => $request->crime_scene_location,
                    'incident_time'        => $request->incident_time,
                ]),
            ]);

            // Sync Suspects
            $sprintHenti->suspects()->sync($suspects);

            // Re-sync Officers
            $sprintHenti->suratPerintahPenghentianPenyelidikanDocumentOfficers()->delete();

            $signatory = Officer::find($signatoryId);
            if ($signatory) {
                $sprintHenti->suratPerintahPenghentianPenyelidikanDocumentOfficers()->create([
                    'surat_perintah_penghentian_penyelidikan_document_id' => $sprintHenti->id,
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
                        $sprintHenti->suratPerintahPenghentianPenyelidikanDocumentOfficers()->create([
                            'surat_perintah_penghentian_penyelidikan_document_id' => $sprintHenti->id,
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
                $fileName = time() . '_sprint_henti_lidik_' . str_replace(' ', '_', $file->getClientOriginalName());
                $file->move(public_path('documents/attachments/'), $fileName);

                $sprintHenti->suratPerintahPenghentianPenyelidikanDocumentAttachment()->updateOrCreate(
                    ['surat_perintah_penghentian_penyelidikan_document_id' => $sprintHenti->id],
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
            ->with('success', 'Surat Perintah Penghentian Penyelidikan berhasil diperbarui.');
    }

    // ─────────────────────────────────────────────
    // DELETE
    // ─────────────────────────────────────────────
    public function delete($id)
    {
        $sprintHenti = SuratPerintahPenghentianPenyelidikanDocument::findOrFail($id);
        $accidentId  = $sprintHenti->accident_id;

        $sprintHenti->delete();

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
            ->with('success', 'Surat Perintah Penghentian Penyelidikan berhasil dihapus.');
    }

    // ─────────────────────────────────────────────
    // DOWNLOAD WORD
    // ─────────────────────────────────────────────
    public function download($id)
    {
        $sprintHenti = SuratPerintahPenghentianPenyelidikanDocument::with([
            'accident',
            'accident.polres',
            'accident.polres.polda',
            'accident.suspects',
            'accident.suspects.gender',
            'accident.suspects.job',
            'accident.suspects.country',
            'suratPerintahPenyelidikanDocument',
            'laporanHasilGelarPerkaraDocument',
            'suspects',
            'suspects.gender',
            'suspects.job',
            'suspects.country',
            'suratPerintahPenghentianPenyelidikanDocumentOfficers',
        ])->where('id', $id)->firstOrFail();

        $accident = $sprintHenti->accident;
        $polres   = $accident->polres ?? null;
        $polda    = $polres->polda ?? null;

        $daerahPoliceFullName = $polda ? strtoupper($polda->name ?? $polda->full_name) : '-';
        $daerahPoliceFullName = trim(preg_replace('/^(POLDA|KEPOLISIAN DAERAH)\s+/i', '', $daerahPoliceFullName));
        $resorPoliceFullName  = $polres ? strtoupper($polres->name ?? $polres->full_name) : '-';
        $resorPoliceFullName  = 'RESOR ' . trim(preg_replace('/^(RESOR|POLRES|KEPOLISIAN RESOR)\s+/i', '', $resorPoliceFullName));
        $resorPoliceAddress   = $polres ? ucwords($polres->address ?? '') : '-';

        $documentNumber   = $sprintHenti->document_number ?? '-';
        $documentLocation = $sprintHenti->document_location ?: ($polres->polres_regency ?? ($polres->name ?? '-'));
        $accidentNumber   = trim(preg_replace('/^Nomor\s*:\s*/i', '', $accident->no_lp ?? '-'));
        $accidentDate     = $accident->accident_date ? Carbon::parse($accident->accident_date)->locale('id')->isoFormat('D MMMM Y') : '-';
        $roadName         = $accident->road_name ?: '-';

        // Sprinlidik Dasar
        $sprinlidik = $sprintHenti->suratPerintahPenyelidikanDocument
            ?: SuratPerintahPenyelidikanDocument::where('accident_id', $accident->id)->latest()->first();
        $suratPerintahPenyelidikanDocumentNumber = $sprinlidik->document_number ?? '-';
        $suratPerintahPenyelidikanDocumentNumber = trim(preg_replace('/^Nomor\s*:\s*/i', '', $suratPerintahPenyelidikanDocumentNumber));
        $rawSprinlidikDate = $sprinlidik ? ($sprinlidik->document_date ?: ($sprinlidik->date ?: null)) : null;
        $suratPerintahPenyelidikanDocumentDate   = $rawSprinlidikDate ? Carbon::parse($rawSprinlidikDate)->locale('id')->isoFormat('D MMMM Y') : '-';


        $crimeSceneLocation = $sprintHenti->messages['crime_scene_location'] ?? ($accident->crime_scene_district ? ($accident->crime_scene_district . ', ' . ($polres->polres_regency ?? '')) : ($accident->road_name ?: '-'));
        $incidentTime       = $sprintHenti->messages['incident_time'] ?? ($accident->accident_date ? (Carbon::parse($accident->accident_date)->locale('id')->isoFormat('D MMMM Y') . ($accident->accident_time ? ' sekira pukul ' . $accident->accident_time . ' WIB' : '')) : '-');
        $reporterName       = $sprintHenti->messages['pelapor_name'] ?? ($accident->reporter_name ?: 'Pelapor');

        // LHGP Dasar
        $lhgp = $sprintHenti->laporanHasilGelarPerkaraDocument
            ?: LaporanHasilGelarPerkaraDocument::where('accident_id', $accident->id)->latest()->first();
        $rawLhgpDate = $lhgp ? ($lhgp->document_date ?: ($lhgp->date ?: null)) : null;
        $laporanHasilGelarPerkaraDocumentDate = $rawLhgpDate ? Carbon::parse($rawLhgpDate)->locale('id')->isoFormat('D MMMM Y') : '-';

        // Pihak terkait / Terlapor
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
        if (strtoupper($suspectNationality) === 'INDONESIA') {
            $suspectNationality = 'INDONESIA';
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
        $alasanPenghentian = !empty($alasanList) ? implode('; ', $alasanList) : ($sprintHenti->alasan_penghentian ?: 'Bukan merupakan tindak pidana');

        $documentDate = $sprintHenti->document_date ? Carbon::parse($sprintHenti->document_date)->locale('id')->isoFormat('D MMMM Y') : '-';

        // Signatory (Yang Memberi Perintah)
        $signatory = $sprintHenti->suratPerintahPenghentianPenyelidikanDocumentOfficers->firstWhere('class', 'SIGNATORY');
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
        $members = $sprintHenti->suratPerintahPenghentianPenyelidikanDocumentOfficers
            ->where('class', 'MEMBER')
            ->sortBy('sort')
            ->values();

        // Yang Menerima Perintah (Penyelidik Pertama)
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

        $templatePath = public_path('word-template/surat_perintah_penghentian_penyelidikan.docx');
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
            'roadName'                                             => $roadName,
            'reporterName'                                         => $reporterName,
            'laporanHasilGelarPerkaraDocumentDate'                 => $laporanHasilGelarPerkaraDocumentDate,
            'suratPerintahPenyelidikanDocumentNumber'              => $suratPerintahPenyelidikanDocumentNumber,
            'suratPerintahPenyelidikanDocumentDate'                => $suratPerintahPenyelidikanDocumentDate,
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
            'receiverOfficerRankName'                              => strtoupper($receiverOfficerRankName),
            'receiverOfficerRegisterNumber'                        => $receiverOfficerRegisterNumber,
            'documentDate'                                         => $documentDate,
            'suratPerintahPenyidikanDocumentNumber'                => $suratPerintahPenyelidikanDocumentNumber,
            'crimeSceneLocation'                                  => $crimeSceneLocation,
            'incidentTime'                                        => $incidentTime,
            'suratPemberitahuanDimulainyaPenyidikanDocumentNumber' => '-',
            'suratPemberitahuanDimulainyaPenyidikanDocumentDate'   => '-',
            'suratKetetapanPenetapanTersangkaDocumentNumber'       => '-',
            'suratKetetapanPenetapanTersangkaDocumentDate'         => '-',
        ]);

        $filename = 'generate/' . $sprintHenti->id . ' - Surat Perintah Penghentian Penyelidikan - ' . ($polres->full_name ?? '');
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
            'document_number.required' => 'Nomor Surat Perintah Penghentian Penyelidikan wajib diisi.',
            'document_date.required'   => 'Tanggal Surat Perintah Penghentian Penyelidikan wajib diisi.',
            'signatory.required'       => 'Pejabat Pemberi Perintah (Penandatangan) wajib dipilih.',
            'kode_alasan.required'     => 'Minimal satu alasan penghentian penyelidikan wajib dipilih.',
            'kode_alasan.min'          => 'Minimal satu alasan penghentian penyelidikan wajib dipilih.',
        ]);
    }
}
