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

use App\Models\Doc\SuratLaporanPersetujuanPenyitaanDocument\SuratLaporanPersetujuanPenyitaanDocument;
use App\Models\Doc\SuratLaporanPersetujuanPenyitaanDocument\SuratLaporanPersetujuanPenyitaanDocumentOfficer;
use App\Models\Doc\SuratLaporanPersetujuanPenyitaanDocument\SuratLaporanPersetujuanPenyitaanDocumentLaw;
use App\Models\Doc\SuratLaporanPersetujuanPenyitaanDocument\SuratLaporanPersetujuanPenyitaanDocumentSeizedItem;
use App\Models\Doc\SuratLaporanPersetujuanPenyitaanDocument\SuratLaporanPersetujuanPenyitaanDocumentAttachment;
use App\Models\Doc\SuratLaporanPersetujuanPenyitaanDocument\SuratLaporanPersetujuanPenyitaanDocumentPerson;
use App\Models\Pivot\SuratLaporanPersetujuanPenyitaanDocumentSuspect;

class SuratLaporanPersetujuanPenyitaanController extends Controller
{
    use DocsOfficersTraits;

    protected $docService;

    public function __construct(DocService $docService)
    {
        $this->docService = $docService;
    }

    /**
     * Show the form for creating a new Surat Laporan Persetujuan Penyitaan (S-13).
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
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
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

        return view('docs.surat-laporan-persetujuan-penyitaan-document.create', $viewData);
    }

    /**
     * Store a newly created Surat Laporan Persetujuan Penyitaan (S-13) in storage.
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
                if (!$request->filled('sprindik_number')) {
                    $request->merge(['sprindik_number' => $sprindikDoc->document_number]);
                }
                if (!$request->filled('sprindik_date') && $sprindikDoc->document_date) {
                    $request->merge(['sprindik_date' => Carbon::parse($sprindikDoc->document_date)->format('Y-m-d')]);
                }
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
        $suratPerintahPenyitaanNumber = $request->input('surat_perintah_penyitaan_number') ?: null;
        $suratPerintahPenyitaanDate = $request->input('surat_perintah_penyitaan_date')
            ? Carbon::parse($request->input('surat_perintah_penyitaan_date'))->format('Y-m-d')
            : null;
        $spdpNumber = $request->input('spdp_number') ?: null;
        $spdpDate = $request->input('spdp_date')
            ? Carbon::parse($request->input('spdp_date'))->format('Y-m-d')
            : null;
        $courtId = $request->input('court_id') ?: null;

        $exists = DB::table('doc.surat_laporan_persetujuan_penyitaan_documents')
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
            $document = new SuratLaporanPersetujuanPenyitaanDocument();
            $document->id = $documentId;
            $document->accident_id = $accidentId;
            $document->surat_perintah_penyidikan_document_id = $suratPerintahPenyidikanDocumentId;
            $document->document_number = $documentNumber;
            $document->document_date = $documentDate;

            $document->sprindik_number = $sprindikNumber;
            $document->sprindik_date = $sprindikDate;
            $document->surat_perintah_penyitaan_number = $suratPerintahPenyitaanNumber;
            $document->surat_perintah_penyitaan_date = $suratPerintahPenyitaanDate;
            $document->spdp_number = $spdpNumber;
            $document->spdp_date = $spdpDate;
            $document->court_id = $courtId;
            $documentClassificationId = $request->input('documentClassification')
                ?: $request->input('classification')
                ?: $request->input('document_classification_id');
            if ($documentClassificationId) {
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
            $document->document_category_id = '0505';
            $document->is_active = true;
            $document->created_by_user_id = Auth::id();
            $document->save();

            // 2. Simpan Ketua Tim Penyidik (LEADER)
            $leaderOfficerId = $request->officerLeader;
            if ($leaderOfficerId) {
                $leader = Officer::find($leaderOfficerId);
                if ($leader) {
                    $docLeader = new SuratLaporanPersetujuanPenyitaanDocumentOfficer();
                    $docLeader->surat_laporan_persetujuan_penyitaan_document_id = $documentId;
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
                    $docLeader->save();
                }
            }

            // Simpan pejabat penandatangan (SIGNATORY)
            $officerId = $request->input('officers');
            if ($officerId && is_string($officerId)) {
                $officer = Officer::find($officerId);
                if ($officer) {
                    $documentOfficer = new SuratLaporanPersetujuanPenyitaanDocumentOfficer();
                    $documentOfficer->surat_laporan_persetujuan_penyitaan_document_id = $documentId;
                    $documentOfficer->sort = 0;
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
                    $documentOfficer->save();
                }
            }

            // 3. Simpan daftar UU / Pasal (laws)
            if ($request->has('lawCrimeTypeIds')) {
                $lawCrimeTypeIds = $request->lawCrimeTypeIds ?? [];
                $lawCrimeClassIds = $request->lawCrimeClassIds ?? [];
                $lawCrimeConstitutionIds = $request->lawCrimeConstitutionIds ?? [];
                $lawCrimeConstitutionChapters = $request->lawCrimeConstitutionChapters ?? [];

                foreach ($lawCrimeTypeIds as $key => $crimeTypeId) {
                    if (!empty($crimeTypeId)) {
                        $docLaw = new SuratLaporanPersetujuanPenyitaanDocumentLaw();
                        $docLaw->surat_laporan_persetujuan_penyitaan_document_id = $documentId;
                        $docLaw->crime_type_id = $crimeTypeId;
                        $docLaw->crime_class_id = $lawCrimeClassIds[$key] ?? null;
                        $docLaw->crime_constitution_id = $lawCrimeConstitutionIds[$key] ?? null;
                        $docLaw->constitution_chapter = $lawCrimeConstitutionChapters[$key] ?? null;
                        $docLaw->flag = 'MAIN';
                        $docLaw->save();
                    }
                }

            } else {
                $laws = (array) $request->input('laws', []);
                foreach ($laws as $lawItem) {
                    if (!empty($lawItem['crime_constitution_id']) || !empty($lawItem['constitution']) || !empty($lawItem['constitution_chapter']) || !empty($lawItem['pasal'])) {
                        $docLaw = new SuratLaporanPersetujuanPenyitaanDocumentLaw();
                        $docLaw->surat_laporan_persetujuan_penyitaan_document_id = $documentId;
                        $docLaw->crime_type_id = $lawItem['crime_type_id'] ?? null;
                        $docLaw->crime_class_id = $lawItem['crime_class_id'] ?? null;
                        $docLaw->crime_constitution_id = !empty($lawItem['crime_constitution_id']) ? $lawItem['crime_constitution_id'] : null;
                        $docLaw->constitution = $lawItem['constitution'] ?? null;
                        $docLaw->constitution_chapter = $lawItem['constitution_chapter'] ?? ($lawItem['pasal'] ?? null);
                        $docLaw->description = $lawItem['description'] ?? null;
                        
                        $flag = $lawItem['flag'] ?? '';
                        if ($flag === 'ADDT') $flag = 'ADDITIONAL';
                        $docLaw->flag = in_array($flag, ['MAIN', 'ADDITIONAL']) ? $flag : 'MAIN';
                        
                        $docLaw->save();
                    }
                }
            }

            // Additional Laws
            $lawAdditionalNames = $request->lawAdditionalNames ?? [];
            foreach ($lawAdditionalNames as $additionalName) {
                if (!empty(trim($additionalName))) {
                    $docLaw = new SuratLaporanPersetujuanPenyitaanDocumentLaw();
                    $docLaw->surat_laporan_persetujuan_penyitaan_document_id = $documentId;
                    $docLaw->crime_type_id = null;
                    $docLaw->crime_class_id = null;
                    $docLaw->crime_constitution_id = null;
                    $docLaw->constitution = trim($additionalName);
                    $docLaw->flag = 'ADDITIONAL';
                    $docLaw->save();
                }
            }

            // 4. Simpan daftar orang (persons) dan barang sitaannya (seized_items)
            $persons = (array) $request->input('persons', []);
            foreach ($persons as $personData) {
                if (!empty($personData['person_id']) && !empty($personData['person_type'])) {
                    $person = new SuratLaporanPersetujuanPenyitaanDocumentPerson();
                    $person->surat_laporan_persetujuan_penyitaan_document_id = $documentId;
                    
                    if ($personData['person_type'] === 'suspect') {
                        $person->suspect_id = $personData['person_id'];
                    } elseif ($personData['person_type'] === 'witness') {
                        $person->witness_id = $personData['person_id'];
                    } elseif ($personData['person_type'] === 'reported_person') {
                        $person->reported_person_id = $personData['person_id'];
                    }

                    $person->bap_number = $personData['bap_number'] ?? '-';
                    $person->bap_date = !empty($personData['bap_date']) ? Carbon::parse($personData['bap_date'])->format('Y-m-d') : date('Y-m-d');
                    
                    // Lokasi Penyitaan
                    $isSeizedAtWorkUnit = !isset($personData['is_seized_at_work_unit']) || $personData['is_seized_at_work_unit'] === '1' || $personData['is_seized_at_work_unit'] === 'true' || $personData['is_seized_at_work_unit'] === true;
                    $person->is_seized_at_work_unit = $isSeizedAtWorkUnit;
                    $person->seized_location = !$isSeizedAtWorkUnit ? ($personData['seized_location'] ?? null) : null;

                    $person->save();

                    // Simpan barang sitaan untuk orang ini
                    if (isset($personData['seized_items']) && is_array($personData['seized_items'])) {
                        foreach ($personData['seized_items'] as $item) {
                            if (!empty($item['name'])) {
                                $seizedItem = new SuratLaporanPersetujuanPenyitaanDocumentSeizedItem();
                                $seizedItem->surat_laporan_persetujuan_penyitaan_document_person_id = $person->id;
                                $seizedItem->name = $item['name'];
                                $seizedItem->quantity = isset($item['quantity']) && is_numeric($item['quantity']) ? (int) $item['quantity'] : 1;
                                $seizedItem->unit = $item['unit'] ?? null;
                                $seizedItem->description = $item['description'] ?? null;
                                $seizedItem->save();
                            }
                        }
                    }
                }
            }

            // 6. Simpan dokumen digital (attachments)
            // (Attachment upload moved to Document Action)

            DB::commit();

            Log::info('Surat Laporan Persetujuan Penyitaan (S-13) created successfully', [
                'id' => $documentId,
                'accident_id' => $accidentId,
                'document_number' => $documentNumber,
                'created_by' => Auth::id(),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            // Clean up newly uploaded files on failure
            foreach ($newlyStoredFiles as $storedFile) {
                if (Storage::disk('public')->exists($storedFile)) {
                    Storage::disk('public')->delete($storedFile);
                }
            }

            Log::error('Error storing Surat Laporan Persetujuan Penyitaan S-13: ' . $e->getMessage(), [
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
                'message' => 'Dokumen Surat Laporan Persetujuan Penyitaan (S-13) berhasil disimpan.',
                'data' => [
                    'id' => $documentId,
                    'accident_id' => $accidentId,
                    'document_number' => $documentNumber,
                ],
            ], 200);
        }

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
            ->with('success', 'Dokumen Surat Laporan Persetujuan Penyitaan (S-13) berhasil dibuat.');
    }

    /**
     * Display the specified Surat Laporan Persetujuan Penyitaan (S-13).
     *
     * @param  string  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        if (!Auth::check()) {
            abort(403, 'Akses ditolak.');
        }

        $document = SuratLaporanPersetujuanPenyitaanDocument::with([
            'accident.polres.polda',
            'officers.rank',
            'officers.position',
            'laws.crimeConstitution',
            'persons.seizedItems',
            'persons.suspect',
            'persons.witness',
            'persons.reportedPerson',
            'attachments',
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
        $laws = $document->laws;
        $seizedItems = $document->persons->flatMap->seizedItems;
        $attachments = $document->attachments;
        $documentSuspects = $document->persons;
        $suratPerintahPenyidikanDocument = $document->suratPerintahPenyidikanDocument;
        $court = $document->court;
        $documentCategory = $document->documentCategory;
        $createdByUser = $document->createdByUser;
        $updatedByUser = $document->updatedByUser;

        $viewData = [
            'id' => $id,
            'documentId' => $id,
            'document' => $document,
            'accidentId' => $accidentId,
            'accident' => $accident,
            'officers' => $officers,
            'leaderOfficer' => $leaderOfficer,
            'signatories' => $signatories,
            'laws' => $laws,
            'seizedItems' => $seizedItems,
            'attachments' => $attachments,
            'persons' => $documentSuspects,
            'documentSuspects' => $documentSuspects,
            'suratPerintahPenyidikanDocument' => $suratPerintahPenyidikanDocument,
            'court' => $court,
            'documentCategory' => $documentCategory,
            'createdByUser' => $createdByUser,
            'updatedByUser' => $updatedByUser,
        ];

        return view('docs.surat-laporan-persetujuan-penyitaan-document.show', $viewData);
    }

    /**
     * Show the form for editing the specified Surat Laporan Persetujuan Penyitaan (S-13).
     *
     * @param  string  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        if (!Auth::check()) {
            abort(403, 'Akses ditolak.');
        }

        $document = SuratLaporanPersetujuanPenyitaanDocument::with([
            'officers.rank',
            'officers.position',
            'laws.crimeConstitution',
            'persons.seizedItems',
            'persons.suspect',
            'persons.witness',
            'persons.reportedPerson',
            'attachments',
            'accident',
        ])->where('id', $id)->firstOrFail();

        $accidentId = $document->accident_id;
        $accident = $document->accident ?? Accident::where('id', $accidentId)->firstOrFail();

        $officers = $document->officers;
        $laws = $document->laws;
        $seizedItems = $document->persons->flatMap->seizedItems;
        $attachments = $document->attachments;
        $documentSuspects = $document->persons;

        $selectedSuspectIds = $documentSuspects->pluck('suspect_id')->toArray();

        $suratPerintahPenyidikanDocuments = SuratPerintahPenyidikanDocument::with(['suratPerintahPenyidikanDocumentOfficers'])
            ->where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->get();

        $spdpDocuments = SuratPemberitahuanDimulainyaPenyidikanDocument::where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
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
            'persons' => $documentSuspects,
            'documentSuspects' => $documentSuspects,
            'selectedSuspectIds' => $selectedSuspectIds,
            'suratPerintahPenyidikanDocuments' => $suratPerintahPenyidikanDocuments,
            'spdpDocuments' => $spdpDocuments,
            'authorizedSignatories' => $authorizedSignatories,
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

        return view('docs.surat-laporan-persetujuan-penyitaan-document.edit', $viewData);
    }

    /**
     * Update the specified Surat Laporan Persetujuan Penyitaan (S-13) in storage.
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

        $document = SuratLaporanPersetujuanPenyitaanDocument::where('id', $id)->firstOrFail();

        if ($request->filled('surat_perintah_penyidikan_document_id')) {
            $sprindikDoc = SuratPerintahPenyidikanDocument::find($request->input('surat_perintah_penyidikan_document_id'));
            if ($sprindikDoc) {
                if (!$request->filled('sprindik_number')) {
                    $request->merge(['sprindik_number' => $sprindikDoc->document_number]);
                }
                if (!$request->filled('sprindik_date') && $sprindikDoc->document_date) {
                    $request->merge(['sprindik_date' => Carbon::parse($sprindikDoc->document_date)->format('Y-m-d')]);
                }
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

        $suratPerintahPenyidikanDocumentId = $request->input('surat_perintah_penyidikan_document_id') ?: null;
        $suratPerintahPenyitaanNumber = $request->input('surat_perintah_penyitaan_number') ?: null;
        $suratPerintahPenyitaanDate = $request->input('surat_perintah_penyitaan_date')
            ? Carbon::parse($request->input('surat_perintah_penyitaan_date'))->format('Y-m-d')
            : null;
        $spdpNumber = $request->input('spdp_number') ?: null;
        $spdpDate = $request->input('spdp_date')
            ? Carbon::parse($request->input('spdp_date'))->format('Y-m-d')
            : null;
        $courtId = $request->input('court_id') ?: null;

        // Cek duplikasi nomor dokumen jika nomor dokumen diubah
        $oldDocumentNumber = $document->document_number;
        if (strtolower($oldDocumentNumber) != strtolower($documentNumber)) {
            $exists = DB::table('doc.surat_laporan_persetujuan_penyitaan_documents')
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
            $document->spdp_number = $spdpNumber;
            $document->spdp_date = $spdpDate;
            $document->court_id = $courtId;
            $documentClassificationId = $request->input('documentClassification')
                ?: $request->input('classification')
                ?: $request->input('document_classification_id');
            if ($documentClassificationId) {
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

            // 2. Sync / update Ketua Tim Penyidik (LEADER)
            SuratLaporanPersetujuanPenyitaanDocumentOfficer::where('surat_laporan_persetujuan_penyitaan_document_id', $id)->delete();

            $leaderOfficerId = $request->officerLeader;
            if ($leaderOfficerId) {
                $leader = Officer::find($leaderOfficerId);
                if ($leader) {
                    $docLeader = new SuratLaporanPersetujuanPenyitaanDocumentOfficer();
                    $docLeader->surat_laporan_persetujuan_penyitaan_document_id = $id;
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
                    $docLeader->save();
                }
            }

            // Sync / update pejabat penandatangan (SIGNATORY)
            $officerId = $request->input('officers');
            if ($officerId && is_string($officerId)) {
                $officer = Officer::find($officerId);
                if ($officer) {
                    $documentOfficer = new SuratLaporanPersetujuanPenyitaanDocumentOfficer();
                    $documentOfficer->surat_laporan_persetujuan_penyitaan_document_id = $id;
                    $documentOfficer->sort = 0;
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
                    $documentOfficer->save();
                }
            }


            // 3. Sync / update daftar UU / Pasal (laws)
            SuratLaporanPersetujuanPenyitaanDocumentLaw::where('surat_laporan_persetujuan_penyitaan_document_id', $id)->delete();

            if ($request->has('lawCrimeTypeIds')) {
                $lawCrimeTypeIds = $request->lawCrimeTypeIds ?? [];
                $lawCrimeClassIds = $request->lawCrimeClassIds ?? [];
                $lawCrimeConstitutionIds = $request->lawCrimeConstitutionIds ?? [];
                $lawCrimeConstitutionChapters = $request->lawCrimeConstitutionChapters ?? [];

                foreach ($lawCrimeTypeIds as $key => $crimeTypeId) {
                    if (!empty($crimeTypeId)) {
                        $docLaw = new SuratLaporanPersetujuanPenyitaanDocumentLaw();
                        $docLaw->surat_laporan_persetujuan_penyitaan_document_id = $id;
                        $docLaw->crime_type_id = $crimeTypeId;
                        $docLaw->crime_class_id = $lawCrimeClassIds[$key] ?? null;
                        $docLaw->crime_constitution_id = $lawCrimeConstitutionIds[$key] ?? null;
                        $docLaw->constitution_chapter = $lawCrimeConstitutionChapters[$key] ?? null;
                        $docLaw->flag = 'MAIN';
                        $docLaw->save();
                    }
                }

            } else {
                $laws = (array) $request->input('laws', []);
                foreach ($laws as $lawItem) {
                    if (!empty($lawItem['crime_constitution_id']) || !empty($lawItem['constitution']) || !empty($lawItem['constitution_chapter']) || !empty($lawItem['pasal'])) {
                        $docLaw = new SuratLaporanPersetujuanPenyitaanDocumentLaw();
                        $docLaw->surat_laporan_persetujuan_penyitaan_document_id = $id;
                        $docLaw->crime_type_id = $lawItem['crime_type_id'] ?? null;
                        $docLaw->crime_class_id = $lawItem['crime_class_id'] ?? null;
                        $docLaw->crime_constitution_id = !empty($lawItem['crime_constitution_id']) ? $lawItem['crime_constitution_id'] : null;
                        $docLaw->constitution = $lawItem['constitution'] ?? null;
                        $docLaw->constitution_chapter = $lawItem['constitution_chapter'] ?? ($lawItem['pasal'] ?? null);
                        $docLaw->description = $lawItem['description'] ?? null;
                        
                        $flag = $lawItem['flag'] ?? '';
                        if ($flag === 'ADDT') $flag = 'ADDITIONAL';
                        $docLaw->flag = in_array($flag, ['MAIN', 'ADDITIONAL']) ? $flag : 'MAIN';
                        
                        $docLaw->save();
                    }
                }
            }

            // Additional Laws
            $lawAdditionalNames = $request->lawAdditionalNames ?? [];
            foreach ($lawAdditionalNames as $additionalName) {
                if (!empty(trim($additionalName))) {
                    $docLaw = new SuratLaporanPersetujuanPenyitaanDocumentLaw();
                    $docLaw->surat_laporan_persetujuan_penyitaan_document_id = $id;
                    $docLaw->crime_type_id = null;
                    $docLaw->crime_class_id = null;
                    $docLaw->crime_constitution_id = null;
                    $docLaw->constitution = trim($additionalName);
                    $docLaw->flag = 'ADDITIONAL';
                    $docLaw->save();
                }
            }

            // 4. Sync / update daftar orang (persons) dan barang sitaannya
            $existingPersons = SuratLaporanPersetujuanPenyitaanDocumentPerson::where('surat_laporan_persetujuan_penyitaan_document_id', $id)->get();
            foreach ($existingPersons as $extPerson) {
                SuratLaporanPersetujuanPenyitaanDocumentSeizedItem::where('surat_laporan_persetujuan_penyitaan_document_person_id', $extPerson->id)->delete();
                $extPerson->delete();
            }

            $persons = (array) $request->input('persons', []);
            foreach ($persons as $personData) {
                if (!empty($personData['person_id']) && !empty($personData['person_type'])) {
                    $person = new SuratLaporanPersetujuanPenyitaanDocumentPerson();
                    $person->surat_laporan_persetujuan_penyitaan_document_id = $id;
                    
                    if ($personData['person_type'] === 'suspect') {
                        $person->suspect_id = $personData['person_id'];
                    } elseif ($personData['person_type'] === 'witness') {
                        $person->witness_id = $personData['person_id'];
                    } elseif ($personData['person_type'] === 'reported_person') {
                        $person->reported_person_id = $personData['person_id'];
                    }

                    $person->bap_number = $personData['bap_number'] ?? '-';
                    $person->bap_date = !empty($personData['bap_date']) ? Carbon::parse($personData['bap_date'])->format('Y-m-d') : date('Y-m-d');

                    // Lokasi Penyitaan
                    $isSeizedAtWorkUnit = !isset($personData['is_seized_at_work_unit']) || $personData['is_seized_at_work_unit'] === '1' || $personData['is_seized_at_work_unit'] === 'true' || $personData['is_seized_at_work_unit'] === true;
                    $person->is_seized_at_work_unit = $isSeizedAtWorkUnit;
                    $person->seized_location = !$isSeizedAtWorkUnit ? ($personData['seized_location'] ?? null) : null;

                    $person->save();

                    if (isset($personData['seized_items']) && is_array($personData['seized_items'])) {
                        foreach ($personData['seized_items'] as $item) {
                            if (!empty($item['name'])) {
                                $seizedItem = new SuratLaporanPersetujuanPenyitaanDocumentSeizedItem();
                                $seizedItem->surat_laporan_persetujuan_penyitaan_document_person_id = $person->id;
                                $seizedItem->name = $item['name'];
                                $seizedItem->quantity = isset($item['quantity']) && is_numeric($item['quantity']) ? (int) $item['quantity'] : 1;
                                $seizedItem->unit = $item['unit'] ?? null;
                                $seizedItem->description = $item['description'] ?? null;
                                $seizedItem->save();
                            }
                        }
                    }
                }
            }

            // 6. Simpan dokumen digital baru (attachments)
            // (Attachment upload moved to Document Action)

            // 6c. Hapus dokumen digital spesifik jika diminta
            // (Attachment deletion moved to Document Action or disabled here)

            DB::commit();

            // Hapus file fisik hanya setelah transaksi database berhasil di-commit
            foreach ($filesToDeleteAfterCommit as $filePath) {
                if (Storage::disk('public')->exists($filePath)) {
                    Storage::disk('public')->delete($filePath);
                }
            }

            Log::info('Surat Laporan Persetujuan Penyitaan (S-13) updated successfully', [
                'id' => $id,
                'accident_id' => $accidentId,
                'document_number' => $documentNumber,
                'updated_by' => Auth::id(),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            // Clean up newly uploaded files on failure
            foreach ($newlyStoredFiles as $storedFile) {
                if (Storage::disk('public')->exists($storedFile)) {
                    Storage::disk('public')->delete($storedFile);
                }
            }

            Log::error('Error updating Surat Laporan Persetujuan Penyitaan S-13: ' . $e->getMessage(), [
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
                'message' => 'Dokumen Surat Laporan Persetujuan Penyitaan (S-13) berhasil diperbarui.',
                'data' => [
                    'id' => $id,
                    'accident_id' => $accidentId,
                    'document_number' => $documentNumber,
                ],
            ], 200);
        }

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
            ->with('success', 'Dokumen Surat Laporan Persetujuan Penyitaan (S-13) berhasil diperbarui.');
    }

    /**
     * Remove the specified Surat Laporan Persetujuan Penyitaan (S-13) from storage.
     *
     * @param  string  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($id)
    {
        // Get URL Parameter
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $suratLaporanPersetujuanPenyitaanDocumentId = $id;

        DB::beginTransaction();
        try {
            // Delete from database
            $suratLaporanPersetujuanPenyitaanDocument = SuratLaporanPersetujuanPenyitaanDocument::where('id', $suratLaporanPersetujuanPenyitaanDocumentId)->first();
            if ($suratLaporanPersetujuanPenyitaanDocument) {
                $suratLaporanPersetujuanPenyitaanDocument->delete();
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
     * Download the specified Surat Laporan Persetujuan Penyitaan (S-13) as Word document.
     *
     * @param  string  $id
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download($id)
    {
        if (!Auth::check()) {
            abort(403, 'Akses ditolak.');
        }

        $document = SuratLaporanPersetujuanPenyitaanDocument::with([
            'officers.rank',
            'officers.position',
            'officers.position.positionCluster',
            'laws.crimeConstitution',
            'laws.crimeClass',
            'laws.crimeType',
            'persons.suspect.country',
            'persons.suspect.gender',
            'persons.suspect.job',
            'persons.suspect.religion',
            'persons.witness.country',
            'persons.witness.gender',
            'persons.witness.job',
            'persons.witness.religion',
            'persons.reportedPerson.country',
            'persons.reportedPerson.gender',
            'persons.reportedPerson.job',
            'persons.reportedPerson.religion',
            'persons.seizedItems',
            'court',
            'suratPerintahPenyidikanDocument.suratPerintahPenyidikanDocumentOfficers.position',
            'suratPerintahPenyidikanDocument.suratPerintahPenyidikanDocumentOfficers.rank',
            'createdByUser.officer.position',
            'createdByUser.officer.rank',
        ])->where('id', $id)->firstOrFail();

        $accidentId = $document->accident_id;
        $accident = Accident::with(['polres', 'polres.polda', 'police'])->where('id', $accidentId)->firstOrFail();

        $officers = $document->officers;
        $laws = $document->laws;
        $persons = $document->persons;
        $court = $document->court;
        $signatory = $officers->where('class', 'SIGNATORY')->first() ?? $officers->first();

        // Template path: prioritize public/word-template/surat_laporan_persetujuan_penyitaan.docx
        $templatePath = public_path('word-template/surat_laporan_mendapatkan_persetujuan_penyitaan.docx');
        if (!file_exists($templatePath)) {
            $templatePath = 'word-template/surat_laporan_mendapatkan_persetujuan_penyitaan.docx';
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
        $document->loadMissing(['laws.crimeConstitution', 'laws.crimeType']);
        $rawReferences = [];
        $rawReferences[] = 'Undang-Undang Nomor 2 Tahun 2002 tentang Kepolisian Negara Republik Indonesia;';
        $rawReferences[] = 'Pasal 3 dan Pasal 618 Undang-Undang Nomor 1 Tahun 2023 tentang Kitab Undang-Undang Hukum Pidana;';
        $rawReferences[] = 'Pasal 1 angka 14 dan angka 35, Pasal 5 ayat (2) huruf b, Pasal 7 ayat (1) huruf f, Pasal 44, Pasal 45, Pasal 46, Pasal 47, Pasal 89 huruf e, Pasal 113 ayat (3), Pasal 118, Pasal 119, Pasal 120, Pasal 121, Pasal 122, Pasal 123, Pasal 124, Pasal 125, Pasal 127, Pasal 128, Pasal 129, Pasal 130, Pasal 133, Pasal 156 ayat (1) huruf e, Pasal 158 huruf d dan Pasal 179 dan Pasal 361 Undang-Undang Nomor 20 Tahun 2025 tentang Kitab Undang-Undang Hukum Acara Pidana;';

        // Pasal yang dipersangkakan dari form S-13 (jika ada, mulai dari poin d)
        $lawStrings = [];
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
                
                if (empty($constName)) {
                    $constName = 'Tanpa UU';
                }

                if ($l->flag === 'ADDITIONAL' || $l->flag === 'ADDT') {
                    if (!empty($l->constitution)) {
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
                $chapters = array_values(array_filter(array_map('trim', $chapters), function($c) { return !empty($c); }));
                $text = '';
                if (count($chapters) > 0) {
                    if (count($chapters) > 1) {
                        $last = array_pop($chapters);
                        $text = implode(', ', $chapters) . ' dan ' . $last;
                    } else {
                        $text = $chapters[0];
                    }
                    if ($uu !== 'Tanpa UU') {
                        $text .= ' ' . $uu;
                    }
                } else {
                    if ($uu !== 'Tanpa UU') {
                        $text = $uu;
                    }
                }
                
                if (!empty($text)) {
                    $lawStrings[] = trim($text);
                    $rawReferences[] = trim($text) . ';';
                }
            }
        }

        // Laporan Polisi
        $rawReferences[] = 'Laporan Polisi Nomor: ' . ($accident->no_lp ?? '-') . ($laporanPengaduanDate ? ', tanggal ' . $laporanPengaduanDate : '') . ';';

        // Surat Perintah Penyidikan
        $rawReferences[] = 'Surat Perintah Penyidikan Nomor: ' . $document->sprindik_number . ($sprindikDate ? ', tanggal ' . $sprindikDate : '') . ';';

        // Surat Pemberitahuan Dimulainya Penyidikan (SPDP)
        if (!empty($document->spdp_number)) {
            $rawReferences[] = 'Surat Pemberitahuan Dimulainya Penyidikan Nomor: ' . $document->spdp_number . ($spdpDate ? ', tanggal ' . $spdpDate : '') . ';';
        }

        // Surat Perintah Penyitaan
        if (!empty($document->surat_perintah_penyitaan_number)) {
            $rawReferences[] = 'Surat Perintah Penyitaan Nomor: ' . $document->surat_perintah_penyitaan_number . ($suratPerintahPenyitaanDate ? ', tanggal ' . $suratPerintahPenyitaanDate : '') . ';';
        }

        // BAP Penyitaan (semua orang)
        if ($persons && $persons->isNotEmpty()) {
            $bapStrings = [];
            foreach ($persons as $p) {
                if (!empty($p->bap_number)) {
                    $bapDateFormatted = $p->bap_date ? \Carbon\Carbon::parse($p->bap_date)->locale('id')->translatedFormat('d F Y') : '';
                    $bapStrings[] = 'Berita Acara Penyitaan Nomor ' . $p->bap_number . ($bapDateFormatted ? ' tanggal ' . $bapDateFormatted : '');
                }
            }
            if (!empty($bapStrings)) {
                $bapText = '';
                if (count($bapStrings) > 1) {
                    $last = array_pop($bapStrings);
                    $bapText = implode(', ', $bapStrings) . ', dan ' . $last;
                } else {
                    $bapText = $bapStrings[0];
                }
                $rawReferences[] = trim($bapText) . ';';
            }
        }

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

        // 2. Daftar Orang, BAP, dan Barang Sitaan
        $blockSuspects = [];
        $uniqueLocations = [];
        
        $hasPersons = $persons && $persons->isNotEmpty();
        if ($hasPersons) {
            foreach ($persons as $index => $person) {
                // Track location
                $loc = $person->is_seized_at_work_unit ? ucwords(strtolower($accident->police->full_name ?? '')) : trim($person->seized_location ?? '');
                if (!empty($loc) && !in_array($loc, $uniqueLocations)) {
                    $uniqueLocations[] = $loc;
                }

                // Data orang (suspectIteration)
                $s = null;
                if ($person->suspect_id) { $s = $person->suspect; }
                elseif ($person->witness_id) { $s = $person->witness; }
                elseif ($person->reported_person_id) { $s = $person->reportedPerson; }

                $name = $s ? ($s->full_name ?? ($s->name ?? '-')) : '-';
                $nik = $s ? ($s->id_card_number ?? ($s->identity_number ?? '-')) : '-';
                $birthPlace = $s ? ($s->birth_place ?? '-') : '-';
                $birthDate = ($s && $s->birth_date) ? \Carbon\Carbon::parse($s->birth_date)->locale('id')->translatedFormat('d F Y') : '-';
                $job = $s ? ($s->job->name ?? ($s->occupation ?? '-')) : '-';
                
                $address = '-';
                if ($s) {
                    $prop = is_array($s->properties) ? $s->properties : (json_decode($s->properties, true) ?? []);
                    $isUnknownAddr = $prop['is_unknown_address'] ?? false;
                    $fullAddr = $s->address ?? '';
                    if (!empty($s->village->name)) $fullAddr .= ', ' . $s->village->name;
                    if (!empty($s->district->name)) $fullAddr .= ', ' . $s->district->name;
                    if (!empty($s->regency->name)) $fullAddr .= ', ' . $s->regency->name;
                    if (!empty($s->province->name)) $fullAddr .= ', ' . $s->province->name;
                    $address = $isUnknownAddr ? 'TIDAK DIKETAHUI' : ($fullAddr ?: '-');
                }

                // Data BAP (BAPDate)
                $bapDate = $person->bap_date ? \Carbon\Carbon::parse($person->bap_date)->locale('id')->translatedFormat('d F Y') : '-';

                // Data Barang Sitaan
                $seizedIters = [];
                $seizedDescs = [];
                if ($person->seizedItems && $person->seizedItems->isNotEmpty()) {
                    $itemIndex = 0;
                    foreach ($person->seizedItems as $item) {
                        $amount = trim(((float) $item->quantity . ' ' . ($item->unit ?? '')));
                        $itemTitle = $amount ? ($amount . ' ' . $item->name) : $item->name;
                        $details = [];
                        if (!empty($item->description)) {
                            $details[] = 'Keterangan: ' . $item->description;
                        }

                        $itemDesc = $itemTitle;
                        if (!empty($details)) {
                            $itemDesc .= ' (' . implode(', ', $details) . ')';
                        }

                        $letter = ($itemIndex + 1) . ')';
                        $seizedIters[] = $letter;
                        $seizedDescs[] = $itemDesc;
                        $itemIndex++;
                    }
                } else {
                    $seizedIters[] = '-';
                    $seizedDescs[] = 'Tidak ada barang bukti';
                }

        // Group everything for this person block
                $blockSuspects[] = [
                    'suspectIteration' => chr(ord('a') + $index) . '.',
                    'suspectNameIteration' => htmlspecialchars($name),
                    'suspectIdentityNumberIteration' => htmlspecialchars($nik),
                    'suspectBirthPlaces' => htmlspecialchars($birthPlace),
                    'suspectBirthDates' => htmlspecialchars($birthDate),
                    'suspectJobIteration' => htmlspecialchars($job),
                    'suspectAddressIteration' => htmlspecialchars($address),
                    'BAPDate' => htmlspecialchars($bapDate),
                    'seizedIteration' => implode('</w:t><w:br/><w:t>', array_map('htmlspecialchars', $seizedIters)),
                    'seizedItems' => implode('</w:t><w:br/><w:t>', array_map('htmlspecialchars', $seizedDescs)),
                ];
            }
        } else {
            $blockSuspects[] = [
                'suspectIteration' => '-', 'suspectNameIteration' => '-', 'suspectIdentityNumberIteration' => '-',
                'suspectBirthPlaces' => '-', 'suspectBirthDates' => '-', 'suspectJobIteration' => '-', 'suspectAddressIteration' => '-',
                'BAPDate' => '-', 'seizedIteration' => '-', 'seizedItems' => 'Tidak ada barang bukti'
            ];
        }

        // Apply cloneRow only on the outer row (suspectIteration) which contains BAPDate and seizedIteration
        if (in_array('suspectIteration', $templateProcessor->getVariables())) {
            $templateProcessor->cloneRow('suspectIteration', count($blockSuspects));
            foreach ($blockSuspects as $i => $data) {
                $idx = $i + 1;
                foreach ($data as $k => $v) {
                    $templateProcessor->setValue("$k#$idx", $v);
                }
            }
        }
        
        $seizedLocationStr = '-';
        if (!empty($uniqueLocations)) {
            if (count($uniqueLocations) === 1) {
                $seizedLocationStr = $uniqueLocations[0];
            } elseif (count($uniqueLocations) === 2) {
                $seizedLocationStr = $uniqueLocations[0] . ' dan ' . $uniqueLocations[1];
            } else {
                $last = array_pop($uniqueLocations);
                $seizedLocationStr = implode(', ', $uniqueLocations) . ', dan ' . $last;
            }
        }
        $templateProcessor->setValue('seizedLocation', $seizedLocationStr);

        // 3. Tembusan (Carbon Copies)
        $carbonCopies = $document->carbon_copies ?? [];
        $blockCarbonCopies = [];
        $noCc = 1;
        if (is_array($carbonCopies)) {
            foreach ($carbonCopies as $ccValue) {
                if (!empty($ccValue)) {
                    $blockCarbonCopies[] = [
                        'carbon_copy_iteration' => $noCc,
                        'carbon_copy_name' => htmlspecialchars($ccValue),
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
        $suspectExistText = 'orang dengan identitas sebagai berikut:';
        $suspectNameVal = '-';
        $suspectNikVal = '-';
        $suspectNatVal = '-';
        $suspectGenderVal = '-';
        $suspectBirthPlaceVal = '-';
        $suspectBirthDateVal = '-';
        $suspectJobVal = '-';
        $suspectReligionVal = '-';
        $suspectAddressVal = '-';

        if ($hasPersons) {
            $sNames = [];
            $sNiks = [];
            $sNats = [];
            $sGenders = [];
            $sBirthPlaces = [];
            $sBirthDates = [];
            $sJobs = [];
            $sReligions = [];
            $sAddresses = [];

            foreach ($persons as $person) {
                // Determine the model
                $s = null;
                if ($person->suspect_id) {
                    $s = $person->suspect;
                } elseif ($person->witness_id) {
                    $s = $person->witness;
                } elseif ($person->reported_person_id) {
                    $s = $person->reportedPerson;
                }

                if ($s) {
                    $sNames[] = $s->full_name ?? ($s->name ?? '-');
                    $sNiks[] = $s->id_card_number ?? ($s->identity_number ?? '-');
                    $natVal = $s->country->name ?? ($s->citizenship ?? (!empty($s->country) && is_string($s->country) ? $s->country : 'Indonesia'));
                    if (strtoupper($natVal) === 'WNI') {
                        $natVal = 'Indonesia';
                    }
                    $sNats[] = $natVal;
                    $sGenders[] = $s->gender->name ?? ($s->gender_name ?? ($s->gender == 'M' || $s->gender == '1' ? 'Laki-laki' : ($s->gender == 'F' || $s->gender == '2' ? 'Perempuan' : '-')));
                    $sBirthPlaces[] = $s->birth_place ?? '-';
                    $sBirthDates[] = $s->birth_date ? \Carbon\Carbon::parse($s->birth_date)->locale('id')->translatedFormat('d F Y') : '-';
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
                $suspectNameVal = $sNames[0];
                $suspectNikVal = $sNiks[0];
                $suspectNatVal = $sNats[0];
                $suspectGenderVal = $sGenders[0];
                $suspectBirthPlaceVal = $sBirthPlaces[0];
                $suspectBirthDateVal = $sBirthDates[0];
                $suspectJobVal = $sJobs[0];
                $suspectReligionVal = $sReligions[0];
                $suspectAddressVal = $sAddresses[0];
                
                $firstPerson = $persons->first();
                if ($firstPerson && $firstPerson->suspect_id) {
                    $suspectExistText = 'tersangka dengan identitas sebagai berikut:';
                } else {
                    $suspectExistText = 'saksi dengan identitas sebagai berikut:';
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
        $templateProcessor->setValue('suspectJob', $suspectJobVal); // Template uses suspectJob
        $templateProcessor->setValue('suspectReligion', $suspectReligionVal); // Template uses suspectReligion
        $templateProcessor->setValue('suspectAddressIteration', $suspectAddressVal); // Template uses suspectAddressIteration for address

        // 5. Narahubung / Penyidik (Investigator) - Diambil HANYA dari Ketua Tim Penyidik (class = 'LEADER')
        $investigatorDocOfficer = $document->officers->where('class', 'LEADER')->first();
        $investigatorOfficer = $investigatorDocOfficer ? $investigatorDocOfficer : null;
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
        // Hitung total item sitaan
        $totalSeizedCount = 0;
        if ($persons && $persons->isNotEmpty()) {
            foreach ($persons as $person) {
                if ($person->seizedItems) {
                    $totalSeizedCount += $person->seizedItems->count();
                }
            }
        }
        $seizedCount = $totalSeizedCount;
        $appendixText = '##APPENDIX_PLACEHOLDER##';

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

        $downloadFilename = $document->id . ' - Surat Laporan Persetujuan Penyitaan - ' . ($safePolresName ?: 'Dokumen') . '.docx';
        $savePath = public_path('generate/' . $downloadFilename);

        $templateProcessor->saveAs($savePath);

        // Post-processing: Format Tembusan borders & docProps page count
        if (file_exists($savePath)) {
            $zip = new \ZipArchive();
            if ($zip->open($savePath) === true) {
                $docXml = $zip->getFromName('word/document.xml');
                if ($docXml) {
                    // Remove any residual KETUA before courtName
                    $docXml = preg_replace('/<w:r[^>]*>(?:(?!<w:r[ >]).)*?KETUA\s*<\/w:t><\/w:r>\s*(<w:r[^>]*>(?:(?!<w:r[ >]).)*?KEPALA)/s', '$1', $docXml);

                    // Ensure all w14:paraId and w14:textId are unique to prevent MS Word 'Locked by another user' error
                    $docXml = preg_replace_callback('/w14:paraId="([A-F0-9]+)"/i', function ($matches) {
                        return 'w14:paraId="' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8)) . '"';
                    }, $docXml);
                    $docXml = preg_replace_callback('/w14:textId="([A-F0-9]+)"/i', function ($matches) {
                        return 'w14:textId="' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8)) . '"';
                    }, $docXml);

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
                            $modifiedTr = preg_replace_callback('/(<w:tcBorders>.*?)(<\/w:tcBorders>)/s', function($matches) {
                                $inner = preg_replace('/<w:bottom[^>]*\/>/s', '', $matches[1]);
                                if (trim($inner) === '<w:tcBorders>') return '';
                                return $inner . $matches[2];
                            }, $trs[0][$idx]);
                            $tblXml = str_replace($trs[0][$idx], $modifiedTr, $tblXml);
                        }

                        $docXml = str_replace($tblMatch[0], $tblXml, $docXml);
                    }

                    // Dynamically calculate actual page count from the XML structure
                    $pageCount = substr_count($docXml, '<w:lastRenderedPageBreak/>') + 1;
                    $terbilangWords = [
                        1 => 'satu', 2 => 'dua', 3 => 'tiga', 4 => 'empat', 5 => 'lima',
                        6 => 'enam', 7 => 'tujuh', 8 => 'delapan', 9 => 'sembilan', 10 => 'sepuluh',
                    ];
                    $word = $terbilangWords[$pageCount] ?? (string)$pageCount;
                    $appendixText = $pageCount . ' (' . $word . ') lembar';
                    $docXml = str_replace('##APPENDIX_PLACEHOLDER##', $appendixText, $docXml);

                    $zip->addFromString('word/document.xml', $docXml);
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
        if ($documentId) {
            $hasExistingAttachments = SuratLaporanPersetujuanPenyitaanDocumentAttachment::where('surat_laporan_persetujuan_penyitaan_document_id', $documentId)->exists();
        }

        $effectiveAccidentId = $fixedAccidentId ?? $request->input('accident_id');

        $hasValidAdditionalLaw = false;
        if ($request->has('lawAdditionalNames') && is_array($request->lawAdditionalNames)) {
            foreach ($request->lawAdditionalNames as $name) {
                if (!empty(trim($name))) {
                    $hasValidAdditionalLaw = true;
                    break;
                }
            }
        }

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
            'surat_perintah_penyitaan_number' => 'nullable|string|max:255',
            'surat_perintah_penyitaan_date' => 'nullable|date',
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

            // Pejabat penandatangan wajib min 1 dan authorized untuk kesatuan polres
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

            // Daftar UU / Pasal (Sprindik lawCrimeTypeIds atau legacy laws)
            'lawCrimeTypeIds' => [
                Rule::requiredIf(function () use ($request, $hasValidAdditionalLaw) {
                    if ($hasValidAdditionalLaw) return false;
                    return $request->has('lawCrimeTypeIds') || (!$request->has('laws') && !$request->has('lawCrimeTypeIds'));
                }),
                'nullable',
                'array',
                'min:1',
            ],
            'lawCrimeTypeIds.*' => 'required',
            'laws' => [
                Rule::requiredIf(function () use ($request, $hasValidAdditionalLaw) {
                    if ($hasValidAdditionalLaw) return false;
                    return $request->has('laws') && !$request->has('lawCrimeTypeIds');
                }),
                'nullable',
                'array',
                'min:1',
                function ($attribute, $value, $fail) use ($request, $hasValidAdditionalLaw) {
                    if (!$request->has('laws') || $request->has('lawCrimeTypeIds') || $hasValidAdditionalLaw) {
                        return;
                    }
                    if (!is_array($value) || empty($value)) {
                        $fail('Daftar UU / Pasal yang dipersangkakan wajib diisi minimal 1.');
                        return;
                    }

                    $validCount = 0;
                    foreach ($value as $law) {
                        if (!is_array($law)) {
                            continue;
                        }
                        $crimeId = $law['crime_constitution_id'] ?? null;
                        $chapter = trim($law['constitution_chapter'] ?? ($law['pasal'] ?? ''));
                        $const = trim($law['constitution'] ?? '');

                        if (!empty($crimeId) || !empty($chapter) || !empty($const)) {
                            $validCount++;
                        }
                    }

                    if ($validCount === 0) {
                        $fail('Daftar UU / Pasal yang dipersangkakan wajib diisi minimal 1 baris pasal/UU yang valid.');
                    }
                },
            ],
            'laws.*.crime_constitution_id' => [
                'nullable',
                'string',
                Rule::exists(CrimeConstitution::class, 'id'),
            ],
            'laws.*.constitution_chapter' => 'nullable|string|max:255',
            'laws.*.constitution' => 'nullable|string|max:255',
            'laws.*.description' => 'nullable|string',
            'laws.*.flag' => 'nullable|in:MAIN,ADDITIONAL',

            // Daftar Orang (Tersangka/Saksi/Terlapor) optional
            'persons' => 'required|array|min:1',
            'persons.*.person_id' => 'required|uuid',
            'persons.*.person_type' => 'required|string|in:suspect,witness,reported_person',
            'persons.*.bap_number' => 'required|string|max:255',
            'persons.*.bap_date' => 'required|date',
            
            // Daftar Barang Sitaan per orang
            'persons.*.seized_items' => 'required|array|min:1',
            'persons.*.seized_items.*.name' => 'required|string|max:255',
            'persons.*.seized_items.*.quantity' => 'required|numeric|min:0.01',
            'persons.*.seized_items.*.unit' => 'required|string|max:50',
            'persons.*.seized_items.*.description' => 'nullable|string|max:1000',

            // Dokumen digital tidak lagi diwajibkan / diproses di sini
        ];

        $messages = [
            'accident_id.required' => 'Id Kecelakaan wajib diisi.',
            'accident_id.exists' => 'Data Kecelakaan tidak ditemukan.',
            'document_number.required' => 'Nomor Dokumen S-13 wajib diisi.',
            'document_number.max' => 'Nomor Dokumen S-13 maksimal 255 karakter.',
            'document_date.required' => 'Tanggal Dokumen S-13 wajib diisi.',
            'document_date.date' => 'Format Tanggal Dokumen S-13 tidak valid.',



            'sprindik_number.required' => 'No SP Penyidikan wajib diisi.',
            'sprindik_number.max' => 'No SP Penyidikan maksimal 255 karakter.',
            'sprindik_date.required' => 'Tanggal SP Penyidikan wajib diisi.',
            'sprindik_date.date' => 'Format Tanggal Sprindik tidak valid.',

            'surat_perintah_penyidikan_document_id.exists' => 'Dokumen Sprindik yang dipilih tidak valid.',
            'court_id.exists' => 'Pengadilan Negeri yang dipilih tidak valid.',
            'documentClassification.required' => 'Klasifikasi dokumen wajib dipilih.',
            'documentClassification.required_without_all' => 'Klasifikasi dokumen wajib dipilih.',
            'carbonCopies.required' => 'Tembusan wajib diisi minimal 1.',
            'carbonCopies.min' => 'Tembusan wajib diisi minimal 1.',
            'carbonCopies.*.required' => 'Mohon Jangan Kosongkan Isi Tembusan, Hapus Jika Memang Tidak Ada.',
            'persons.required' => 'Minimal harus ada 1 orang saksi/tersangka yang barangnya disita.',
            'persons.min' => 'Minimal harus ada 1 orang saksi/tersangka yang barangnya disita.',

            'officerLeader.required' => 'Ketua Tim Penyidik wajib dipilih.',
            'officerLeader.exists' => 'Ketua Tim Penyidik yang dipilih tidak valid.',

            'officers.required' => 'Pejabat Penandatangan wajib dipilih.',
            'officers.exists' => 'Pejabat Penandatangan yang dipilih tidak valid.',

            'lawCrimeTypeIds.required' => 'Wajib memilih minimal 1 UU / Pasal.',
            'lawCrimeTypeIds.min' => 'Wajib memilih minimal 1 UU / Pasal.',
            'lawCrimeTypeIds.*.required' => 'Jenis kejahatan pada daftar pasal wajib dipilih.',

            'laws.required' => 'Daftar UU / Pasal yang dipersangkakan wajib diisi minimal 1.',
            'laws.min' => 'Daftar UU / Pasal yang dipersangkakan wajib diisi minimal 1.',
            'laws.*.crime_constitution_id.exists' => 'Dasar UU yang dipilih tidak valid.',

            'persons.*.person_id.required' => 'Identitas orang wajib dipilih.',
            'persons.*.person_type.required' => 'Tipe identitas wajib diisi.',
            'persons.*.bap_number.required' => 'No. Berita Acara Penyitaan wajib diisi.',
            'persons.*.bap_date.required' => 'Tgl Berita Acara Penyitaan wajib diisi.',
            'persons.*.seized_items.required' => 'Minimal harus ada 1 barang sitaan.',
            'persons.*.seized_items.min' => 'Minimal harus ada 1 barang sitaan.',
            'persons.*.seized_items.*.name.required' => 'Nama barang sitaan wajib diisi.',
            'persons.*.seized_items.*.quantity.required' => 'Jumlah barang sitaan wajib diisi.',
            'persons.*.seized_items.*.quantity.min' => 'Jumlah barang sitaan harus lebih besar dari 0.',
            'persons.*.seized_items.*.unit.required' => 'Satuan barang sitaan wajib diisi.',
            'court_id.required' => 'Pengadilan Negeri Tujuan wajib dipilih.',
        ];

        return Validator::make($request->all(), $rules, $messages);
    }
}
