<?php

namespace App\Http\Controllers\Doc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Helpers\PeopleNameHelper;

use App\Services\Doc\DocService;

use App\Models\Doc\SuratPemberitahuanPenghentianPenyidikanDocument\SuratPemberitahuanPenghentianPenyidikanDocument;
use App\Models\Doc\SuratPemberitahuanPenghentianPenyidikanDocument\SuratPemberitahuanPenghentianPenyidikanDocumentOfficer;
use App\Models\Doc\SuratPemberitahuanDimulainyaPenyidikanDocument\SuratPemberitahuanDimulainyaPenyidikanDocument;
use App\Models\Accident;
use App\Models\Officer;
use App\Models\Suspect;
use App\Models\Lib\Ref;
use App\Models\Lib\DocumentClassification;
use App\Models\Opt\Status;

use App\Models\Doc\SuratPerintahPenghentianPenyidikanDocument\SuratPerintahPenghentianPenyidikanDocument;
use App\Models\Doc\SuratKetetapanPenghentianPenyidikanDocument\SuratKetetapanPenghentianPenyidikanDocument;
use App\Models\Lib\Prosecutor;
use App\Models\Lib\Court;
use App\Traits\DocsOfficersTraits;

/**
 * Sp3PusiknasDocumentController
 *
 * Controller form web untuk SP3 (Surat Pemberitahuan Penghentian Penyidikan)
 * versi Pusiknas Bareskrim — sesuai skema SPPT-TI.
 *
 * Field SPPT-TI (identitas_dokumen):
 *   nomor, tanggal, nomor_spdp
 *
 * Field SPPT-TI (konten_dokumen):
 *   kode_alasan[] (array integer), pejabat_penandatangan[], daftar_terlapor_atau_tersangka[]
 *
 * Alur: menyimpan ke tabel sp3 yang sudah ada.
 */
class SuratPemberitahuanPenghentianPenyidikanDocumentController extends Controller
{
    protected $docService;

    use DocsOfficersTraits;

    // Master alasan penghentian perkara (sesuai referensi SPPT-TI)
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

