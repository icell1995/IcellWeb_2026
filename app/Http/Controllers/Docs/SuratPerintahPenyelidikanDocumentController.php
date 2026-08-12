<?php

namespace App\Http\Controllers\Docs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Barryvdh\DomPDF\Facade as PDF;

use App\Services\Doc\DocService;

use App\Models\Doc\SuratPerintahPenyelidikanDocument\SuratPerintahPenyelidikanDocument;
use App\Models\Doc\SuratPerintahPenyelidikanDocument\SuratPerintahPenyelidikanDocumentOfficer;

use App\Models\Officer;
use App\Models\Accident;
use App\Models\Lib\Rank;
use App\Models\Lib\CaseKeyword;
use App\Models\Lib\Position;
use App\Models\Lib\CaseClassification;
use App\Models\Lib\Police;
use App\Models\Polda;
use App\Models\Polres;

use App\Traits\DocsOfficersTraits;

class SuratPerintahPenyelidikanDocumentController extends Controller
{
    protected $docService;

    public function __construct(DocService $docService)
    {
        $this->docService = $docService;
    }

    use DocsOfficersTraits;

    public function create()
    {
        // Get URL Parameter
        $accidentId = htmlspecialchars(request()->query('accident_id'));

        $accident = Accident::where('id', $accidentId)->first();

        $ranks = Rank::where('is_active', true)->wherePolri()->orderBy('sort')->get();

        $positions = Position::where('is_active', true)->orderBy('sort')->get();

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

        $caseKeywords = CaseKeyword::where('is_active', true)->orderBy('id')->get();

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

        $suratPerintahPenyelidikanDocuments = SuratPerintahPenyelidikanDocument::where('accident_id', $accidentId)
            ->get();

        $viewData = [
            'authorizedSignatories' => $authorizedSignatories,
            'leaderOfficers' => $leaderOfficers,
            'accidentId' => $accidentId,
            'accident' => $accident,
            'ranks' => $ranks,
            'positions' => $positions,
            'resortPoliceId' => $accident->polres_id,
            'caseKeywords' => $caseKeywords,
            'suratPerintahPenyelidikanDocuments' => $suratPerintahPenyelidikanDocuments,
        ];

        return view('docs.surat-perintah-penyelidikan-document.create', $viewData);
    }

