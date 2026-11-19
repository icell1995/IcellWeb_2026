<?php

namespace App\Http\Controllers\Docs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;
use PhpOffice\PhpWord\TemplateProcessor;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

use App\Services\Doc\DocService;
use App\Traits\DocsOfficersTraits;
use App\Helpers\PeopleNameHelper;

use App\Models\Accident;
use App\Models\Officer;
use App\Models\Suspect;
use App\Models\Informant;
use App\Models\ReportedPerson;
use App\Models\User;
use App\Models\Lib\Court;
use App\Models\Lib\Prosecutor;
use App\Models\Lib\Rank;
use App\Models\Lib\Position;
use App\Models\Lib\CrimeType;
use App\Models\Lib\CrimeClass;
use App\Models\Lib\CrimeConstitution;
use App\Models\Lib\DocumentClassification;
use App\Models\Lib\DocumentCategory;
use App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument;
use App\Models\Doc\SuratPemberitahuanDimulainyaPenyidikanDocument\SuratPemberitahuanDimulainyaPenyidikanDocument;

use App\Models\Doc\SuratPermintaanIzinPenyitaanDocument\SuratPermintaanIzinPenyitaanDocument;
use App\Models\Doc\SuratPermintaanIzinPenyitaanDocument\SuratPermintaanIzinPenyitaanDocumentOfficer;
use App\Models\Doc\SuratPermintaanIzinPenyitaanDocument\SuratPermintaanIzinPenyitaanDocumentSeizedItem;
use App\Models\Doc\SuratPermintaanIzinPenyitaanDocument\SuratPermintaanIzinPenyitaanDocumentAttachment;
use App\Models\Pivot\SuratPermintaanIzinPenyitaanDocumentSuspect;

class SuratPermintaanIzinPenyitaanController extends Controller
{
    use DocsOfficersTraits;

    protected $docService;

    public function __construct(DocService $docService)
    {
        $this->docService = $docService;
    }

