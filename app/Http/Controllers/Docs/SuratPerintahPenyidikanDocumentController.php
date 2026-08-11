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

use App\Services\Doc\DocService;

use App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument;
use App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocumentOfficer;
use App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocumentLaw;
use App\Models\Lib\CaseKeyword;
use App\Models\Lib\Rank;
use App\Models\Lib\Position;
use App\Models\Officer;
use App\Models\Accident;
use App\Models\Polda;
use App\Models\Polres;
use App\Models\Lib\CrimeType;
use App\Models\Lib\CrimeClass;
use App\Models\Lib\CrimeConstitution;
use App\Models\Lib\CaseClassification;
use App\Models\Lib\Police;

use App\Traits\DocsOfficersTraits;

class SuratPerintahPenyidikanDocumentController extends Controller
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

        $accident = Accident::where('id', $accidentId)
            ->first();

        $ranks = Rank::where('is_active', true)
            ->wherePolri()
            ->orderBy('sort')
            ->get();

        $positions = Position::where('is_active', true)
            ->orderBy('sort')
            ->get();

        $crimeTypes = CrimeType::active()
            ->orderBy('sort')
            ->get();
        $crimeClasses = CrimeClass::active()
            ->orderBy('sort')
            ->get();
        $crimeConstitutions = CrimeConstitution::active()
            ->orderBy('sort')
            ->get();

        $caseClassifications = CaseClassification::where('is_active', true)
            ->orderBy('sort')
            ->get();

        $getOldNewPolresIds = $this->getOldNewPolresIds($accident->polres_id);

        $authorizedSignatories = Officer::withRelated()
            ->selectFullName()
            ->whereIn('police_id', $getOldNewPolresIds)
            ->whereHasUserActive()
            ->hasDataComplete()
            ->signatory()
            ->valid()
            ->active()
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

        $viewData =[
            'authorizedSignatories' => $authorizedSignatories,
            'leaderOfficers' => $leaderOfficers,
            'accidentId' => $accidentId,
            'accident' => $accident,
            'ranks' => $ranks,
            'positions' => $positions,
            'crimeTypes' => $crimeTypes,
            'crimeClasses' => $crimeClasses,
            'crimeConstitutions' => $crimeConstitutions,
            'resortPoliceId' => $accident->polres_id,
            'caseKeywords' => $caseKeywords,
            'caseClassifications' => $caseClassifications,
        ];

        return view('docs.surat-perintah-penyidikan-document.create', $viewData);
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
        $endDate = Carbon::parse(htmlspecialchars($request->endDate))->format('Y-m-d');
        $signatoryId = htmlspecialchars($request->signatory);
  
        $caseClassification = htmlspecialchars($request->caseClassification);

        $isExternalOfficers = ($request->isExternalOfficers == 'true') ? true : false;
        $isMovedOfficers = ($request->isMovedOfficers == 'true') ? true : false;

        $isLegacy = filter_var($request->isLegacy, FILTER_VALIDATE_BOOLEAN);

        // Check if document number already exist
        $exists = SuratPerintahPenyidikanDocument::where('accident_id', $accidentId)
            ->where('document_number', 'ILIKE', $documentNumber)
            ->exists();
        if ($exists) {
            return redirect()->back()->with('error', 'Dokumen ' . $documentNumber . ' Sudah Anda Buat Sebelumnya.');
        }
        
        DB::beginTransaction();
        try{
            $caseClassification = CaseClassification::where('name', $caseClassification)->first();
            // Store to database
            $suratPerintahPenyidikanDocument = SuratPerintahPenyidikanDocument::create([
                'accident_id' => $accidentId,
                'document_number' => $documentNumber,
                'document_date' => $documentDate,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'case_classification' => $caseClassification->name,
                'is_legacy' => $isLegacy,
            ]);

            $suratPerintahPenyidikanDocumentId = $suratPerintahPenyidikanDocument->id;

            // LAW
            // Main Law
            $lawCrimeTypeIds = $request->lawCrimeTypeIds ?? [];
            $lawCrimeClassIds = $request->lawCrimeClassIds ?? [];
            $lawCrimeConstitutionIds = $request->lawCrimeConstitutionIds ?? [];
            $lawCrimeConstitutionChapters = $request->lawCrimeConstitutionChapters ?? [];

            $lawCrimeTypeIdCollection = Collection::make($lawCrimeTypeIds);
            $lawCrimeClassIdCollection = Collection::make($lawCrimeClassIds);
            $lawCrimeConstitutionIdCollection = Collection::make($lawCrimeConstitutionIds);
            $lawCrimeConstitutionChapterCollection = Collection::make($lawCrimeConstitutionChapters);

            $mainLaws = $lawCrimeTypeIdCollection->zip(
                                $lawCrimeClassIdCollection, 
                                $lawCrimeConstitutionIdCollection, 
                                $lawCrimeConstitutionChapterCollection,
            )->map(function ($law) {
                return [
                    'crime_type_id' => $law[0],
                    'crime_class_id' => $law[1],
                    'crime_constitution_id' => $law[2],
                    'crime_constitution_chapter' => $law[3],
                ];
            });

            foreach ($mainLaws as $mainLaw) {
                $crimeType = CrimeType::where('id', $mainLaw['crime_type_id'])->first();
                $crimeClass = CrimeClass::where('id', $mainLaw['crime_class_id'])->first();
                $constitution = CrimeConstitution::where('id', $mainLaw['crime_constitution_id'])->first();

                $suratPerintahPenyidikanDocument->suratPerintahPenyidikanDocumentLaws()->create([
                    'surat_perintah_penyidikan_document_id' => $suratPerintahPenyidikanDocumentId,
                    'crime_type_id' => $crimeType->id,
                    'crime_class_id' => $crimeClass->id,
                    'crime_constitution_id' => $constitution->id,
                    'constitution_chapter' => $mainLaw['crime_constitution_chapter'],

                    'flag' => SuratPerintahPenyidikanDocumentLaw::getEnumOption('flag', 'MAIN'),
                ]);
            }
            // Additional Law
            $lawAdditionalNames = $request->lawAdditionalNames ?? [];
            $additionalLawCollection = Collection::make($lawAdditionalNames);
            $additionalLaws = $additionalLawCollection->map(function ($additionalLaw) {
                return [
                    'name' => $additionalLaw,
                ];
            });

            foreach($additionalLaws as $additionalLaw) {
                $suratPerintahPenyidikanDocument->suratPerintahPenyidikanDocumentLaws()->create([
                    'surat_perintah_penyidikan_document_id' => $suratPerintahPenyidikanDocumentId,
                    'crime_type_id' => NULL,
                    'crime_class_id' => NULL,
                    'crime_constitution_id' => NULL,
                    'constitution' => $additionalLaw['name'],

                    'flag' => SuratPerintahPenyidikanDocumentLaw::getEnumOption('flag', 'ADDT'),
                ]);
            }

            
            // SIGNATORY
            $signatory = Officer::where('id',$signatoryId)->first();
            $resortPolice = Polres::with(['polda'])->where('id',$signatory->polres_id)->first();
            $suratPerintahPenyidikanDocument->suratPerintahPenyidikanDocumentOfficers()->create([
                'surat_perintah_penyidikan_document_id' => $suratPerintahPenyidikanDocumentId,
                'register_number' => $signatory->register_number,
                'first_title' => $signatory->first_title,
                'first_name' => $signatory->first_name,
                'last_name' => $signatory->last_name,
                'last_title' => $signatory->last_title,

                'rank_id' => $signatory->rank_id,
                'position_id' =>  $signatory->position_id,
                'phone_number' => $signatory->phone_number,
                'email' => $signatory->email,
      
                'police_id' => $signatory->police_id,
                'status' => SuratPerintahPenyidikanDocumentOfficer::getEnumOption('status', 'PRESENT'),
                'class' => SuratPerintahPenyidikanDocumentOfficer::getEnumOption('class', 'SIGNATORY'),
                'flag' => SuratPerintahPenyidikanDocumentOfficer::getEnumOption('flag', 'INTERNAL'),
            ]);
   

            // LEADER
            $createDataOfficerLeader = [];
            if($request->isPresentOfficerLeader == 'true'){
                $officerLeader = htmlspecialchars($request->officerLeader);
                $officerLeader = Officer::where('id', $officerLeader)->first();

                $resortPolice = Polres::where('id',$officerLeader->polres_id)->first();
                $regionalPolice = Polda::where('id',$officerLeader->polda_id)->first();

                $createDataOfficerLeader = [
                    'surat_perintah_penyidikan_document_id' => $suratPerintahPenyidikanDocumentId,
                    'register_number' => $officerLeader->register_number,
                    
                    'first_title' => $officerLeader->first_title,
                    'first_name' => $officerLeader->first_name,
                    'last_name' => $officerLeader->last_name,
                    'last_title' => $officerLeader->last_title,

                    'rank_id' =>  $officerLeader->rank_id,
                    'position_id' =>  $officerLeader->position_id,
                    'phone_number' => $officerLeader->phone_number,
                    'email' => $officerLeader->email,
                  
                    'police_id' => $officerLeader->police_id,
                    'status' => SuratPerintahPenyidikanDocumentOfficer::getEnumOption('status', 'PRESENT'),
                    'class' => SuratPerintahPenyidikanDocumentOfficer::getEnumOption('class', 'LEADER'),
                    'flag' => SuratPerintahPenyidikanDocumentOfficer::getEnumOption('flag', 'INTERNAL'),
                    'insert_method' => SuratPerintahPenyidikanDocumentOfficer::getEnumOption('insert_method', 'IMPORT'),
                ];
            }else if($request->isPresentOfficerLeader == 'false'){
                $movedOfficerLeaderRegisterNumber = htmlspecialchars($request->movedOfficerLeaderRegisterNumber);
                $movedOfficerLeaderName = htmlspecialchars($request->movedOfficerLeaderName);
                $movedOfficerLeaderPhone = htmlspecialchars($request->movedOfficerLeaderPhone);
                $movedOfficerLeaderRankId = htmlspecialchars($request->movedOfficerLeaderRankId);
                $movedOfficerLeaderRankName = htmlspecialchars($request->movedOfficerLeaderRankName);
                $movedOfficerLeaderPositionId = htmlspecialchars($request->movedOfficerLeaderPositionId);
                $movedOfficerLeaderPositionName = htmlspecialchars($request->movedOfficerLeaderPositionName);

                $movedOfficerLeaderRegionalPoliceId = htmlspecialchars($request->movedOfficerLeaderRegionalPoliceId);
                $movedOfficerLeaderResortPoliceId = htmlspecialchars($request->movedOfficerLeaderResortPoliceId);

                $resortPolice = Polres::where('id',$movedOfficerLeaderResortPoliceId)->first();
                $regionalPolice = Polda::where('id', $movedOfficerLeaderRegionalPoliceId)->first();

                $createDataOfficerLeader = [
                    'surat_perintah_penyidikan_document_id' => $suratPerintahPenyidikanDocumentId,
                    'register_number' => $movedOfficerLeaderRegisterNumber,
                    'first_name' => $movedOfficerLeaderName,
                    'last_name' => NULL,

                    'rank_id' =>  $movedOfficerLeaderRankId,
                    'position_id' =>  $movedOfficerLeaderPositionId,
                    'phone_number' => $movedOfficerLeaderPhone,
                    'email' => NULL,
                 
                    'police_id' => ($movedOfficerLeaderResortPoliceId) ? $movedOfficerLeaderResortPoliceId : $movedOfficerLeaderRegionalPoliceId,
                    'status' => SuratPerintahPenyidikanDocumentOfficer::getEnumOption('status', 'PAST'),
                    'class' => SuratPerintahPenyidikanDocumentOfficer::getEnumOption('class', 'LEADER'),
                    'flag' => SuratPerintahPenyidikanDocumentOfficer::getEnumOption('flag', 'MOVED'),
                    'insert_method' => SuratPerintahPenyidikanDocumentOfficer::getEnumOption('insert_method', 'MANUAL'),
                ];
            }
            $suratPerintahPenyidikanDocument->suratPerintahPenyidikanDocumentOfficers()->create($createDataOfficerLeader);

            //Internal Officers
            $internalOfficers = $request->internalOfficers ?? [];
            foreach($internalOfficers as $internalOfficer){
                $registerNumber = $internalOfficer;
                $officer = Officer::where('register_number', $registerNumber)->first();
                if($officer){
                    $resortPolice = Polres::where('id',$officer->polres_id)->first();
                    $regionalPolice = Polda::where('id',$officer->polda_id)->first();
                    
                    $suratPerintahPenyidikanDocument->suratPerintahPenyidikanDocumentOfficers()->create([
                        'surat_perintah_penyidikan_document_id' => $suratPerintahPenyidikanDocumentId,
                        'register_number' => $officer->register_number,

                        'first_title' => $officer->first_title,
                        'first_name' => $officer->first_name,
                        'last_name' => $officer->last_name,
                        'last_title' => $officer->last_title,

                        'rank_id' =>  $officer->rank_id,
                        'position_id' =>  $officer->position_id,
                        'phone_number' => $officer->phone_number,
                        'email' => $officer->email,
                  
                        'police_id' => $officer->police_id,
                        'status' => SuratPerintahPenyidikanDocumentOfficer::getEnumOption('status', 'PRESENT'),
                        'class' => SuratPerintahPenyidikanDocumentOfficer::getEnumOption('class', 'MEMBER'),
                        'flag' => SuratPerintahPenyidikanDocumentOfficer::getEnumOption('flag', 'INTERNAL'),
                    ]);
                }
            }


            //External Officers
            if($isExternalOfficers == true){
                $externalOfficers = $request->externalOfficers ?? [];
                foreach($externalOfficers as $externalOfficer){
                    $registerNumber = $externalOfficer;
                    $officer = Officer::where('register_number',$registerNumber)->first();
                    if($officer){
                        $resortPolice = Polres::where('id',$officer->polres_id)->first();
                        $regionalPolice = Polda::where('id',$officer->polda_id)->first();
                        $suratPerintahPenyidikanDocument->suratPerintahPenyidikanDocumentOfficers()->create([
                            'surat_perintah_penyidikan_document_id' => $suratPerintahPenyidikanDocumentId,
                            'register_number' => $officer->register_number,

                            'first_title' => $officer->first_title,
                            'first_name' => $officer->first_name,
                            'last_name' => $officer->last_name,
                            'last_title' => $officer->last_title,

                            'rank_id' =>  $officer->rank_id,
                            'position_id' =>  $officer->position_id,
                            'phone_number' => $officer->phone_number,
                            'email' => $officer->email,
                            
                            'police_id' => $officer->police_id,
                            'status' => SuratPerintahPenyidikanDocumentOfficer::getEnumOption('status', 'EXTERNAL'),
                            'class' => SuratPerintahPenyidikanDocumentOfficer::getEnumOption('class', 'MEMBER'),
                            'flag' => SuratPerintahPenyidikanDocumentOfficer::getEnumOption('flag', 'EXTERNAL'),
                        ]);
                    }
                }
            }
      

            //Moved Officers
            if($isMovedOfficers == true){
                $movedOfficers = $request->movedOfficers ?? [];
                foreach($movedOfficers as $movedOfficer){
                    $registerNumber = $movedOfficer;
                    $officer = Officer::where('register_number',$registerNumber)->first();
                    if($officer){
                        $resortPolice = Polres::where('id',$officer->polres_id)->first();
                        $regionalPolice = Polda::where('id',$officer->polda_id)->first();
                        $suratPerintahPenyidikanDocument->suratPerintahPenyidikanDocumentOfficers()->create([
                            'surat_perintah_penyidikan_document_id' => $suratPerintahPenyidikanDocumentId,
                            'register_number' => $officer->register_number,

                            'first_title' => $officer->first_title,
                            'first_name' => $officer->first_name,
                            'last_name' => $officer->last_name,
                            'last_title' => $officer->last_title,

                            'rank_id' =>  $officer->rank_id,
                            'position_id' => $officer->position_id,
                            'phone_number' => $officer->phone_number,
                            'email' => $officer->email,
                            
                            'police_id' => $officer->police_id,
                            'status' => SuratPerintahPenyidikanDocumentOfficer::getEnumOption('status', 'PAST'),
                            'class' => SuratPerintahPenyidikanDocumentOfficer::getEnumOption('class', 'MEMBER'),
                            'flag' => SuratPerintahPenyidikanDocumentOfficer::getEnumOption('flag', 'MOVED'),
                        ]);
                    }
                }
                //Manual Moved Officers
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
                foreach($manualMovedOfficers as $manualMovedOfficer){
                    $resortPolice = Polres::where('id',$manualMovedOfficer['resort_police_id'])->first();
                    $regionalPolice = Polda::where('id',$manualMovedOfficer['regional_police_id'])->first();

                    $rank = Rank::where('id', ($manualMovedOfficer['rank_id'] != NULL && $manualMovedOfficer['rank_id'] != 'null' && $manualMovedOfficer['rank_id'] != '-') ? $manualMovedOfficer['rank_id'] : NULL)->first();

                    $suratPerintahPenyidikanDocument->suratPerintahPenyidikanDocumentOfficers()->create([
                        'surat_perintah_penyidikan_document_id' => $suratPerintahPenyidikanDocumentId,
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
                        'status' => SuratPerintahPenyidikanDocumentOfficer::getEnumOption('status', 'PAST'),
                        'class' => SuratPerintahPenyidikanDocumentOfficer::getEnumOption('class', 'MEMBER'),
                        'flag' => SuratPerintahPenyidikanDocumentOfficer::getEnumOption('flag', 'MOVED'),
                        'insert_method' => SuratPerintahPenyidikanDocumentOfficer::getEnumOption('insert_method', 'MANUAL'),
                    ]);
                }
            }

            $caseKeywords = $request->keywords ?? [];
            foreach($caseKeywords as $caseKeyword){
                $caseKeyword = CaseKeyword::where('id',$caseKeyword)->first();

                $suratPerintahPenyidikanDocument->suratPerintahPenyidikanDocumentCaseKeywords()->create([
                    'surat_perintah_penyidikan_document_id' => $suratPerintahPenyidikanDocumentId,
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
       $suratPerintahPenyidikanDocumentId = $id;

       $suratPerintahPenyidikanDocument = SuratPerintahPenyidikanDocument::with(['suratPerintahPenyidikanDocumentOfficers', 'suratPerintahPenyidikanDocumentCaseKeywords', 'suratPerintahPenyidikanDocumentLaws'])->where('id', $suratPerintahPenyidikanDocumentId)->first();
       $accident = Accident::with(['polres', 'polres.polda'])->where('id', $accidentId)->first();

       $viewData =[
           'accidentId' => $accidentId,
           'accident' => $accident,
           'suratPerintahPenyidikanDocument' => $suratPerintahPenyidikanDocument,
           'suratPerintahPenyidikanDocumentId' => $suratPerintahPenyidikanDocumentId,
       ];

       return view('docs.surat-perintah-penyidikan-document.show', $viewData);
    }

    public function edit($id)
    {
        // Get URL Parameter
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $suratPerintahPenyidikanDocumentId = $id;

        $suratPerintahPenyidikanDocument = SuratPerintahPenyidikanDocument::with(['suratPerintahPenyidikanDocumentOfficers', 'suratPerintahPenyidikanDocumentCaseKeywords', 'suratPerintahPenyidikanDocumentLaws'])
                                                ->where('id', $suratPerintahPenyidikanDocumentId)
                                                ->first();
      
        $accident = Accident::where('id',$accidentId)->first();

        $ranks = Rank::where('is_active', true)->wherePolri()->orderBy('sort')->get();

        $positions = Position::where('is_active', true)->orderBy('sort')->get();

        $crimeTypes = CrimeType::withRelated()
            ->active()
            ->orderBy('sort')
            ->get();
        $crimeClasses = CrimeClass::withRelated()
            ->active()
            ->orderBy('sort')
            ->get();
        $crimeConstitutions = CrimeConstitution::withRelated()
            ->active()
            ->orderBy('sort')
            ->get();

        $caseClassifications = CaseClassification::where('is_active', true)->orderBy('sort')->get();

        $authorizedSignatories = Officer::withRelated()
            ->selectFullName()
            ->whereHasUserActive()
            ->hasDataComplete()
            ->where('police_id', $accident->polres_id)
            ->signatory()
            ->active()
            ->valid()
            ->orderBy('first_name')
            ->get();

        $caseKeywords = CaseKeyword::where('is_active', true)->orderBy('id')->get();

        $leaderOfficers = Officer::withRelated()
            ->selectFullName()
            ->whereHasUserActive()
            ->hasDataComplete()
            ->where('police_id', $accident->polres_id)
            ->member()
            ->valid()
            ->active()
            ->orderBy('first_name')
            ->get();

        $viewData =[
            'authorizedSignatories' => $authorizedSignatories,
            'accidentId' => $accidentId,
            'accident' => $accident,
            'suratPerintahPenyidikanDocument' => $suratPerintahPenyidikanDocument,
            'suratPerintahPenyidikanDocumentId' => $suratPerintahPenyidikanDocumentId,
            'ranks' => $ranks,
            'positions' => $positions,
            'caseKeywords' => $caseKeywords,
            'leaderOfficers' => $leaderOfficers,
            'crimeTypes' => $crimeTypes,
            'crimeClasses' => $crimeClasses,
            'crimeConstitutions' => $crimeConstitutions,
            'caseClassifications' => $caseClassifications,
            'laws' => $suratPerintahPenyidikanDocument->suratPerintahPenyidikanDocumentLaws,
            'officers' => $suratPerintahPenyidikanDocument
                ->suratPerintahPenyidikanDocumentOfficers()
                ->withRelated()
                ->get(),
        ];

       return view('docs.surat-perintah-penyidikan-document.edit', $viewData);
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
        $suratPerintahPenyidikanDocumentId = $id;

        // Define & Sanitize Text Input
        $user = Auth::user();
        $documentNumber = htmlspecialchars($request->documentNumber);
        $documentDate = Carbon::parse(htmlspecialchars($request->documentDate))->format('Y-m-d');
        $startDate = Carbon::parse(htmlspecialchars($request->startDate))->format('Y-m-d');
        $endDate = Carbon::parse(htmlspecialchars($request->endDate))->format('Y-m-d');
        $signatoryId = htmlspecialchars($request->signatory);
      
        $caseClassification = htmlspecialchars($request->caseClassification);
       
        $isExternalOfficers = ($request->isExternalOfficers == 'true') ? true : false;
        $isMovedOfficers = ($request->isMovedOfficers == 'true') ? true : false;

        $isLegacy = filter_var($request->isLegacy, FILTER_VALIDATE_BOOLEAN);

        // Check if document number already exist
        $suratPerintahPenyidikanDocument = SuratPerintahPenyidikanDocument::findOrFail($id);
        $oldDocumentNumber = $suratPerintahPenyidikanDocument->document_number;
        if (strtolower($oldDocumentNumber) != strtolower($documentNumber)) {
            $exists = SuratPerintahPenyidikanDocument::where('accident_id', $request->input('accident_id'))
                ->where('document_number', 'ILIKE', $documentNumber)
                ->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'Dokumen ' . $documentNumber . ' Sudah Anda Buat Sebelumnya.');
            }
        }

        DB::beginTransaction();
        try{
            $caseClassification = CaseClassification::where('name', $caseClassification)->first();

            // Store to database
            $suratPerintahPenyidikanDocument = SuratPerintahPenyidikanDocument::where('id', $suratPerintahPenyidikanDocumentId)->first();
            
            $suratPerintahPenyidikanDocument->update([
                'accident_id' => $accidentId,
                'document_number' => $documentNumber,
                'document_date' => $documentDate,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'case_classification' => $caseClassification->name,
                'is_legacy' => $isLegacy,
            ]);

            $suratPerintahPenyidikanDocumentId = $suratPerintahPenyidikanDocument->id;

            // LAW
            // Main Law
            $lawCrimeTypeIds = $request->lawCrimeTypeIds ?? [];
            $lawCrimeClassIds = $request->lawCrimeClassIds ?? [];
            $lawCrimeConstitutionIds = $request->lawCrimeConstitutionIds ?? [];
            $lawCrimeConstitutionChapters = $request->lawCrimeConstitutionChapters ?? [];

            $lawCrimeTypeIdCollection = Collection::make($lawCrimeTypeIds);
            $lawCrimeClassIdCollection = Collection::make($lawCrimeClassIds);
            $lawCrimeConstitutionIdCollection = Collection::make($lawCrimeConstitutionIds);
            $lawCrimeConstitutionChapterCollection = Collection::make($lawCrimeConstitutionChapters);

            $mainLaws = $lawCrimeTypeIdCollection->zip(
                                $lawCrimeClassIdCollection, 
                                $lawCrimeConstitutionIdCollection, 
                                $lawCrimeConstitutionChapterCollection,
            )->map(function ($law) {
                return [
                    'crime_type_id' => $law[0],
                    'crime_class_id' => $law[1],
                    'crime_constitution_id' => $law[2],
                    'crime_constitution_chapter' => $law[3],
                ];
            });

            $suratPerintahPenyidikanDocument->suratPerintahPenyidikanDocumentLaws()->where('flag', SuratPerintahPenyidikanDocumentLaw::getEnumOption('flag', 'MAIN') )->delete();

            foreach ($mainLaws as $mainLaw) {
                $crimeType = CrimeType::where('id', $mainLaw['crime_type_id'])->first();
                $crimeClass = CrimeClass::where('id', $mainLaw['crime_class_id'])->first();
                $constitution = CrimeConstitution::where('id', $mainLaw['crime_constitution_id'])->first();

                $suratPerintahPenyidikanDocument->suratPerintahPenyidikanDocumentLaws()->create([
                    'surat_perintah_penyidikan_document_id' => $suratPerintahPenyidikanDocumentId,
                    'crime_type_id' => $crimeType->id,
                    'crime_class_id' => $crimeClass->id,
                    'crime_constitution_id' => $constitution->id,
                    'constitution_chapter' => $mainLaw['crime_constitution_chapter'],

                    'flag' => SuratPerintahPenyidikanDocumentLaw::getEnumOption('flag', 'MAIN'),
                ]);
            }
            // Additional Law
            $lawAdditionalNames = $request->lawAdditionalNames ?? [];
            $additionalLawCollection = Collection::make($lawAdditionalNames);
            $additionalLaws = $additionalLawCollection->map(function ($additionalLaw) {
                return [
                    'name' => $additionalLaw,
                ];
            });

            $suratPerintahPenyidikanDocument->suratPerintahPenyidikanDocumentLaws()->where('flag', SuratPerintahPenyidikanDocumentLaw::getEnumOption('flag', 'ADDT'))->delete();

            foreach($additionalLaws as $additionalLaw) {
                $suratPerintahPenyidikanDocument->suratPerintahPenyidikanDocumentLaws()->create([
                    'surat_perintah_penyidikan_document_id' => $suratPerintahPenyidikanDocumentId,
                    'crime_type_id' => NULL,
                    'crime_class_id' => NULL,
                    'crime_constitution_id' => NULL,
                    'constitution' => $additionalLaw['name'],

                    'flag' => SuratPerintahPenyidikanDocumentLaw::getEnumOption('flag', 'ADDT'),
                ]);
            }


            // Signatory
            $signatory = Officer::where('id',$signatoryId)->first();
            $resortPolice = Polres::with(['polda'])->where('id',$signatory->polres_id)->first();
            $suratPerintahPenyidikanDocument->suratPerintahPenyidikanDocumentOfficers()
                ->updateOrCreate(
                    [
                        'class' => SuratPerintahPenyidikanDocumentOfficer::getEnumOption('class', 'SIGNATORY'),
                    ],
                    [
                        'surat_perintah_penyidikan_document_id' => $suratPerintahPenyidikanDocumentId,
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
                        'status' => SuratPerintahPenyidikanDocumentOfficer::getEnumOption('status', 'PRESENT'),
                        'flag' => SuratPerintahPenyidikanDocumentOfficer::getEnumOption('flag', 'INTERNAL'),
                    ]
                );
          

            // Leader
            $updateDataOfficerLeader = [];
            if($request->isPresentOfficerLeader == 'true'){
                $officerLeader = htmlspecialchars($request->officerLeader);
                $officerLeader = Officer::where('id',$officerLeader)->first();

                $resortPolice = Polres::where('id',$officerLeader->polres_id)->first();
                $regionalPolice = Polda::where('id',$officerLeader->polda_id)->first();

                $updateDataOfficerLeader = [
                    'surat_perintah_penyidikan_document_id' => $suratPerintahPenyidikanDocumentId,
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
                    'status' => SuratPerintahPenyidikanDocumentOfficer::getEnumOption('status', 'PRESENT'),
                    'class' => SuratPerintahPenyidikanDocumentOfficer::getEnumOption('class', 'LEADER'),
                    'flag' => SuratPerintahPenyidikanDocumentOfficer::getEnumOption('flag', 'INTERNAL'),
                    'insert_method' => SuratPerintahPenyidikanDocumentOfficer::getEnumOption('insert_method', 'IMPORT'),
                ];
            }else if($request->isPresentOfficerLeader == 'false'){
                    $movedOfficerLeaderRegisterNumber = htmlspecialchars($request->movedOfficerLeaderRegisterNumber);
                    $movedOfficerLeaderName = htmlspecialchars($request->movedOfficerLeaderName);
                    $movedOfficerLeaderPhone = htmlspecialchars($request->movedOfficerLeaderPhone);
                    $movedOfficerLeaderRankId = htmlspecialchars($request->movedOfficerLeaderRankId);
                    $movedOfficerLeaderRankName = htmlspecialchars($request->movedOfficerLeaderRankName);
                    $movedOfficerLeaderPositionId = htmlspecialchars($request->movedOfficerLeaderPositionId);
                    $movedOfficerLeaderPositionName = htmlspecialchars($request->movedOfficerLeaderPositionName);

                    $movedOfficerLeaderRegionalPoliceId = htmlspecialchars($request->movedOfficerLeaderRegionalPoliceId);
                    $movedOfficerLeaderResortPoliceId = htmlspecialchars($request->movedOfficerLeaderResortPoliceId);

                    $resortPolice = Polres::where('id',$movedOfficerLeaderResortPoliceId)->first();
                    $regionalPolice = Polda::where('id', $movedOfficerLeaderRegionalPoliceId)->first();

                    $updateDataOfficerLeader = [
                        'surat_perintah_penyidikan_document_id' => $suratPerintahPenyidikanDocumentId,
                        'register_number' => $movedOfficerLeaderRegisterNumber,

                        'first_title' => NULL,
                        'first_name' => $movedOfficerLeaderName,
                        'last_name' => NULL,
                        'last_title' => NULL,

                        'rank_id' => $movedOfficerLeaderRankId,
                        'position_id' => $movedOfficerLeaderPositionId,
                        'phone_number' => $movedOfficerLeaderPhone,
                        'email' => NULL,
                    
                        'police_id' => ($movedOfficerLeaderResortPoliceId) ? $movedOfficerLeaderResortPoliceId : $movedOfficerLeaderRegionalPoliceId,
                        'status' => SuratPerintahPenyidikanDocumentOfficer::getEnumOption('status', 'PAST'),
                        'class' => SuratPerintahPenyidikanDocumentOfficer::getEnumOption('class', 'LEADER'),
                        'flag' => SuratPerintahPenyidikanDocumentOfficer::getEnumOption('flag', 'MOVED'),
                        'insert_method' => SuratPerintahPenyidikanDocumentOfficer::getEnumOption('insert_method', 'MANUAL'),
                    ];

            }
            $suratPerintahPenyidikanDocument->suratPerintahPenyidikanDocumentOfficers()
                ->updateOrCreate(
                    [
                        'class' => SuratPerintahPenyidikanDocumentOfficer::getEnumOption('class', 'LEADER'),
                    ],
                    $updateDataOfficerLeader,
                );
             

            // Internal Officer
            $internalOfficers = $request->internalOfficers ?? [];
            foreach($internalOfficers as $internalOfficer){
                $registerNumber = $internalOfficer;
                $officer = Officer::where('register_number',$registerNumber)->first();

                if($officer){
                    $resortPolice = Polres::where('id',$officer->polres_id)->first();
                    $regionalPolice = Polda::where('id',$officer->polda_id)->first();
                    $suratPerintahPenyidikanDocument->suratPerintahPenyidikanDocumentOfficers()
                        ->updateOrcreate(
                            [
                                'register_number'=> $officer->register_number,
                                'class' => SuratPerintahPenyidikanDocumentOfficer::getEnumOption('class', 'MEMBER'),
                                'flag' => SuratPerintahPenyidikanDocumentOfficer::getEnumOption('flag', 'INTERNAL'),
                            ],
                            [
                                'surat_perintah_penyidikan_document_id' => $suratPerintahPenyidikanDocumentId,
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
                                'status' => SuratPerintahPenyidikanDocumentOfficer::getEnumOption('status', 'PRESENT'),
                                'class' => SuratPerintahPenyidikanDocumentOfficer::getEnumOption('class', 'MEMBER'),
                                'flag' => SuratPerintahPenyidikanDocumentOfficer::getEnumOption('flag', 'INTERNAL'),
                            ]
                        );
                }
            }
            // Hapus data officer yang tidak ada di $internalOfficers
            $suratPerintahPenyidikanDocument->suratPerintahPenyidikanDocumentOfficers()
                ->where('flag', SuratPerintahPenyidikanDocumentOfficer::getEnumOption('flag', 'INTERNAL'))
                ->where('class', SuratPerintahPenyidikanDocumentOfficer::getEnumOption('class', 'MEMBER'))
                ->whereNotIn('register_number', $internalOfficers)
                ->delete();
   

            // External Officer
            if($isExternalOfficers == true){
                $externalOfficers = $request->externalOfficers ?? [];
                foreach($externalOfficers as $externalOfficer){
                    $registerNumber = $externalOfficer;
                    $officer = Officer::where('register_number',$registerNumber)->first();

                    if($officer){
                        $resortPolice = Polres::where('id',$officer->polres_id)->first();
                        $regionalPolice = Polda::where('id',$officer->polda_id)->first();
                        $suratPerintahPenyidikanDocument->suratPerintahPenyidikanDocumentOfficers()
                            ->updateOrCreate(
                                [
                                    'register_number'=> $officer->register_number,
                                    'class' => SuratPerintahPenyidikanDocumentOfficer::getEnumOption('class', 'MEMBER'),
                                    'flag' => SuratPerintahPenyidikanDocumentOfficer::getEnumOption('flag', 'EXTERNAL'),
                                ],
                                [
                                    'surat_perintah_penyidikan_document_id' => $suratPerintahPenyidikanDocumentId,
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
                                    'status' => SuratPerintahPenyidikanDocumentOfficer::getEnumOption('status', 'EXTERNAL'),
                                    'class' => SuratPerintahPenyidikanDocumentOfficer::getEnumOption('class', 'MEMBER'),
                                    'flag' => SuratPerintahPenyidikanDocumentOfficer::getEnumOption('flag', 'EXTERNAL'),
                                ]
                            );
                    }
                }
                // Hapus data officer yang tidak ada di $externalOfficers
                $suratPerintahPenyidikanDocument->suratPerintahPenyidikanDocumentOfficers()
                    ->where('flag', SuratPerintahPenyidikanDocumentOfficer::getEnumOption('flag', 'EXTERNAL'))
                    ->where('class', SuratPerintahPenyidikanDocumentOfficer::getEnumOption('class', 'MEMBER'))
                    ->whereNotIn('register_number', $externalOfficers)
                    ->delete();
            }else{
                // Hapus data officer yang tidak ada di $externalOfficers
                $suratPerintahPenyidikanDocument->suratPerintahPenyidikanDocumentOfficers()
                ->where('flag', SuratPerintahPenyidikanDocumentOfficer::getEnumOption('flag', 'EXTERNAL'))
                ->where('class', SuratPerintahPenyidikanDocumentOfficer::getEnumOption('class', 'MEMBER'))
                ->whereNotIn('register_number', [])
                ->delete();
            }

            // Moved Officer
            if($isMovedOfficers == true){
                $movedOfficers = $request->movedOfficers ?? [];
                foreach($movedOfficers as $movedOfficer){
                    $registerNumber = $movedOfficer;
                    $officer = Officer::where('register_number',$registerNumber)->first();

                    if($officer){
                        $resortPolice = Polres::where('id',$officer->polres_id)->first();
                        $regionalPolice = Polda::where('id',$officer->polda_id)->first();
                        $suratPerintahPenyidikanDocument->suratPerintahPenyidikanDocumentOfficers()
                            ->updateOrCreate(
                                [
                                    'register_number'=> $officer->register_number,
                                    'class' => SuratPerintahPenyidikanDocumentOfficer::getEnumOption('class', 'MEMBER'),
                                    'flag' => SuratPerintahPenyidikanDocumentOfficer::getEnumOption('flag', 'MOVED'),
                                ],
                                [
                                    'surat_perintah_penyidikan_document_id' => $suratPerintahPenyidikanDocumentId,
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
                                    'status' => SuratPerintahPenyidikanDocumentOfficer::getEnumOption('status', 'PAST'),
                                    'class' => SuratPerintahPenyidikanDocumentOfficer::getEnumOption('class', 'MEMBER'),
                                    'flag' => SuratPerintahPenyidikanDocumentOfficer::getEnumOption('flag', 'MOVED'),
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
                foreach($manualMovedOfficers as $manualMovedOfficer){
                        
                    $resortPolice = Polres::where('id', $manualMovedOfficer['resort_police_id'])->first();
                    $regionalPolice = Polda::where('id', $manualMovedOfficer['regional_police_id'])->first();
                        
                    $rank = Rank::where('id', ($manualMovedOfficer['rank_id'] != NULL && $manualMovedOfficer['rank_id'] != 'null' && $manualMovedOfficer['rank_id'] != '-') ? $manualMovedOfficer['rank_id'] : NULL )->first();
                    
                    $suratPerintahPenyidikanDocument->suratPerintahPenyidikanDocumentOfficers()
                        ->updateOrCreate(
                            [
                                'register_number'=> $manualMovedOfficer['register_number'],
                                'class' => SuratPerintahPenyidikanDocumentOfficer::getEnumOption('class', 'MEMBER'),
                                'flag' => SuratPerintahPenyidikanDocumentOfficer::getEnumOption('flag', 'MOVED'),
                            ],
                            [
                                'surat_perintah_penyidikan_document_id' => $suratPerintahPenyidikanDocumentId,
                                'register_number' => $manualMovedOfficer['register_number'],

                                'first_title' => NULL,
                                'first_name' => $manualMovedOfficer['first_name'],
                                'last_name' => $manualMovedOfficer['last_name'],
                                'last_title' => NULL,

                                'rank_id' => $rank->id,
                                'position_id' => $manualMovedOfficer['position_id'],
                                'phone_number' => $manualMovedOfficer['phone'],
                                'email' => null,
                           
                                'police_id' => ($manualMovedOfficer['resort_police_id']) ? $manualMovedOfficer['resort_police_id'] : $manualMovedOfficer['regional_police_id'],
                                'status' => SuratPerintahPenyidikanDocumentOfficer::getEnumOption('status', 'PAST'),
                                'class' => SuratPerintahPenyidikanDocumentOfficer::getEnumOption('class', 'MEMBER'),
                                'flag' => SuratPerintahPenyidikanDocumentOfficer::getEnumOption('flag', 'MOVED'),
                                'insert_method' => SuratPerintahPenyidikanDocumentOfficer::getEnumOption('insert_method', 'MANUAL'),
                            ]
                        );
                }

                $movedOfficersCollection = Collection::make($movedOfficers);
                $manualMovedOfficerRegisterNumbersCollection = $manualMovedOfficerRegisterNumbersCollection;
                $movedOfficerRegisterNumberMergeCollection = $movedOfficersCollection->concat($manualMovedOfficerRegisterNumbersCollection);
 
                // Delete Moved Officer Yang tidak ada di list
                $suratPerintahPenyidikanDocument->suratPerintahPenyidikanDocumentOfficers()
                    ->where('flag', SuratPerintahPenyidikanDocumentOfficer::getEnumOption('flag', 'MOVED'))
                    ->where('class', SuratPerintahPenyidikanDocumentOfficer::getEnumOption('class', 'MEMBER'))
                    ->whereNotIn('register_number', $movedOfficerRegisterNumberMergeCollection->toArray())
                    ->delete();
            }else{
                $suratPerintahPenyidikanDocument->suratPerintahPenyidikanDocumentOfficers()
                    ->where('flag', SuratPerintahPenyidikanDocumentOfficer::getEnumOption('flag', 'MOVED'))
                    ->where('class', SuratPerintahPenyidikanDocumentOfficer::getEnumOption('class', 'MEMBER'))
                    ->whereNotIn('register_number', [])
                    ->delete();
            }
            

            // Case Keywords
            $caseKeywords = $request->keywords ?? [];
            foreach($caseKeywords as $caseKeyword){
                $caseKeyword = CaseKeyword::where('id',$caseKeyword)->first();

                $suratPerintahPenyidikanDocument->suratPerintahPenyidikanDocumentCaseKeywords()
                    ->updateOrCreate(
                        [
                            'surat_perintah_penyidikan_document_id' => $suratPerintahPenyidikanDocumentId,
                            'keyword_id' => $caseKeyword->id,
                        ],
                        [
                            'surat_perintah_penyidikan_document_id' => $suratPerintahPenyidikanDocumentId,
                            'keyword_id' => $caseKeyword->id,
                            'keyword' => $caseKeyword->name,
                        ]
                    );
            }
            $suratPerintahPenyidikanDocument->suratPerintahPenyidikanDocumentCaseKeywords()
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
        $suratPerintahPenyidikanDocumentId = $id;

        DB::beginTransaction();
        try{
            // Delete from database
            $suratPerintahPenyidikanDocument = SuratPerintahPenyidikanDocument::where('id', $suratPerintahPenyidikanDocumentId)->first();
            $suratPerintahPenyidikanDocument->delete();

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
        $suratPerintahPenyidikanDocumentId = $id;

        // Get data from database
        $suratPerintahPenyidikanDocument = SuratPerintahPenyidikanDocument::with(['suratPerintahPenyidikanDocumentOfficers', 'suratPerintahPenyidikanDocumentCaseKeywords', 'suratPerintahPenyidikanDocumentLaws'])->where('id', $suratPerintahPenyidikanDocumentId)->first();
        $officers = $suratPerintahPenyidikanDocument->suratPerintahPenyidikanDocumentOfficers->where('class','!=',SuratPerintahPenyidikanDocumentOfficer::getEnumOption('class', 'SIGNATORY'))->sortBy('class');
        $leader = $suratPerintahPenyidikanDocument->suratPerintahPenyidikanDocumentOfficers->where('class','=',SuratPerintahPenyidikanDocumentOfficer::getEnumOption('class', 'LEADER'))->first();
        $signatory = $suratPerintahPenyidikanDocument->suratPerintahPenyidikanDocumentOfficers->where('class','=',SuratPerintahPenyidikanDocumentOfficer::getEnumOption('class', 'SIGNATORY'))->first();

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
        
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('word-template/surat_perintah_penyidikan.docx');

        if(isset($signatory->position)){
            if($signatory->position->position_cluster_id == '1'){
                $templateProcessor->setValue('officer_signature_title_text', $signatureTitleText['KAPOLRES']);
                $templateProcessor->setValue('officer_signature_position_head_text', '');
            }else if($signatory->position->position_cluster_id == '9'){
                $templateProcessor->setValue('officer_signature_title_text', $signatureTitleText['NO_DIRLANTAS']);
                $templateProcessor->setValue('officer_signature_position_head_text', $signatoryPositionHeadText['NO_DIRLANTAS']);
            }else{
                $templateProcessor->setValue('officer_signature_title_text', $signatureTitleText['NO_KAPOLRES']);
                $templateProcessor->setValue('officer_signature_position_head_text', $signatoryPositionHeadText['NO_KAPOLRES']);
            }
        }

        $resorPolice = $accident->polres;
        $resorPoliceFullName = (in_array($resorPolice->id, ['1114'])) ? 'DIREKTORAT LALU LINTAS' : 'RESOR ' . strtoupper($resorPolice->full_name);

        $templateProcessor->cloneBlock('block_officers', 2, true, false, $blockOfficers);
        $templateProcessor->setValue('letter_number',  $suratPerintahPenyidikanDocument->document_number);
        $templateProcessor->setValue('letter_end_date', ($suratPerintahPenyidikanDocument->end_date != NULL) ? 'tanggal ' . Carbon::parse($suratPerintahPenyidikanDocument->end_date)->locale('id')->translatedFormat('d F Y') : 'selesai');
        $templateProcessor->setValue('issued_date', Carbon::parse( $suratPerintahPenyidikanDocument->document_date)->locale('id')->translatedFormat('d F Y'));
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

        $filename = 'generate/' . Str::uuid() . ' - Surat Perintah Penyidikan - Resor ' . $accident->polres->full_name;
        $templateProcessor->saveAs($filename.'.docx');
        return response()->download($filename.'.docx')->deleteFileAfterSend(true);
    }

    // =====( API )=====
    public function getLeaderOfficer(Request $request)
    {
        $accidentId = $request->accident_id;
        $searchedLeaderOfficerRegisterNumber = $request->registerNumber;

        try{
            $accident = Accident::with(['polres'])->where('id',$accidentId)->first();

            $getOldNewPolresIds = $this->getOldNewPolresIds($accident->polres_id);

            $leaderOfficer = Officer::withRelated()
                ->selectFullName()
                ->whereIn('police_id', $getOldNewPolresIds)
                ->whereHasUserActive()
                ->hasDataComplete()
                ->member()
                ->active()
                ->valid()
                ->where('officers.register_number', '=', $searchedLeaderOfficerRegisterNumber)
                ->first();

            if(empty($leaderOfficer)){
                return response()->json([
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'Data tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'data' => $leaderOfficer
            ], 200);
        }catch(\Exception $e){
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getInternalOfficers(Request $request)
    {
        $accidentId = $request->accident_id;
        $selectedLeaderOfficerRegisterNumber = $request->selectedLeaderOfficerRegisterNumber;

        try{
            $accident = Accident::with(['polres'])->where('id',$accidentId)->first();

            $getOldNewPolresIds = $this->getOldNewPolresIds($accident->polres_id);

            $officers = Officer::withRelated()
                ->selectFullName()
                ->whereHasUserActive()
                ->hasDataComplete()
                ->member()
                ->active()
                ->valid()
                ->whereIn('officers.police_id', $getOldNewPolresIds)
                ->where('officers.register_number', '!=', $selectedLeaderOfficerRegisterNumber)
                ->get();

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'data' => $officers
            ], 200);
        }catch(\Exception $e){
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

        try{
            $accident = Accident::with(['polres'])->where('id',$accidentId)->first();

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

            if(empty($officers)){
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
        }catch(\Exception $e){
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

        try{
            $accident = Accident::with(['polres'])->where('id',$accidentId)->first();
            
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

            if(empty($officers)){
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
        }catch(\Exception $e){
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

        try{
            switch($policeClass){
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
        }catch(\Exception $e){
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => 'Terjadi kesalahan pada sistem'
            ], 500);
        }
    }
  
    public function validateRequestForm(Request $request)
    {
        try{
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
        }catch(\Exception $e){
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
            'caseClassification' => 'required',
            'documentDate' => 'required',
            'startDate' => 'required',
            'endDate' => 'required',
            'signatory' => 'required',
            'lawCrimeTypeIds' => 'required',

            'officerLeader' => 'required_if:isPresentOfficerLeader,true',
            'movedOfficerLeaderRegisterNumber' => 'required_if:isPresentOfficerLeader,false',

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
            'documentDate.required' => 'Tanggal Dokumen Sidik harus diisi',
            'startDate.required' => 'Tanggal Mulai Sidik harus diisi',
            'endDate.required' => 'Tanggal Akhir Sidik harus diisi',
            'signatory.required' => 'Yang Menandatangani harus diisi',

            'lawCrimeTypeIds.required' => 'Undang-Undang yang Dikenakan harus diisi',

            'officerLeader.required_if' => 'Ketua Tim Penyidik harus diisi',
            'movedOfficerLeaderRegisterNumber.required_if' => 'Ketua Tim Penyidik harus diisi',

            'internalOfficers.required_without_all' => 'Anggota Tim Penyidik harus diisi',
            'movedOfficers.required' => 'Anggota Tim Penyidik yang telah pindah harus diisi',
            'manualMovedOfficerRegisterNumbers.required' => 'Anggota Tim Penyidik yang telah pindah harus diisi',
            'externalOfficers.required_if' => 'Anggota Tim Penyidik dari luar harus diisi',
            'manualMovedOfficerFirstNames.*.max' => 'Nama Depan Anggota Tim Penyidik yang telah pindah maksimal 255 karakter',
            'manualMovedOfficerLastNames.*.max' => 'Nama Belakang Anggota Tim Penyidik yang telah pindah maksimal 255 karakter',
        ]);
    }
}