    public function store(Request $request)
    {
        // Validation
        $validator = $this->validateForm($request);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Get URL Parameter
        $accidentId = htmlspecialchars($request->accident_id);

        // Define & Sanitize Text Input
        $user = Auth::user();
        $documentNumber = htmlspecialchars($request->documentNumber);
        $documentDate = Carbon::parse(htmlspecialchars($request->documentDate))->format('Y-m-d');
        $startDate = Carbon::parse(htmlspecialchars($request->startDate))->format('Y-m-d');
        $endDate = ($request->isFinished == 'true') ? null : Carbon::parse(htmlspecialchars($request->endDate))->format('Y-m-d');
        $signatoryId = htmlspecialchars($request->signatory);
        $officerLeader = htmlspecialchars($request->officerLeader);
        $caseClassification = htmlspecialchars($request->caseClassification);
        $isRenewalDocument = ($request->isRenewalDocument == 'true') ? true : false;
        $isExternalOfficers = ($request->isExternalOfficers == 'true') ? true : false;
        $isMovedOfficers = ($request->isMovedOfficers == 'true') ? true : false;

        $renewalReferenceDocumentId = ($request->isRenewalDocument == 'true') ? htmlspecialchars($request->referenceDocument) : null;
        $renewalReferenceDocument = ($request->isRenewalDocument == 'true') ? SuratPerintahPenyelidikanDocument::where('id', $renewalReferenceDocumentId)->first() : null;
        $renewalReferenceDocumentNumber = ($request->isRenewalDocument == 'true') ? $renewalReferenceDocument->document_number : null;

        $isLegacy = filter_var($request->isLegacy, FILTER_VALIDATE_BOOLEAN);

        // Check if document number already exist
        $exists = SuratPerintahPenyelidikanDocument::where('accident_id', $accidentId)
            ->where('document_number', 'ILIKE', $documentNumber)
            ->exists();
        if ($exists) {
            return redirect()->back()->with('error', 'Dokumen ' . $documentNumber . ' Sudah Anda Buat Sebelumnya.');
        }

        DB::beginTransaction();
        try {
            // Store to database
            $suratPerintahPenyelidikanDocument = SuratPerintahPenyelidikanDocument::create([
                'accident_id' => $accidentId,
                'document_number' => $documentNumber,
                'document_date' => $documentDate,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'is_renewal' => $isRenewalDocument,
                'renewal_reference_document_id' => $renewalReferenceDocumentId,
                'renewal_reference_document_number' => $renewalReferenceDocumentNumber,
                'case_classification' => $caseClassification,
                'is_legacy' => $isLegacy,
            ]);

            $suratPerintahPenyelidikanDocumentId = $suratPerintahPenyelidikanDocument->id;


            // SIGNATORY
            $signatory = Officer::where('id', $signatoryId)->first();
            $resortPolice = Polres::with(['polda'])->where('id', $signatory->polres_id)->first();
            $suratPerintahPenyelidikanDocument->suratPerintahPenyelidikanDocumentOfficers()->create([
                'surat_perintah_penyelidikan_document_id' => $suratPerintahPenyelidikanDocumentId,
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
                'status' => SuratPerintahPenyelidikanDocumentOfficer::getEnumOption('status', 'PRESENT'),
                'class' => SuratPerintahPenyelidikanDocumentOfficer::getEnumOption('class', 'SIGNATORY'),
                'flag' => SuratPerintahPenyelidikanDocumentOfficer::getEnumOption('flag', 'INTERNAL'),
            ]);


            // LEADER
            $officerLeader = Officer::where('id', $officerLeader)->first();
            $resortPolice = Polres::where('id', $officerLeader->polres_id)->first();
            $regionalPolice = Polda::where('id', $officerLeader->polda_id)->first();
            $suratPerintahPenyelidikanDocument->suratPerintahPenyelidikanDocumentOfficers()->create([
                'surat_perintah_penyelidikan_document_id' => $suratPerintahPenyelidikanDocumentId,
                'register_number' => $officerLeader->register_number,

                'first_title' => $officerLeader->first_title,
                'first_name' => $officerLeader->first_name,
                'last_name' => $officerLeader->last_name,
                'last_title' => $officerLeader->last_title,

                'rank_id' => $officerLeader->rank_id,
                'position_id' => $officerLeader->position_id,
                'phone_number' => $officerLeader->phone_number,
                'email' => $officerLeader->email,

                'police_id' => $officerLeader->police_id,
                'status' => SuratPerintahPenyelidikanDocumentOfficer::getEnumOption('status', 'PRESENT'),
                'class' => SuratPerintahPenyelidikanDocumentOfficer::getEnumOption('class', 'LEADER'),
                'flag' => SuratPerintahPenyelidikanDocumentOfficer::getEnumOption('flag', 'INTERNAL'),
            ]);


            //Internal Officers
            $internalOfficers = $request->internalOfficers ?? [];
            foreach ($internalOfficers as $internalOfficer) {
                $registerNumber = $internalOfficer;
                $officer = Officer::where('register_number', $registerNumber)->first();
                if ($officer) {
                    $resortPolice = Polres::where('id', $officer->polres_id)->first();
                    $regionalPolice = Polda::where('id', $officer->polda_id)->first();

                    $suratPerintahPenyelidikanDocument->suratPerintahPenyelidikanDocumentOfficers()->create([
                        'surat_perintah_penyelidikan_document_id' => $suratPerintahPenyelidikanDocumentId,
                        'register_number' => $officer->register_number,

                        'first_title' => $officer->first_title,
                        'first_name' => $officer->first_name,
                        'last_name' => $officer->last_name,
                        'last_title' => $officer->last_title,

                        'rank_id' => $officer->rank_id,
                        'position_id' => $officer->position_id,
                        'phone_number' => $officer->phone_number,
                        'email' => $officer->email,

                        'police_id' => $officer->police_id,
                        'status' => SuratPerintahPenyelidikanDocumentOfficer::getEnumOption('status', 'PRESENT'),
                        'class' => SuratPerintahPenyelidikanDocumentOfficer::getEnumOption('class', 'MEMBER'),
                        'flag' => SuratPerintahPenyelidikanDocumentOfficer::getEnumOption('flag', 'INTERNAL'),
                    ]);
                }
            }


            //External Officers
            if ($isExternalOfficers == true) {
                $externalOfficers = $request->externalOfficers ?? [];
                foreach ($externalOfficers as $externalOfficer) {
                    $registerNumber = $externalOfficer;
                    $officer = Officer::where('register_number', $registerNumber)->first();
                    if ($officer) {
                        $resortPolice = Polres::where('id', $officer->polres_id)->first();
                        $regionalPolice = Polda::where('id', $officer->polda_id)->first();
                        $suratPerintahPenyelidikanDocument->suratPerintahPenyelidikanDocumentOfficers()->create([
                            'surat_perintah_penyelidikan_document_id' => $suratPerintahPenyelidikanDocumentId,
                            'register_number' => $officer->register_number,

                            'first_title' => $officer->first_title,
                            'first_name' => $officer->first_name,
                            'last_name' => $officer->last_name,
                            'last_title' => $officer->last_title,

                            'rank_id' => $officer->rank_id,
                            'position_id' => $officer->position_id,
                            'phone_number' => $officer->phone_number,
                            'email' => $officer->email,

                            'police_id' => $officer->police_id,
                            'status' => SuratPerintahPenyelidikanDocumentOfficer::getEnumOption('status', 'EXTERNAL'),
                            'class' => SuratPerintahPenyelidikanDocumentOfficer::getEnumOption('class', 'MEMBER'),
                            'flag' => SuratPerintahPenyelidikanDocumentOfficer::getEnumOption('flag', 'EXTERNAL'),
                        ]);
                    }
                }
            }


            //Moved Officers
            if ($isMovedOfficers == true) {
                $movedOfficers = $request->movedOfficers ?? [];
                foreach ($movedOfficers as $movedOfficer) {
                    $registerNumber = $movedOfficer;
                    $officer = Officer::where('register_number', $registerNumber)->first();
                    if ($officer) {
                        $resortPolice = Polres::where('id', $officer->polres_id)->first();
                        $regionalPolice = Polda::where('id', $officer->polda_id)->first();
                        $suratPerintahPenyelidikanDocument->suratPerintahPenyelidikanDocumentOfficers()->create([
                            'surat_perintah_penyelidikan_document_id' => $suratPerintahPenyelidikanDocumentId,
                            'register_number' => $officer->register_number,

                            'first_title' => $officer->first_title,
                            'first_name' => $officer->first_name,
                            'last_name' => $officer->last_name,
                            'last_title' => $officer->last_title,

                            'rank_id' => $officer->rank_id,
                            'position_id' =>  $officer->position_id,
                            'phone_number' => $officer->phone_number,
                            'email' => $officer->email,

                            'police_id' => $officer->police_id,
                            'status' => SuratPerintahPenyelidikanDocumentOfficer::getEnumOption('status', 'PRESENT'),
                            'class' => SuratPerintahPenyelidikanDocumentOfficer::getEnumOption('class', 'MEMBER'),
                            'flag' => SuratPerintahPenyelidikanDocumentOfficer::getEnumOption('flag', 'MOVED'),
                        ]);
                    }
                }

                $manualMovedOfficerRegisterNumbers = $request->manualMovedOfficerRegisterNumbers ?? [];
                $manualMovedOfficerFirstNames = $request->manualMovedOfficerFirstNames ?? [];
                $manualMovedOfficerLastNames = $request->manualMovedOfficerLastNames ?? [];
                $manualMovedOfficerRankIds = $request->manualMovedOfficerRankIds ?? [];
                $manualMovedOfficerPositionIds = $request->manualMovedOfficerPositionIds ?? [];
                $manualMovedOfficerPositionNames = $request->manualMovedOfficerPositionNames ?? [];
                $manualMovedOfficerPhones = $request->manualMovedOfficerPhones ?? [];
                $manualMovedOfficerRegionalPoliceIds = $request->manualMovedOfficerRegionalPoliceIds ?? [];
                $manualMovedOfficerResortPoliceIds = $request->manualMovedOfficerResortPoliceIds ?? [];

                $manualMovedOfficerRegisterNumbersCollection = Collection::make($manualMovedOfficerRegisterNumbers);
                $manualMovedOfficerFirstNamesCollection = Collection::make($manualMovedOfficerFirstNames);
                $manualMovedOfficerLastNamesCollection = Collection::make($manualMovedOfficerLastNames);
                $manualMovedOfficerRankIdsCollection = Collection::make($manualMovedOfficerRankIds);
                $manualMovedOfficerPositionIdsCollection = Collection::make($manualMovedOfficerPositionIds);
                $manualMovedOfficerPositionNamesCollection = Collection::make($manualMovedOfficerPositionNames);
                $manualMovedOfficerPhonesCollection = Collection::make($manualMovedOfficerPhones);
                $manualMovedOfficerRegionalPoliceIdsCollection = Collection::make($manualMovedOfficerRegionalPoliceIds);
                $manualMovedOfficerResortPoliceIdsCollection = Collection::make($manualMovedOfficerResortPoliceIds);

                $manualMovedOfficers = $manualMovedOfficerRegisterNumbersCollection->zip(
                    $manualMovedOfficerFirstNamesCollection,
                    $manualMovedOfficerLastNamesCollection,
                    $manualMovedOfficerRankIdsCollection,
                    $manualMovedOfficerPositionIdsCollection,
                    $manualMovedOfficerPositionNamesCollection,
                    $manualMovedOfficerPhonesCollection,
                    $manualMovedOfficerRegionalPoliceIdsCollection,
                    $manualMovedOfficerResortPoliceIdsCollection
                )->map(function ($item) {
                    return [
                        'register_number' => $item[0],
                        'first_name' => $item[1],
                        'last_name' => $item[2],
                        'rank_id' => $item[3],
                        'position_id' => $item[4],
                        'position_name' => $item[5],
                        'phone' => $item[6],
                        'regional_police_id' => $item[7],
                        'resort_police_id' => $item[8],
                    ];
                })->all();
                foreach ($manualMovedOfficers as $manualMovedOfficer) {
                    $resortPolice = Polres::where('id', $manualMovedOfficer['resort_police_id'])->first();
                    $regionalPolice = Polda::where('id', $manualMovedOfficer['regional_police_id'])->first();

                    $rank = Rank::where('id', ($manualMovedOfficer['rank_id'] != NULL && $manualMovedOfficer['rank_id'] != 'null' && $manualMovedOfficer['rank_id'] != '-') ? $manualMovedOfficer['rank_id'] : NULL)->first();

                    $suratPerintahPenyelidikanDocument->suratPerintahPenyelidikanDocumentOfficers()->create([
                        'surat_perintah_penyelidikan_document_id' => $suratPerintahPenyelidikanDocumentId,
                        'register_number' => $manualMovedOfficer['register_number'],

                        'first_title' => null,
                        'first_name' => $manualMovedOfficer['first_name'],
                        'last_name' => $manualMovedOfficer['last_name'],
                        'last_title' => null,

                        'rank_id' => $rank->id,
                        'position_id' => $manualMovedOfficer['position_id'],
                        'phone_number' => $manualMovedOfficer['phone'],
                        'email' => null,

                        'police_id' => ($manualMovedOfficer['resort_police_id']) ? $manualMovedOfficer['resort_police_id'] : $manualMovedOfficer['regional_police_id'],
                        'status' => SuratPerintahPenyelidikanDocumentOfficer::getEnumOption('status', 'PAST'),
                        'class' => SuratPerintahPenyelidikanDocumentOfficer::getEnumOption('class', 'MEMBER'),
                        'flag' => SuratPerintahPenyelidikanDocumentOfficer::getEnumOption('flag', 'MOVED'),
                        'insert_method' => SuratPerintahPenyelidikanDocumentOfficer::getEnumOption('insert_method', 'MANUAL'),
                    ]);
                }
            }

            $caseKeywords = $request->keywords ?? [];
            foreach ($caseKeywords as $caseKeyword) {
                $caseKeyword = CaseKeyword::where('id', $caseKeyword)->first();

                $suratPerintahPenyelidikanDocument->suratPerintahPenyelidikanDocumentCaseKeywords()->create([
                    'surat_perintah_penyelidikan_document_id' => $suratPerintahPenyelidikanDocumentId,
                    'keyword_id' => $caseKeyword->id,
                    'keyword' => $caseKeyword->name,
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan pada saat menyimpan data.');
        }

        // Redirect
        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId]);
    }

    public function show($id)
    {
        // Get URL Parameter
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $suratPerintahPenyelidikanDocumentId = $id;

        $suratPerintahPenyelidikanDocument = SuratPerintahPenyelidikanDocument::with(['suratPerintahPenyelidikanDocumentOfficers', 'suratPerintahPenyelidikanDocumentCaseKeywords'])->where('id', $suratPerintahPenyelidikanDocumentId)->first();
        $accident = Accident::with(['polres', 'polres.polda'])->where('id', $accidentId)->first();

        $viewData = [
            'accidentId' => $accidentId,
            'accident' => $accident,
            'suratPerintahPenyelidikanDocument' => $suratPerintahPenyelidikanDocument,
            'suratPerintahPenyelidikanDocumentId' => $suratPerintahPenyelidikanDocumentId,
        ];

        return view('docs.surat-perintah-penyelidikan-document.show', $viewData);
    }

    public function edit($id)
    {
        // Get URL Parameter
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $suratPerintahPenyelidikanDocumentId = $id;

        $suratPerintahPenyelidikanDocument = SuratPerintahPenyelidikanDocument::with(['suratPerintahPenyelidikanDocumentOfficers', 'suratPerintahPenyelidikanDocumentCaseKeywords'])->where('id', $suratPerintahPenyelidikanDocumentId)->first();
        $accident = Accident::where('id', $accidentId)->first();

        $ranks = Rank::where('is_active', true)->wherePolri()->orderBy('sort')->get();

        $positions = Position::where('is_active', true)->orderBy('sort')->get();

        $authorizedSignatories = Officer::withRelated()
            ->selectFullName()
            ->where('police_id', $accident->polres_id)
            ->whereHasUserActive()
            ->hasDataComplete()
            ->signatory()
            ->active()
            ->valid()
            ->orderBy('first_name')
            ->get();

        $caseKeywords = CaseKeyword::where('is_active', true)->orderBy('id')->get();

        $leaderOfficers = Officer::withRelated()
            ->selectFullName()
            ->where('police_id', $accident->polres_id)
            ->whereHasUserActive()
            ->hasDataComplete()
            ->member()
            ->active()
            ->valid()
            ->orderBy('first_name')
            ->get();

        $suratPerintahPenyelidikanDocuments = SuratPerintahPenyelidikanDocument::where('accident_id', $accidentId)->get();
        $viewData = [
            'authorizedSignatories' => $authorizedSignatories,
            'accidentId' => $accidentId,
            'accident' => $accident,
            'suratPerintahPenyelidikanDocuments' => $suratPerintahPenyelidikanDocuments,
            'suratPerintahPenyelidikanDocument' => $suratPerintahPenyelidikanDocument,
            'suratPerintahPenyelidikanDocumentId' => $suratPerintahPenyelidikanDocumentId,
            'ranks' => $ranks,
            'positions' => $positions,
            'caseKeywords' => $caseKeywords,
            'leaderOfficers' => $leaderOfficers,
            'officers' => $suratPerintahPenyelidikanDocument
                ->suratPerintahPenyelidikanDocumentOfficers()
                ->withRelated()
                ->get(),
        ];

        return view('docs.surat-perintah-penyelidikan-document.edit', $viewData);
    }

    public function update(Request $request, $id)
    {
        // Validation
        $validator = $this->validateForm($request);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Get URL Parameter
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $suratPerintahPenyelidikanDocumentId = $id;

        // Define & Sanitize Text Input
        $user = Auth::user();
        $documentNumber = htmlspecialchars($request->documentNumber);
        $documentDate = Carbon::parse(htmlspecialchars($request->documentDate))->format('Y-m-d');
        $startDate = Carbon::parse(htmlspecialchars($request->startDate))->format('Y-m-d');
        $endDate = ($request->isFinished == 'true') ? null : Carbon::parse(htmlspecialchars($request->endDate))->format('Y-m-d');
        $signatoryId = htmlspecialchars($request->signatory);
        $officerLeader = htmlspecialchars($request->officerLeader);
        $caseClassification = htmlspecialchars($request->caseClassification);
        $isRenewalDocument = ($request->isRenewalDocument == 'true') ? true : false;
        $isExternalOfficers = ($request->isExternalOfficers == 'true') ? true : false;
        $isMovedOfficers = ($request->isMovedOfficers == 'true') ? true : false;

        $renewalReferenceDocumentId = ($request->isRenewalDocument == 'true') ? htmlspecialchars($request->referenceDocument) : null;
        $renewalReferenceDocument = ($request->isRenewalDocument == 'true') ? SuratPerintahPenyelidikanDocument::where('id', $renewalReferenceDocumentId)->first() : null;
        $renewalReferenceDocumentNumber = ($request->isRenewalDocument == 'true') ? $renewalReferenceDocument->document_number : null;

        $isLegacy = filter_var($request->isLegacy, FILTER_VALIDATE_BOOLEAN);

        // Check if document number already exist
        $suratPerintahPenyelidikanDocument = SuratPerintahPenyelidikanDocument::findOrFail($id);
        $oldDocumentNumber = $suratPerintahPenyelidikanDocument->document_number;
        if (strtolower($oldDocumentNumber) != strtolower($documentNumber)) {
            $exists = SuratPerintahPenyelidikanDocument::where('accident_id', $request->input('accident_id'))
                ->where('document_number', 'ILIKE', $documentNumber)
                ->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'Dokumen ' . $documentNumber . ' Sudah Anda Buat Sebelumnya.');
            }
        }

