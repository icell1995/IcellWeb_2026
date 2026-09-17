<?php

namespace App\Http\Controllers\Docs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Webpatser\Uuid\Uuid;
use Carbon\Carbon;

use App\Services\Doc\DocService;
use App\Traits\DocsOfficersTraits;
use App\Helpers\PeopleNameHelper;

use App\Models\Accident;
use App\Models\Officer;
use App\Models\Suspect;
use App\Models\Lib\Court;
use App\Models\Lib\Rank;
use App\Models\Lib\Position;
use App\Models\Lib\IdentityType;
use App\Models\Lib\Gender;
use App\Models\Lib\Ethnic;
use App\Models\Lib\Job;
use App\Models\Lib\Religion;
use App\Models\Lib\Education;
use App\Models\Lib\MaritalStatus;
use App\Models\Lib\Location;
use App\Models\Lib\Nationality;
use App\Models\Lib\DocumentClassification;
use App\Models\Lib\Prosecutor;

use App\Models\Doc\SuratPermohonanPenetapanDiversiDocument\SuratPermohonanPenetapanDiversiDocument;
use App\Models\Doc\SuratPermohonanPenetapanDiversiDocument\SuratPermohonanPenetapanDiversiDocumentOfficer;
use App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument;

class SuratPermohonanPenetapanDiversiDocumentController extends Controller
{
    use DocsOfficersTraits;

    protected $docService;

    public function __construct(DocService $docService)
    {
        $this->docService = $docService;
    }