    /**
     * Show the form for creating a new Surat Permintaan Izin Penyitaan (S-12).
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        if (!Auth::check()) {
            abort(403, 'Akses ditolak.');
        }

        $accidentId = request()->query('accident_id');
        $accident = Accident::where('id', $accidentId)->firstOrFail();

        $suratPerintahPenyidikanDocuments = SuratPerintahPenyidikanDocument::with(['suratPerintahPenyidikanDocumentOfficers'])
            ->where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->get();

        $spdpDocuments = SuratPemberitahuanDimulainyaPenyidikanDocument::where('accident_id', $accidentId)
            ->where('status_id', '86')
            ->orderBy('document_date', 'desc')
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

        $leaderOfficers = Officer::withRelated()
            ->selectFullName()
            ->whereIn('police_id', $getOldNewPolresIds)
            ->whereHasUserActive()
            ->hasDataComplete()
            ->member()
            ->active()
            ->valid()
            ->orderBy('first_name')
            ->get();

        $suspects = Suspect::where('accident_id', $accidentId)->get();
        $informants = Informant::where('accident_id', $accidentId)->get();
        $reportedPersons = ReportedPerson::where('accident_id', $accidentId)->get();

        $courts = Court::where('is_active', true)
            ->orderBy('sort')
            ->get();

        $prosecutors = Prosecutor::where('is_active', true)
            ->orderBy('sort')
            ->get();

        $crimeTypes = CrimeType::where('is_active', true)
            ->orderBy('sort')
            ->get();

        $crimeClasses = CrimeClass::where('is_active', true)
            ->orderBy('sort')
            ->get();

        $crimeConstitutions = CrimeConstitution::where('is_active', true)
            ->orderBy('sort')
            ->get();

        $documentClassifications = DocumentClassification::where('is_active', true)
            ->orderBy('id')
            ->get();

        $ranks = Rank::where('is_active', true)
            ->wherePolri()
            ->orderBy('sort')
            ->get();

        $positions = Position::where('is_active', true)
            ->orderBy('sort')
            ->get();

        $viewData = [
            'accidentId' => $accidentId,
            'accident' => $accident,
            'suratPerintahPenyidikanDocuments' => $suratPerintahPenyidikanDocuments,
            'spdpDocuments' => $spdpDocuments,
            'authorizedSignatories' => $authorizedSignatories,
            'leaderOfficers' => $leaderOfficers,
            'suspects' => $suspects,
            'informants' => $informants,
            'reportedPersons' => $reportedPersons,
            'courts' => $courts,
            'prosecutors' => $prosecutors,
            'documentClassifications' => $documentClassifications,
            'crimeTypes' => $crimeTypes,
            'crimeClasses' => $crimeClasses,
            'crimeConstitutions' => $crimeConstitutions,
            'ranks' => $ranks,
            'positions' => $positions,
        ];

        return view('docs.surat-permintaan-izin-penyitaan-document.create', $viewData);
    }

    /**
     * Store a newly created Surat Permintaan Izin Penyitaan (S-12) in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        if (!Auth::check()) {
            abort(403, 'Akses ditolak.');
        }

        if ($request->filled('surat_perintah_penyidikan_document_id')) {
            $sprindikDoc = SuratPerintahPenyidikanDocument::find($request->input('surat_perintah_penyidikan_document_id'));
            if ($sprindikDoc) {
                $request->merge([
                    'sprindik_number' => $sprindikDoc->document_number,
                    'sprindik_date' => $sprindikDoc->document_date ? Carbon::parse($sprindikDoc->document_date)->format('Y-m-d') : null
                ]);
            }
        }

        if ($request->filled('spdp_number')) {
            $spdpDoc = SuratPemberitahuanDimulainyaPenyidikanDocument::where('accident_id', $request->input('accident_id'))
                ->where('document_number', $request->input('spdp_number'))
                ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
                ->first();

            if ($spdpDoc) {
                $request->merge(['spdp_date' => $spdpDoc->document_date ? Carbon::parse($spdpDoc->document_date)->format('Y-m-d') : null]);
            } else {
                $request->merge(['spdp_date' => null]);
            }
        }

        $validator = $this->validateForm($request);

        if ($validator->fails()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $accidentId = $request->input('accident_id');
        $accident = Accident::find($accidentId);
        $documentNumber = $request->input('document_number');
        $documentDate = Carbon::parse($request->input('document_date'))->format('Y-m-d');

        $sprindikNumber = $request->input('sprindik_number');
        $sprindikDate = Carbon::parse($request->input('sprindik_date'))->format('Y-m-d');

        $suratPerintahPenyidikanDocumentId = $request->input('surat_perintah_penyidikan_document_id') ?: null;
        $hasSuratPerintahPenyitaan = $request->input('has_surat_perintah_penyitaan') === '1' || $request->input('has_surat_perintah_penyitaan') === 'true' || $request->input('has_surat_perintah_penyitaan') === true;

        if ($hasSuratPerintahPenyitaan) {
            $suratPerintahPenyitaanNumber = $request->input('surat_perintah_penyitaan_number') ?: null;
            $suratPerintahPenyitaanDate = $request->input('surat_perintah_penyitaan_date')
                ? Carbon::parse($request->input('surat_perintah_penyitaan_date'))->format('Y-m-d')
                : null;
        } else {
            $suratPerintahPenyitaanNumber = null;
            $suratPerintahPenyitaanDate = null;
        }
        $spdpNumber = $request->input('spdp_number') ?: null;
        $spdpDate = $request->input('spdp_date')
            ? Carbon::parse($request->input('spdp_date'))->format('Y-m-d')
            : null;
        $courtId = $request->input('court_id') ?: null;

        $exists = DB::table('doc.surat_permintaan_izin_penyitaan_documents')
            ->where('accident_id', $accidentId)
            ->where('document_number', 'ILIKE', $documentNumber)
            ->whereNull('deleted_at')
            ->exists();

        if ($exists) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dokumen ' . $documentNumber . ' sudah dibuat sebelumnya.',
                ], 422);
            }
            return redirect()->back()->with('error', 'Dokumen ' . $documentNumber . ' sudah Anda buat sebelumnya.')->withInput();
        }

        $newlyStoredFiles = [];

        DB::beginTransaction();
        try {
            $documentId = (string) Str::uuid();

            // 1. Simpan data dokumen utama
            $document = new SuratPermintaanIzinPenyitaanDocument();
            $document->id = $documentId;
            $document->accident_id = $accidentId;
            $document->surat_perintah_penyidikan_document_id = $suratPerintahPenyidikanDocumentId;
            $document->document_number = $documentNumber;
            $document->document_date = $documentDate;

            $document->sprindik_number = $sprindikNumber;
            $document->sprindik_date = $sprindikDate;
            $document->surat_perintah_penyitaan_number = $suratPerintahPenyitaanNumber;
            $document->surat_perintah_penyitaan_date = $suratPerintahPenyitaanDate;
            
            // Upload SP Penyitaan
            $spFileToStore = null;
            if ($hasSuratPerintahPenyitaan && $request->hasFile('surat_perintah_penyitaan_file')) {
                $spFile = $request->file('surat_perintah_penyitaan_file');
                $spFilename = 'SP_PENYITAAN_S12_' . date('Ymd') . '_' . Str::random(10) . '.' . $spFile->getClientOriginalExtension();
                $spPath = public_path('file/penyitaan/surat-perintah-penyitaan');
                if (!file_exists($spPath)) {
                    mkdir($spPath, 0755, true);
                }
                $spFile->move($spPath, $spFilename);
                $newlyStoredFiles[] = $spPath . '/' . $spFilename;
                $spFileToStore = $spFilename;
            }
            $document->surat_perintah_penyitaan_file = $spFileToStore;

            $document->spdp_number = $spdpNumber;
            $document->spdp_date = $spdpDate;
            $document->court_id = $courtId;
            $documentClassificationId = $request->input('documentClassification')
                ?: $request->input('classification')
                ?: $request->input('document_classification_id');
            if ($documentClassificationId && Schema::hasColumn('doc.surat_permintaan_izin_penyitaan_documents', 'document_classification_id')) {
                $document->document_classification_id = $documentClassificationId;
            }
            $rawCarbonCopies = $request->input('carbonCopies', []);
            $carbonCopies = [];
            if (is_array($rawCarbonCopies)) {
                foreach ($rawCarbonCopies as $cc) {
                    if (!is_null($cc) && trim($cc) !== '') {
                        $carbonCopies[] = trim($cc);
                    }
                }
            }
            $document->carbon_copies = !empty($carbonCopies) ? array_values($carbonCopies) : null;
            $document->status_id = '2';
            $document->document_category_id = '0504';
            $document->is_active = true;
            $document->created_by_user_id = Auth::id();
            $document->save();

            // 2. Simpan Ketua Tim Penyidik (LEADER)
            $leaderOfficerId = $request->officerLeader;
            if ($leaderOfficerId) {
                $leader = Officer::find($leaderOfficerId);
                if ($leader) {
                    $docLeader = new SuratPermintaanIzinPenyitaanDocumentOfficer();
                    $docLeader->surat_permintaan_izin_penyitaan_document_id = $documentId;
                    $docLeader->sort = 0;
                    $docLeader->register_number = $leader->register_number;
                    $docLeader->first_title = $leader->first_title;
                    $docLeader->first_name = $leader->first_name;
                    $docLeader->last_name = $leader->last_name;
                    $docLeader->last_title = $leader->last_title;
                    $docLeader->rank_id = $leader->rank_id;
                    $docLeader->position_id = $leader->position_id;
                    $docLeader->phone_number = $leader->phone_number;
                    $docLeader->email = $leader->email;
                    $docLeader->police_id = $leader->police_id;
                    $docLeader->status = 'PRESENT';
                    $docLeader->class = 'LEADER';
                    $docLeader->flag = 'INTERNAL';
                    $docLeader->insert_method = 'IMPORT';
                    $docLeader->save();
                }
            }

            // 3. Simpan pejabat penandatangan (SIGNATORY)
            $officerIds = $request->input('officers', $request->input('signatories', []));
            if (!is_array($officerIds)) {
                $officerIds = [$officerIds];
            }

            foreach (array_values($officerIds) as $sort => $officerId) {
                $officer = Officer::find($officerId);
                if ($officer) {
                    $documentOfficer = new SuratPermintaanIzinPenyitaanDocumentOfficer();
                    $documentOfficer->surat_permintaan_izin_penyitaan_document_id = $documentId;
                    $documentOfficer->sort = $sort + 1;
                    $documentOfficer->register_number = $officer->register_number;
                    $documentOfficer->first_title = $officer->first_title;
                    $documentOfficer->first_name = $officer->first_name;
                    $documentOfficer->last_name = $officer->last_name;
                    $documentOfficer->last_title = $officer->last_title;
                    $documentOfficer->rank_id = $officer->rank_id;
                    $documentOfficer->position_id = $officer->position_id;
                    $documentOfficer->phone_number = $officer->phone_number;
                    $documentOfficer->email = $officer->email;
                    $documentOfficer->police_id = $officer->police_id;
                    $documentOfficer->status = 'PRESENT';
                    $documentOfficer->class = 'SIGNATORY';
                    $documentOfficer->flag = 'INTERNAL';
                    $documentOfficer->insert_method = 'IMPORT';
                    $documentOfficer->save();
                }
            }

            // 3. (Dihapus: Laws dikelola dari Sprindik)
            // 4. Simpan tersangka atau saksi
            $suspectIds = (array) $request->input('suspects', []);
            foreach ($suspectIds as $suspectId) {
                if (!empty($suspectId)) {
                    $pivotSuspect = new SuratPermintaanIzinPenyitaanDocumentSuspect();
                    $pivotSuspect->surat_permintaan_izin_penyitaan_document_id = $documentId;
                    $pivotSuspect->suspect_id = $suspectId;
                    $pivotSuspect->save();
                }
            }

            // 5. Simpan daftar barang sitaan (seized_items)
            $seizedItems = (array) $request->input('seized_items', []);
            foreach ($seizedItems as $item) {
                if (!empty($item['nama'])) {
                    $seizedItem = new SuratPermintaanIzinPenyitaanDocumentSeizedItem();
                    $seizedItem->surat_permintaan_izin_penyitaan_document_id = $documentId;
                    $seizedItem->nama = $item['nama'];
                    $seizedItem->jenis = $item['jenis'] ?? null;
                    $seizedItem->jumlah = isset($item['jumlah']) && is_numeric($item['jumlah']) ? (float) $item['jumlah'] : 1.00;
                    $seizedItem->satuan = $item['satuan'] ?? null;
                    $seizedItem->keterangan = $item['keterangan'] ?? ($item['description'] ?? null);
                    $seizedItem->save();
                }
            }

            // 6. Simpan dokumen digital (attachments)
            // (Attachment upload moved to Document Action)

            DB::commit();

            Log::info('Surat Permintaan Izin Penyitaan (S-12) created successfully', [
                'id' => $documentId,
                'accident_id' => $accidentId,
                'document_number' => $documentNumber,
                'created_by' => Auth::id(),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            // Clean up newly uploaded files on failure
            foreach ($newlyStoredFiles as $storedFile) {
                if (file_exists($storedFile)) {
                    unlink($storedFile);
                }
            }

            Log::error('Error storing Surat Permintaan Izin Penyitaan S-12: ' . $e->getMessage(), [
                'exception' => $e,
                'payload' => $request->except(['attachments', 'file_s12', 'file_sprindik', 'file_sprin_sita', 'file_resume']),
            ]);

            $errorMessage = config('app.debug') ? 'Terjadi kesalahan: ' . $e->getMessage() : 'Terjadi kesalahan saat menyimpan data.';

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                ], 500);
            }

            return redirect()->back()->with('error', $errorMessage)->withInput();
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Dokumen Surat Permintaan Izin Penyitaan (S-12) berhasil disimpan.',
                'data' => [
                    'id' => $documentId,
                    'accident_id' => $accidentId,
                    'document_number' => $documentNumber,
                ],
            ], 200);
        }

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
            ->with('success', 'Dokumen Surat Permintaan Izin Penyitaan (S-12) berhasil dibuat.');
    }

    /**
     * Display the specified Surat Permintaan Izin Penyitaan (S-12).
     *
     * @param  string  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        if (!Auth::check()) {
            abort(403, 'Akses ditolak.');
        }

        $document = SuratPermintaanIzinPenyitaanDocument::with([
            'accident.polres.polda',
            'officers.rank',
            'officers.position',
            'suratPerintahPenyidikanDocument.suratPerintahPenyidikanDocumentLaws.crimeConstitution',
            'suratPerintahPenyidikanDocument.suratPerintahPenyidikanDocumentLaws.crimeType',
            'seizedItems',
            'attachments',
            'documentSuspects.suspect',
            'suratPerintahPenyidikanDocument',
            'court',
            'documentCategory',
            'createdByUser',
            'updatedByUser',
        ])->where('id', $id)->firstOrFail();

        $accidentId = $document->accident_id;
        $accident = $document->accident ?? Accident::with(['polres', 'polres.polda'])->where('id', $accidentId)->firstOrFail();

        $officers = $document->officers;
        $leaderOfficer = $officers->where('class', 'LEADER')->first();
        $signatories = $officers->where('class', 'SIGNATORY')->values();
        $laws = $document->suratPerintahPenyidikanDocument?->suratPerintahPenyidikanDocumentLaws ?? collect();
        $seizedItems = $document->seizedItems;
        $attachments = $document->attachments;
        $documentSuspects = $document->documentSuspects;
        $suratPerintahPenyidikanDocument = $document->suratPerintahPenyidikanDocument;
        $court = $document->court;
        $documentCategory = $document->documentCategory;
        $createdByUser = $document->createdByUser;
        $updatedByUser = $document->updatedByUser;

        $viewData = [
            'id' => $id,
            'documentId' => $id,
            'document' => $document,
            'suratPermintaanIzinPenyitaanDocument' => $document,
            'accidentId' => $accidentId,
            'accident' => $accident,
            'officers' => $officers,
            'leaderOfficer' => $leaderOfficer,
            'signatories' => $signatories,
            'laws' => $laws,
            'seizedItems' => $seizedItems,
            'attachments' => $attachments,
            'documentSuspects' => $documentSuspects,
            'suratPerintahPenyidikanDocument' => $suratPerintahPenyidikanDocument,
            'court' => $court,
            'documentCategory' => $documentCategory,
            'createdByUser' => $createdByUser,
            'updatedByUser' => $updatedByUser,
        ];

        return view('docs.surat-permintaan-izin-penyitaan-document.show', $viewData);
    }

    /**
     * Show the form for editing the specified Surat Permintaan Izin Penyitaan (S-12).
     *
     * @param  string  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        if (!Auth::check()) {
            abort(403, 'Akses ditolak.');
        }

        $document = SuratPermintaanIzinPenyitaanDocument::with([
            'officers.rank',
            'officers.position',
            'suratPerintahPenyidikanDocument.suratPerintahPenyidikanDocumentLaws.crimeConstitution',
            'suratPerintahPenyidikanDocument.suratPerintahPenyidikanDocumentLaws.crimeType',
            'seizedItems',
            'attachments',
            'documentSuspects.suspect',
            'accident',
        ])->where('id', $id)->firstOrFail();

        $accidentId = $document->accident_id;
        $accident = $document->accident ?? Accident::where('id', $accidentId)->firstOrFail();

        $officers = $document->officers;
        $laws = $document->suratPerintahPenyidikanDocument?->suratPerintahPenyidikanDocumentLaws ?? collect();
        $seizedItems = $document->seizedItems;
        $attachments = $document->attachments;
        $documentSuspects = $document->documentSuspects;

        $selectedSuspectIds = $documentSuspects->pluck('suspect_id')->toArray();

        $suratPerintahPenyidikanDocuments = SuratPerintahPenyidikanDocument::with(['suratPerintahPenyidikanDocumentOfficers'])
            ->where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->get();

        $spdpDocuments = SuratPemberitahuanDimulainyaPenyidikanDocument::where('accident_id', $accidentId)
            ->where(function ($query) use ($document) {
                $query->where('status_id', '86');
                if (!empty($document->spdp_number)) {
                    $query->orWhere('document_number', $document->spdp_number);
                }
            })
            ->orderBy('document_date', 'desc')
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

        $leaderOfficers = Officer::withRelated()
            ->selectFullName()
            ->whereIn('police_id', $getOldNewPolresIds)
            ->whereHasUserActive()
            ->hasDataComplete()
            ->member()
            ->active()
            ->valid()
            ->orderBy('first_name')
            ->get();

        $currentLeaderOfficer = $officers->where('class', 'LEADER')->first();

        $suspects = Suspect::where('accident_id', $accidentId)->get();
        $informants = Informant::where('accident_id', $accidentId)->get();
        $reportedPersons = ReportedPerson::where('accident_id', $accidentId)->get();
        $selectedReportedPersonIds = $document->reportedPersons()->pluck('public.reported_persons.id')->toArray();

        $courts = Court::where('is_active', true)
            ->orderBy('sort')
            ->get();

        $prosecutors = Prosecutor::where('is_active', true)
            ->orderBy('sort')
            ->get();

        $crimeTypes = CrimeType::where('is_active', true)
            ->orderBy('sort')
            ->get();

        $crimeClasses = CrimeClass::where('is_active', true)
            ->orderBy('sort')
            ->get();

        $crimeConstitutions = CrimeConstitution::where('is_active', true)
            ->orderBy('sort')
            ->get();

        $mainLaws = $laws->filter(function ($l) {
            return $l->flag === 'MAIN' || (empty($l->flag) && (!empty($l->crime_type_id) || !empty($l->crime_constitution_id)));
        })->values();

        $additionalDbLaws = $laws->filter(function ($l) {
            return $l->flag === 'ADDT' || $l->flag === 'ADDITIONAL' || (!empty($l->constitution) && empty($l->crime_type_id) && empty($l->crime_constitution_id));
        })->values();

        $ranks = Rank::where('is_active', true)
            ->wherePolri()
            ->orderBy('sort')
            ->get();

        $positions = Position::where('is_active', true)
            ->orderBy('sort')
            ->get();

        $documentClassifications = DocumentClassification::where('is_active', true)
            ->orderBy('id')
            ->get();

        $viewData = [
            'id' => $id,
            'documentId' => $id,
            'document' => $document,
            'suratPermintaanIzinPenyitaanDocument' => $document,
            'accidentId' => $accidentId,
            'accident' => $accident,
            'officers' => $officers,
            'leaderOfficers' => $leaderOfficers,
            'currentLeaderOfficer' => $currentLeaderOfficer,
            'laws' => $laws,
            'mainLaws' => $mainLaws,
            'additionalDbLaws' => $additionalDbLaws,
            'seizedItems' => $seizedItems,
            'attachments' => $attachments,
            'documentSuspects' => $documentSuspects,
            'selectedSuspectIds' => $selectedSuspectIds,
            'suratPerintahPenyidikanDocuments' => $suratPerintahPenyidikanDocuments,
            'spdpDocuments' => $spdpDocuments,
            'authorizedSignatories' => $authorizedSignatories,
            'suspects' => $suspects,
            'informants' => $informants,
            'reportedPersons' => $reportedPersons,
            'selectedReportedPersonIds' => $selectedReportedPersonIds,
            'courts' => $courts,
            'prosecutors' => $prosecutors,
            'documentClassifications' => $documentClassifications,
            'crimeTypes' => $crimeTypes,
            'crimeClasses' => $crimeClasses,
            'crimeConstitutions' => $crimeConstitutions,
            'ranks' => $ranks,
            'positions' => $positions,
        ];

        return view('docs.surat-permintaan-izin-penyitaan-document.edit', $viewData);
    }

    /**
     * Update the specified Surat Permintaan Izin Penyitaan (S-12) in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        if (!Auth::check()) {
            abort(403, 'Akses ditolak.');
        }

        $document = SuratPermintaanIzinPenyitaanDocument::where('id', $id)->firstOrFail();

        if ($request->filled('surat_perintah_penyidikan_document_id')) {
            $sprindikDoc = SuratPerintahPenyidikanDocument::find($request->input('surat_perintah_penyidikan_document_id'));
            if ($sprindikDoc) {
                $request->merge([
                    'sprindik_number' => $sprindikDoc->document_number,
                    'sprindik_date' => $sprindikDoc->document_date ? Carbon::parse($sprindikDoc->document_date)->format('Y-m-d') : null
                ]);
            }
        }

        if ($request->filled('spdp_number')) {
            $spdpDoc = SuratPemberitahuanDimulainyaPenyidikanDocument::where('accident_id', $document->accident_id)
                ->where('document_number', $request->input('spdp_number'))
                ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
                ->first();

            if ($spdpDoc) {
                $request->merge(['spdp_date' => $spdpDoc->document_date ? Carbon::parse($spdpDoc->document_date)->format('Y-m-d') : null]);
            } else {
                $request->merge(['spdp_date' => null]);
            }
        }

        $validator = $this->validateForm($request, $id, $document->accident_id);

        if ($validator->fails()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Pertahankan accident_id dokumen yang ada untuk integritas data
        $accidentId = $document->accident_id;
        $accident = Accident::find($accidentId);
        $documentNumber = $request->input('document_number');
        $documentDate = Carbon::parse($request->input('document_date'))->format('Y-m-d');

        $sprindikNumber = $request->input('sprindik_number');
        $sprindikDate = Carbon::parse($request->input('sprindik_date'))->format('Y-m-d');

        $suratPerintahPenyidikanDocumentId = $request->input('surat_perintah_penyidikan_document_id') ?: ($document->surat_perintah_penyidikan_document_id ?: null);
        $hasSuratPerintahPenyitaan = $request->input('has_surat_perintah_penyitaan') === '1' || $request->input('has_surat_perintah_penyitaan') === 'true' || $request->input('has_surat_perintah_penyitaan') === true;

        if ($hasSuratPerintahPenyitaan) {
            $suratPerintahPenyitaanNumber = $request->input('surat_perintah_penyitaan_number') ?: null;
            $suratPerintahPenyitaanDate = $request->input('surat_perintah_penyitaan_date')
                ? Carbon::parse($request->input('surat_perintah_penyitaan_date'))->format('Y-m-d')
                : null;
        } else {
            $suratPerintahPenyitaanNumber = null;
            $suratPerintahPenyitaanDate = null;
        }
        $spdpNumber = $request->input('spdp_number') ?: null;
        $spdpDate = $request->input('spdp_date')
            ? Carbon::parse($request->input('spdp_date'))->format('Y-m-d')
            : null;
        $courtId = $request->input('court_id') ?: null;

        // Cek duplikasi nomor dokumen jika nomor dokumen diubah
        $oldDocumentNumber = $document->document_number;
        if (strtolower($oldDocumentNumber) != strtolower($documentNumber)) {
            $exists = DB::table('doc.surat_permintaan_izin_penyitaan_documents')
                ->where('accident_id', $accidentId)
                ->where('document_number', 'ILIKE', $documentNumber)
                ->where('id', '!=', $id)
                ->whereNull('deleted_at')
                ->exists();

            if ($exists) {
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Dokumen ' . $documentNumber . ' sudah dibuat sebelumnya.',
                    ], 422);
                }
                return redirect()->back()->with('error', 'Dokumen ' . $documentNumber . ' sudah Anda buat sebelumnya.')->withInput();
            }
        }

        $newlyStoredFiles = [];
        $filesToDeleteAfterCommit = [];

        DB::beginTransaction();
        try {
            // 1. Update data dokumen utama (mempertahankan UUID & audit update)
            $document->surat_perintah_penyidikan_document_id = $suratPerintahPenyidikanDocumentId;
            $document->document_number = $documentNumber;
            $document->document_date = $documentDate;

            $document->sprindik_number = $sprindikNumber;
            $document->sprindik_date = $sprindikDate;
            $document->surat_perintah_penyitaan_number = $suratPerintahPenyitaanNumber;
            $document->surat_perintah_penyitaan_date = $suratPerintahPenyitaanDate;

            // Upload SP Penyitaan
            if (!$hasSuratPerintahPenyitaan) {
                if ($document->surat_perintah_penyitaan_file) {
                    $filesToDeleteAfterCommit[] = public_path('file/penyitaan/surat-perintah-penyitaan/' . $document->surat_perintah_penyitaan_file);
                }
                $document->surat_perintah_penyitaan_file = null;
            } else {
                if ($request->hasFile('surat_perintah_penyitaan_file')) {
                    $spFile = $request->file('surat_perintah_penyitaan_file');
                    $spFilename = 'SP_PENYITAAN_S12_' . date('Ymd') . '_' . Str::random(10) . '.' . $spFile->getClientOriginalExtension();
                    $spPath = public_path('file/penyitaan/surat-perintah-penyitaan');
                    if (!file_exists($spPath)) {
                        mkdir($spPath, 0755, true);
                    }
                    $spFile->move($spPath, $spFilename);
                    $newlyStoredFiles[] = $spPath . '/' . $spFilename;
                    
                    if ($document->surat_perintah_penyitaan_file) {
                        $filesToDeleteAfterCommit[] = public_path('file/penyitaan/surat-perintah-penyitaan/' . $document->surat_perintah_penyitaan_file);
                    }
                    
                    $document->surat_perintah_penyitaan_file = $spFilename;
                }
            }
            $document->spdp_number = $spdpNumber;
            $document->spdp_date = $spdpDate;
            $document->court_id = $courtId;
            $documentClassificationId = $request->input('documentClassification')
                ?: $request->input('classification')
                ?: $request->input('document_classification_id');
            if ($documentClassificationId && Schema::hasColumn('doc.surat_permintaan_izin_penyitaan_documents', 'document_classification_id')) {
                $document->document_classification_id = $documentClassificationId;
            }
            $rawCarbonCopies = $request->input('carbonCopies', []);
            $carbonCopies = [];
            if (is_array($rawCarbonCopies)) {
                foreach ($rawCarbonCopies as $cc) {
                    if (!is_null($cc) && trim($cc) !== '') {
                        $carbonCopies[] = trim($cc);
                    }
                }
            }
            $document->carbon_copies = !empty($carbonCopies) ? array_values($carbonCopies) : null;
            $document->updated_by_user_id = Auth::id();
            $document->save();

            // 2. Sync / update Ketua Tim Penyidik & pejabat penandatangan (officers)
            SuratPermintaanIzinPenyitaanDocumentOfficer::where('surat_permintaan_izin_penyitaan_document_id', $id)->delete();

            $leaderOfficerId = $request->officerLeader;
            if ($leaderOfficerId) {
                $leader = Officer::find($leaderOfficerId);
                if ($leader) {
                    $docLeader = new SuratPermintaanIzinPenyitaanDocumentOfficer();
                    $docLeader->surat_permintaan_izin_penyitaan_document_id = $id;
                    $docLeader->sort = 0;
                    $docLeader->register_number = $leader->register_number;
                    $docLeader->first_title = $leader->first_title;
                    $docLeader->first_name = $leader->first_name;
                    $docLeader->last_name = $leader->last_name;
                    $docLeader->last_title = $leader->last_title;
                    $docLeader->rank_id = $leader->rank_id;
                    $docLeader->position_id = $leader->position_id;
                    $docLeader->phone_number = $leader->phone_number;
                    $docLeader->email = $leader->email;
                    $docLeader->police_id = $leader->police_id;
                    $docLeader->status = 'PRESENT';
                    $docLeader->class = 'LEADER';
                    $docLeader->flag = 'INTERNAL';
                    $docLeader->insert_method = 'IMPORT';
                    $docLeader->save();
                }
            }

            $officerIds = $request->input('officers', $request->input('signatories', []));
            if (!is_array($officerIds)) {
                $officerIds = [$officerIds];
            }

            foreach (array_values($officerIds) as $sort => $officerId) {
                $officer = Officer::find($officerId);
                if ($officer) {
                    $documentOfficer = new SuratPermintaanIzinPenyitaanDocumentOfficer();
                    $documentOfficer->surat_permintaan_izin_penyitaan_document_id = $id;
                    $documentOfficer->sort = $sort + 1;
                    $documentOfficer->register_number = $officer->register_number;
                    $documentOfficer->first_title = $officer->first_title;
                    $documentOfficer->first_name = $officer->first_name;
                    $documentOfficer->last_name = $officer->last_name;
                    $documentOfficer->last_title = $officer->last_title;
                    $documentOfficer->rank_id = $officer->rank_id;
                    $documentOfficer->position_id = $officer->position_id;
                    $documentOfficer->phone_number = $officer->phone_number;
                    $documentOfficer->email = $officer->email;
                    $documentOfficer->police_id = $officer->police_id;
                    $documentOfficer->status = 'PRESENT';
                    $documentOfficer->class = 'SIGNATORY';
                    $documentOfficer->flag = 'INTERNAL';
                    $documentOfficer->insert_method = 'IMPORT';
                    $documentOfficer->save();
                }
            }

            // 3. (Dihapus: Laws dikelola dari Sprindik)
            // 4. Sync / update tersangka atau saksi
            // 4. Sync / update tersangka atau terlapor
            SuratPermintaanIzinPenyitaanDocumentSuspect::where('surat_permintaan_izin_penyitaan_document_id', $id)->delete();
            $document->reportedPersons()->detach();
            
            $suspectIds = (array) $request->input('suspects', []);
            foreach ($suspectIds as $suspectId) {
                if (!empty($suspectId)) {
                    $pivotSuspect = new SuratPermintaanIzinPenyitaanDocumentSuspect();
                    $pivotSuspect->surat_permintaan_izin_penyitaan_document_id = $id;
                    $pivotSuspect->suspect_id = $suspectId;
                    $pivotSuspect->save();
                }
            }

            // 5. Sync / update daftar barang sitaan (seized_items)
            SuratPermintaanIzinPenyitaanDocumentSeizedItem::where('surat_permintaan_izin_penyitaan_document_id', $id)->delete();
            $seizedItems = (array) $request->input('seized_items', []);
            foreach ($seizedItems as $item) {
                if (!empty($item['nama'])) {
                    $seizedItem = new SuratPermintaanIzinPenyitaanDocumentSeizedItem();
                    $seizedItem->surat_permintaan_izin_penyitaan_document_id = $id;
                    $seizedItem->nama = $item['nama'];
                    $seizedItem->jenis = $item['jenis'] ?? null;
                    $seizedItem->jumlah = isset($item['jumlah']) && is_numeric($item['jumlah']) ? (float) $item['jumlah'] : 1.00;
                    $seizedItem->satuan = $item['satuan'] ?? null;
                    $seizedItem->keterangan = $item['keterangan'] ?? ($item['description'] ?? null);
                    $seizedItem->save();
                }
            }

            // 6. Simpan dokumen digital baru (attachments)
            // (Attachment upload moved to Document Action)

            // 6c. Hapus dokumen digital spesifik jika diminta
            // (Attachment deletion moved to Document Action or disabled here)

            DB::commit();

            // Hapus file fisik hanya setelah transaksi database berhasil di-commit
            foreach ($filesToDeleteAfterCommit as $filePath) {
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            Log::info('Surat Permintaan Izin Penyitaan (S-12) updated successfully', [
                'id' => $id,
                'accident_id' => $accidentId,
                'document_number' => $documentNumber,
                'updated_by' => Auth::id(),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            // Clean up newly uploaded files on failure
            foreach ($newlyStoredFiles as $storedFile) {
                if (file_exists($storedFile)) {
                    unlink($storedFile);
                }
            }

            Log::error('Error updating Surat Permintaan Izin Penyitaan S-12: ' . $e->getMessage(), [
                'exception' => $e,
                'payload' => $request->except(['attachments', 'file_s12', 'file_sprindik', 'file_sprin_sita', 'file_resume']),
            ]);

            $errorMessage = config('app.debug') ? 'Terjadi kesalahan: ' . $e->getMessage() : 'Terjadi kesalahan saat memperbarui data.';

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                ], 500);
            }

            return redirect()->back()->with('error', $errorMessage)->withInput();
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Dokumen Surat Permintaan Izin Penyitaan (S-12) berhasil diperbarui.',
                'data' => [
                    'id' => $id,
                    'accident_id' => $accidentId,
                    'document_number' => $documentNumber,
                ],
            ], 200);
        }

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
            ->with('success', 'Dokumen Surat Permintaan Izin Penyitaan (S-12) berhasil diperbarui.');
    }

    /**
     * Remove the specified Surat Permintaan Izin Penyitaan (S-12) from storage.
     *
     * @param  string  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($id)
    {
        // Get URL Parameter
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $suratPermintaanIzinPenyitaanDocumentId = $id;

        DB::beginTransaction();
        try {
            // Delete from database
            $suratPermintaanIzinPenyitaanDocument = SuratPermintaanIzinPenyitaanDocument::where('id', $suratPermintaanIzinPenyitaanDocumentId)->first();
            if ($suratPermintaanIzinPenyitaanDocument) {
                $suratPermintaanIzinPenyitaanDocument->delete();
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan pada saat menghapus data.');
        }

        // Redirect with param accident_id
        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId]);
    }

    /**
     * Download the specified Surat Permintaan Izin Penyitaan (S-12) as Word document.
     *
     * @param  string  $id
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download($id)
    {
        if (!Auth::check()) {
            abort(403, 'Akses ditolak.');
        }

        $document = SuratPermintaanIzinPenyitaanDocument::with([
            'officers.rank',
            'officers.position',
            'officers.position.positionCluster',

            'seizedItems',
            'documentSuspects.suspect.country',
            'documentSuspects.suspect.gender',
            'documentSuspects.suspect.job',
            'documentSuspects.suspect.religion',
            'documentSuspects.suspect.village',
            'documentSuspects.suspect.district',
            'documentSuspects.suspect.regency',
            'documentSuspects.suspect.province',
            'court',
            'suratPerintahPenyidikanDocument.suratPerintahPenyidikanDocumentOfficers.position',
            'suratPerintahPenyidikanDocument.suratPerintahPenyidikanDocumentOfficers.rank',
            'suratPerintahPenyidikanDocument.suratPerintahPenyidikanDocumentLaws.crimeConstitution',
            'suratPerintahPenyidikanDocument.suratPerintahPenyidikanDocumentLaws.crimeType',
            'createdByUser.officer.position',
            'createdByUser.officer.rank',
        ])->where('id', $id)->firstOrFail();

        $accidentId = $document->accident_id;
        $accident = Accident::with(['polres', 'polres.polda', 'police'])->where('id', $accidentId)->firstOrFail();

        $officers = $document->officers;
        $laws = $document->suratPerintahPenyidikanDocument?->suratPerintahPenyidikanDocumentLaws ?? collect();
        $seizedItems = $document->seizedItems;
        $documentSuspects = $document->documentSuspects;
        $court = $document->court;
        $signatory = $officers->where('class', 'SIGNATORY')->first() ?? $officers->first();

        // Template path: prioritize public/word-template/surat_permintaan_izin_penyitaan.docx
        $templatePath = public_path('word-template/surat_permintaan_izin_penyitaan.docx');
        if (!file_exists($templatePath)) {
            $templatePath = 'word-template/surat_permintaan_izin_penyitaan.docx';
        }

        $templateProcessor = new TemplateProcessor($templatePath);

        // Formatted dates
        $documentDate = Carbon::parse($document->document_date)->locale('id')->translatedFormat('d F Y');
        $laporanPengaduanDate = $accident && $accident->report_date
            ? Carbon::parse($accident->report_date)->locale('id')->translatedFormat('d F Y')
            : '';
        $sprindikDate = $document->sprindik_date
            ? Carbon::parse($document->sprindik_date)->locale('id')->translatedFormat('d F Y')
            : '';
        $suratPerintahPenyitaanDate = $document->surat_perintah_penyitaan_date
            ? Carbon::parse($document->surat_perintah_penyitaan_date)->locale('id')->translatedFormat('d F Y')
            : '';
        $spdpDate = $document->spdp_date
            ? Carbon::parse($document->spdp_date)->locale('id')->translatedFormat('d F Y')
            : '';

        $accidentDate = $accident->accident_date
            ? Carbon::parse($accident->accident_date)->locale('id')->translatedFormat('d F Y')
            : '';
        $accidentDay = $accident->accident_date
            ? Carbon::parse($accident->accident_date)->locale('id')->translatedFormat('l')
            : '';
        $accidentTime = $accident->accident_time
            ? Carbon::parse($accident->accident_time)->format('H:i')
            : '';
        $reportDate = $accident->report_date
            ? Carbon::parse($accident->report_date)->locale('id')->translatedFormat('d F Y')
            : '';

        // 1. Rujukan (References)
        $rawReferences = [];
        $rawReferences[] = 'Undang-Undang Nomor 2 Tahun 2002 tentang Kepolisian Negara Republik Indonesia;';
        $rawReferences[] = 'Pasal 3 dan Pasal 618 Undang-Undang Nomor 1 Tahun 2023 tentang Kitab Undang-Undang Hukum Pidana;';
        $rawReferences[] = 'Pasal 1 angka 14 dan angka 35, Pasal 5 ayat (2) huruf b, Pasal 7 ayat (1) huruf f, Pasal 44, Pasal 45, Pasal 46, Pasal 47, Pasal 89 huruf e, Pasal 113 ayat (3), Pasal 118, Pasal 119, Pasal 121, Pasal 122, Pasal 123, Pasal 124, Pasal 125, Pasal 128, Pasal 129, Pasal 130, Pasal 133, Pasal 156 ayat (1) huruf e dan Pasal 361 Undang-Undang Nomor 20 Tahun 2025 tentang Kitab Undang-Undang Hukum Acara Pidana;';

        // Pasal yang dipersangkakan dari form S-12 (jika ada, mulai dari poin d)
        if ($laws && $laws->isNotEmpty()) {
            $groupedLaws = [];
            foreach ($laws as $l) {
                $chapter = trim($l->constitution_chapter ?? '');
                if (!empty($chapter) && stripos($chapter, 'pasal') !== 0) {
                    $chapter = 'Pasal ' . $chapter;
                }

                $constName = '';
                if (!empty($l->crimeConstitution) && !empty($l->crimeConstitution->name)) {
                    $constName = trim($l->crimeConstitution->name);
                } elseif (!empty($l->constitution)) {
                    $constName = trim($l->constitution);
                }
                
                if ($l->flag === 'ADDITIONAL' || $l->flag === 'ADDT') {
                    if (!empty($l->constitution) && empty($chapter)) {
                        $groupedLaws[$constName][] = '';
                    } elseif (!empty($chapter)) {
                        $groupedLaws[$constName][] = $chapter;
                    } else {
                        $groupedLaws[$constName][] = '';
                    }
                } else {
                    if (!empty($chapter)) {
                        $groupedLaws[$constName][] = $chapter;
                    } else {
                        $groupedLaws[$constName][] = '';
                    }
                }
            }

            foreach ($groupedLaws as $uu => $chapters) {
                $chapters = array_values(array_unique(array_filter(array_map('trim', $chapters), function($c) { return !empty($c); })));
                $text = '';
                if (count($chapters) > 0) {
                    if (count($chapters) > 1) {
                        $last = array_pop($chapters);
                        if (count($chapters) > 1) {
                            $text = implode(', ', $chapters) . ', dan ' . $last;
                        } else {
                            $text = $chapters[0] . ' dan ' . $last;
                        }
                    } else {
                        $text = $chapters[0];
                    }
                    if (!empty($uu)) {
                        $text .= ' ' . $uu;
                    }
                } else {
                    if (!empty($uu)) {
                        $text = $uu;
                    }
                }
                
                $text = trim($text);
                if (!empty($text)) {
                    if (!str_ends_with($text, ';')) {
                        $text .= ';';
                    }
                    $rawReferences[] = $text;
                }
            }
        }

        // Laporan Polisi
        $rawReferences[] = 'Laporan Polisi Nomor: ' . ($accident->no_lp ?? '-') . ($laporanPengaduanDate ? ', tanggal ' . $laporanPengaduanDate : '') . ';';

        // Surat Perintah Penyidikan
        $rawReferences[] = 'Surat Perintah Penyidikan Nomor: ' . $document->sprindik_number . ($sprindikDate ? ', tanggal ' . $sprindikDate : '') . ';';

        // Generate penomoran otomatis a., b., c., ...
        $references = [];
        foreach ($rawReferences as $index => $refText) {
            $letter = chr(ord('a') + $index);
            $references[] = [
                'referenceIteration' => trim($letter . '.'),
                'referenceName' => trim($refText),
            ];
        }

        if (in_array('referenceIteration', $templateProcessor->getVariables())) {
            $templateProcessor->cloneRowAndSetValues('referenceIteration', $references);
        }

        // 2. Daftar Barang Sitaan (Seized Items)
        $blockSeizedItems = [];
        $itemIndex = 0;
        foreach ($seizedItems as $item) {
            $amount = trim(((float) $item->jumlah . ' ' . ($item->satuan ?? '')));
            $itemTitle = $amount ? ($amount . ' ' . $item->nama) : $item->nama;
            $details = [];
            if (!empty($item->jenis)) {
                $details[] = 'Jenis: ' . $item->jenis;
            }
            if (!empty($item->keterangan)) {
                $details[] = 'Keterangan: ' . $item->keterangan;
            }
            $itemDesc = $itemTitle . (!empty($details) ? ' (' . implode(', ', $details) . ')' : '');

            $letter = chr(ord('a') + $itemIndex) . '.';
            $blockSeizedItems[] = [
                'seizedIteration' => $letter,
                'seizedItems' => $itemDesc,
            ];
            $itemIndex++;
        }

        if (empty($blockSeizedItems)) {
            $blockSeizedItems[] = [
                'seizedIteration' => '-',
                'seizedItems' => 'Tidak ada barang bukti',
            ];
        }

        if (in_array('seizedIteration', $templateProcessor->getVariables())) {
            $templateProcessor->cloneRowAndSetValues('seizedIteration', $blockSeizedItems);
        }

        // 3. Tembusan (Carbon Copies)
        $carbonCopies = $document->carbon_copies ?? [];
        $blockCarbonCopies = [];
        $noCc = 1;
        if (is_array($carbonCopies)) {
            foreach ($carbonCopies as $ccValue) {
                if (!empty($ccValue)) {
                    $blockCarbonCopies[] = [
                        'carbon_copy_iteration' => $noCc,
                        'carbon_copy_name' => $ccValue,
                        'carbon_opy_iteration' => $noCc,
                    ];
                    $noCc++;
                }
            }
        }

        if (empty($blockCarbonCopies)) {
            $courtProvince = $court ? ($court->regency->province->name ?? '') : ($accident->polres->polda->full_name ?? '');
            $blockCarbonCopies = [
                ['carbon_copy_iteration' => 1, 'carbon_copy_name' => 'Ketua Pengadilan Tinggi ' . ($courtProvince ? ucwords(strtolower($courtProvince)) : 'Jawa Barat'), 'carbon_opy_iteration' => 1],
                ['carbon_copy_iteration' => 2, 'carbon_copy_name' => 'Kapolres ' . ucwords(strtolower($accident->polres->full_name ?? '')), 'carbon_opy_iteration' => 2],
            ];
        }

        if (in_array('carbon_copy_iteration', $templateProcessor->getVariables())) {
            $templateProcessor->cloneRowAndSetValues('carbon_copy_iteration', $blockCarbonCopies);
        } elseif (in_array('carbon_opy_iteration', $templateProcessor->getVariables())) {
            $templateProcessor->cloneRowAndSetValues('carbon_opy_iteration', $blockCarbonCopies);
        }

        // 4. Tersangka / Saksi (Identitas)
        if (in_array('block_suspects', $templateProcessor->getVariables())) {
            $templateProcessor->setValue('block_suspects', '');
        }

        $hasSuspects = $documentSuspects && $documentSuspects->isNotEmpty();
        $suspectExistText = $hasSuspects ? 'tersangka/saksi dengan identitas sebagai berikut:' : 'saksi dengan identitas sebagai berikut:';
        $suspectNameVal = '-';
        $suspectNikVal = '-';
        $suspectNatVal = '-';
        $suspectGenderVal = '-';
        $suspectBirthPlaceVal = '-';
        $suspectBirthDateVal = '-';
        $suspectJobVal = '-';
        $suspectReligionVal = '-';
        $suspectAddressVal = '-';

        if ($hasSuspects) {
            $sNames = [];
            $sNiks = [];
            $sNats = [];
            $sGenders = [];
            $sBirthPlaces = [];
            $sBirthDates = [];
            $sJobs = [];
            $sReligions = [];
            $sAddresses = [];

            foreach ($documentSuspects as $ds) {
                $s = $ds->suspect;
                if ($s) {
                    $sNames[] = $s->full_name ?? ($s->name ?? '-');
                    $sNiks[] = $s->id_card_number ?? ($s->identity_number ?? '-');
                    $natVal = !empty($s->nationality) ? $s->nationality : '-';
                    $sNats[] = $natVal;
                    $sGenders[] = $s->gender->name ?? ($s->gender_name ?? ($s->gender == 'M' || $s->gender == '1' ? 'Laki-laki' : ($s->gender == 'F' || $s->gender == '2' ? 'Perempuan' : '-')));
                    $sBirthPlaces[] = $s->birth_place ?? '-';
                    $sBirthDates[] = $s->birth_date ? Carbon::parse($s->birth_date)->locale('id')->translatedFormat('d F Y') : '-';
                    $sJobs[] = $s->job->name ?? ($s->occupation ?? '-');
                    $sReligions[] = $s->religion->name ?? ($s->religion_name ?? '-');

                    $prop = is_array($s->properties) ? $s->properties : (json_decode($s->properties, true) ?? []);
                    $isUnknownAddr = $prop['is_unknown_address'] ?? false;
                    $fullAddr = $s->address ?? '';
                    if (!empty($s->village->name)) $fullAddr .= ', ' . $s->village->name;
                    if (!empty($s->district->name)) $fullAddr .= ', ' . $s->district->name;
                    if (!empty($s->regency->name)) $fullAddr .= ', ' . $s->regency->name;
                    if (!empty($s->province->name)) $fullAddr .= ', ' . $s->province->name;
                    $sAddresses[] = $isUnknownAddr ? 'TIDAK DIKETAHUI' : ($fullAddr ?: '-');
                }
            }

            if (!empty($sNames)) {
                $suspectNameVal = implode(', ', $sNames);
                $suspectNikVal = implode(', ', $sNiks);
                $suspectNatVal = implode(', ', array_unique($sNats));
                $suspectGenderVal = implode(', ', $sGenders);
                $suspectBirthPlaceVal = implode(', ', $sBirthPlaces);
                $suspectBirthDateVal = implode(', ', $sBirthDates);
                $suspectJobVal = implode(', ', $sJobs);
                $suspectReligionVal = implode(', ', array_unique($sReligions));
                $suspectAddressVal = implode('; ', $sAddresses);
            }
        } else {
            // Jika tidak ada tersangka, gunakan data reported_person yang dipilih
            $reportedPersons = $document->reportedPersons()->withRelated()->get();

            if ($reportedPersons->isNotEmpty()) {
                $rNames = [];
                $rNiks = [];
                $rNats = [];
                $rGenders = [];
                $rBirthPlaces = [];
                $rBirthDates = [];
                $rJobs = [];
                $rReligions = [];
                $rAddresses = [];

                foreach ($reportedPersons as $reportedPerson) {
                    $rNames[] = !empty($reportedPerson->name) ? $reportedPerson->name : '-';
                    $rNiks[] = !empty($reportedPerson->identity_number) ? $reportedPerson->identity_number : (!empty($reportedPerson->id_card_number) ? $reportedPerson->id_card_number : '-');

                    $nat = $reportedPerson->country->name ?? ($reportedPerson->citizenship ?? 'Indonesia');
                    if (strtoupper($nat) === 'WNI') {
                        $nat = 'Indonesia';
                    }
                    $rNats[] = !empty($nat) ? $nat : 'Indonesia';

                    if (!empty($reportedPerson->is_unknown_gender)) {
                        $rGenders[] = 'TIDAK DIKETAHUI';
                    } elseif (isset($reportedPerson->gender->name)) {
                        $rGenders[] = ucwords(strtolower($reportedPerson->gender->name));
                    } elseif (!empty($reportedPerson->gender)) {
                        $gStr = strtoupper((string) $reportedPerson->gender);
                        $rGenders[] = ($gStr === '1' || $gStr === 'M' || $gStr === 'L' || $gStr === 'LAKI-LAKI') ? 'Laki-laki' : (($gStr === '2' || $gStr === 'F' || $gStr === 'P' || $gStr === 'PEREMPUAN') ? 'Perempuan' : '-');
                    } else {
                        $rGenders[] = '-';
                    }

                    if (!empty($reportedPerson->is_unknown_birth_place)) {
                        $rBirthPlaces[] = 'TIDAK DIKETAHUI';
                    } elseif (!empty($reportedPerson->birth_place)) {
                        $rBirthPlaces[] = ucwords(strtolower(trim($reportedPerson->birth_place)));
                    } else {
                        $rBirthPlaces[] = '-';
                    }

                    if (!empty($reportedPerson->is_unknown_birth_date)) {
                        $rBirthDates[] = 'TIDAK DIKETAHUI';
                    } elseif (!empty($reportedPerson->birth_date)) {
                        $rBirthDates[] = Carbon::parse($reportedPerson->birth_date)->locale('id')->translatedFormat('d F Y');
                    } else {
                        $rBirthDates[] = '-';
                    }

                    $rJobs[] = isset($reportedPerson->job->name) ? ucwords(strtolower($reportedPerson->job->name)) : (!empty($reportedPerson->occupation) ? ucwords(strtolower($reportedPerson->occupation)) : '-');
                    $rReligions[] = isset($reportedPerson->religion->name) ? ucwords(strtolower($reportedPerson->religion->name)) : (!empty($reportedPerson->religion_name) ? ucwords(strtolower($reportedPerson->religion_name)) : '-');

                    if (!empty($reportedPerson->is_unknown_address)) {
                        $rAddresses[] = 'TIDAK DIKETAHUI';
                    } else {
                        $addrParts = [];
                        if (!empty($reportedPerson->address)) $addrParts[] = trim($reportedPerson->address);
                        if (!empty($reportedPerson->village->name)) $addrParts[] = trim($reportedPerson->village->name);
                        if (!empty($reportedPerson->district->name)) $addrParts[] = trim($reportedPerson->district->name);
                        if (!empty($reportedPerson->regency->name)) $addrParts[] = trim($reportedPerson->regency->name);
                        if (!empty($reportedPerson->province->name)) $addrParts[] = trim($reportedPerson->province->name);
                        $rAddresses[] = !empty($addrParts) ? ucwords(strtolower(implode(', ', $addrParts))) : (!empty($reportedPerson->address) ? $reportedPerson->address : '-');
                    }
                }

                if (!empty($rNames)) {
                    $suspectNameVal = implode(', ', $rNames);
                    $suspectNikVal = implode(', ', $rNiks);
                    $suspectNatVal = implode(', ', array_unique($rNats));
                    $suspectGenderVal = implode(', ', $rGenders);
                    $suspectBirthPlaceVal = implode(', ', $rBirthPlaces);
                    $suspectBirthDateVal = implode(', ', $rBirthDates);
                    $suspectJobVal = implode(', ', $rJobs);
                    $suspectReligionVal = implode(', ', array_unique($rReligions));
                    $suspectAddressVal = implode('; ', $rAddresses);
                }
            }
        }

        $templateProcessor->setValue('suspectExistText', $suspectExistText);
        $templateProcessor->setValue('suspectName', $suspectNameVal);
        $templateProcessor->setValue('suspectIdentityNumber', $suspectNikVal);
        $templateProcessor->setValue('suspectNationality', $suspectNatVal);
        $templateProcessor->setValue('suspectGenderName', $suspectGenderVal);
        $templateProcessor->setValue('suspectBirthPlace', $suspectBirthPlaceVal);
        $templateProcessor->setValue('suspectBirthDate', $suspectBirthDateVal);
        $templateProcessor->setValue('suspectJobName', $suspectJobVal);
        $templateProcessor->setValue('suspectReligionName', $suspectReligionVal);
        $templateProcessor->setValue('suspectFullAddress', $suspectAddressVal);

        // 5. Narahubung / Penyidik (Investigator) - Diambil HANYA dari Ketua Tim Penyidik (class = 'LEADER')
        $investigatorOfficer = $officers->where('class', 'LEADER')->first();
        if (!$investigatorOfficer && $document->suratPerintahPenyidikanDocument) {
            $spOfficers = $document->suratPerintahPenyidikanDocument->suratPerintahPenyidikanDocumentOfficers;
            if ($spOfficers && $spOfficers->isNotEmpty()) {
                $investigatorOfficer = $spOfficers->where('class', 'LEADER')->first();
            }
        }

        $invName = '-';
        $invPhone = '-';

        if ($investigatorOfficer) {
            $invName = PeopleNameHelper::getFullName($investigatorOfficer->first_title, $investigatorOfficer->first_name, $investigatorOfficer->last_name, $investigatorOfficer->last_title);

            // Fetch fresh Officer record to ensure phone number is accurate
            $fullOfficer = null;
            if (!empty($investigatorOfficer->register_number)) {
                $fullOfficer = Officer::with(['position', 'rank'])->where('register_number', $investigatorOfficer->register_number)->first();
            }

            $rankName = '';
            if ($investigatorOfficer->rank) {
                $rankName = $investigatorOfficer->rank->name;
            } elseif (!empty($investigatorOfficer->rank_name)) {
                $rankName = $investigatorOfficer->rank_name;
            } elseif ($fullOfficer && $fullOfficer->rank) {
                $rankName = $fullOfficer->rank->name;
            }

            if (!empty($rankName)) {
                $invName = trim($rankName . ' ' . $invName);
            }

            $invPhone = !empty($investigatorOfficer->phone_number)
                ? $investigatorOfficer->phone_number
                : (!empty($fullOfficer->phone_number) ? $fullOfficer->phone_number : ($fullOfficer->phone ?? '-'));
            if (empty(trim($invPhone))) {
                $invPhone = '-';
            }
        }

        if (in_array('investigatorPosition', $templateProcessor->getVariables())) {
            $templateProcessor->setValue('investigatorPosition', 'penyidik');
        }
        $templateProcessor->setValue('investigatorName', $invName);
        $templateProcessor->setValue('investigatorPhoneNumber', $invPhone);

        // 6. Penandatangan (Signatory)
        $signatoryName = '';
        $signatoryRankName = '';
        $signatoryRegisterNumber = '';
        $signatoryPositionTitle = 'KASAT LANTAS';
        $signatureTitleText = [
            'KAPOLRES' => 'KEPALA KEPOLISIAN RESOR ' . strtoupper($accident->polres->full_name ?? ''),
            'NO_KAPOLRES' => 'a.n. KEPALA KEPOLISIAN RESOR ' . strtoupper($accident->polres->full_name ?? ''),
            'NO_DIRLANTAS' => 'a.n. DIREKTUR LALU LINTAS POLDA ' . strtoupper($accident->polres->polda->full_name ?? ''),
        ];
        $signatoryHeadText = $signatureTitleText['NO_KAPOLRES'];

        if ($signatory) {
            $signatoryName = PeopleNameHelper::getFullName($signatory->first_title, $signatory->first_name, $signatory->last_name, $signatory->last_title);
            $signatoryRankName = strtoupper($signatory->rank->full_name ?? ($signatory->rank->name ?? ''));
            $signatoryRegisterNumber = $signatory->register_number ?? '';

            if ($signatory->position) {
                $posClusterId = $signatory->position->position_cluster_id ?? null;
                $posName = $signatory->position->name ?? 'KASAT LANTAS';
                if ($posClusterId == '1') {
                    $signatoryHeadText = $signatureTitleText['KAPOLRES'];
                    $signatoryPositionTitle = '';
                } else if ($posClusterId == '9' || (isset($accident->police) && $accident->police->class == 'DAERAH')) {
                    $signatoryHeadText = $signatureTitleText['NO_DIRLANTAS'];
                    $signatoryPositionTitle = $signatory->position->positionCluster->alias_name ?? $posName;
                } else {
                    $signatoryHeadText = $signatureTitleText['NO_KAPOLRES'];
                    $signatoryPositionTitle = $signatory->position->positionCluster->alias_name ?? $posName;
                }
            }
        }

        $templateProcessor->setValue('signatoryHeadText', $signatoryHeadText);
        $templateProcessor->setValue('signatoryPositionName', $signatoryPositionTitle);
        $templateProcessor->setValue('signatoryName', $signatoryName);
        $templateProcessor->setValue('signatoryRankName', $signatoryRankName);
        $templateProcessor->setValue('signatoryRegisterNumber', $signatoryRegisterNumber);

        // 7. General Headers & Information
        $daerahPoliceFullName = strtoupper($accident->polres->polda->full_name ?? 'DAERAH');
        $resorPoliceFullName = (in_array($accident->polres->id ?? '', ['1114'])) ? 'DIREKTORAT LALU LINTAS' : 'RESOR ' . strtoupper($accident->polres->full_name ?? '');
        $resorPoliceAddress = ($accident->polres->address ?? '') . ($accident->polres->polres_zipcode ? ', ' . $accident->polres->polres_zipcode : '');
        $documentLocation = ucwords(strtolower($accident->polres->polres_regency ?? ($accident->polres->city->name ?? ($accident->polres->polres_province ?? 'Tempat'))));

        // Format Tujuan Surat: Yth. KEPALA PENGADILAN NEGERI ... di Kabupaten ...
        if (!$court && !empty($accident->polres->full_name)) {
            $cleanPolres = trim(str_ireplace(['POLRES', 'KEPOLISIAN RESOR'], '', $accident->polres->full_name));
            $court = \App\Models\Lib\Court::where('name', 'ilike', '%' . $cleanPolres . '%')->first();
        }

        $courtNameRaw = $court->name ?? ($court->full_name ?? '');
        $cleanLocation = trim(str_ireplace(['PENGADILAN NEGERI', 'PENGADILAN', 'PN'], '', $courtNameRaw));

        if (empty($cleanLocation)) {
            $cleanLocation = trim(str_ireplace(['POLRES', 'KEPOLISIAN RESOR'], '', $accident->polres->full_name ?? ''));
        }

        if (!empty($courtNameRaw)) {
            if (stripos($courtNameRaw, 'PENGADILAN NEGERI') !== false) {
                $courtNameVal = strtoupper(trim(preg_replace('/\s+/', ' ', $courtNameRaw)));
            } else {
                $courtNameVal = 'PENGADILAN NEGERI ' . strtoupper(trim(preg_replace('/\s+/', ' ', $cleanLocation)));
            }
        } else {
            $courtNameVal = 'PENGADILAN NEGERI ' . strtoupper($cleanLocation ?: 'SOLOK SELATAN');
        }

        if ($court && $court->regency && !empty($court->regency->name)) {
            $courtLocationVal = ucwords(strtolower($court->regency->name));
        } else {
            $locStr = $cleanLocation ?: ($accident->polres->polres_district ?? ($accident->polres->full_name ?? 'Tempat'));
            if (stripos($locStr, 'KABUPATEN') === false && stripos($locStr, 'KOTA') === false) {
                $courtLocationVal = 'Kabupaten ' . ucwords(strtolower($locStr));
            } else {
                $courtLocationVal = ucwords(strtolower($locStr));
            }
        }

        // Dinamis Jumlah Lampiran
        $refCount = count($references);
        $seizedCount = count($blockSeizedItems);
        $totalPages = 2;
        if (($refCount + $seizedCount) > 12) {
            $totalPages += (int) ceil((($refCount + $seizedCount) - 12) / 15);
        }

        $terbilangWords = [
            1 => 'satu', 2 => 'dua', 3 => 'tiga', 4 => 'empat', 5 => 'lima',
            6 => 'enam', 7 => 'tujuh', 8 => 'delapan', 9 => 'sembilan', 10 => 'sepuluh',
        ];
        $word = $terbilangWords[$totalPages] ?? (string)$totalPages;
        $appendixText = $totalPages . ' (' . $word . ') lembar';

        $workUnitName = '';
        if (!empty($accident->police)) {
            if ($accident->police->class == 'DAERAH') {
                $workUnitName = 'Dit Lantas ' . ucwords(strtolower($accident->police->full_name));
            } else if ($accident->police->class == 'RESOR') {
                $workUnitName = 'Sat Lantas ' . ucwords(strtolower($accident->police->full_name));
            }
        }
        if (empty($workUnitName)) {
            $workUnitName = 'Sat Lantas ' . ucwords(strtolower($accident->polres->full_name ?? ''));
        }

        $crimeClass = 'Kejahatan Lalu Lintas';
        if ($laws && $laws->isNotEmpty()) {
            $firstLaw = $laws->first();
            $crimeClass = $firstLaw->crimeClass->name ?? ($firstLaw->crimeType->name ?? 'Kejahatan Lalu Lintas');
        }

        $groupedLaws = [];
        foreach ($laws as $l) {
            $chapter = trim($l->constitution_chapter ?? '');
            if (!empty($chapter) && stripos($chapter, 'pasal') !== 0) {
                $chapter = 'Pasal ' . $chapter;
            }

            $constName = '';
            if (!empty($l->crimeConstitution) && !empty($l->crimeConstitution->name)) {
                $constName = trim($l->crimeConstitution->name);
            } elseif (!empty($l->constitution)) {
                $constName = trim($l->constitution);
            }

            if ($l->flag === 'ADDITIONAL' || $l->flag === 'ADDT') {
                if (!empty($l->constitution) && empty($chapter)) {
                    $groupedLaws[$constName][] = '';
                } elseif (!empty($chapter)) {
                    $groupedLaws[$constName][] = $chapter;
                } else {
                    $groupedLaws[$constName][] = '';
                }
            } else {
                if (!empty($chapter)) {
                    $groupedLaws[$constName][] = $chapter;
                } else {
                    $groupedLaws[$constName][] = '';
                }
            }
        }

        $lawStrings = [];
        foreach ($groupedLaws as $uu => $chapters) {
            $chapters = array_values(array_unique(array_filter(array_map('trim', $chapters), function($c) { return !empty($c); })));
            $text = '';
            if (count($chapters) > 0) {
                if (count($chapters) > 1) {
                    $last = array_pop($chapters);
                    $text = implode(', ', $chapters) . ' dan ' . $last;
                } else {
                    $text = $chapters[0];
                }
                if (!empty($uu)) {
                    $text .= ' ' . $uu;
                }
            } else {
                if (!empty($uu)) {
                    $text = $uu;
                }
            }
            
            $text = trim($text);
            if (!empty($text)) {
                $lawStrings[] = $text;
            }
        }
        $crimeConstitutionText = !empty($lawStrings) ? implode(', ', $lawStrings) : 'Undang-Undang Nomor 22 Tahun 2009 tentang Lalu Lintas dan Angkutan Jalan';

        $accidentRoad = $accident->location ?? ($accident->road ?? ($accident->accident_place ?? ($accident->road_name ?? '-')));

        $templateProcessor->setValue('daerahPoliceFullName', $daerahPoliceFullName);
        $templateProcessor->setValue('resorPoliceFullName', $resorPoliceFullName);
        $templateProcessor->setValue('resorPoliceAddress', $resorPoliceAddress);
        $templateProcessor->setValue('documentLocation', $documentLocation);
        $templateProcessor->setValue('documentDate', $documentDate);
        $templateProcessor->setValue('documentNumber', $document->document_number);
        $docClassification = $document->documentClassification;
        $documentClassificationName = $docClassification ? ucwords(strtolower($docClassification->name)) : 'Biasa';
        $templateProcessor->setValue('documentClassificationName', $documentClassificationName);
        $templateProcessor->setValue('appendix', $appendixText);
        $templateProcessor->setValue('courtName', $courtNameVal);
        $templateProcessor->setValue('courtLocation', $courtLocationVal);
        $templateProcessor->setValue('workUnitName', $workUnitName);
        $templateProcessor->setValue('crimeClass', $crimeClass);
        $templateProcessor->setValue('CrimeConstitution', $crimeConstitutionText);
        $templateProcessor->setValue('accidentRoad', $accidentRoad);
        $templateProcessor->setValue('accidentDay', $accidentDay);
        $templateProcessor->setValue('accidentDate', $accidentDate);

        // Backwards compatibility mappings
        $templateProcessor->setValue('no_lp', $accident->no_lp ?? '');
        $templateProcessor->setValue('accidentNumber', $accident->no_lp ?? '');
        $templateProcessor->setValue('accidentTime', $accidentTime);
        $templateProcessor->setValue('reportDate', $reportDate);
        $templateProcessor->setValue('laporanPengaduanNumber', $accident->no_lp ?? '');
        $templateProcessor->setValue('laporanPengaduanDate', $laporanPengaduanDate);
        $templateProcessor->setValue('sprindikNumber', $document->sprindik_number);
        $templateProcessor->setValue('sprindikDate', $sprindikDate);
        $templateProcessor->setValue('suratPerintahPenyitaanNumber', $document->surat_perintah_penyitaan_number ?? '');
        $templateProcessor->setValue('suratPerintahPenyitaanDate', $suratPerintahPenyitaanDate);
        $templateProcessor->setValue('spdpNumber', $document->spdp_number ?? '');
        $templateProcessor->setValue('spdpDate', $spdpDate);

        if (!file_exists(public_path('generate'))) {
            mkdir(public_path('generate'), 0755, true);
        }

        // Sanitize filename
        $rawPolresName = $accident->polres->full_name ?? 'Dokumen';
        $safePolresName = preg_replace('/[^A-Za-z0-9\-_ ]/', '', $rawPolresName);
        $safePolresName = trim(preg_replace('/\s+/', ' ', $safePolresName));

        $downloadFilename = $document->id . ' - Surat Permintaan Izin Penyitaan - ' . ($safePolresName ?: 'Dokumen') . '.docx';
        $savePath = public_path('generate/' . $downloadFilename);

        $templateProcessor->saveAs($savePath);

        // Post-processing: Format Tembusan borders & docProps page count
        if (file_exists($savePath)) {
            $zip = new \ZipArchive();
            if ($zip->open($savePath) === true) {
                $docXml = $zip->getFromName('word/document.xml');
                if ($docXml) {
                    // Remove any residual KETUA before courtName
                    $docXml = preg_replace('/<w:r[^>]*>(?:(?!<w:r>).)*?KETUA\s*<\/w:t><\/w:r>\s*(<w:r[^>]*>(?:(?!<w:r>).)*?KEPALA)/s', '$1', $docXml);

                    // Format Tembusan table borders so only the last row has a bottom border
                    if (preg_match('/(<w:tbl[^>]*>(?:(?!<w:tbl>).)*?Tembusan.*?<\/w:tbl>)/s', $docXml, $tblMatch)) {
                        $tblXml = $tblMatch[0];
                        preg_match_all('/<w:tr[^>]*>.*?<\/w:tr>/s', $tblXml, $trs);

                        $tembusanFound = false;
                        $ccRowIndices = [];
                        foreach ($trs[0] as $i => $tr) {
                            if (strpos($tr, 'Tembusan') !== false) {
                                $tembusanFound = true;
                                continue;
                            }
                            if ($tembusanFound) {
                                if (preg_match('/<w:bottom[^>]*\/>/s', $tr)) {
                                    $ccRowIndices[] = $i;
                                }
                            }
                        }

                        for ($k = 0; $k < count($ccRowIndices) - 1; $k++) {
                            $idx = $ccRowIndices[$k];
                            $modifiedTr = preg_replace('/<w:tcBorders>.*?<\/w:tcBorders>/s', '', $trs[0][$idx]);
                            $modifiedTr = preg_replace('/<w:bottom[^>]*\/>/s', '', $modifiedTr);
                            $tblXml = str_replace($trs[0][$idx], $modifiedTr, $tblXml);
                        }

                        $docXml = str_replace($tblMatch[0], $tblXml, $docXml);
                    }

                    $zip->addFromString('word/document.xml', $docXml);
                }

                // Update docProps/app.xml Pages
                $appXml = $zip->getFromName('docProps/app.xml');
                if ($appXml) {
                    $appXml = preg_replace('/<Pages>\d+<\/Pages>/', '<Pages>' . $totalPages . '</Pages>', $appXml);
                    $zip->addFromString('docProps/app.xml', $appXml);
                }

                $zip->close();
            }
        }

        return response()->download($savePath, $downloadFilename)->deleteFileAfterSend(true);
    }

    /**
     * Process and store attachment files.
     *
     * Validate the form request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string|null  $documentId
     * @param  string|null  $fixedAccidentId
     * @return \Illuminate\Contracts\Validation\Validator
     */
    private function validateForm(Request $request, $documentId = null, $fixedAccidentId = null)
    {
        $hasExistingAttachments = false;
        $hasExistingSpPenyitaanFile = false;
        if ($documentId) {
            $hasExistingAttachments = SuratPermintaanIzinPenyitaanDocumentAttachment::where('surat_permintaan_izin_penyitaan_document_id', $documentId)->exists();
            
            $existingDoc = \Illuminate\Support\Facades\DB::table('doc.surat_permintaan_izin_penyitaan_documents')->where('id', $documentId)->first();
            if ($existingDoc) {
                $hasExistingSpPenyitaanFile = !empty($existingDoc->surat_perintah_penyitaan_file);
            }
        }

        $effectiveAccidentId = $fixedAccidentId ?? $request->input('accident_id');

        $rules = [
            'accident_id' => [
                'required',
                'uuid',
                Rule::exists(Accident::class, 'id'),
            ],
            'document_number' => 'required|string|max:255',
            'document_date' => 'required|date',

            'sprindik_number' => 'required|string|max:255',
            'sprindik_date' => 'required|date',

            'surat_perintah_penyidikan_document_id' => [
                'nullable',
                'uuid',
                Rule::exists(SuratPerintahPenyidikanDocument::class, 'id'),
                function ($attribute, $value, $fail) use ($effectiveAccidentId) {
                    if (!empty($value) && !empty($effectiveAccidentId)) {
                        $exists = SuratPerintahPenyidikanDocument::where('id', $value)
                            ->where('accident_id', $effectiveAccidentId)
                            ->exists();
                        if (!$exists) {
                            $fail('Dokumen Sprindik yang dipilih tidak sesuai dengan kasus kecelakaan ini.');
                        }
                    }
                },
            ],
            'has_surat_perintah_penyitaan' => 'required|boolean',
            'surat_perintah_penyitaan_number' => 'required_if:has_surat_perintah_penyitaan,1,true|nullable|string|max:255',
            'surat_perintah_penyitaan_date' => 'required_if:has_surat_perintah_penyitaan,1,true|nullable|date',
            'surat_perintah_penyitaan_file' => [
                $hasExistingSpPenyitaanFile ? 'nullable' : 'required_if:has_surat_perintah_penyitaan,1,true',
                'mimes:pdf',
                'max:10240',
            ],
            'spdp_number' => 'nullable|string|max:255',
            'spdp_date' => 'nullable|date',
            'court_id' => [
                'required',
                'string',
                Rule::exists(Court::class, 'id'),
            ],
            'documentClassification' => [
                'required_without_all:classification,document_classification_id',
                function ($attribute, $value, $fail) use ($request) {
                    $val = $request->input('documentClassification')
                        ?: $request->input('classification')
                        ?: $request->input('document_classification_id');
                    if (empty($val)) {
                        $fail('Klasifikasi dokumen wajib dipilih.');
                        return;
                    }
                    if (!DocumentClassification::where('id', $val)->exists()) {
                        $fail('Klasifikasi dokumen yang dipilih tidak valid.');
                    }
                },
            ],
            'classification' => 'nullable|string',
            'document_classification_id' => 'nullable|string',
            'carbonCopies' => 'required|array|min:1',
            'carbonCopies.*' => 'required|string|max:255',

            // Ketua Tim Penyidik wajib diisi
            'officerLeader' => [
                'required',
                'string',
                Rule::exists(Officer::class, 'id'),
                function ($attribute, $value, $fail) use ($effectiveAccidentId) {
                    if (!empty($value) && !empty($effectiveAccidentId)) {
                        $acc = Accident::find($effectiveAccidentId);
                        if ($acc) {
                            $polresIds = $this->getOldNewPolresIds($acc->polres_id);
                            $isAuthorized = Officer::where('id', $value)
                                ->whereIn('police_id', $polresIds)
                                ->where('is_active', true)
                                ->exists();
                            if (!$isAuthorized) {
                                $fail('Ketua Tim Penyidik yang dipilih tidak terdaftar pada kesatuan yang berwenang.');
                            }
                        }
                    }
                },
            ],

            // Pejabat penandatangan wajib 1 dan authorized untuk kesatuan polres
            'officers' => [
                'required',
                'string',
                Rule::exists(Officer::class, 'id'),
                function ($attribute, $value, $fail) use ($effectiveAccidentId) {
                    if (!empty($value) && !empty($effectiveAccidentId)) {
                        $acc = Accident::find($effectiveAccidentId);
                        if ($acc) {
                            $polresIds = $this->getOldNewPolresIds($acc->polres_id);
                            $isAuthorized = Officer::where('id', $value)
                                ->whereIn('police_id', $polresIds)
                                ->where('is_active', true)
                                ->exists();
                            if (!$isAuthorized) {
                                $fail('Pejabat penandatangan yang dipilih tidak terdaftar pada kesatuan yang berwenang.');
                            }
                        }
                    }
                },
            ],

            // (Dihapus: Laws dikelola dari Sprindik)

            // Daftar Tersangka required min 1
            'suspects' => [
                'required',
                'array',
            ],
            'suspects.*' => [
                'required',
                'uuid',
                Rule::exists(Suspect::class, 'id'),
                function ($attribute, $value, $fail) use ($effectiveAccidentId) {
                    if (!empty($value) && !empty($effectiveAccidentId)) {
                        $belongsToAccident = Suspect::where('id', $value)
                            ->where('accident_id', $effectiveAccidentId)
                            ->exists();
                        if (!$belongsToAccident) {
                            $fail('Tersangka yang dipilih tidak terdaftar pada kecelakaan ini.');
                        }
                    }
                },
            ],



            // Daftar Barang Sitaan optional, min 1 jika diisi
            'seized_items' => 'nullable|array',
            'seized_items.*.nama' => 'required_with:seized_items|string|max:255',
            'seized_items.*.jenis' => 'nullable|string|max:255',
            'seized_items.*.jumlah' => 'nullable|numeric|min:0',
            'seized_items.*.satuan' => 'nullable|string|max:50',
            'seized_items.*.keterangan' => 'nullable|string|max:1000',

            // Dokumen digital tidak lagi diwajibkan / diproses di sini
        ];

        $messages = [
            'accident_id.required' => 'Id Kecelakaan wajib diisi.',
            'accident_id.exists' => 'Data Kecelakaan tidak ditemukan.',
            'document_number.required' => 'Nomor Dokumen S-12 wajib diisi.',
            'document_number.max' => 'Nomor Dokumen S-12 maksimal 255 karakter.',
            'document_date.required' => 'Tanggal Dokumen S-12 wajib diisi.',
            'document_date.date' => 'Format Tanggal Dokumen S-12 tidak valid.',



            'sprindik_number.required' => 'No SP Penyidikan wajib diisi.',
            'sprindik_number.max' => 'No SP Penyidikan maksimal 255 karakter.',
            'sprindik_date.required' => 'Tanggal SP Penyidikan wajib diisi.',
            'sprindik_date.date' => 'Format Tanggal Sprindik tidak valid.',

            'has_surat_perintah_penyitaan.required' => 'Pilihan Ada/Tidak Ada SPRINSITA wajib dipilih.',
            'has_surat_perintah_penyitaan.boolean' => 'Pilihan Ada/Tidak Ada SPRINSITA tidak valid.',
            'surat_perintah_penyitaan_number.required_if' => 'Nomor SPRINSITA wajib diisi jika Ada SPRINSITA.',
            'surat_perintah_penyitaan_date.required_if' => 'Tanggal SPRINSITA wajib diisi jika Ada SPRINSITA.',
            'surat_perintah_penyitaan_file.required_if' => 'File SPRINSITA wajib diupload jika Ada SPRINSITA.',
            'surat_perintah_penyitaan_file.mimes' => 'Format file SPRINSITA harus berupa PDF.',
            'surat_perintah_penyitaan_file.max' => 'Ukuran file SPRINSITA maksimal 10 MB.',

            'surat_perintah_penyidikan_document_id.exists' => 'Dokumen Sprindik yang dipilih tidak valid.',
            'court_id.required' => 'Pengadilan Negeri Tujuan wajib dipilih.',
            'court_id.exists' => 'Pengadilan Negeri yang dipilih tidak valid.',
            'documentClassification.required' => 'Klasifikasi dokumen wajib dipilih.',
            'documentClassification.required_without_all' => 'Klasifikasi dokumen wajib dipilih.',
            
            'carbonCopies.required' => 'Tembusan wajib diisi.',
            'carbonCopies.min' => 'Tembusan wajib diisi minimal 1.',
            'carbonCopies.*.required' => 'Mohon Jangan Kosongkan Isi Tembusan, Hapus Jika Memang Tidak Ada.',

            'officerLeader.required' => 'Ketua Tim Penyidik wajib dipilih.',
            'officerLeader.exists' => 'Ketua Tim Penyidik yang dipilih tidak valid.',

            'officers.required' => 'Pejabat Penandatangan wajib dipilih.',
            'officers.exists' => 'Pejabat Penandatangan yang dipilih tidak valid.',



            'suspects.required' => 'Tersangka yang disebutkan di dalam S.P. Izin Penyitaan ke Pengadilan harus diisi.',
            'suspects.*.exists' => 'Tersangka yang dipilih tidak valid.',
            
            'reportedPerson.required' => 'Terlapor yang disebutkan di dalam S.P. Izin Penyitaan ke Pengadilan harus diisi.',
            'reportedPerson.exists' => 'Terlapor yang dipilih tidak valid.',

            'seized_items.*.nama.required_with' => 'Nama barang sitaan wajib diisi.',
        ];

        return Validator::make($request->all(), $rules, $messages);
    }
}