        DB::beginTransaction();
        try {
            // Store to database
            $suratPerintahPenyelidikanDocument = SuratPerintahPenyelidikanDocument::where('id', $suratPerintahPenyelidikanDocumentId)->first();

            $suratPerintahPenyelidikanDocument->update([
                'accident_id' => $accidentId,
                'document_number' => $documentNumber,
                'document_date' => $documentDate,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'is_renewal' => $isRenewalDocument,
                'renewal_reference_document_id' => $renewalReferenceDocumentId,
                'renewal_reference_document_number' => $renewalReferenceDocumentNumber,
                'case_classification' => $caseClassification,
                'is_legacy' => $isLegacy,
            ]);

            $suratPerintahPenyelidikanDocumentId = $suratPerintahPenyelidikanDocument->id;

            // Signatory
            $signatory = Officer::where('id', $signatoryId)->first();
            $resortPolice = Polres::with(['polda'])->where('id', $signatory->polres_id)->first();
            $suratPerintahPenyelidikanDocument->suratPerintahPenyelidikanDocumentOfficers()
                ->updateOrCreate(
                    [
                        'class' => SuratPerintahPenyelidikanDocumentOfficer::getEnumOption('class', 'SIGNATORY'),
                    ],
                    [
                        'surat_perintah_penyelidikan_document_id' => $suratPerintahPenyelidikanDocumentId,
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
                        'status' => SuratPerintahPenyelidikanDocumentOfficer::getEnumOption('status', 'PRESENT'),
                        'flag' => SuratPerintahPenyelidikanDocumentOfficer::getEnumOption('flag', 'INTERNAL'),
                    ]
                );


            // Leader
            $officerLeader = Officer::where('id', $officerLeader)->first();
            $resortPolice = Polres::where('id', $officerLeader->polres_id)->first();
            $regionalPolice = Polda::where('id', $officerLeader->polda_id)->first();
            $suratPerintahPenyelidikanDocument->suratPerintahPenyelidikanDocumentOfficers()
                ->updateOrCreate(
                    [
                        'class' => SuratPerintahPenyelidikanDocumentOfficer::getEnumOption('class', 'LEADER'),
                    ],
                    [
                        'surat_perintah_penyelidikan_document_id' => $suratPerintahPenyelidikanDocumentId,
                        'register_number' => $officerLeader->register_number,

                        'first_title' => $officerLeader->first_title,
                        'first_name' => $officerLeader->first_name,
                        'last_name' => $officerLeader->last_name,
                        'last_title' => $officerLeader->last_title,

                        'rank_id' => $officerLeader->rank_id,
                        'position_id' => $officerLeader->position_id,
                        'phone_number' => $officerLeader->phone_number,
                        'email' => $officerLeader->email,

                        'police_id' => $officerLeader->police_id,
                        'status' => SuratPerintahPenyelidikanDocumentOfficer::getEnumOption('status', 'PRESENT'),
                        'flag' => SuratPerintahPenyelidikanDocumentOfficer::getEnumOption('flag', 'INTERNAL'),
                    ]
                );


            // Internal Officer
            $internalOfficers = $request->internalOfficers ?? [];
            foreach ($internalOfficers as $internalOfficer) {
                $registerNumber = $internalOfficer;
                $officer = Officer::where('register_number', $registerNumber)->first();

                if ($officer) {
                    $resortPolice = Polres::where('id', $officer->polres_id)->first();
                    $regionalPolice = Polda::where('id', $officer->polda_id)->first();
                    $suratPerintahPenyelidikanDocument->suratPerintahPenyelidikanDocumentOfficers()
                        ->updateOrcreate(
                            [
                                'register_number' => $officer->register_number,
                                'class' => SuratPerintahPenyelidikanDocumentOfficer::getEnumOption('class', 'MEMBER'),
                                'flag' => SuratPerintahPenyelidikanDocumentOfficer::getEnumOption('flag', 'INTERNAL'),
                            ],
                            [
                                'surat_perintah_penyelidikan_document_id' => $suratPerintahPenyelidikanDocumentId,
                                'register_number' => $officer->register_number,

                                'first_title' => $officer->first_title,
                                'first_name' => $officer->first_name,
                                'last_name' => $officer->last_name,
                                'last_title' => $officer->last_title,

                                'rank_id' => $officer->rank_id,
                                'position_id' => $officer->position_id,
                                'phone_number' => $officer->phone_number,
                                'email' => $officer->email,

                                'police_id' => $officer->police_id,
                                'status' => SuratPerintahPenyelidikanDocumentOfficer::getEnumOption('status', 'PRESENT'),
                                'class' => SuratPerintahPenyelidikanDocumentOfficer::getEnumOption('class', 'MEMBER'),
                                'flag' => SuratPerintahPenyelidikanDocumentOfficer::getEnumOption('flag', 'INTERNAL'),
                            ]
                        );
                }
            }
            // Hapus data officer yang tidak ada di $internalOfficers
            $suratPerintahPenyelidikanDocument->suratPerintahPenyelidikanDocumentOfficers()
                ->where('flag', SuratPerintahPenyelidikanDocumentOfficer::getEnumOption('flag', 'INTERNAL'))
                ->where('class', SuratPerintahPenyelidikanDocumentOfficer::getEnumOption('class', 'MEMBER'))
                ->whereNotIn('register_number', $internalOfficers)
                ->delete();


            // External Officer
            if ($isExternalOfficers == true) {
                $externalOfficers = $request->externalOfficers ?? [];
                foreach ($externalOfficers as $externalOfficer) {
                    $registerNumber = $externalOfficer;
                    $officer = Officer::where('register_number', $registerNumber)->first();

                    if ($officer) {
                        $resortPolice = Polres::where('id', $officer->polres_id)->first();
                        $regionalPolice = Polda::where('id', $officer->polda_id)->first();
                        $suratPerintahPenyelidikanDocument->suratPerintahPenyelidikanDocumentOfficers()
                            ->updateOrCreate(
                                [
                                    'register_number' => $officer->register_number,
                                    'class' => SuratPerintahPenyelidikanDocumentOfficer::getEnumOption('class', 'MEMBER'),
                                    'flag' => SuratPerintahPenyelidikanDocumentOfficer::getEnumOption('flag', 'EXTERNAL'),
                                ],
                                [
                                    'surat_perintah_penyelidikan_document_id' => $suratPerintahPenyelidikanDocumentId,
                                    'register_number' => $officer->register_number,

                                    'first_title' => $officer->first_title,
                                    'first_name' => $officer->first_name,
                                    'last_name' => $officer->last_name,
                                    'last_title' => $officer->last_title,

                                    'rank_id' => $officer->rank_id,
                                    'position_id' => $officer->position_id,
                                    'phone_number' => $officer->phone_number,
                                    'email' => $officer->email,

                                    'police_id' => $officer->police_id,
                                    'status' => SuratPerintahPenyelidikanDocumentOfficer::getEnumOption('status', 'EXTERNAL'),
                                    'class' => SuratPerintahPenyelidikanDocumentOfficer::getEnumOption('class', 'MEMBER'),
                                    'flag' => SuratPerintahPenyelidikanDocumentOfficer::getEnumOption('flag', 'EXTERNAL'),
                                ]
                            );
                    }
                }
                // Hapus data officer yang tidak ada di $externalOfficers
                $suratPerintahPenyelidikanDocument->suratPerintahPenyelidikanDocumentOfficers()
                    ->where('flag', SuratPerintahPenyelidikanDocumentOfficer::getEnumOption('flag', 'EXTERNAL'))
                    ->where('class', SuratPerintahPenyelidikanDocumentOfficer::getEnumOption('class', 'MEMBER'))
                    ->whereNotIn('register_number', $externalOfficers)
                    ->delete();
            } else {
                // Hapus data officer yang tidak ada di $externalOfficers
                $suratPerintahPenyelidikanDocument->suratPerintahPenyelidikanDocumentOfficers()
                    ->where('flag', SuratPerintahPenyelidikanDocumentOfficer::getEnumOption('flag', 'EXTERNAL'))
                    ->where('class', SuratPerintahPenyelidikanDocumentOfficer::getEnumOption('class', 'MEMBER'))
                    ->whereNotIn('register_number', [])
                    ->delete();
            }

            // Moved Officer
            if ($isMovedOfficers == true) {
                $movedOfficers = $request->movedOfficers ?? [];
                foreach ($movedOfficers as $movedOfficer) {
                    $registerNumber = $movedOfficer;
                    $officer = Officer::where('register_number', $registerNumber)->first();

                    if ($officer) {
                        $resortPolice = Polres::where('id', $officer->polres_id)->first();
                        $regionalPolice = Polda::where('id', $officer->polda_id)->first();
                        $suratPerintahPenyelidikanDocument->suratPerintahPenyelidikanDocumentOfficers()
                            ->updateOrCreate(
                                [
                                    'register_number' => $officer->register_number,
                                    'class' => SuratPerintahPenyelidikanDocumentOfficer::getEnumOption('class', 'MEMBER'),
                                    'flag' => SuratPerintahPenyelidikanDocumentOfficer::getEnumOption('flag', 'MOVED'),
                                ],
                                [
                                    'surat_perintah_penyelidikan_document_id' => $suratPerintahPenyelidikanDocumentId,
                                    'register_number' => $officer->register_number,

                                    'first_title' => $officer->first_title,
                                    'first_name' => $officer->first_name,
                                    'last_name' => $officer->last_name,
                                    'last_title' => $officer->last_title,

                                    'rank_id' => $officer->rank_id,
                                    'position_id' => $officer->position_id,
                                    'phone_number' => $officer->phone_number,
                                    'email' => $officer->email,

                                    'police_id' => $officer->police_id,
                                    'status' => SuratPerintahPenyelidikanDocumentOfficer::getEnumOption('status', 'PAST'),
                                    'class' => SuratPerintahPenyelidikanDocumentOfficer::getEnumOption('class', 'MEMBER'),
                                    'flag' => SuratPerintahPenyelidikanDocumentOfficer::getEnumOption('flag', 'MOVED'),
                                ]
                            );
                    }
                }

                // Moved Officer Manual
                $manualMovedOfficerRegisterNumbers = $request->manualMovedOfficerRegisterNumbers ?? [];
                $manualMovedOfficerFirstNames = $request->manualMovedOfficerFirstNames ?? [];
                $manualMovedOfficerLastNames = $request->manualMovedOfficerLastNames ?? [];
                $manualMovedOfficerRankIds = $request->manualMovedOfficerRankIds ?? [];
                $manualMovedOfficerRankNames = $request->manualMovedOfficerRankNames ?? [];
                $manualMovedOfficerPositionIds = $request->manualMovedOfficerPositionIds ?? [];
                $manualMovedOfficerPositionNames = $request->manualMovedOfficerPositionNames ?? [];
                $manualMovedOfficerPhones = $request->manualMovedOfficerPhones ?? [];
                $manualMovedOfficerRegionalPoliceIds = $request->manualMovedOfficerRegionalPoliceIds ?? [];
                $manualMovedOfficerResortPoliceIds = $request->manualMovedOfficerResortPoliceIds ?? [];

                $manualMovedOfficerRegisterNumbersCollection = Collection::make($manualMovedOfficerRegisterNumbers);
                $manualMovedOfficerFirstNamesCollection = Collection::make($manualMovedOfficerFirstNames);
                $manualMovedOfficerLastNamesCollection = Collection::make($manualMovedOfficerLastNames);
                $manualMovedOfficerRankIdsCollection = Collection::make($manualMovedOfficerRankIds);
                $manualMovedOfficerRankNamesCollection = Collection::make($manualMovedOfficerRankNames);
                $manualMovedOfficerPositionIdsCollection = Collection::make($manualMovedOfficerPositionIds);
                $manualMovedOfficerPositionNamesCollection = Collection::make($manualMovedOfficerPositionNames);
                $manualMovedOfficerPhonesCollection = Collection::make($manualMovedOfficerPhones);
                $manualMovedOfficerRegionalPoliceIdsCollection = Collection::make($manualMovedOfficerRegionalPoliceIds);
                $manualMovedOfficerResortPoliceIdsCollection = Collection::make($manualMovedOfficerResortPoliceIds);

                $manualMovedOfficers = $manualMovedOfficerRegisterNumbersCollection->zip(
                    $manualMovedOfficerFirstNamesCollection,
                    $manualMovedOfficerLastNamesCollection,
                    $manualMovedOfficerRankIdsCollection,
                    $manualMovedOfficerRankNamesCollection,
                    $manualMovedOfficerPositionIdsCollection,
                    $manualMovedOfficerPositionNamesCollection,
                    $manualMovedOfficerPhonesCollection,
                    $manualMovedOfficerRegionalPoliceIdsCollection,
                    $manualMovedOfficerResortPoliceIdsCollection
                )->map(function ($item) {
                    return [
                        'register_number' => $item[0],
                        'first_name' => $item[1],
                        'last_name' => $item[2],
                        'rank_id' => $item[3],
                        'rank_name' => $item[4],
                        'position_id' => $item[5],
                        'position_name' => $item[6],
                        'phone' => $item[7],
                        'regional_police_id' => $item[8],
                        'resort_police_id' => $item[9],
                    ];
                })->all();
                foreach ($manualMovedOfficers as $manualMovedOfficer) {

                    $resortPolice = Polres::where('id', $manualMovedOfficer['resort_police_id'])->first();
                    $regionalPolice = Polda::where('id', $manualMovedOfficer['regional_police_id'])->first();

                    $rank = Rank::where('id', ($manualMovedOfficer['rank_id'] != NULL && $manualMovedOfficer['rank_id'] != 'null' && $manualMovedOfficer['rank_id'] != '-') ? $manualMovedOfficer['rank_id'] : NULL)->first();

                    $suratPerintahPenyelidikanDocument->suratPerintahPenyelidikanDocumentOfficers()
                        ->updateOrCreate(
                            [
                                'register_number' => $manualMovedOfficer['register_number'],
                                'class' => SuratPerintahPenyelidikanDocumentOfficer::getEnumOption('class', 'MEMBER'),
                                'flag' => SuratPerintahPenyelidikanDocumentOfficer::getEnumOption('flag', 'MOVED'),
                            ],
                            [
                                'surat_perintah_penyelidikan_document_id' => $suratPerintahPenyelidikanDocumentId,
                                'register_number' => $manualMovedOfficer['register_number'],

                                'first_title' => null,
                                'first_name' => $manualMovedOfficer['first_name'],
                                'last_name' => $manualMovedOfficer['last_name'],
                                'last_title' => null,

                                'rank_id' => $rank->id,
                                'position_id' => $manualMovedOfficer['position_id'],
                                'phone_number' => $manualMovedOfficer['phone'],
                                'email' => null,

                                'police_id' => ($manualMovedOfficer['resort_police_id']) ? $manualMovedOfficer['resort_police_id'] : $manualMovedOfficer['regional_police_id'],
                                'status' => SuratPerintahPenyelidikanDocumentOfficer::getEnumOption('status', 'PAST'),
                                'class' => SuratPerintahPenyelidikanDocumentOfficer::getEnumOption('class', 'MEMBER'),
                                'flag' => SuratPerintahPenyelidikanDocumentOfficer::getEnumOption('flag', 'MOVED'),
                                'insert_method' => SuratPerintahPenyelidikanDocumentOfficer::getEnumOption('insert_method', 'MANUAL'),
                            ]
                        );
                }

                $movedOfficersCollection = Collection::make($movedOfficers);
                $manualMovedOfficerRegisterNumbersCollection = $manualMovedOfficerRegisterNumbersCollection;
                $movedOfficerRegisterNumberMergeCollection = $movedOfficersCollection->concat($manualMovedOfficerRegisterNumbersCollection);

                // Delete Moved Officer Yang tidak ada di list
                $suratPerintahPenyelidikanDocument->suratPerintahPenyelidikanDocumentOfficers()
                    ->where('flag', SuratPerintahPenyelidikanDocumentOfficer::getEnumOption('flag', 'MOVED'))
                    ->where('class', SuratPerintahPenyelidikanDocumentOfficer::getEnumOption('class', 'MEMBER'))
                    ->whereNotIn('register_number', $movedOfficerRegisterNumberMergeCollection->toArray())
                    ->delete();
            } else {
                $suratPerintahPenyelidikanDocument->suratPerintahPenyelidikanDocumentOfficers()
                    ->where('flag', SuratPerintahPenyelidikanDocumentOfficer::getEnumOption('flag', 'MOVED'))
                    ->where('class', SuratPerintahPenyelidikanDocumentOfficer::getEnumOption('class', 'MEMBER'))
                    ->whereNotIn('register_number', [])
                    ->delete();
            }


            // Case Keywords
            $caseKeywords = $request->keywords ?? [];
            foreach ($caseKeywords as $caseKeyword) {
                $caseKeyword = CaseKeyword::where('id', $caseKeyword)->first();

                $suratPerintahPenyelidikanDocument->suratPerintahPenyelidikanDocumentCaseKeywords()
                    ->updateOrCreate(
                        [
                            'surat_perintah_penyelidikan_document_id' => $suratPerintahPenyelidikanDocumentId,
                            'keyword_id' => $caseKeyword->id,
                        ],
                        [
                            'surat_perintah_penyelidikan_document_id' => $suratPerintahPenyelidikanDocumentId,
                            'keyword_id' => $caseKeyword->id,
                            'keyword' => $caseKeyword->name,
                        ]
                    );
            }
            $suratPerintahPenyelidikanDocument->suratPerintahPenyelidikanDocumentCaseKeywords()
                ->whereNotIn('keyword_id', $caseKeywords)
                ->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data');
        }