    public function index(Request $request)
    {
        $accidentId = $request->query('accident_id');
        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId]);
    }

    public function create()
    {
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $accident = Accident::with(['polres', 'polres.polda', 'police'])->where('id', $accidentId)->firstOrFail();

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

        $ranks = Rank::where('is_active', true)->wherePolri()->orderBy('sort')->get();

        $positions = Position::whereIn('police_id', [$accident->polres_id, $accident->polres->parent->id ?? null])
            ->where('is_active', true)
            ->orderBy('sort')
            ->get();

        $suratPerintahPenyidikanDocuments = SuratPerintahPenyidikanDocument::with('suratPerintahPenyidikanDocumentLaws.crimeConstitution')
            ->where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->get();

        $suspects = Suspect::with([
            'identityType',
            'gender',
            'job',
            'religion',
            'village',
            'district',
            'regency',
            'province',
        ])->where('accident_id', $accidentId)
            ->where('flag', Suspect::getEnumOption('flag', 'TERSANGKA'))
            ->get();

        $courts = Court::where('is_active', true)
            ->orderBy('name')
            ->orderByRaw("CASE WHEN class = 'NEGERI' THEN 0 ELSE 1 END")
            ->orderByRaw("CASE WHEN full_name IS NOT NULL THEN 0 ELSE 1 END")
            ->orderByRaw("CASE WHEN regency_id IS NOT NULL THEN 0 ELSE 1 END")
            ->get()
            ->unique('name');
        $documentClassifications = DocumentClassification::where('is_active', true)->orderBy('sort')->get();
        $identityTypes = IdentityType::where('is_active', true)->orderBy('sort')->get();
        $genders = Gender::where('is_active', true)->orderBy('sort')->get();
        $religions = Religion::where('is_active', true)->orderBy('sort')->get();
        $jobs = Job::where('is_active', true)->orderBy('sort')->get();
        $nationalities = Nationality::where('is_active', true)->orderBy('sort')->get();

        $viewData = [
            'accidentId'                        => $accidentId,
            'accident'                          => $accident,
            'authorizedSignatories'             => $authorizedSignatories,
            'ranks'                             => $ranks,
            'positions'                         => $positions,
            'suratPerintahPenyidikanDocuments'  => $suratPerintahPenyidikanDocuments,
            'suspects'                          => $suspects,
            'courts'                            => $courts,
            'documentClassifications'           => $documentClassifications,
            'identityTypes'                     => $identityTypes,
            'genders'                           => $genders,
            'religions'                         => $religions,
            'jobs'                              => $jobs,
            'nationalities'                     => $nationalities,
        ];

        return view('docs.surat-permohonan-penetapan-diversi-document.create', $viewData);
    }

    public function store(Request $request)
    {
        $validator = $this->validateForm($request);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $accidentId = htmlspecialchars($request->input('accidentId') ?? $request->query('accident_id'));
        $documentNumber = htmlspecialchars($request->input('documentNumber'));
        $documentDate = htmlspecialchars($request->input('documentDate'));
        $documentClassificationId = htmlspecialchars($request->input('documentClassification'));
        $appendix = htmlspecialchars($request->input('appendix'));
        $perihal = htmlspecialchars($request->input('perihal'));
        $courtId = htmlspecialchars($request->input('courtId'));
        $suratPerintahPenyidikanDocumentId = htmlspecialchars($request->input('suratPerintahPenyidikanDocument'));
        $agreementDate = htmlspecialchars($request->input('agreementDate'));

        $referenceLaw = '';
        if (!empty($suratPerintahPenyidikanDocumentId)) {
            $spSidik = SuratPerintahPenyidikanDocument::with('suratPerintahPenyidikanDocumentLaws.crimeConstitution')->find($suratPerintahPenyidikanDocumentId);
            if ($spSidik && $spSidik->suratPerintahPenyidikanDocumentLaws) {
                $lawParts = [];
                foreach ($spSidik->suratPerintahPenyidikanDocumentLaws as $law) {
                    if ($law->flag == 'MAIN') {
                        $cName = $law->crimeConstitution->name ?? '';
                        $cChap = $law->constitution_chapter ?? '';
                        $lawParts[] = trim($cChap . ' ' . $cName);
                    } elseif ($law->flag == 'ADDITIONAL') {
                        $lawParts[] = trim($law->constitution ?? '');
                    }
                }
                $referenceLaw = implode(', ', array_filter($lawParts));
            }
        }
        $suspectId = htmlspecialchars($request->input('suspectId'));
        $meetingDate = htmlspecialchars($request->input('meetingDate'));
        $agreementResultType = htmlspecialchars($request->input('agreementResultType', 'ORANG_TUA'));
        $agreementInstitution = htmlspecialchars($request->input('agreementInstitution'));
        $agreementDurationMonths = htmlspecialchars($request->input('agreementDurationMonths'));
        $signatoryId = htmlspecialchars($request->input('signatory'));
        $carbonCopies = $request->input('carbonCopies', []);

        // Filter carbon copies
        $filteredCarbonCopies = [];
        if (is_array($carbonCopies)) {
            foreach ($carbonCopies as $cc) {
                if (!empty(trim($cc))) {
                    $filteredCarbonCopies[] = trim($cc);
                }
            }
        }

        // Cek duplikasi nomor dokumen pada perkara ini
        $exists = SuratPermohonanPenetapanDiversiDocument::where('accident_id', $accidentId)
            ->where('document_number', 'ILIKE', $documentNumber)
            ->exists();
        if ($exists) {
            return redirect()->back()->with('error', 'Dokumen ' . $documentNumber . ' sudah dibuat sebelumnya.')->withInput();
        }

        $court = Court::find($courtId);
        $courtName = $court ? $court->name : '';
        $cleanCourtPlace = preg_replace('/^(PENGADILAN\s+NEGERI|PENGADILAN)\s+/i', '', $courtName);
        $courtPlace = !empty($cleanCourtPlace) ? ucwords(strtolower(trim($cleanCourtPlace))) : 'Tempat';

        $docClassification = DocumentClassification::find($documentClassificationId);
        $classificationName = $docClassification ? $docClassification->name : 'BIASA';

        $suspect = Suspect::with(['gender', 'job', 'religion', 'village', 'district', 'regency', 'province'])->find($suspectId);
        $suspectAge = null;
        if ($suspect && !empty($suspect->birth_date)) {
            try {
                $birthDate = Carbon::parse($suspect->birth_date);
                $targetDate = !empty($documentDate) ? Carbon::parse($documentDate) : Carbon::now();
                $diff = $birthDate->diff($targetDate);
                $suspectAge = $diff->y . ' tahun ' . $diff->m . ' bulan ' . $diff->d . ' hari';
            } catch (\Exception $e) {
                $suspectAge = null;
            }
        }

        $genderInput = $request->input('childGender');
        $genderModel = Gender::find($genderInput);
        $genderName = $genderModel ? $genderModel->name : ($suspect && $suspect->gender ? $suspect->gender->name : $genderInput);
        $genderId = $genderModel ? $genderModel->id : ($suspect ? $suspect->gender_id : $genderInput);

        $religionInput = $request->input('childReligion');
        $religionModel = Religion::find($religionInput);
        $religionName = $religionModel ? $religionModel->name : ($suspect && $suspect->religion ? $suspect->religion->name : $religionInput);
        $religionId = $religionModel ? $religionModel->id : ($suspect ? $suspect->religion_id : $religionInput);

        $jobInput = $request->input('childJob');
        $jobModel = Job::find($jobInput);
        $jobName = $jobModel ? $jobModel->name : ($suspect && $suspect->job ? $suspect->job->name : $jobInput);
        $jobId = $jobModel ? $jobModel->id : ($suspect ? $suspect->job_id : $jobInput);

        $natInput = $request->input('childNationality');
        $natModel = Nationality::find($natInput);
        $natName = $natModel ? $natModel->name : ($natInput == '1' || str_contains(strtoupper((string)$natInput), 'INDO') ? 'Indonesia' : 'Indonesia');
        $natId = $natModel ? $natModel->id : '1';

        $idTypeInput = $request->input('childIdentityType');
        $idTypeModel = IdentityType::find($idTypeInput);
        $idTypeName = $idTypeModel ? $idTypeModel->name : '';
        $idTypeId = $idTypeModel ? $idTypeModel->id : $idTypeInput;

        $payload = [
            'classification_id'         => $documentClassificationId,
            'classification_name'       => $classificationName,
            'appendix'                  => $appendix,
            'perihal'                   => $perihal,
            'court_id'                  => $courtId,
            'court_name'                => $courtName,
            'court_place'               => $courtPlace,
            'surat_perintah_penyidikan_document_id' => $suratPerintahPenyidikanDocumentId,
            'agreement_date'            => $agreementDate,
            'reference_law'             => $referenceLaw,
            'suspect_id'                => $suspectId,
            'suspect_name'              => $request->input('childName', $suspect->name ?? ''),
            'suspect_identity_type_id'  => $idTypeId,
            'suspect_identity_type'     => $idTypeName,
            'suspect_identity_number'   => $request->input('childIdentityNumber', $suspect->identity_number ?? ''),
            'suspect_nationality_id'    => $natId,
            'suspect_nationality'       => $natName,
            'suspect_gender_id'         => $genderId,
            'suspect_gender'            => $genderName,
            'suspect_birth_place'       => $request->input('childBirthPlace', $suspect->birth_place ?? ''),
            'suspect_birth_date'        => $request->input('childBirthDate', $suspect->birth_date ?? ''),
            'suspect_age_year'          => $request->input('childAgeYear'),
            'suspect_age_month'         => $request->input('childAgeMonth'),
            'suspect_age_day'           => $request->input('childAgeDay'),
            'suspect_age'               => $suspectAge,
            'suspect_job_id'            => $jobId,
            'suspect_job'               => $jobName,
            'suspect_religion_id'       => $religionId,
            'suspect_religion'          => $religionName,
            'suspect_address'           => $request->input('childAddress', $suspect->address ?? ''),
            'meeting_date'              => $meetingDate,
            'agreement_result_type'     => $agreementResultType,
            'agreement_institution'     => $agreementInstitution,
            'agreement_duration_months' => $agreementDurationMonths,
            'carbon_copies'             => $filteredCarbonCopies,
            'signatory_id'              => $signatoryId,
        ];

        DB::beginTransaction();
        try {
            $document = SuratPermohonanPenetapanDiversiDocument::create([
                'accident_id'                           => $accidentId,
                'suspect_id'                            => $suspectId,
                'surat_perintah_penyidikan_document_id' => $suratPerintahPenyidikanDocumentId,
                'court_id'                              => $courtId,
                'court_name'                            => $courtName,
                'court_place'                           => $courtPlace,
                'document_number'                       => $documentNumber,
                'document_date'                         => $documentDate,
                'payload'                               => $payload,
                'messages'                              => $suspectAge ? ['suspect_age' => $suspectAge] : null,
                'created_by_user_id'                    => Auth::id(),
            ]);

            // Simpan Penandatangan (Signatory)
            $signatory = Officer::where('id', $signatoryId)->first();
            if ($signatory) {
                $document->suratPermohonanPenetapanDiversiDocumentOfficers()->create([
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
                    'status'          => SuratPermohonanPenetapanDiversiDocumentOfficer::getEnumOption('status', 'PRESENT'),
                    'class'           => SuratPermohonanPenetapanDiversiDocumentOfficer::getEnumOption('class', 'SIGNATORY'),
                    'flag'            => SuratPermohonanPenetapanDiversiDocumentOfficer::getEnumOption('flag', 'INTERNAL'),
                    'insert_method'   => SuratPermohonanPenetapanDiversiDocumentOfficer::getEnumOption('insert_method', 'IMPORT'),
                ]);
            }

            // Tandai tersangka sebagai anak
            if (!empty($suspectId)) {
                Suspect::where('id', $suspectId)->update(['is_child' => true]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan dokumen: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
            ->with('success', 'Dokumen Surat Permohonan Penetapan Diversi berhasil dibuat.');
    }

    public function show($id)
    {
        $document = SuratPermohonanPenetapanDiversiDocument::withRelated()->where('id', $id)->firstOrFail();
        $accidentId = request()->query('accident_id') ?? $document->accident_id;
        return redirect()->route('doc.surat-permohonan-penetapan-diversi-document.edit', ['id' => $id, 'accident_id' => $accidentId]);
    }

    public function edit($id)
    {
        $document = SuratPermohonanPenetapanDiversiDocument::with([
            'suratPermohonanPenetapanDiversiDocumentOfficers',
            'suspect',
            'court',
            'suratPerintahPenyidikanDocument',
        ])->where('id', $id)->firstOrFail();

        $accidentId = htmlspecialchars(request()->query('accident_id') ?? $document->accident_id);
        $accident = Accident::with(['polres', 'polres.polda', 'police'])->where('id', $accidentId)->firstOrFail();

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

        $ranks = Rank::where('is_active', true)->wherePolri()->orderBy('sort')->get();

        $positions = Position::whereIn('police_id', [$accident->polres_id, $accident->polres->parent->id ?? null])
            ->where('is_active', true)
            ->orderBy('sort')
            ->get();

        $suratPerintahPenyidikanDocuments = SuratPerintahPenyidikanDocument::with('suratPerintahPenyidikanDocumentLaws.crimeConstitution')
            ->where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->get();

        $suspects = Suspect::with([
            'identityType',
            'gender',
            'job',
            'religion',
            'village',
            'district',
            'regency',
            'province',
        ])->where('accident_id', $accidentId)
            ->where('flag', Suspect::getEnumOption('flag', 'TERSANGKA'))
            ->get();

        $courts = Court::where('is_active', true)
            ->orderBy('name')
            ->orderByRaw("CASE WHEN class = 'NEGERI' THEN 0 ELSE 1 END")
            ->orderByRaw("CASE WHEN full_name IS NOT NULL THEN 0 ELSE 1 END")
            ->orderByRaw("CASE WHEN regency_id IS NOT NULL THEN 0 ELSE 1 END")
            ->get();

        $selectedCourtId = $document->court_id ?? ($document->payload['court_id'] ?? null);
        if (!empty($selectedCourtId)) {
            $currentSelected = $courts->firstWhere('id', $selectedCourtId);
            $uniqueCourts = $courts->unique('name');
            if ($currentSelected && !$uniqueCourts->contains('id', $currentSelected->id)) {
                $uniqueCourts = $uniqueCourts->reject(function ($c) use ($currentSelected) {
                    return $c->name === $currentSelected->name;
                })->push($currentSelected)->sortBy('name');
            }
            $courts = $uniqueCourts;
        } else {
            $courts = $courts->unique('name');
        }
        $documentClassifications = DocumentClassification::where('is_active', true)->orderBy('sort')->get();
        $identityTypes = IdentityType::where('is_active', true)->orderBy('sort')->get();
        $genders = Gender::where('is_active', true)->orderBy('sort')->get();
        $religions = Religion::where('is_active', true)->orderBy('sort')->get();
        $jobs = Job::where('is_active', true)->orderBy('sort')->get();
        $nationalities = Nationality::where('is_active', true)->orderBy('sort')->get();

        $currentSignatory = $document->suratPermohonanPenetapanDiversiDocumentOfficers
            ->where('class', SuratPermohonanPenetapanDiversiDocumentOfficer::getEnumOption('class', 'SIGNATORY'))
            ->first();

        $viewData = [
            'document'                          => $document,
            'accidentId'                        => $accidentId,
            'accident'                          => $accident,
            'authorizedSignatories'             => $authorizedSignatories,
            'ranks'                             => $ranks,
            'positions'                         => $positions,
            'suratPerintahPenyidikanDocuments'  => $suratPerintahPenyidikanDocuments,
            'suspects'                          => $suspects,
            'courts'                            => $courts,
            'documentClassifications'           => $documentClassifications,
            'identityTypes'                     => $identityTypes,
            'genders'                           => $genders,
            'religions'                         => $religions,
            'jobs'                              => $jobs,
            'nationalities'                     => $nationalities,
            'currentSignatory'                  => $currentSignatory,
        ];

        return view('docs.surat-permohonan-penetapan-diversi-document.edit', $viewData);
    }

    public function update(Request $request, $id)
    {
        $document = SuratPermohonanPenetapanDiversiDocument::where('id', $id)->firstOrFail();
        $accidentId = htmlspecialchars($request->input('accidentId') ?? $request->query('accident_id') ?? $document->accident_id);

        $validator = $this->validateForm($request, $id);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $documentNumber = htmlspecialchars($request->input('documentNumber'));
        $documentDate = htmlspecialchars($request->input('documentDate'));
        $documentClassificationId = htmlspecialchars($request->input('documentClassification'));
        $appendix = htmlspecialchars($request->input('appendix'));
        $perihal = htmlspecialchars($request->input('perihal'));
        $courtId = htmlspecialchars($request->input('courtId'));
        $suratPerintahPenyidikanDocumentId = htmlspecialchars($request->input('suratPerintahPenyidikanDocument'));
        $agreementDate = htmlspecialchars($request->input('agreementDate'));

        $referenceLaw = '';
        if (!empty($suratPerintahPenyidikanDocumentId)) {
            $spSidik = SuratPerintahPenyidikanDocument::with('suratPerintahPenyidikanDocumentLaws.crimeConstitution')->find($suratPerintahPenyidikanDocumentId);
            if ($spSidik && $spSidik->suratPerintahPenyidikanDocumentLaws) {
                $lawParts = [];
                foreach ($spSidik->suratPerintahPenyidikanDocumentLaws as $law) {
                    if ($law->flag == 'MAIN') {
                        $cName = $law->crimeConstitution->name ?? '';
                        $cChap = $law->constitution_chapter ?? '';
                        $lawParts[] = trim($cChap . ' ' . $cName);
                    } elseif ($law->flag == 'ADDITIONAL') {
                        $lawParts[] = trim($law->constitution ?? '');
                    }
                }
                $referenceLaw = implode(', ', array_filter($lawParts));
            }
        }
        $suspectId = htmlspecialchars($request->input('suspectId'));
        $meetingDate = htmlspecialchars($request->input('meetingDate'));
        $agreementResultType = htmlspecialchars($request->input('agreementResultType', 'ORANG_TUA'));
        $agreementInstitution = htmlspecialchars($request->input('agreementInstitution'));
        $agreementDurationMonths = htmlspecialchars($request->input('agreementDurationMonths'));
        $signatoryId = htmlspecialchars($request->input('signatory'));
        $carbonCopies = $request->input('carbonCopies', []);

        // Filter carbon copies
        $filteredCarbonCopies = [];
        if (is_array($carbonCopies)) {
            foreach ($carbonCopies as $cc) {
                if (!empty(trim($cc))) {
                    $filteredCarbonCopies[] = trim($cc);
                }
            }
        }

        $court = Court::find($courtId);
        $courtName = $court ? $court->name : '';
        $cleanCourtPlace = preg_replace('/^(PENGADILAN\s+NEGERI|PENGADILAN)\s+/i', '', $courtName);
        $courtPlace = !empty($cleanCourtPlace) ? ucwords(strtolower(trim($cleanCourtPlace))) : 'Tempat';

        $docClassification = DocumentClassification::find($documentClassificationId);
        $classificationName = $docClassification ? $docClassification->name : 'BIASA';

        $suspect = Suspect::with(['gender', 'job', 'religion', 'village', 'district', 'regency', 'province'])->find($suspectId);
        $suspectAge = null;
        if ($suspect && !empty($suspect->birth_date)) {
            try {
                $birthDate = Carbon::parse($suspect->birth_date);
                $targetDate = !empty($documentDate) ? Carbon::parse($documentDate) : Carbon::now();
                $diff = $birthDate->diff($targetDate);
                $suspectAge = $diff->y . ' tahun ' . $diff->m . ' bulan ' . $diff->d . ' hari';
            } catch (\Exception $e) {
                $suspectAge = null;
            }
        }

        $genderInput = $request->input('childGender');
        $genderModel = Gender::find($genderInput);
        $genderName = $genderModel ? $genderModel->name : ($suspect && $suspect->gender ? $suspect->gender->name : $genderInput);
        $genderId = $genderModel ? $genderModel->id : ($suspect ? $suspect->gender_id : $genderInput);

        $religionInput = $request->input('childReligion');
        $religionModel = Religion::find($religionInput);
        $religionName = $religionModel ? $religionModel->name : ($suspect && $suspect->religion ? $suspect->religion->name : $religionInput);
        $religionId = $religionModel ? $religionModel->id : ($suspect ? $suspect->religion_id : $religionInput);

        $jobInput = $request->input('childJob');
        $jobModel = Job::find($jobInput);
        $jobName = $jobModel ? $jobModel->name : ($suspect && $suspect->job ? $suspect->job->name : $jobInput);
        $jobId = $jobModel ? $jobModel->id : ($suspect ? $suspect->job_id : $jobInput);

        $natInput = $request->input('childNationality');
        $natModel = Nationality::find($natInput);
        $natName = $natModel ? $natModel->name : ($natInput == '1' || str_contains(strtoupper((string)$natInput), 'INDO') ? 'Indonesia' : 'Indonesia');
        $natId = $natModel ? $natModel->id : '1';

        $idTypeInput = $request->input('childIdentityType');
        $idTypeModel = IdentityType::find($idTypeInput);
        $idTypeName = $idTypeModel ? $idTypeModel->name : '';
        $idTypeId = $idTypeModel ? $idTypeModel->id : $idTypeInput;

        $payload = [
            'classification_id'         => $documentClassificationId,
            'classification_name'       => $classificationName,
            'appendix'                  => $appendix,
            'perihal'                   => $perihal,
            'court_id'                  => $courtId,
            'court_name'                => $courtName,
            'court_place'               => $courtPlace,
            'surat_perintah_penyidikan_document_id' => $suratPerintahPenyidikanDocumentId,
            'agreement_date'            => $agreementDate,
            'reference_law'             => $referenceLaw,
            'suspect_id'                => $suspectId,
            'suspect_name'              => $request->input('childName', $suspect->name ?? ''),
            'suspect_identity_type_id'  => $idTypeId,
            'suspect_identity_type'     => $idTypeName,
            'suspect_identity_number'   => $request->input('childIdentityNumber', $suspect->identity_number ?? ''),
            'suspect_nationality_id'    => $natId,
            'suspect_nationality'       => $natName,
            'suspect_gender_id'         => $genderId,
            'suspect_gender'            => $genderName,
            'suspect_birth_place'       => $request->input('childBirthPlace', $suspect->birth_place ?? ''),
            'suspect_birth_date'        => $request->input('childBirthDate', $suspect->birth_date ?? ''),
            'suspect_age_year'          => $request->input('childAgeYear'),
            'suspect_age_month'         => $request->input('childAgeMonth'),
            'suspect_age_day'           => $request->input('childAgeDay'),
            'suspect_age'               => $suspectAge,
            'suspect_job_id'            => $jobId,
            'suspect_job'               => $jobName,
            'suspect_religion_id'       => $religionId,
            'suspect_religion'          => $religionName,
            'suspect_address'           => $request->input('childAddress', $suspect->address ?? ''),
            'meeting_date'              => $meetingDate,
            'agreement_result_type'     => $agreementResultType,
            'agreement_institution'     => $agreementInstitution,
            'agreement_duration_months' => $agreementDurationMonths,
            'carbon_copies'             => $filteredCarbonCopies,
            'signatory_id'              => $signatoryId,
        ];

        DB::beginTransaction();
        try {
            $document->update([
                'suspect_id'                            => $suspectId,
                'surat_perintah_penyidikan_document_id' => $suratPerintahPenyidikanDocumentId,
                'court_id'                              => $courtId,
                'court_name'                            => $courtName,
                'court_place'                           => $courtPlace,
                'document_number'                       => $documentNumber,
                'document_date'                         => $documentDate,
                'payload'                               => $payload,
                'messages'                              => $suspectAge ? ['suspect_age' => $suspectAge] : null,
                'updated_by_user_id'                    => Auth::id(),
            ]);

            // Update Signatory
            $signatory = Officer::where('id', $signatoryId)->first();
            if ($signatory) {
                $document->suratPermohonanPenetapanDiversiDocumentOfficers()
                    ->where('class', SuratPermohonanPenetapanDiversiDocumentOfficer::getEnumOption('class', 'SIGNATORY'))
                    ->delete();

                $document->suratPermohonanPenetapanDiversiDocumentOfficers()->create([
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
                    'status'          => SuratPermohonanPenetapanDiversiDocumentOfficer::getEnumOption('status', 'PRESENT'),
                    'class'           => SuratPermohonanPenetapanDiversiDocumentOfficer::getEnumOption('class', 'SIGNATORY'),
                    'flag'            => SuratPermohonanPenetapanDiversiDocumentOfficer::getEnumOption('flag', 'INTERNAL'),
                    'insert_method'   => SuratPermohonanPenetapanDiversiDocumentOfficer::getEnumOption('insert_method', 'IMPORT'),
                ]);
            }

            // Tandai tersangka sebagai anak
            if (!empty($suspectId)) {
                Suspect::where('id', $suspectId)->update(['is_child' => true]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui dokumen: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
            ->with('success', 'Dokumen Surat Permohonan Penetapan Diversi berhasil diperbarui.');
    }

    public function delete($id)
    {
        $document = SuratPermohonanPenetapanDiversiDocument::where('id', $id)->firstOrFail();
        $accidentId = request()->query('accident_id') ?? $document->accident_id;

        DB::beginTransaction();
        try {
            $document->suratPermohonanPenetapanDiversiDocumentOfficers()->delete();
            $document->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus data.');
        }

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
            ->with('success', 'Dokumen Surat Permohonan Penetapan Diversi berhasil dihapus.');
    }

    public function download($id)
    {
        $document = SuratPermohonanPenetapanDiversiDocument::with([
            'accident.polres.polda',
            'accident.police',
            'suspect',
            'court',
            'suratPerintahPenyidikanDocument.suratPerintahPenyidikanDocumentLaws.crimeConstitution',
            'suratPermohonanPenetapanDiversiDocumentOfficers.rank',
            'suratPermohonanPenetapanDiversiDocumentOfficers.position',
        ])->where('id', $id)->firstOrFail();

        $accident = $document->accident;
        $signatory = $document->suratPermohonanPenetapanDiversiDocumentOfficers
            ->where('class', SuratPermohonanPenetapanDiversiDocumentOfficer::getEnumOption('class', 'SIGNATORY'))
            ->first();

        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor(public_path('word-template/surat_permohonan_penetapan_diversi.docx'));

        $workUnitName = '';
        if (!empty($accident->police)) {
            if ($accident->police->class == 'DAERAH') {
                $workUnitName = 'Dit Lantas ' . ucwords(strtolower($accident->police->full_name));
            } else if ($accident->police->class == 'RESOR') {
                $workUnitName = 'Sat Lantas ' . ucwords(strtolower($accident->police->full_name));
            }
        }

        $daerahPolice = $accident->polres->polda ?? null;
        $daerahPoliceFullName = $daerahPolice ? strtoupper($daerahPolice->full_name) : '';

        $resorPolice = $accident->polres;
        $resorPoliceAddress = ($resorPolice->address ?? '') . ', ' . ($resorPolice->polres_zipcode ?? '');
        $resorPoliceFullName = (isset($resorPolice->id) && in_array($resorPolice->id, ['1114'])) ? 'DIREKTORAT LALU LINTAS' : 'RESOR ' . strtoupper($resorPolice->full_name ?? '');
        $resorPoliceProvinceName = $resorPolice->polres_province ?? '';

        $documentLocation = ucwords(strtolower($resorPoliceProvinceName));
        $documentDate = !empty($document->document_date) ? Carbon::parse($document->document_date)->locale('id')->translatedFormat('d F Y') : '-';
        $documentNumber = $document->document_number ?? '-';
        $appendix = $document->payload['appendix'] ?? 'satu berkas';

        $classificationName = $document->payload['classification_name'] ?? 'BIASA';
        $courtName = $document->court_name ?? ($document->court->name ?? '-');
        $courtLocation = !empty($document->court_place)
            ? $document->court_place
            : (!empty($document->payload['court_place'])
                ? $document->payload['court_place']
                : (!empty($courtName) && $courtName !== '-'
                    ? ucwords(strtolower(trim(preg_replace('/^(PENGADILAN NEGERI|PENGADILAN)\s+/i', '', $courtName))))
                    : 'Tempat'));

        $accidentNumber = $accident->no_lp ?? '-';
        $accidentDate = !empty($accident->accident_date) ? Carbon::parse($accident->accident_date)->locale('id')->translatedFormat('d F Y') : '-';
        $reportDate = !empty($accident->report_date) ? Carbon::parse($accident->report_date)->locale('id')->translatedFormat('d F Y') : '-';
        $accidentDay = !empty($accident->accident_date) ? Carbon::parse($accident->accident_date)->locale('id')->translatedFormat('l') : '-';
        $accidentRoad = $accident->road_name ?? '-';

        // Crime Constitution from SP.Sidik
        $suratPerintahPenyidikanDocument = $document->suratPerintahPenyidikanDocument;
        $suratPerintahPenyidikanDocumentNumber = $suratPerintahPenyidikanDocument->document_number ?? '-';
        $suratPerintahPenyidikanDocumentDate = $suratPerintahPenyidikanDocument && !empty($suratPerintahPenyidikanDocument->document_date)
            ? Carbon::parse($suratPerintahPenyidikanDocument->document_date)->locale('id')->translatedFormat('d F Y')
            : '-';

        $crimeClassText = 'Kejahatan Lalu Lintas';
        $crimeConstitutionText = $document->payload['reference_law'] ?? '';
        if (empty($crimeConstitutionText) && $suratPerintahPenyidikanDocument) {
            $laws = $suratPerintahPenyidikanDocument->suratPerintahPenyidikanDocumentLaws;
            if ($laws && $laws->count() > 0) {
                $lawParts = [];
                foreach ($laws as $law) {
                    if ($law->flag == 'MAIN') {
                        $cName = $law->crimeConstitution->name ?? '';
                        $cChap = $law->constitution_chapter ?? '';
                        $lawParts[] = trim($cChap . ' ' . $cName);
                    } elseif ($law->flag == 'ADDITIONAL') {
                        $lawParts[] = trim($law->constitution ?? '');
                    }
                }
                $crimeConstitutionText = implode(', ', array_filter($lawParts));
            }
        }
        if (empty($crimeConstitutionText)) {
            $crimeConstitutionText = 'Pasal 310 Ayat (4) Undang-Undang Nomor 22 Tahun 2009 tentang Lalu Lintas dan Angkutan Jalan';
        }

        // References Poin 1
        $agreementDateFormatted = !empty($document->payload['agreement_date'])
            ? Carbon::parse($document->payload['agreement_date'])->locale('id')->translatedFormat('d F Y')
            : '-';

        $references = [
            [
                'reference_iteration' => 'a.',
                'reference_name' => 'Undang-Undang Nomor 2 Tahun 2002 tentang Kepolisian Negara Republik Indonesia;'
            ],
            [
                'reference_iteration' => 'b.',
                'reference_name' => 'Pasal 21 Ayat (2) Undang-Undang Nomor 11 Tahun 2012 tentang Sistem Peradilan Pidana Anak (SPPA);'
            ],
            [
                'reference_iteration' => 'c.',
                'reference_name' => $crimeConstitutionText . ';'
            ],
            [
                'reference_iteration' => 'd.',
                'reference_name' => 'Laporan Polisi Nomor: ' . $accidentNumber . ' tanggal ' . $reportDate . ';'
            ],
            [
                'reference_iteration' => 'e.',
                'reference_name' => 'Surat Perintah Penyidikan Nomor: ' . $suratPerintahPenyidikanDocumentNumber . ' tanggal ' . $suratPerintahPenyidikanDocumentDate . ';'
            ],
            [
                'reference_iteration' => 'f.',
                'reference_name' => 'Keputusan Bersama tanggal ' . $agreementDateFormatted . '.'
            ]
        ];

        // Suspect identity (Anak)
        $suspect = $document->suspect;
        $blockSuspects = [];
        if ($suspect || !empty($document->payload['suspect_name'])) {
            $suspectProperties = $suspect->properties ?? [];

            $suspectAge = $document->payload['suspect_age'] ?? ($document->messages['suspect_age'] ?? null);
            if (empty($suspectAge) && $suspect && !empty($suspect->birth_date) && empty($suspectProperties['is_unknown_birth_date'])) {
                try {
                    $birthDate = Carbon::parse($suspect->birth_date);
                    $targetDate = !empty($document->document_date) ? Carbon::parse($document->document_date) : Carbon::now();
                    $diff = $birthDate->diff($targetDate);
                    $suspectAge = $diff->y . ' tahun ' . $diff->m . ' bulan ' . $diff->d . ' hari';
                } catch (\Exception $e) {
                    $suspectAge = '-';
                }
            }

            $fullAddress = $document->payload['suspect_address'] ?? ($suspect ? (!empty($suspectProperties['is_unknown_address'])
                ? 'TIDAK DIKETAHUI'
                : ucwords(strtolower(
                    ($suspect->address ?? '') . ', ' .
                    ($suspect->village->name ?? '') . ', ' .
                    ($suspect->district->name ?? '') . ', ' .
                    ($suspect->regency->name ?? '') . ', ' .
                    ($suspect->province->name ?? '')
                ))) : '-');

            $birthDateFormatted = !empty($document->payload['suspect_birth_date'])
                ? Carbon::parse($document->payload['suspect_birth_date'])->locale('id')->translatedFormat('d F Y')
                : ($suspect && !empty($suspect->birth_date) ? Carbon::parse($suspect->birth_date)->locale('id')->translatedFormat('d F Y') : '-');

            $blockSuspects[] = [
                'suspectName'           => $document->payload['suspect_name'] ?? ($suspect->name ?? '-'),
                'suspectIdentityNumber' => $document->payload['suspect_identity_number'] ?? ($suspect->identity_number ?? '-'),
                'suspectNationality'    => $document->payload['suspect_nationality'] ?? ($suspect->nationality ?? 'Indonesia'),
                'suspectGenderName'     => $document->payload['suspect_gender'] ?? ($suspect->gender->name ?? '-'),
                'suspectBirthPlace'     => $document->payload['suspect_birth_place'] ?? ($suspect->birth_place ?? '-'),
                'suspectBirthDate'      => $birthDateFormatted,
                'suspectAge'            => $suspectAge ?? '-',
                'suspectJobName'        => $document->payload['suspect_job'] ?? ($suspect->job->name ?? '-'),
                'suspectReligionName'   => $document->payload['suspect_religion'] ?? ($suspect->religion->name ?? '-'),
                'suspectFullAddress'    => $fullAddress,
            ];
        }

        // Rapat Koordinasi & Hasil Kesepakatan
        $meetingDateFormatted = !empty($document->payload['meeting_date'])
            ? Carbon::parse($document->payload['meeting_date'])->locale('id')->translatedFormat('d F Y')
            : '-';

        $agreementResultType = $document->payload['agreement_result_type'] ?? 'ORANG_TUA';
        if ($agreementResultType === 'PEMBINAAN') {
            $institution = $document->payload['agreement_institution'] ?? '-';
            $duration = $document->payload['agreement_duration_months'] ?? '-';
            $agreementResult = "dilakukan pembinaan di {$institution} selama {$duration} bulan";
        } else {
            $agreementResult = "dikembalikan kepada orang tua";
        }

        // Carbon Copies (Tembusan)
        $carbonCopies = $document->payload['carbon_copies'] ?? [];
        $blockCarbonCopies = [];
        $no = 1;
        foreach ($carbonCopies as $cc) {
            if (!empty(trim($cc))) {
                $blockCarbonCopies[] = [
                    'carbon_copy_iteration' => $no,
                    'carbon_copy_name'      => trim($cc),
                ];
                $no++;
            }
        }
        if (empty($blockCarbonCopies)) {
            $blockCarbonCopies[] = [
                'carbon_copy_iteration' => '1',
                'carbon_copy_name'      => '-',
            ];
        }

        // Signatory
        $signatoryName = $signatory ? PeopleNameHelper::getFullName($signatory->first_title, $signatory->first_name, $signatory->last_name, $signatory->last_title) : '';
        $signatoryRankName = $signatory && $signatory->rank ? $signatory->rank->full_name : '';
        $signatoryRegisterNumber = $signatory->register_number ?? '';

        // Template Population
        $templateProcessor->cloneBlock('block_suspects', 0, true, false, $blockSuspects);
        $templateProcessor->cloneRowAndSetValues('reference_iteration', $references);
        $templateProcessor->cloneRowAndSetValues('carbon_copy_iteration', $blockCarbonCopies);

        $templateProcessor->setValue('daerahPoliceFullName', $daerahPoliceFullName);
        $templateProcessor->setValue('resorPoliceAddress', $resorPoliceAddress);
        $templateProcessor->setValue('resorPoliceFullName', $resorPoliceFullName);

        $templateProcessor->setValue('documentLocation', $documentLocation);
        $templateProcessor->setValue('documentDate', $documentDate);
        $templateProcessor->setValue('documentNumber', $documentNumber);
        $templateProcessor->setValue('documentClassificationName', $classificationName);
        $templateProcessor->setValue('appendix', $appendix);
        $templateProcessor->setValue('perihal', $document->payload['perihal'] ?? '-');

        $templateProcessor->setValue('courtName', $courtName);
        $templateProcessor->setValue('courtLocation', $courtLocation);

        $templateProcessor->setValue('workUnitName', $workUnitName);
        $templateProcessor->setValue('crimeClass', $crimeClassText);
        $templateProcessor->setValue('crimeConstitution', $crimeConstitutionText);

        $templateProcessor->setValue('accidentRoad', $accidentRoad);
        $templateProcessor->setValue('accidentDay', $accidentDay);
        $templateProcessor->setValue('accidentDate', $accidentDate);

        $templateProcessor->setValue('meetingDate', $meetingDateFormatted);
        $templateProcessor->setValue('agreementResult', $agreementResult);

        $templateProcessor->setValue('signatoryName', $signatoryName);
        $templateProcessor->setValue('signatoryRankName', strtoupper($signatoryRankName));
        $templateProcessor->setValue('signatoryRegisterNumber', $signatoryRegisterNumber);

        $filename = 'generate/' . $document->id . ' - Surat Permohonan Penetapan Diversi - ' . ($accident->polres->full_name ?? '');
        if (!file_exists(public_path('generate'))) {
            mkdir(public_path('generate'), 0755, true);
        }
        $fullPath = public_path($filename . '.docx');
        $templateProcessor->saveAs($fullPath);
        return response()->download($fullPath)->deleteFileAfterSend(true);
    }

    private function validateForm(Request $request, $id = null)
    {
        $rules = [
            'documentNumber'                    => 'required',
            'documentDate'                      => 'required|date',
            'documentClassification'            => 'required',
            'appendix'                          => 'required',
            'perihal'                           => 'required',
            'courtId'                           => 'required',
            'suratPerintahPenyidikanDocument'   => 'required',
            'agreementDate'                     => 'required|date',
            'suspectId'                         => 'required',
            'meetingDate'                       => 'required|date',
            'agreementResultType'               => 'required|in:ORANG_TUA,PEMBINAAN',
            'agreementInstitution'              => 'required_if:agreementResultType,PEMBINAAN',
            'agreementDurationMonths'           => 'required_if:agreementResultType,PEMBINAAN|nullable|integer',
            'signatory'                         => 'required',
        ];

        $messages = [
            'documentNumber.required'                   => 'Mohon mengisi Nomor Dokumen.',
            'documentDate.required'                     => 'Mohon mengisi Tanggal Ditandatangani Dokumen.',
            'documentDate.date'                         => 'Format Tanggal Ditandatangani Dokumen tidak valid.',
            'documentClassification.required'           => 'Mohon memilih Klasifikasi Dokumen.',
            'appendix.required'                         => 'Mohon mengisi Lampiran Dokumen.',
            'perihal.required'                          => 'Mohon mengisi Perihal Dokumen.',
            'courtId.required'                          => 'Mohon memilih Pengadilan Penerima.',
            'suratPerintahPenyidikanDocument.required'  => 'Mohon memilih Nomor Surat Perintah Penyidikan.',
            'agreementDate.required'                    => 'Mohon mengisi Tanggal Keputusan Bersama (Diversi).',
            'agreementDate.date'                        => 'Format Tanggal Keputusan Bersama tidak valid.',
            'suspectId.required'                        => 'Mohon memilih Tersangka Anak.',
            'meetingDate.required'                      => 'Mohon mengisi Tanggal Rapat Koordinasi.',
            'meetingDate.date'                          => 'Format Tanggal Rapat Koordinasi tidak valid.',
            'agreementResultType.required'              => 'Mohon memilih Hasil Keputusan Bersama.',
            'agreementInstitution.required_if'          => 'Mohon mengisi Nama Lembaga Pembinaan.',
            'agreementDurationMonths.required_if'       => 'Mohon mengisi Lama Pembinaan.',
            'agreementDurationMonths.integer'           => 'Lama Pembinaan harus berupa angka (bulan).',
            'signatory.required'                        => 'Mohon memilih Penandatangan Surat.',
        ];

        return Validator::make($request->all(), $rules, $messages);
    }
}