    // ─────────────────────────────────────────────
    // CREATE
    // ─────────────────────────────────────────────
    public function create()
    {
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $accident   = Accident::with(['polres', 'polres.polda'])->where('id', $accidentId)->first();

        // Ambil SPDP yang sudah diterbitkan
        $spdpDocuments = SuratPemberitahuanDimulainyaPenyidikanDocument::where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->get()
            ->merge(
                \App\Models\Doc\SuratPemberitahuanDimulainyaPenyidikanPusiknasDocument\SuratPemberitahuanDimulainyaPenyidikanPusiknasDocument::where('accident_id', $accidentId)
                    ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
                    ->get()
            )->sortByDesc('created_at')->values();

        // Tersangka di perkara ini
        $suspects = Suspect::where('accident_id', $accidentId)
            ->where('flag', Suspect::getEnumOption('flag', 'TERSANGKA'))
            ->get();
            
        $documentClassifications = DocumentClassification::where('group', 'SURAT_PEMBERITAHUAN_DIMULAINYA_PENYIDIKAN')
            ->where('is_active', true)->orderBy('sort')->get();

        $getOldNewPolresIds = $this->getOldNewPolresIds($accident->polres_id);
        $authorizedSignatories = Officer::withRelated()
            ->selectFullName()
            ->whereIn('police_id', $getOldNewPolresIds)
            ->whereHasUserActive()
            ->hasDataComplete()
            ->signatory()->active()->valid()
            ->orderBy('first_name')->get();

        $masterAlasan = self::$masterAlasan;

        $sprintHentiDocuments = SuratPerintahPenghentianPenyidikanDocument::where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->orderBy('created_at', 'desc')->get();

        $sketHentiDocuments = SuratKetetapanPenghentianPenyidikanDocument::where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->orderBy('created_at', 'desc')->get();

        $prosecutors = Prosecutor::where('is_active', true)->orderBy('name')->get();
        $courts      = Court::where('is_active', true)->orderBy('name')->get();

        $latestSketHenti     = $sketHentiDocuments->first();
        $defaultProsecutorId = $latestSketHenti ? $latestSketHenti->prosecutor_id : null;
        $defaultCourtId      = $latestSketHenti ? $latestSketHenti->court_id : null;
        if (!$defaultProsecutorId && $spdpDocuments->isNotEmpty()) {
            $defaultProsecutorId = $spdpDocuments->first()->prosecutor_id;
        }

        return view('docs.surat-pemberitahuan-penghentian-penyidikan-document.create', compact(
            'accidentId',
            'accident',
            'spdpDocuments',
            'sprintHentiDocuments',
            'sketHentiDocuments',
            'suspects',
            'documentClassifications',
            'authorizedSignatories',
            'masterAlasan',
            'prosecutors',
            'courts',
            'defaultProsecutorId',
            'defaultCourtId'
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

        // identitas_dokumen
        $noSp3      = htmlspecialchars($request->noSp3);
        $tanggalSp3 = htmlspecialchars($request->tanggalSp3);
        $noSpdp     = htmlspecialchars($request->noSpdp);

        $exists = SuratPemberitahuanPenghentianPenyidikanDocument::where('accident_id', $accidentId)
            ->where('document_number', 'ILIKE', $noSp3)
            ->exists();
        if ($exists) {
            return redirect()->back()
                ->withErrors(['noSp3' => 'Nomor dokumen "' . $noSp3 . '" sudah ada sebelumnya. Gunakan nomor yang berbeda.'])
                ->withInput();
        }

        // konten_dokumen
        $kodeAlasan   = $request->kode_alasan ?? [];   // array of int
        $signatoryId  = htmlspecialchars($request->signatory);
        $suspects     = $request->suspects ?? [];
        $prosecutorId = htmlspecialchars($request->prosecutor_id ?? '');
        $courtId      = htmlspecialchars($request->court_id ?? '');

        $klasifikasi = htmlspecialchars($request->klasifikasi ?? '');
        $noSkPenghentian = htmlspecialchars($request->noSkPenghentian ?? '');
        $tanggalSkPenghentian = htmlspecialchars($request->tanggalSkPenghentian ?? '');
        $noSpPenghentian = htmlspecialchars($request->noSpPenghentian ?? '');
        $tanggalSpPenghentian = htmlspecialchars($request->tanggalSpPenghentian ?? '');
        $carbonCopies = $request->carbonCopies ?? [];
        $appendix = $request->appendix ?? $request->lampiran ?? 0;

        // Cek duplikat
        $exists = SuratPemberitahuanPenghentianPenyidikanDocument::where('accident_id', $accidentId)->where('document_number', 'ILIKE', $noSp3)->exists();
        if ($exists) {
            return redirect()->back()->with('error', 'SP3 dengan nomor ' . $noSp3 . ' sudah ada.');
        }

        DB::beginTransaction();
        try {
            // Simpan ke tabel sp3
            $sp3 = SuratPemberitahuanPenghentianPenyidikanDocument::create([
                'accident_id'                => $accidentId,
                'document_number'            => $noSp3,
                'document_date'              => $tanggalSp3,
                'document_classification_id' => $klasifikasi,
                'prosecutor_id'              => $prosecutorId ?: null,
                'court_id'                   => $courtId ?: null,
                'no_spdp'                    => $noSpdp,
                'no_sk_penghentian'          => $noSkPenghentian,
                'tanggal_sk_penghentian'     => $tanggalSkPenghentian,
                'no_sp_penghentian'          => $noSpPenghentian,
                'tanggal_sp_penghentian'     => $tanggalSpPenghentian,
                'kode_alasan'                => json_encode(array_map('intval', $kodeAlasan)),
                'suspect_ids'                => json_encode($suspects),
                'carbon_copies'              => $carbonCopies,
                'appendix'                   => $appendix,
                'messages'                   => [
                    'signatory_id'     => $signatoryId,
                    'sumber'           => 'PUSIKNAS_FORM',
                    'kode_alasan_raw'  => $kodeAlasan,
                ],
            ]);

            if (!empty($suspects)) {
                $sp3->suspects()->sync($suspects);
            }

            // Insert Officer (Penandatangan)
            $signatory = Officer::find($signatoryId);
            if ($signatory) {
                $sp3->suratPemberitahuanPenghentianPenyidikanDocumentOfficers()->create([
                    'surat_pemberitahuan_penghentian_penyidikan_document_id' => $sp3->id,
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
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId]);
    }

    // ─────────────────────────────────────────────
    // SHOW
    // ─────────────────────────────────────────────
    public function show($id)
    {
        $accidentId  = htmlspecialchars(request()->query('accident_id'));
        $sp3         = SuratPemberitahuanPenghentianPenyidikanDocument::with(['accident', 'accident.polres', 'accident.polres.polda', 'accident.suspects', 'prosecutor', 'court'])->where('id', $id)->firstOrFail();
        $accident    = Accident::with(['polres', 'polres.polda'])->where('id', $accidentId)->first();
        $masterAlasan = self::$masterAlasan;

        $kodeAlasan = json_decode($sp3->kode_alasan, true) ?? [];
        $extraData  = is_string($sp3->messages) ? json_decode($sp3->messages, true) : ($sp3->messages ?? []);

        return view('docs.surat-pemberitahuan-penghentian-penyidikan-document.show', compact(
            'accidentId',
            'accident',
            'sp3',
            'masterAlasan',
            'kodeAlasan',
            'extraData'
        ));
    }

    // ─────────────────────────────────────────────
    // EDIT
    // ─────────────────────────────────────────────
    public function edit($id)
    {
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $sp3        = SuratPemberitahuanPenghentianPenyidikanDocument::where('id', $id)->firstOrFail();

        if (!$sp3->isEditable()) {
            return redirect()->back()->with('error', 'Dokumen tidak dapat diedit karena sudah disetujui atau sedang dalam proses persetujuan.');
        }

        $accident   = Accident::with(['polres', 'polres.polda'])->where('id', $accidentId)->first();

        $spdpDocuments = SuratPemberitahuanDimulainyaPenyidikanDocument::where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->get()
            ->merge(
                \App\Models\Doc\SuratPemberitahuanDimulainyaPenyidikanPusiknasDocument\SuratPemberitahuanDimulainyaPenyidikanPusiknasDocument::where('accident_id', $accidentId)
                    ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
                    ->get()
            )->sortByDesc('created_at')->values();

        $suspects = Suspect::where('accident_id', $accidentId)
            ->where('flag', Suspect::getEnumOption('flag', 'TERSANGKA'))->get();
            
        $documentClassifications = DocumentClassification::where('group', 'SURAT_PEMBERITAHUAN_DIMULAINYA_PENYIDIKAN')
            ->where('is_active', true)->orderBy('sort')->get();

        $getOldNewPolresIds = $this->getOldNewPolresIds($accident->polres_id);
        $authorizedSignatories = Officer::withRelated()
            ->selectFullName()
            ->whereIn('police_id', $getOldNewPolresIds)
            ->whereHasUserActive()
            ->hasDataComplete()
            ->signatory()->active()->valid()
            ->orderBy('first_name')->get();

        $masterAlasan   = self::$masterAlasan;
        $kodeAlasan     = json_decode($sp3->kode_alasan, true) ?? [];
        $extraData      = is_string($sp3->messages) ? json_decode($sp3->messages, true) : ($sp3->messages ?? []);

        $sprintHentiDocuments = SuratPerintahPenghentianPenyidikanDocument::where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->orderBy('created_at', 'desc')->get();

        $sketHentiDocuments = SuratKetetapanPenghentianPenyidikanDocument::where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->orderBy('created_at', 'desc')->get();

        $prosecutors = Prosecutor::where('is_active', true)->orderBy('name')->get();
        $courts      = Court::where('is_active', true)->orderBy('name')->get();

        return view('docs.surat-pemberitahuan-penghentian-penyidikan-document.edit', compact(
            'accidentId',
            'accident',
            'sp3',
            'spdpDocuments',
            'sprintHentiDocuments',
            'sketHentiDocuments',
            'suspects',
            'documentClassifications',
            'authorizedSignatories',
            'masterAlasan',
            'kodeAlasan',
            'extraData',
            'prosecutors',
            'courts'
        ));
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────
    public function update(Request $request, $id)
    {
        $accidentId = htmlspecialchars($request->accident_id);
        $sp3        = SuratPemberitahuanPenghentianPenyidikanDocument::where('id', $id)->firstOrFail();

        if (!$sp3->isEditable()) {
            return redirect()->back()->with('error', 'Dokumen tidak dapat diedit karena sudah disetujui atau sedang dalam proses persetujuan.');
        }

        $validator  = $this->validateForm($request);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $exists = SuratPemberitahuanPenghentianPenyidikanDocument::where('accident_id', $accidentId)
            ->where('document_number', 'ILIKE', htmlspecialchars($request->noSp3))
            ->where('id', '!=', $id)
            ->exists();
        if ($exists) {
            return redirect()->back()
                ->withErrors(['noSp3' => 'Nomor dokumen "' . htmlspecialchars($request->noSp3) . '" sudah ada sebelumnya. Gunakan nomor yang berbeda.'])
                ->withInput();
        }

        $kodeAlasan = $request->kode_alasan ?? [];
        $extraData  = is_string($sp3->messages) ? json_decode($sp3->messages, true) : ($sp3->messages ?? []);

        DB::beginTransaction();
        try {
            $sp3->update([
                'document_number'            => htmlspecialchars($request->noSp3),
                'document_date'              => htmlspecialchars($request->tanggalSp3),
                'document_classification_id' => htmlspecialchars($request->klasifikasi ?? ''),
                'prosecutor_id'              => htmlspecialchars($request->prosecutor_id ?? '') ?: null,
                'court_id'                   => htmlspecialchars($request->court_id ?? '') ?: null,
                'no_spdp'                    => htmlspecialchars($request->noSpdp),
                'no_sk_penghentian'          => htmlspecialchars($request->noSkPenghentian),
                'tanggal_sk_penghentian'     => htmlspecialchars($request->tanggalSkPenghentian),
                'no_sp_penghentian'          => htmlspecialchars($request->noSpPenghentian),
                'tanggal_sp_penghentian'     => htmlspecialchars($request->tanggalSpPenghentian),
                'kode_alasan'                => json_encode(array_map('intval', $kodeAlasan)),
                'suspect_ids'                => json_encode($request->suspects ?? []),
                'carbon_copies'              => $request->carbonCopies ?? [],
                'appendix'                   => $request->appendix ?? $request->lampiran ?? 0,
                'messages'                   => [
                    'signatory_id'    => htmlspecialchars($request->signatory),
                    'sumber'          => 'PUSIKNAS_FORM',
                    'kode_alasan_raw' => $kodeAlasan,
                    'file_name'       => $extraData['file_name'] ?? null,
                ],
            ]);

            $suspects = $request->suspects ?? [];
            $sp3->suspects()->sync($suspects);

            // Re-insert Officer (delete old first if necessary, or just insert)
            DB::table('doc.surat_pemberitahuan_penghentian_penyidikan_document_officers')
                ->where('surat_pemberitahuan_penghentian_penyidikan_document_id', $sp3->id)
                ->where('class', 'SIGNATORY')
                ->delete();
            
            $officer = Officer::find($request->signatory);
            if ($officer) {
                $sp3->suratPemberitahuanPenghentianPenyidikanDocumentOfficers()->create([
                    'surat_pemberitahuan_penghentian_penyidikan_document_id' => $sp3->id,
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
                    'class'           => 'SIGNATORY',
                    'flag'            => 'INTERNAL',
                    'insert_method'   => 'IMPORT',
                    'sort'            => 0,
                ]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal mengubah data: ' . $e->getMessage());
        }

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId]);
    }

    // ─────────────────────────────────────────────
    // DELETE
    // ─────────────────────────────────────────────
    public function delete($id)
    {
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $sp3        = SuratPemberitahuanPenghentianPenyidikanDocument::where('id', $id)->firstOrFail();

        DB::beginTransaction();
        try {
            $sp3->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal menghapus dokumen.');
        }

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId]);
    }

    // ─────────────────────────────────────────────
    // DOWNLOAD (Generate Word)
    // ─────────────────────────────────────────────
    public function download($id)
    {
        $sp3 = SuratPemberitahuanPenghentianPenyidikanDocument::with([
            'suratPemberitahuanPenghentianPenyidikanDocumentOfficers',
            'documentClassification',
            'accident.polres.polda',
            'prosecutor.regency',
            'suratPerintahPenyidikanDocument.suratPerintahPenyidikanDocumentLaws.crimeConstitution',
            'suratPerintahTugasDocument.suratPerintahTugasDocumentOfficers',
            'suspects',
        ])->where('id', $id)->firstOrFail();

        $accidentId = $sp3->accident_id;
        $accident   = Accident::with(['polres.polda', 'police'])->where('id', $accidentId)->first();

        // Signatory
        $signatory = $sp3->suratPemberitahuanPenghentianPenyidikanDocumentOfficers()
            ->with(['position.positionCluster', 'rank'])
            ->where('class', 'SIGNATORY')
            ->first();
        if (!$signatory) {
            return redirect()->back()->with('error', 'Penandatangan belum diset.');
        }

        // Police Info
        $resorPolice          = $accident->polres;
        $daerahPoliceFullName = strtoupper($accident->polres->polda->full_name ?? '');
        $resorPoliceFullName  = $resorPolice ? ((in_array($resorPolice->id, ['1114'])) ? 'DIREKTORAT LALU LINTAS' : 'RESOR ' . strtoupper($resorPolice->full_name)) : '';
        $resorPoliceAddress   = $resorPolice ? ($resorPolice->address . ', ' . $resorPolice->polres_zipcode) : '';
        $documentLocation     = ucwords(strtolower($resorPolice->polres_regency ?? ($resorPolice->name ?? '')));

        // Signatory head text
        $signatoryPositionId = $signatory ? (is_array($signatory->position) ? ($signatory->position['id'] ?? null) : $signatory->position_id) : null;
        $signatoryPositionDetail = $signatoryPositionId
            ? \App\Models\Lib\Position::with('positionCluster')->find($signatoryPositionId)
            : null;

        $polresFullName = $accident->polres->full_name ?? ($resorPolice->full_name ?? '');
        $poldaFullName  = $accident->polres->polda->full_name ?? '';
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

        // Document fields
        $documentNumber             = $sp3->document_number;
        $documentDate               = \Carbon\Carbon::parse($sp3->document_date)->locale('id')->translatedFormat('d F Y');
        $documentClassificationName = $sp3->documentClassification->name ?? '';
        $appendix                   = $sp3->appendix ?? 0;
        $accidentNumber             = $accident->no_lp;
        $accidentDate               = \Carbon\Carbon::parse($accident->accident_date)->locale('id')->translatedFormat('d F Y');

        // SP3 fields
        $noSpdp               = $sp3->no_spdp ?? '-';
        $noSkPenghentian      = $sp3->no_sk_penghentian ?? '-';
        $tanggalSkPenghentian = $sp3->tanggal_sk_penghentian
            ? \Carbon\Carbon::parse($sp3->tanggal_sk_penghentian)->locale('id')->translatedFormat('d F Y') : '-';
        $noSpPenghentian      = $sp3->no_sp_penghentian ?? '-';
        $tanggalSpPenghentian = $sp3->tanggal_sp_penghentian
            ? \Carbon\Carbon::parse($sp3->tanggal_sp_penghentian)->locale('id')->translatedFormat('d F Y') : '-';

        // Alasan penghentian
        $masterAlasan = self::$masterAlasan;
        $kodeAlasan   = json_decode($sp3->kode_alasan, true) ?? [];
        $alasanTexts  = [];
        foreach ($kodeAlasan as $kode) {
            if (isset($masterAlasan[$kode])) {
                $alasanTexts[] = $masterAlasan[$kode];
            }
        }
        $alasanPenghentian = implode('; ', $alasanTexts);

        // Signatory info
        $signatoryName = trim(implode(' ', array_filter([
            $signatory->first_title ?? '',
            $signatory->first_name ?? '',
            $signatory->last_name ?? '',
            $signatory->last_title ?? ''
        ])));
        $signatoryName = $signatoryName ?: '-';
        $signatoryRank = $signatory->rank ?? ($signatory->rank_id ? \App\Models\Lib\Rank::find($signatory->rank_id) : null);
        $signatoryRankName = $signatoryRank->full_name ?? ($signatoryRank->name ?? '');
        $signatoryRegisterNumber = $signatory->register_number ?? '';

        // Suspect
        $suspect = $sp3->suspects->first();
        if (!$suspect && !empty($sp3->suspect_ids)) {
            $suspectIdsArray = json_decode($sp3->suspect_ids, true) ?? [];
            if (!empty($suspectIdsArray)) {
                $suspect = \App\Models\Suspect::whereIn('id', $suspectIdsArray)->first();
            }
        }
        $suspectName = $suspect ? $suspect->name : '-';
        
        // SPDP
        $spdp = SuratPemberitahuanDimulainyaPenyidikanDocument::where('document_number', $sp3->no_spdp)
            ->where('accident_id', $accidentId)->first();
        if (!$spdp) {
            $spdp = SuratPemberitahuanDimulainyaPenyidikanDocument::where('accident_id', $accidentId)->first();
        }
        $spdpNumber = $spdp ? $spdp->document_number : ($sp3->no_spdp ?? '-');
        $spdpDate = $spdp ? \Carbon\Carbon::parse($spdp->document_date)->locale('id')->translatedFormat('d F Y') : '-';

        // Prosecutor (from SP3 if available, otherwise from SPDP)
        $prosecutor = $sp3->prosecutor;
        if (!$prosecutor && $spdp) {
            $prosecutor = $spdp->prosecutor;
        }
        $prosecutorName     = $prosecutor ? ($prosecutor->name ?? $prosecutor->full_name ?? '-') : '-';
        $prosecutorLocation = $prosecutor ? ucwords(strtolower($prosecutor->regency->name ?? '-')) : '-';

        // Court (from SP3 if available)
        $court     = $sp3->court;
        $courtName = $court ? ($court->name ?? '-') : '-';

        // SKPPT
        $skppt = $suspect ? $suspect->suratKetetapanTentangPenetapanTersangkaDocument->first() : null;
        $skpptNumber = $skppt ? $skppt->document_number : '-';
        $skpptDate = $skppt ? \Carbon\Carbon::parse($skppt->document_date)->locale('id')->translatedFormat('d F Y') : '-';

        // SP Penyidikan
        $sprindik = $sp3->suratPerintahPenyidikanDocument;
        if (!$sprindik) {
            $sprindik = \App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument::with('suratPerintahPenyidikanDocumentLaws.crimeConstitution')
                ->where('accident_id', $accidentId)->first();
        }
        $sprindikNumber = $sprindik ? $sprindik->document_number : '-';
        $sprindikDocumentDay = $sprindik ? \Carbon\Carbon::parse($sprindik->document_date)->locale('id')->translatedFormat('l') : '-';
        $sprindikDate = $sprindik ? \Carbon\Carbon::parse($sprindik->document_date)->format('d') : '-';
        $sprindikMonth = $sprindik ? \Carbon\Carbon::parse($sprindik->document_date)->locale('id')->translatedFormat('F') : '-';
        $sprindikYear = $sprindik ? \Carbon\Carbon::parse($sprindik->document_date)->format('Y') : '-';
        $sprindikFullDate = $sprindik ? \Carbon\Carbon::parse($sprindik->document_date)->locale('id')->translatedFormat('d F Y') : '-';

        // SP Tugas (Ketua Tim)
        $spt = $sp3->suratPerintahTugasDocument;
        if (!$spt) {
            $spt = \App\Models\Doc\SuratPerintahTugasDocument\SuratPerintahTugasDocument::with('suratPerintahTugasDocumentOfficers')
                ->where('accident_id', $accidentId)->first();
        }
        $ketuaTimName = '-';
        $ketuaTimPhone = '-';
        if ($spt) {
            $ketuaTimOfficer = $spt->suratPerintahTugasDocumentOfficers()->where('class', 'LEADER')->first();
            if ($ketuaTimOfficer) {
                $ketuaTimName = \App\Helpers\PeopleNameHelper::getFullName($ketuaTimOfficer->first_title ?? '', $ketuaTimOfficer->first_name ?? '', $ketuaTimOfficer->last_name ?? '', $ketuaTimOfficer->last_title ?? '');
                $ketuaTimPhone = $ketuaTimOfficer->phone_number ?? '-';
            }
        }

        // Crime Constitution
        $crimeConstitutionText = $sprindik ? ($sprindik->pasal_formatted ?? '') : '';
        if (!$crimeConstitutionText && $sprindik && $sprindik->suratPerintahPenyidikanDocumentLaws) {
            $crimeTexts = [];
            foreach ($sprindik->suratPerintahPenyidikanDocumentLaws as $law) {
                if ($law->flag == 'MAIN') {
                    $crimeTexts[] = ($law->constitution_chapter ?? '') . ' ' . ($law->crimeConstitution->name ?? '');
                } elseif ($law->flag == 'ADDITIONAL') {
                    $crimeTexts[] = $law->constitution ?? '';
                }
            }
            $crimeConstitutionText = implode(', ', array_filter($crimeTexts));
        }

        // Carbon copies
        $carbonCopies      = $sp3->carbon_copies ?? '[]';
        if (is_string($carbonCopies)) {
            $carbonCopies = json_decode($carbonCopies, true) ?? [];
        }
        $no                = 1;
        $blockCarbonCopies = [];
        foreach ($carbonCopies as $carbonCopy) {
            $blockCarbonCopies[] = ['carbon_copy_iteration' => $no, 'carbon_copy_name' => $carbonCopy];
            $no++;
        }

        // QR Code
        $tempQrCodePath = storage_path('images/qrcode-signature-' . $sp3->id . '.png');
        \SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')
            ->size(300)
            ->errorCorrection('H')
            ->merge(public_path('images/logo2x.png'), .2, true)
            ->generate('https://dokumen-tte.bareskrim.polri.go.id/DocumentInfo/Icell?id=' . $sp3->id, $tempQrCodePath);

        $templateFile = file_exists(public_path('word-template/surat_pemberitahuan_penghentian_penyidikan_2026.docx'))
            ? public_path('word-template/surat_pemberitahuan_penghentian_penyidikan_2026.docx')
            : public_path('word-template/surat_pemberitahuan_penghentian_penyidikan.docx');
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($templateFile);

        $templateVariables = $templateProcessor->getVariables();

        if (in_array('block_carbon_copies', $templateVariables)) {
            if (count($blockCarbonCopies) > 0) {
                $templateProcessor->cloneBlock('block_carbon_copies', 0, true, false, $blockCarbonCopies);
            } else {
                $templateProcessor->cloneBlock('block_carbon_copies', 0);
            }
        } elseif (in_array('carbon_copy_iteration', $templateVariables)) {
            if (count($blockCarbonCopies) > 0) {
                $templateProcessor->cloneRowAndSetValues('carbon_copy_iteration', $blockCarbonCopies);
            } else {
                $templateProcessor->setValue('carbon_copy_iteration', '-');
                $templateProcessor->setValue('carbon_copy_name', '-');
            }
        }

        $templateProcessor->setValues([
            'daerahPoliceFullName'       => $daerahPoliceFullName,
            'resorPoliceFullName'        => $resorPoliceFullName,
            'resorPoliceAddress'         => $resorPoliceAddress,
            'documentLocation'           => $documentLocation,
            'documentDate'               => $documentDate,
            'documentNumber'             => $documentNumber,
            'documentClassificationName' => $documentClassificationName,
            'appendix'                   => $appendix,
            'accidentNumber'             => $accidentNumber,
            'accidentDate'               => $accidentDate,
            'lpNumber'                   => $accidentNumber,
            'lpDate'                     => $accidentDate,
            'noSpdp'                     => $noSpdp,
            'noSkPenghentian'            => $noSkPenghentian,
            'tanggalSkPenghentian'       => $tanggalSkPenghentian,
            'noSpPenghentian'            => $noSpPenghentian,
            'tanggalSpPenghentian'       => $tanggalSpPenghentian,
            'alasanPenghentian'          => $alasanPenghentian,
            'signatoryHeadText'          => $signatoryHeadText,
            'signatoryPositionName'      => $signatoryPositionName,
            'signatoryPositionHeadText'  => $signatoryPositionName,
            'signatoryName'              => strtoupper($signatoryName),
            'signatoryRankName'          => strtoupper($signatoryRankName),
            'signatoryRegisterNumber'    => $signatoryRegisterNumber,
            'prosecutorName'             => $prosecutorName,
            'nama_kejaksaan'             => $prosecutorName,
            'prosecutorLocation'         => $prosecutorLocation,
            'courtName'                  => $courtName,
            'PutusanPengadilanName'      => '-',
            'PutusanPengadilanDate'      => '-',
            'kejaksaanExtensionNumber'   => '-',
            'kejaksaanExtensionDate'     => '-',
            'kejaksaanExtensionSuspectName' => $suspectName,
            'perpanjanganOrderNumber'    => '-',
            'perpanjanganOrderDate'      => '-',
            'perpanjanganOrderSuspectName' => $suspectName,
            'suratPerintahPenyidikanDocumentNumber'          => $sprindikNumber,
            'suratPerintahPenyidikanDocumentDocumentDate'    => $sprindikFullDate,
            'SuratPerintahPenyidikanNumber'                  => $sprindikNumber,
            'SuratPerintahPenyidikanDate'                    => $sprindikFullDate,
            'SuratKetetapantentangPenetapanDocumentNumber'   => $skpptNumber,
            'SuratKetetapantentangPenetapanDocumentDate'     => $skpptDate,
            'sketNumber'                                     => $skpptNumber,
            'sketDate'                                       => $skpptDate,
            'sketSuspectName'                                => $suspectName,
            'SuratPemberitahuanDimulainyaPenyidikanNumber'   => $spdpNumber,
            'SuratPemberitahuanDimulainyaPenyidikanDate'     => $spdpDate,
            'spdpNumber'                                     => $spdpNumber,
            'spdpDate'                                       => $spdpDate,
            'SuratKetetapantentangPenghentianPenyidikanNumber' => $noSkPenghentian,
            'SuratKetetapantentangPenghentianPenyidikanDate' => $tanggalSkPenghentian,
            'StapPenghentianPenyidikanNumber'                => $noSkPenghentian,
            ' StapPenghentianPenyidikanNumber '              => $tanggalSkPenghentian,
            'SuratPerintahPenghentianPenyidikanNumber'       => $noSpPenghentian,
            'SuratPerintahPenghentianPenyidikanDate'         => $tanggalSpPenghentian,
            'SprinHentiSidikNumber'                          => $noSpPenghentian,
            'SprinHentiSidikDate'                            => $tanggalSpPenghentian,
            ' SprinHentiSidikDate'                           => $tanggalSpPenghentian,
            'SuratPerintahPenyidikanDay'                     => $sprindikDocumentDay,
            'SuratPerintahDate'                              => $sprindikDate,
            'SuratPerintahPenyidikanMonth'                   => $sprindikMonth,
            'SuratPerintahPenyidikanYear'                    => $sprindikYear,
            'SuratPerintahPenyidikanLawsDocument'            => $crimeConstitutionText,
            'SuspectName'                                    => $suspectName,
            'dugaan_tindak_pidana'                           => 'Kecelakaan Lalu Lintas',
            'terlapor/tersangka'                             => 'Tersangka',
            'terlapor/tersangkaName'                         => $suspectName,
            'alasan'                                         => $alasanPenghentian,
            'KetuaTimPenyidik'                               => $ketuaTimName,
            'KetuaTimPenyidikPhoneNumber'                    => $ketuaTimPhone,
            'contactOfficerName'                             => $ketuaTimName,
            'contactOfficerPhone'                            => $ketuaTimPhone,
        ]);

        if (in_array('QRCodeImage', $templateVariables)) {
            $templateProcessor->setImageValue('QRCodeImage', [
                'path'   => $tempQrCodePath,
                'width'  => 111,
                'height' => 111,
            ]);
        }

        $filename = 'generate/' . $sp3->id . ' - SP3 Pusiknas - ' . ($accident->polres->full_name ?? '');
        $templateProcessor->saveAs(public_path($filename . '.docx'));
        return response()->download(public_path($filename . '.docx'))->deleteFileAfterSend(true);
    }

    /**
     * Submit document for approval
     */
    public function submit($id, Request $request)
    {
        try {
            DB::beginTransaction();

            $document = SuratPemberitahuanPenghentianPenyidikanDocument::findOrFail($id);
            
            $timestampsLog = is_array($document->getAttributeValue('timestamps')) ? $document->getAttributeValue('timestamps') : [];
            $timestampsLog[] = [
                'status_id' => '3',
                'updated_at' => Carbon::now()->toDateTimeString(),
                'updated_by' => Auth::id(),
                'message' => 'Dokumen diajukan untuk persetujuan'
            ];

            $ipAddresses = is_array($document->getAttributeValue('ip_addresses')) ? $document->getAttributeValue('ip_addresses') : [];
            if (!in_array($request->ip(), $ipAddresses)) {
                $ipAddresses[] = $request->ip();
            }

            $document->update([
                'status_id' => '3',
                'submitted_at' => Carbon::now(),
                'updated_by_user_id' => Auth::id(),
                'timestamps' => $timestampsLog,
                'ip_addresses' => $ipAddresses,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Dokumen berhasil diajukan untuk persetujuan'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error submitting document: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengajukan dokumen: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Approve document
     */
    public function approve($id, Request $request)
    {
        try {
            DB::beginTransaction();

            $document = SuratPemberitahuanPenghentianPenyidikanDocument::findOrFail($id);
            
            $timestampsLog = is_array($document->getAttributeValue('timestamps')) ? $document->getAttributeValue('timestamps') : [];
            $timestampsLog[] = [
                'status_id' => '5',
                'updated_at' => Carbon::now()->toDateTimeString(),
                'updated_by' => Auth::id(),
                'message' => 'Dokumen disetujui'
            ];

            $ipAddresses = is_array($document->getAttributeValue('ip_addresses')) ? $document->getAttributeValue('ip_addresses') : [];
            if (!in_array($request->ip(), $ipAddresses)) {
                $ipAddresses[] = $request->ip();
            }

            $document->update([
                'status_id' => '5',
                'approved_at' => Carbon::now(),
                'released_at' => Carbon::now(),
                'updated_by_user_id' => Auth::id(),
                'timestamps' => $timestampsLog,
                'ip_addresses' => $ipAddresses,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Dokumen berhasil disetujui'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error approving document: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyetujui dokumen: ' . $e->getMessage()
            ], 500);
        }
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
            'noSp3'                => 'required|string|max:255',
            'tanggalSp3'           => 'required|date',
            'klasifikasi'          => 'required',
            'appendix'             => 'required|numeric|min:1|max:999',
            'noSpdp'               => 'required|string',
            'noSkPenghentian'      => 'required|string',
            'tanggalSkPenghentian' => 'required|date',
            'noSpPenghentian'      => 'required|string',
            'tanggalSpPenghentian' => 'required|date',
            'kode_alasan'          => 'required|array|min:1',
            'prosecutor_id'        => 'required',
            'court_id'             => 'required',
            'signatory'            => 'required',
            'carbonCopies'         => 'required|array|min:1',
        ], [
            'noSp3.required'                => 'Nomor surat SP3 wajib diisi.',
            'tanggalSp3.required'           => 'Tanggal surat SP3 wajib diisi.',
            'klasifikasi.required'          => 'Klasifikasi surat wajib dipilih.',
            'appendix.required'             => 'Jumlah lampiran wajib diisi.',
            'noSpdp.required'               => 'Nomor SPDP terkait wajib dipilih.',
            'noSkPenghentian.required'      => 'Nomor SK Penghentian wajib dipilih atau diisi.',
            'tanggalSkPenghentian.required' => 'Tanggal SK Penghentian wajib diisi.',
            'noSpPenghentian.required'      => 'Nomor Surat Perintah Penghentian wajib dipilih atau diisi.',
            'tanggalSpPenghentian.required' => 'Tanggal Surat Perintah Penghentian wajib diisi.',
            'kode_alasan.required'          => 'Alasan penghentian penyidikan wajib dipilih minimal 1.',
            'prosecutor_id.required'        => 'Kejaksaan Negeri terkait wajib dipilih.',
            'court_id.required'             => 'Pengadilan Negeri terkait wajib dipilih.',
            'signatory.required'            => 'Pejabat penandatangan wajib dipilih.',
            'carbonCopies.required'         => 'Tembusan wajib diisi.',
        ]);
    }
}