        // Redirect with param accident_id
        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId]);
    }

    public function delete($id)
    {
        // Get URL Parameter
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $suratPerintahPenyelidikanDocumentId = $id;

        DB::beginTransaction();
        try {
            // Delete from database
            $suratPerintahPenyelidikanDocument = SuratPerintahPenyelidikanDocument::where('id', $suratPerintahPenyelidikanDocumentId)->first();
            $suratPerintahPenyelidikanDocument->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan pada saat menghapus data.');
        }

        // Redirect with param accident_id
        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId]);
    }

    public function download($id)
    {
        // Get URL Parameter
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $suratPerintahPenyelidikanDocumentId = $id;

        // Get data from database
        $suratPerintahPenyelidikanDocument = SuratPerintahPenyelidikanDocument::with(['suratPerintahPenyelidikanDocumentOfficers', 'suratPerintahPenyelidikanDocumentCaseKeywords'])->where('id', $suratPerintahPenyelidikanDocumentId)->first();
        $officers = $suratPerintahPenyelidikanDocument->suratPerintahPenyelidikanDocumentOfficers->where('class', '!=', SuratPerintahPenyelidikanDocumentOfficer::getEnumOption('class', 'SIGNATORY'))->sortBy('class');
        $leader = $suratPerintahPenyelidikanDocument->suratPerintahPenyelidikanDocumentOfficers->where('class', '=', SuratPerintahPenyelidikanDocumentOfficer::getEnumOption('class', 'LEADER'))->first();
        $signatory = $suratPerintahPenyelidikanDocument->suratPerintahPenyelidikanDocumentOfficers->where('class', '=', SuratPerintahPenyelidikanDocumentOfficer::getEnumOption('class', 'SIGNATORY'))->first();

        $accident = Accident::with(['polres', 'polres.polda'])->where('id', $accidentId)->first();

        $no = 1;
        $blockOfficers = [];
        foreach ($officers as $officer) {
            $blockOfficers[] = [
                'number' => $no,
                'first_name'   => ($officer->first_title) ? $officer->first_title . ' ' . $officer->first_name : $officer->first_name,
                'last_name'  => ($officer->last_title) ? $officer->last_name . ', ' . $officer->last_title : $officer->last_name,
                'rank_id' => $officer->rank->name ?? '',
                'officer_id' => $officer->register_number,
                'position' => $officer->position->name ?? '',
            ];
            $no++;
        }

        $signatureTitleText = [
            'KAPOLRES' => 'KEPALA KEPOLISIAN RESOR ' . $accident->polres->full_name,
            'NO_KAPOLRES' => 'a.n. KEPALA KEPOLISIAN RESOR ' . $accident->polres->full_name,
            'NO_DIRLANTAS' => 'a.n. DIREKTUR LALU LINTAS POLDA ' . $accident->polres->polda->full_name,
        ];

        $signatoryPositionHeadText = [
            'NO_KAPOLRES' => $signatory->position->positionCluster->alias_name ?? '',
            'NO_DIRLANTAS' => $signatory->position->positionCluster->alias_name ?? '',
        ];

        // $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('word-template/surat_perintah_penyelidikan.docx');

        // Tentukan template file berdasarkan tanggal kecelakaan
        $cutoffDate = Carbon::parse('2026-01-02');
        $accidentDate = Carbon::parse($accident->accident_date);

        $templateFile = $accidentDate->lt($cutoffDate) 
            ? 'word-template/surat_perintah_penyelidikan_2025.docx'
            : 'word-template/surat_perintah_penyelidikan.docx';

        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($templateFile);

        if (isset($signatory->position)) {
            if ($signatory->position->position_cluster_id == '1') {
                $templateProcessor->setValue('officer_signature_title_text', $signatureTitleText['KAPOLRES']);
                $templateProcessor->setValue('officer_signature_position_head_text', '');
            } else if ($signatory->position->position_cluster_id == '9') {
                $templateProcessor->setValue('officer_signature_title_text', $signatureTitleText['NO_DIRLANTAS']);
                $templateProcessor->setValue('officer_signature_position_head_text', $signatoryPositionHeadText['NO_DIRLANTAS']);
            } else {
                $templateProcessor->setValue('officer_signature_title_text', $signatureTitleText['NO_KAPOLRES']);
                $templateProcessor->setValue('officer_signature_position_head_text', $signatoryPositionHeadText['NO_KAPOLRES']);
            }
        }

        $qrCodeImagePath = public_path('temp/qrcode.png');
        $templateProcessor->setImageValue('image_placeholder_name', $qrCodeImagePath, array(
            'width' => 120,
            'height' => 120,
        ));

        $resorPolice = $accident->polres;
        $resorPoliceFullName = (in_array($resorPolice->id, ['1114'])) ? 'DIREKTORAT LALU LINTAS' : 'RESOR ' . strtoupper($resorPolice->full_name);

        $templateProcessor->cloneBlock('block_officers', 2, true, false, $blockOfficers);
        $templateProcessor->setValue('letter_number',  $suratPerintahPenyelidikanDocument->document_number);
        $templateProcessor->setValue('letter_end_date', ($suratPerintahPenyelidikanDocument->end_date != NULL) ? 'tanggal ' . Carbon::parse($suratPerintahPenyelidikanDocument->end_date)->locale('id')->translatedFormat('d F Y') : 'selesai');
        $templateProcessor->setValue('issued_date', Carbon::parse($suratPerintahPenyelidikanDocument->document_date)->locale('id')->translatedFormat('d F Y'));
        $templateProcessor->setValue('accident_day', Carbon::parse($accident->accident_date)->locale('id')->translatedFormat('l'));
        $templateProcessor->setValue('accident_date', Carbon::parse($accident->accident_date)->locale('id')->translatedFormat('d F Y'));
        $templateProcessor->setValue('report_date', Carbon::parse($accident->report_date)->locale('id')->translatedFormat('d F Y'));
        $templateProcessor->setValue('accident_time', Carbon::parse($accident->accident_time)->locale('id')->translatedFormat('H:i'));
        $templateProcessor->setValue('polda_full_name', $accident->polres->polda->full_name);
        $templateProcessor->setValue('polda_name', $accident->polres->polda->full_name);
        $templateProcessor->setValue('polres_name', $resorPoliceFullName);
        $templateProcessor->setValue('polres_alamat', ucwords(strtolower($accident->polres->address . ', ' . $accident->polres->polres_district . ', ' . $accident->polres->polres_zipcode)));
        $templateProcessor->setValue('road_name', $accident->road_name);
        $templateProcessor->setValue('no_lp', $accident->no_lp);
        $templateProcessor->setValue('officer_signature_sebagai_kepala', strtoupper($signatory->position->name ?? ''));
        $templateProcessor->setValue('officer_signature_rank', strtoupper($signatory->rank->name ?? ''));
        $templateProcessor->setValue('officer_signature_nrp', $signatory->register_number);
        $templateProcessor->setValue('officer_signature_name', (($signatory->first_title) ? $signatory->first_title . ' ' . $signatory->first_name : $signatory->first_name) . ' ' . (($signatory->last_title) ? $signatory->last_name . ', ' . $signatory->last_title : $signatory->last_name));
        $templateProcessor->setValue('officer_assign_rank', strtoupper($leader->rank->name ?? ''));
        $templateProcessor->setValue('officer_assign_nrp', $leader->register_number);
        $templateProcessor->setValue('officer_assign_name', (($leader->first_title) ? $leader->first_title . ' ' . $leader->first_name : $leader->first_name) . ' ' . (($leader->last_title) ? $leader->last_name . ', ' . $leader->last_title : $leader->last_name));
        $templateProcessor->setValue('location_created', ucwords(strtolower($accident->polres->polres_district)));

        $filename = 'generate/' . $suratPerintahPenyelidikanDocument->id . ' - Surat Perintah Penyelidikan - ' . $accident->polres->full_name;
        $templateProcessor->saveAs($filename . '.docx');
        return response()->download($filename . '.docx')->deleteFileAfterSend(true);
    }

    /*public function generatePDF($id)
    {
        // Get URL Parameter
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $suratPerintahPenyelidikanDocumentId = $id;

        // Get data from database
        $suratPerintahPenyelidikanDocument = SuratPerintahPenyelidikanDocument::with(['suratPerintahPenyelidikanDocumentOfficers', 'suratPerintahPenyelidikanDocumentCaseKeywords'])->where('id', $suratPerintahPenyelidikanDocumentId)->first();
        $officers = $suratPerintahPenyelidikanDocument->suratPerintahPenyelidikanDocumentOfficers->where('class', '!=', SuratPerintahPenyelidikanDocumentOfficer::getEnumOption('class', 'SIGNATORY'))->sortBy('class');
        $leader = $suratPerintahPenyelidikanDocument->suratPerintahPenyelidikanDocumentOfficers->where('class', '=', SuratPerintahPenyelidikanDocumentOfficer::getEnumOption('class', 'LEADER'))->first();
        $signatory = $suratPerintahPenyelidikanDocument->suratPerintahPenyelidikanDocumentOfficers->where('class', '=', SuratPerintahPenyelidikanDocumentOfficer::getEnumOption('class', 'SIGNATORY'))->first();

        $accident = Accident::with(['polres', 'polres.polda'])->where('id', $accidentId)->first();

        $no = 1;
        $blockOfficers = [];
        foreach ($officers as $officer) {
            $blockOfficers[] = [
                'number' => $no,
                'first_name'   => ($officer->first_title) ? $officer->first_title . ' ' . $officer->first_name : $officer->first_name,
                'last_name'  => ($officer->last_title) ? $officer->last_name . ', ' . $officer->last_title : $officer->last_name,
                'rank_id' => $officer->rank->name ?? '',
                'officer_id' => $officer->register_number,
                'position' => $officer->position->name ?? '',
            ];
            $no++;
        }

        $signatureTitleText = [
            'KAPOLRES' => 'KEPALA KEPOLISIAN RESOR ' . $accident->polres->full_name,
            'NO_KAPOLRES' => 'a.n. KEPALA KEPOLISIAN RESOR ' . $accident->polres->full_name . '</w:t><w:p/><w:t>' . $signatory->position->name ?? '',
            'NO_DIRLANTAS' => 'a.n. DIREKTUR LALU LINTAS POLDA ' . $accident->polres->polda->full_name . '</w:t><w:p/><w:t>' . $signatory->position->name ?? '',
        ];

        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('word-template/surat_perintah_penyelidikan.docx');

        if (isset($signatory->position)) {
            if ($signatory->position->position_cluster_id == '1') {
                $templateProcessor->setValue('officer_signature_title_text', $signatureTitleText['KAPOLRES']);
            } else if ($signatory->position->position_cluster_id == '9') {
                $templateProcessor->setValue('officer_signature_title_text', $signatureTitleText['NO_DIRLANTAS']);
            } else {
                $templateProcessor->setValue('officer_signature_title_text', $signatureTitleText['NO_KAPOLRES']);
            }
        }

        $templateProcessor->cloneBlock('block_officers', 2, true, false, $blockOfficers);
        $templateProcessor->setValue('letter_number',  $suratPerintahPenyelidikanDocument->document_number);
        $templateProcessor->setValue('letter_end_date', ($suratPerintahPenyelidikanDocument->end_date != NULL) ? 'tanggal ' . Carbon::parse($suratPerintahPenyelidikanDocument->end_date)->locale('id')->translatedFormat('d F Y') : 'selesai');
        $templateProcessor->setValue('issued_date', Carbon::parse($suratPerintahPenyelidikanDocument->document_date)->locale('id')->translatedFormat('d F Y'));
        $templateProcessor->setValue('accident_day', Carbon::parse($accident->accident_date)->locale('id')->translatedFormat('l'));
        $templateProcessor->setValue('accident_date', Carbon::parse($accident->accident_date)->locale('id')->translatedFormat('d F Y'));
        $templateProcessor->setValue('accident_time', Carbon::parse($accident->accident_time)->locale('id')->translatedFormat('H:i'));
        $templateProcessor->setValue('polda_full_name', $accident->polres->polda->full_name);
        $templateProcessor->setValue('polda_name', $accident->polres->polda->full_name);
        $templateProcessor->setValue('polres_name', $accident->polres->full_name);
        $templateProcessor->setValue('polres_alamat', ucwords(strtolower($accident->polres->address . ', ' . $accident->polres->polres_district . ', ' . $accident->polres->polres_zipcode)));
        $templateProcessor->setValue('road_name', $accident->road_name);
        $templateProcessor->setValue('no_lp', $accident->no_lp);
        $templateProcessor->setValue('officer_signature_sebagai_kepala', strtoupper($signatory->position->name ?? ''));
        $templateProcessor->setValue('officer_signature_rank', strtoupper($signatory->rank->name ?? ''));
        $templateProcessor->setValue('officer_signature_nrp', $signatory->register_number);
        $templateProcessor->setValue('officer_signature_name', (($signatory->first_title) ? $signatory->first_title . ' ' . $signatory->first_name : $signatory->first_name) . ' ' . (($signatory->last_title) ? $signatory->last_name . ', ' . $signatory->last_title : $signatory->last_name));
        $templateProcessor->setValue('officer_assign_rank', strtoupper($leader->rank->name ?? ''));
        $templateProcessor->setValue('officer_assign_nrp', $leader->register_number);
        $templateProcessor->setValue('officer_assign_name', (($leader->first_title) ? $leader->first_title . ' ' . $leader->first_name : $leader->first_name) . ' ' . (($leader->last_title) ? $leader->last_name . ', ' . $leader->last_title : $leader->last_name));
        $templateProcessor->setValue('location_created', ucwords(strtolower($accident->polres->polres_district)));

        $filename = 'generate/' . $suratPerintahPenyelidikanDocument->id . ' - Surat Perintah Penyelidikan - ' . $accident->polres->full_name;
        $templateProcessor->saveAs($filename . '.docx');

        $content = \PhpOffice\PhpWord\IOFactory::load($filename . '.docx');
        $PDFWriter = \PhpOffice\PhpWord\IOFactory::createWriter($content, 'PDF');
        $PDFWriter->save(storage_path('/documents/' . $suratPerintahPenyelidikanDocument->documentCategory->alt_code . '/attachments/' . $suratPerintahPenyelidikanDocument->id . '.pdf'));

        //update to database attachment
        $suratPerintahPenyelidikanDocument->attachment()->updateOrCreate(
            [
                'surat_perintah_penyelidikan_document_id' => $suratPerintahPenyelidikanDocument->id,
            ],
            [
                'surat_perintah_penyelidikan_document_id' => $suratPerintahPenyelidikanDocument->id,
                'file_name' => $suratPerintahPenyelidikanDocument->id . '.pdf',
                'original_name' => $suratPerintahPenyelidikanDocument->id . '.pdf',
                'extension' => 'pdf',
                'mimetype' => 'application/pdf',
                'size' => filesize(storage_path('/documents/' . $suratPerintahPenyelidikanDocument->documentCategory->alt_code . '/attachments/' . $suratPerintahPenyelidikanDocument->id . '.pdf')),
                'type' => 'DOCUMENT',
            ]
        );

        // remove word file
        unlink(public_path($filename . '.docx'));
        
        // Redirect with param accident_id
        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId]);
    }*/

    // =====( API )=====
    public function getInternalOfficers(Request $request)
    {
        $accidentId = $request->accident_id;
        $selectedLeaderOfficerRegisterNumber = $request->selectedLeaderOfficerRegisterNumber;

        try {
            $accident = Accident::with(['polres'])->where('id', $accidentId)->first();

            $getOldNewPolresIds = $this->getOldNewPolresIds($accident->polres_id);

            $officers = Officer::withRelated()
                ->selectFullName()
                ->whereIn('officers.police_id', $getOldNewPolresIds)
                ->whereHasUserActive()
                ->hasDataComplete()
                ->member()
                ->active()
                ->valid()
                ->where('officers.register_number', '!=', $selectedLeaderOfficerRegisterNumber)
                ->get();

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'data' => $officers
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getMovedOfficers(Request $request)
    {
        $accidentId = $request->accident_id;
        $searchedOfficerRegisterNumber = $request->searchedOfficerRegisterNumber;

        try {
            $accident = Accident::with(['polres'])->where('id', $accidentId)->first();

            $officers = Officer::withRelated()
                ->selectFullName()
                ->where('officers.police_id', '!=', $accident->polres->id)
                ->whereHasUserActive()
                ->hasDataComplete()
                ->member()
                ->active()
                ->valid()
                ->where('officers.register_number', $searchedOfficerRegisterNumber)
                ->first();

            if (empty($officers)) {
                return response()->json([
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'Data tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'data' => $officers
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getExternalOfficers(Request $request)
    {
        $accidentId = $request->accident_id;
        $searchedOfficerRegisterNumber = $request->searchedOfficerRegisterNumber;

        try {
            $accident = Accident::with(['polres'])->where('id', $accidentId)->first();

            $officers = Officer::withRelated()
                ->selectFullName()
                ->whereHasUserActive()
                ->hasDataComplete()
                ->member()
                ->active()
                ->valid()
                ->where('officers.police_id', '!=', $accident->polres->id)
                ->where('officers.register_number', $searchedOfficerRegisterNumber)
                ->first();

            if (empty($officers)) {
                return response()->json([
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'Data tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'data' => $officers
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getPolices(Request $request)
    {
        $policeClass = $request->policeClass;
        $policeId = $request->policeId;

        try {
            switch ($policeClass) {
                case 'DAERAH':
                    $polices = Police::where('is_active', true)
                        ->where('class', $policeClass)
                        ->orderBy('sort', 'asc')
                        ->get();
                    break;

                case 'RESOR':
                    $polices = Police::where('is_active', true)
                        ->where('class', $policeClass)
                        ->where('parent_id', $policeId)
                        ->orderBy('sort', 'asc')
                        ->get();
                    break;
            }

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'data' => $polices
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => 'Terjadi kesalahan pada sistem'
            ], 500);
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
                    'errors' => $validator->errors()
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
                'errors' => 'Terjadi kesalahan pada sistem.',
                'code' => 500,
            ], 500);
        }
    }

    private function validateForm(Request $request)
    {
        return Validator::make($request->all(), [
            'documentNumber' => 'required|min:5|max:255|regex:/^(?=.*[a-zA-Z])(?=.*[0-9])(?=.*\/).+$/',
            'referenceDocument' => 'required_if:isRenewalDocument,true',
            'caseClassification' => 'required',
            'documentDate' => 'required',
            'startDate' => 'required',
            'endDate' => 'required_unless:isFinished,true',
            'signatory' => 'required',
            'officerLeader' => 'required',

            'internalOfficers' => 'required_without_all:isExternalOfficers,"true",isMovedOfficers,"true"',
            'movedOfficers' => [
                Rule::requiredIf(function () use ($request) {
                    return $request->isMovedOfficers == 'true' && empty($request->manualMovedOfficerRegisterNumbers) == true && empty($request->movedOfficers) == true;
                }),
            ],
            'manualMovedOfficerRegisterNumbers' => [
                Rule::requiredIf(function () use ($request) {
                    return $request->isMovedOfficers == 'true' && empty($request->movedOfficers) == true && empty($request->manualMovedOfficerRegisterNumbers) == true;
                }),
            ],
            'externalOfficers' => 'required_if:isExternalOfficers,true',
            'manualMovedOfficerFirstNames.*' => 'max:255',
            'manualMovedOfficerLastNames.*' => 'max:255',
        ], [
            'documentNumber.required' => 'No Dokumen harus diisi',
            'documentNumber.max' => 'No Dokumen maksimal 255 karakter',
            'documentNumber.min' => 'No Dokumen harus lengkap',
            'documentNumber.regex' => 'No Dokumen harus lengkap',

            'caseClassification.required' => 'Klasifikasi Kasus harus diisi',
            'referenceDocument.required_if' => 'SP Penyelidikan Referensi harus diisi',
            'documentDate.required' => 'Tanggal Dokumen Lidik harus diisi',
            'startDate.required' => 'Tanggal Mulai Lidik harus diisi',
            'endDate.required_unless' => 'Tanggal Akhir Lidik harus diisi',
            'signatory.required' => 'Yang Menandatangani harus diisi',
            'officerLeader.required' => 'Ketua Tim Penyelidik harus diisi',

            'internalOfficers.required_without_all' => 'Anggota Tim Penyelidik harus diisi',
            'movedOfficers.required' => 'Anggota Tim Penyelidik yang telah pindah harus diisi',
            'manualMovedOfficerRegisterNumbers.required' => 'Anggota Tim Penyelidik yang telah pindah harus diisi',
            'externalOfficers.required_if' => 'Anggota Tim Penyelidik dari luar harus diisi',
            'manualMovedOfficerFirstNames.*.max' => 'Nama Depan Anggota Tim Penyelidik yang telah pindah maksimal 255 karakter',
            'manualMovedOfficerLastNames.*.max' => 'Nama Belakang Anggota Tim Penyelidik yang telah pindah maksimal 255 karakter',
        ]);
    }
}
