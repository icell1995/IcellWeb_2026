<?php

namespace App\Http\Controllers\Docs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Helpers\PeopleNameHelper;

use App\Services\Doc\DocService;

use App\Models\Doc\SuratKetetapanTentangPenetapanTersangkaDocument\SuratKetetapanTentangPenetapanTersangkaDocument;
use App\Models\Doc\SuratKetetapanTentangPenetapanTersangkaDocument\SuratKetetapanTentangPenetapanTersangkaDocumentOfficer;
use App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument;
use App\Models\Doc\LaporanHasilGelarPerkaraDocument\LaporanHasilGelarPerkaraDocument;
use App\Models\Accident;
use App\Models\Lib\AccidentCause;
use App\Models\Lib\Rank;
use App\Models\Lib\Position;
use App\Models\Lib\CaseDegreeType;
use App\Models\Lib\Timezone;
use App\Models\Lib\SuspectSource;
use App\Models\Lib\IdentityType;
use App\Models\Lib\Gender;
use App\Models\Lib\Ethnic;
use App\Models\Lib\Job;
use App\Models\Lib\Religion;
use App\Models\Lib\Education;
use App\Models\Lib\MaritalStatus;
use App\Models\Lib\Location;
use App\Models\Lib\Prosecutor;
use App\Models\Lib\AccidentType;
use App\Models\Lib\DrivingLicenseType;
use App\Models\Lib\VehicleType;
use App\Models\Officer;
use App\Models\Suspect;
use App\Models\VehicleAssociatedSuspect;
use App\Models\CaseVehicle;

use App\Traits\DocsOfficersTraits;

class SuratKetetapanTentangPenetapanTersangkaDocumentController extends Controller
{
    protected $docService;

    use DocsOfficersTraits;

    public function __construct(DocService $docService)
    {
        $this->docService = $docService;
    }

    public function create()
    {
        // Get URL Parameter
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $accident = Accident::where('id', $accidentId)->first();

        $policeId = $accident->polres_id;

        $getOldNewPolresIds = $this->getOldNewPolresIds($policeId);

        $vehicleListResponse = Http::withHeaders([
            "Key" => "16s08e23TBJ1hEXwAMSIH00eBI1F5BODfeLVlHMHnIZrNsDmtS=getdataKorbanirsmS",
            "Content-Type" => "application/json"
        ])
        ->withBody(
            json_encode([
                'accident_id' => $accidentId
            ]), // Request body
            'application/json'               // Content type)
        )
            ->get('https://irsms.korlantas.polri.go.id/irsmsapi/api/dataKorban')
            ->json();
        // $client = new Client();
        // $response = $client->get('https://irsms.korlantas.polri.go.id/irsmsapi/api/dataKorban', [
        //     'headers' => [
        //         'Key' => '16s08e23TBJ1hEXwAMSIH00eBI1F5BODfeLVlHMHnIZrNsDmtS=getdataKorbanirsmS',
        //         "Content-Type" => "application/json"
        //     ],
        //     'json' => [
        //         'accident_id' => $accidentId
        //     ]
        // ]);

        // $vehicleListResponse = json_decode($response->getBody(), true);
        Log::info('Vehicle List Response: ', [$vehicleListResponse]);
        $vehicleListCollection = collect($vehicleListResponse['result'] ?? []);
        
        $accidentTypes = AccidentType::where('is_active', true)
            ->get();
        $accidentTypesCollection = collect($accidentTypes);

        $vehicleTypes = VehicleType::where('is_active', true)
            ->get();
        $vehicleTypesCollection = collect($vehicleTypes);

        $accidentCauses = AccidentCause::where('is_active', true)
            ->get();
        $accidentCausesCollection = collect($accidentCauses);

        $identityTypes = IdentityType::where('is_active', true)
            ->orderBy('sort')
            ->get();
        $identityTypesCollection = collect($identityTypes);

        $drivingLicenseTypes = DrivingLicenseType::where('is_active', true)
            ->orderBy('sort')
            ->get();
        $drivingLicenseTypesCollection = collect($drivingLicenseTypes);
        
        Log::info('Vehicle List Collection: ', [$vehicleListCollection]);

        $vehicleList = $vehicleListCollection->map(function ($vehicle) use ($accidentTypesCollection, $vehicleTypesCollection, $accidentCausesCollection, $identityTypesCollection, $drivingLicenseTypesCollection) {
            $accidentTypeMatch = $accidentTypesCollection->firstWhere('irsms_id', $vehicle['jenis_kecelakaan']);
            if($accidentTypeMatch){
                $vehicle['accident_type_id'] = $accidentTypeMatch['id'];
                $vehicle['accident_type_name'] = $accidentTypeMatch['name'];
            }

            $vehicleTypeMatch = $vehicleTypesCollection->firstWhere('irsms_id', $vehicle['jenis_ranmor']);
            if($vehicleTypeMatch){
                $vehicle['vehicle_type_id'] = $vehicleTypeMatch['id'];
                $vehicle['vehicle_type_name'] = $vehicleTypeMatch['name'];
            }

            $accidentCauseMatch = $accidentCausesCollection->firstWhere('irsms_id', $vehicle['penyebab']);
            if($accidentCauseMatch){
                $vehicle['accident_cause_id'] = $accidentCauseMatch['id'];
                $vehicle['accident_cause_name'] = $accidentCauseMatch['name'];
            }

            $identityTypeMatch = $identityTypesCollection->firstWhere('irsms_id', $vehicle['tipe_identitas']);
            if($identityTypeMatch){
                $vehicle['identity_type_id'] = $identityTypeMatch['id'];
                $vehicle['identity_type_name'] = $identityTypeMatch['name'];
            }

            $drivingLicenseTypeMatch = $drivingLicenseTypesCollection->firstWhere('irsms_id', $vehicle['jenis_sim'] ?? '-');
            if($drivingLicenseTypeMatch){
                $vehicle['driving_license_type_id'] = $drivingLicenseTypeMatch['id'];
                $vehicle['driving_license_type_name'] = $drivingLicenseTypeMatch['name'];
            }

            return $vehicle;
        });
        
        $suratPerintahPenyidikanDocuments = SuratPerintahPenyidikanDocument::where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->get();

        $laporanHasilGelarPerkaraDocumentSuspectDeterminations = LaporanHasilGelarPerkaraDocument::whereHas('caseDegreeType', function($query){
                $query->where('id', '1');
        })
            ->where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->get();

        $getOldNewPolresIds = $this->getOldNewPolresIds($policeId);
        
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
                    
        $ranks = Rank::where('is_active', true)
            ->wherePolri()
            ->orderBy('sort')
            ->get();

        $timezones = Timezone::where('is_active', true)
            ->orderBy('sort')
            ->get();

        $suspectSources = SuspectSource::where('is_active', true)
            ->orderBy('sort')
            ->get();

        $genders = Gender::where('is_active', true)
            ->orderBy('sort')
            ->get();
        
        $ethnics = Ethnic::where('is_active', true)
            ->orderBy('sort')
            ->get();
        
        $jobs = Job::where('is_active', true)
            ->orderBy('sort')
            ->get();

        $religions = Religion::where('is_active', true)
            ->orderBy('sort')
            ->get();

        $educations = Education::where('is_active', true)
            ->orderBy('sort')
            ->get();

        $maritalStatuses = MaritalStatus::where('is_active', true)
            ->orderBy('sort')
            ->get();
        
        $countries = Location::where('is_active', true)
            ->where('class', 'COUNTRY')
            ->orderBy('sort')
            ->get();

        $prosecutors = Prosecutor::whereHas('polices', function($query) use ($policeId){
                $query->whereIn('lib.polices.id', $this->getOldNewPolresIds($policeId));
            })
            ->active()
            ->orderBy('sort')
            ->get();

        $suspectSources = SuspectSource::where('group', 'SURAT_KETETAPAN_TENTANG_PENETAPAN_TERSANGKA')
            ->where('is_active', true)
            ->orderBy('sort')
            ->get();

        $suspects = Suspect::where('accident_id', $accidentId)->get();

        $viewData = [
            'accidentId' => $accidentId,
            'accident' => $accident,
            'suratPerintahPenyidikanDocuments' => $suratPerintahPenyidikanDocuments,
            'authorizedSignatories' => $authorizedSignatories,
            'ranks' => $ranks,
            'timezones' => $timezones,
            'suspectSources' => $suspectSources,
            'identityTypes' => $identityTypes,
            'genders' => $genders,
            'ethnics' => $ethnics,
            'jobs' => $jobs,
            'religions' => $religions,
            'educations' => $educations,
            'maritalStatuses' => $maritalStatuses,
            'countries' => $countries,
            'prosecutors' => $prosecutors,
            'suspectSources' => $suspectSources,
            'laporanHasilGelarPerkaraDocumentSuspectDeterminations' => $laporanHasilGelarPerkaraDocumentSuspectDeterminations,
            'suspects' => $suspects,
            'vehicleList' => $vehicleList,
        ];

        return view('docs.surat-ketetapan-tentang-penetapan-tersangka-document.create', $viewData);
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
        $documentDate = htmlspecialchars($request->documentDate);
        $documentNumber = htmlspecialchars($request->documentNumber);
        $suratPerintahPenyidikanDocumentId = htmlspecialchars($request->suratPerintahPenyidikanDocument);
        $prosecutorId = htmlspecialchars($request->prosecutor);
        $signatoryId = htmlspecialchars($request->signatory);
        $suspectSourceId = htmlspecialchars($request->suspectSource);
        $laporanHasilGelarPerkaraDocumentSuspectDeterminationId = ($request->laporanHasilGelarPerkaraDocumentSuspectDetermination) ? htmlspecialchars($request->laporanHasilGelarPerkaraDocumentSuspectDetermination) : NULL;
        $resumeSuspectDeterminationDate = ($request->resumeSuspectDeterminationDate) ? htmlspecialchars($request->resumeSuspectDeterminationDate) : NULL;
        $suspectId = $request->suspect;

        $identityTypeIdSuspect = $request->identityTypeFieldSuspect;
        $identityNumberSuspect = htmlspecialchars($request->identityNumberFieldSuspect);
        $nameSuspect = htmlspecialchars($request->nameFieldSuspect);
        $genderIdSuspect = htmlspecialchars($request->genderFieldSuspect);
        $birthPlaceSuspect = htmlspecialchars($request->birthPlaceFieldSuspect);
        $birthDateSuspect = htmlspecialchars($request->birthDateFieldSuspect);
        $fatherSuspect = htmlspecialchars($request->fatherFieldSuspect);
        $motherSuspect = htmlspecialchars($request->motherFieldSuspect);
        $nationalitySuspect = htmlspecialchars($request->nationalityFieldSuspect);
        $ethnicIdSuspect = htmlspecialchars($request->ethnicFieldSuspect);
        $jobIdSuspect = htmlspecialchars($request->jobFieldSuspect);
        $religionIdSuspect = htmlspecialchars($request->religionFieldSuspect);
        $educationIdSuspect = htmlspecialchars($request->educationFieldSuspect);
        $maritalStatusIdSuspect = htmlspecialchars($request->maritalStatusFieldSuspect);
        $phoneNumberSuspect = htmlspecialchars($request->phoneNumberFieldSuspect);
        $emailSuspect = htmlspecialchars($request->emailFieldSuspect);
        $countryIdSuspect = htmlspecialchars($request->countryFieldSuspect);
        $provinceIdSuspect = htmlspecialchars($request->provinceFieldSuspect);
        $regencyIdSuspect = htmlspecialchars($request->regencyFieldSuspect);
        $districtIdSuspect = htmlspecialchars($request->districtFieldSuspect);
        $villageIdSuspect = htmlspecialchars($request->villageFieldSuspect);
        $addressSuspect = htmlspecialchars($request->addressFieldSuspect);
        
        $isUnknownGenderSuspect = $request->isUnknownGenderFieldSuspect;
        $isUnknownBirthPlaceSuspect = $request->isUnknownBirthPlaceFieldSuspect;
        $isUnknownBirthDateSuspect = $request->isUnknownBirthDateFieldSuspect;
        $isUnknownFatherSuspect = $request->isUnknownFatherFieldSuspect;
        $isUnknownMotherSuspect = $request->isUnknownMotherFieldSuspect;
        $isUnknownNationalitySuspect = $request->isUnknownNationalityFieldSuspect;
        $isUnknownMaritalStatusSuspect = $request->isUnknownMaritalStatusFieldSuspect;
        $isExistsPhoneNumberSuspect = $request->isExistsPhoneNumberFieldSuspect;
        $isExistsEmailSuspect = $request->isExistsEmailFieldSuspect;
        $isAvailablePhoneNumberSuspect = $request->isAvailablePhoneNumberFieldSuspect;
        $isAvailableEmailSuspect = $request->isAvailableEmailFieldSuspect;
        $isUnknownAddressSuspect = $request->isUnknownAddressFieldSuspect;  

        $isLegacy = filter_var($request->isLegacy, FILTER_VALIDATE_BOOLEAN);

        $vehicleSuspect = htmlspecialchars($request->vehicleFieldSuspect);

        // Check if document number already exist
        $exists = SuratKetetapanTentangPenetapanTersangkaDocument::where('accident_id', $accidentId)
            ->where('document_number', 'ILIKE', $documentNumber)
            ->exists();
        if ($exists) {
            return redirect()->back()->with('error', 'Dokumen ' . $documentNumber . ' Sudah Anda Buat Sebelumnya.');
        }

        DB::beginTransaction();
        try{
            $vehicleListResponse = Http::withHeaders([
                "Key" => "16s08e23TBJ1hEXwAMSIH00eBI1F5BODfeLVlHMHnIZrNsDmtS=getdataKorbanirsmS",
                "Content-Type" => "application/json"
            ])
                ->get('https://irsms.korlantas.polri.go.id/irsmsapi/api/dataKorban', [
                    'accident_id' => $accidentId
                ])
                ->json();
            $vehicleListCollection = collect($vehicleListResponse['result'] ?? []);
            
            $accidentTypes = AccidentType::where('is_active', true)
                ->get();
            $accidentTypesCollection = collect($accidentTypes);
    
            $vehicleTypes = VehicleType::where('is_active', true)
                ->get();
            $vehicleTypesCollection = collect($vehicleTypes);
    
            $accidentCauses = AccidentCause::where('is_active', true)
                ->get();
            $accidentCausesCollection = collect($accidentCauses);
    
            $identityTypes = IdentityType::where('is_active', true)
                ->orderBy('sort')
                ->get();
            $identityTypesCollection = collect($identityTypes);

            $drivingLicenseTypes = DrivingLicenseType::where('is_active', true)
                ->orderBy('sort')
                ->get();
            $drivingLicenseTypesCollection = collect($drivingLicenseTypes);
            
            $vehicleList = $vehicleListCollection->map(function ($vehicle) use ($accidentTypesCollection, $vehicleTypesCollection, $accidentCausesCollection, $identityTypesCollection, $drivingLicenseTypesCollection) {
                $accidentTypeMatch = $accidentTypesCollection->firstWhere('irsms_id', $vehicle['jenis_kecelakaan']);
                if($accidentTypeMatch){
                    $vehicle['accident_type_id'] = $accidentTypeMatch['id'];
                    $vehicle['accident_type_name'] = $accidentTypeMatch['name'];
                }
    
                $vehicleTypeMatch = $vehicleTypesCollection->firstWhere('irsms_id', $vehicle['jenis_ranmor']);
                if($vehicleTypeMatch){
                    $vehicle['vehicle_type_id'] = $vehicleTypeMatch['id'];
                    $vehicle['vehicle_type_name'] = $vehicleTypeMatch['name'];
                }
    
                $accidentCauseMatch = $accidentCausesCollection->firstWhere('irsms_id', $vehicle['penyebab']);
                if($accidentCauseMatch){
                    $vehicle['accident_cause_id'] = $accidentCauseMatch['id'];
                    $vehicle['accident_cause_name'] = $accidentCauseMatch['name'];
                }
    
                $identityTypeMatch = $identityTypesCollection->firstWhere('irsms_id', $vehicle['tipe_identitas']);
                if($identityTypeMatch){
                    $vehicle['identity_type_id'] = $identityTypeMatch['id'];
                    $vehicle['identity_type_name'] = $identityTypeMatch['name'];
                }

                $drivingLicenseTypeMatch = $drivingLicenseTypesCollection->firstWhere('irsms_id', $vehicle['jenis_sim'] ?? '-');
                if($drivingLicenseTypeMatch){
                    $vehicle['driving_license_type_id'] = $drivingLicenseTypeMatch['id'];
                    $vehicle['driving_license_type_name'] = $drivingLicenseTypeMatch['name'];
                }
    
                return $vehicle;
            });
            $vehicleAssociatedSuspect = $vehicleList->firstWhere('nopol', $vehicleSuspect);

            $suratKetetapanTentangPenetapanTersangkaDocument = SuratKetetapanTentangPenetapanTersangkaDocument::create([
                'accident_id'=> $accidentId,
                'surat_perintah_penyidikan_document_id'=> $suratPerintahPenyidikanDocumentId,
                'document_number'=> $documentNumber,
                'document_date'=> $documentDate,
                'suspect_source_id'=> $suspectSourceId,
                'laporan_hasil_gelar_perkara_document_id'=> $laporanHasilGelarPerkaraDocumentSuspectDeterminationId,
                'resume_suspect_determination_date'=> $resumeSuspectDeterminationDate,
                'prosecutor_id'=> $prosecutorId,
                'is_legacy' => $isLegacy,
            ]);

            $suratKetetapanTentangPenetapanTersangkaDocumentId = $suratKetetapanTentangPenetapanTersangkaDocument->id;

            // SIGNATORY
            $signatory = Officer::where('id',$signatoryId)->first();
            $suratKetetapanTentangPenetapanTersangkaDocument->suratKetetapanTentangPenetapanTersangkaDocumentOfficers()->create([
                'surat_ketetapan_tentang_penetapan_tersangka_document_id' => $suratKetetapanTentangPenetapanTersangkaDocumentId,
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
                'status' => SuratKetetapanTentangPenetapanTersangkaDocumentOfficer::getEnumOption('status', 'PRESENT'),
                'class' => SuratKetetapanTentangPenetapanTersangkaDocumentOfficer::getEnumOption('class', 'SIGNATORY'),
                'flag' => SuratKetetapanTentangPenetapanTersangkaDocumentOfficer::getEnumOption('flag', 'INTERNAL'),
                'insert_method' => SuratKetetapanTentangPenetapanTersangkaDocumentOfficer::getEnumOption('flag', 'IMPORT'),
            ]);
  
            // SUSPECT
            $suspect = Suspect::where('id', $suspectId)->first();
            
            $suspect->update([
                'accident_id' => $accidentId,
                'identity_number' => $identityNumberSuspect,
                'name' => $nameSuspect,
                'birth_place' => $birthPlaceSuspect,
                'birth_date' => ($birthDateSuspect) ? $birthDateSuspect : null,
                'phone_number' => $phoneNumberSuspect,
                'email_address' => $emailSuspect,
                'father_name' => $fatherSuspect,
                'mother_name' => $motherSuspect,
                'nationality' => $nationalitySuspect,
    
                'address' => $addressSuspect,
                'identity_type_id' => ($identityTypeIdSuspect) ? $identityTypeIdSuspect : null,
                'gender_id' => ($genderIdSuspect) ? $genderIdSuspect : null,
                'ethnic_id' => ($ethnicIdSuspect) ? $ethnicIdSuspect : null,
                'job_id' => ($jobIdSuspect) ? $jobIdSuspect : null,
                'religion_id' => ($religionIdSuspect) ? $religionIdSuspect : null,
                'education_id' => ($educationIdSuspect) ? $educationIdSuspect : null,
                'marital_status_id' => ($maritalStatusIdSuspect) ? $maritalStatusIdSuspect : null,

                'flag' => Suspect::getEnumOption('flag', 'TERSANGKA'),
                'class' => Suspect::getEnumOption('class', 'DETERMINATION'),
                'group' => Suspect::getEnumOption('group', 'SURAT_KETETAPAN_TENTANG_PENETAPAN_TERSANGKA'),

                'country_id' => ($countryIdSuspect) ? $countryIdSuspect : null,
                'province_id' => ($provinceIdSuspect) ? $provinceIdSuspect : null,
                'regency_id' => ($regencyIdSuspect) ? $regencyIdSuspect : null,
                'district_id' => ($districtIdSuspect) ? $districtIdSuspect : null,
                'village_id' => ($villageIdSuspect) ? $villageIdSuspect : null,

                'properties' => [
                    'is_exists_phone_number' => ($isExistsPhoneNumberSuspect == 'null' || $isExistsPhoneNumberSuspect == 'undefined' || $isExistsPhoneNumberSuspect == null) ? null : filter_var($isExistsPhoneNumberSuspect, FILTER_VALIDATE_BOOLEAN),
                    'is_available_phone_number' => ($isAvailablePhoneNumberSuspect == 'null' || $isAvailablePhoneNumberSuspect == 'undefined' || $isAvailablePhoneNumberSuspect == null) ? null : filter_var($isAvailablePhoneNumberSuspect, FILTER_VALIDATE_BOOLEAN),
                    'is_exists_email' => ($isExistsEmailSuspect == 'null' || $isExistsEmailSuspect == 'undefined' || $isExistsEmailSuspect == null) ? null : filter_var($isExistsEmailSuspect, FILTER_VALIDATE_BOOLEAN),
                    'is_available_email' => ($isAvailableEmailSuspect == 'null' || $isAvailableEmailSuspect == 'undefined' || $isAvailableEmailSuspect == null) ? null : filter_var($isAvailableEmailSuspect, FILTER_VALIDATE_BOOLEAN),
                    'is_unknown_gender' => ($isUnknownGenderSuspect == 'null' || $isUnknownGenderSuspect == 'undefined' || $isUnknownGenderSuspect == null) ? null : filter_var($isUnknownGenderSuspect, FILTER_VALIDATE_BOOLEAN),
                    'is_unknown_birth_place' => ($isUnknownBirthPlaceSuspect == 'null' || $isUnknownBirthPlaceSuspect == 'undefined' || $isUnknownBirthPlaceSuspect == null) ? null : filter_var($isUnknownBirthPlaceSuspect, FILTER_VALIDATE_BOOLEAN),
                    'is_unknown_birth_date' => ($isUnknownBirthDateSuspect == 'null' || $isUnknownBirthDateSuspect == 'undefined' || $isUnknownBirthDateSuspect == null) ? null : filter_var($isUnknownBirthDateSuspect, FILTER_VALIDATE_BOOLEAN),
                    'is_unknown_father' => ($isUnknownFatherSuspect == 'null' || $isUnknownFatherSuspect == 'undefined' || $isUnknownFatherSuspect == null) ? null : filter_var($isUnknownFatherSuspect, FILTER_VALIDATE_BOOLEAN),
                    'is_unknown_mother' => ($isUnknownMotherSuspect == 'null' || $isUnknownMotherSuspect == 'undefined' || $isUnknownMotherSuspect == null) ? null : filter_var($isUnknownMotherSuspect, FILTER_VALIDATE_BOOLEAN),
                    'is_unknown_nationality' => ($isUnknownNationalitySuspect == 'null' || $isUnknownNationalitySuspect == 'undefined' || $isUnknownNationalitySuspect == null) ? null : filter_var($isUnknownNationalitySuspect, FILTER_VALIDATE_BOOLEAN),
                    'is_unknown_marital_status' => ($isUnknownMaritalStatusSuspect == 'null' || $isUnknownMaritalStatusSuspect == 'undefined' || $isUnknownMaritalStatusSuspect == null) ? null : filter_var($isUnknownMaritalStatusSuspect, FILTER_VALIDATE_BOOLEAN),
                    'is_unknown_address' => ($isUnknownAddressSuspect == 'null' || $isUnknownAddressSuspect == 'undefined' || $isUnknownAddressSuspect == null) ? null : filter_var($isUnknownAddressSuspect, FILTER_VALIDATE_BOOLEAN),
                ],
            ]);

            $suratKetetapanTentangPenetapanTersangkaDocument->suspect()->attach($suspect->id);

            VehicleAssociatedSuspect::updateOrCreate(
                [
                    'suspect_id' => $suspect->id,
                ],
                [
                'suspect_id' => $suspect->id,
                'accident_id' => $accidentId,
    
                'accident_type_id' => $vehicleAssociatedSuspect['accident_type_id'] ?? null,
                'vehicle_type_id' => $vehicleAssociatedSuspect['vehicle_type_id'] ?? null,
                'identity_type_id' => $vehicleAssociatedSuspect['identity_type_id'] ?? null,
                'accident_cause_id' => $vehicleAssociatedSuspect['accident_cause_id'] ?? null,
                'driving_license_type_id' => $vehicleAssociatedSuspect['driving_license_type_id'] ?? null,
                'identity_number' => $vehicleAssociatedSuspect['nomor_identitas'] ?? null,
                'identity_type' => $vehicleAssociatedSuspect['tipe_identitas'] ?? null,
                'driving_license_type' => $vehicleAssociatedSuspect['jenis_sim'] ?? null,
                'driver_name' => $vehicleAssociatedSuspect['nama_pengemudi'] ?? null,
                'accident_number' => $vehicleAssociatedSuspect['no_lp'] ?? null,
                'vehicle_type' => $vehicleAssociatedSuspect['jenis_ranmor'] ?? null,
                'plate_number' => $vehicleAssociatedSuspect['nopol'] ?? null,
                'accident_location' => $vehicleAssociatedSuspect['lokasi_kejadian'] ?? null,
                'accident_date' => $vehicleAssociatedSuspect['tgl_kejadian'] ?? null,
                'accident_type' => $vehicleAssociatedSuspect['jenis_kecelakaan'] ?? null,
                'accident_cause' => $vehicleAssociatedSuspect['penyebab'] ?? null,
                'total_victim' => $vehicleAssociatedSuspect['total_korban'] ?? null,
                'total_material_loss' => $vehicleAssociatedSuspect['total_kermat'] ?? null,
                'latitude' => $vehicleAssociatedSuspect['latitude'] ?? null,
                'longitude' => $vehicleAssociatedSuspect['longtitude'] ?? null,
            ]);
            
            DB::commit();
        }catch(\Exception $e){
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
        $suratKetetapanTentangPenetapanTersangkaDocumentId = $id;

        $suratKetetapanTentangPenetapanTersangkaDocument = SuratKetetapanTentangPenetapanTersangkaDocument::with(['suratPerintahPenyidikanDocument', 'laporanHasilGelarPerkaraDocument'])->where('id', $suratKetetapanTentangPenetapanTersangkaDocumentId)->first();
        $accident = Accident::with(['polres', 'polres.polda'])->where('id', $accidentId)->first();

        $viewData =[
            'accidentId' => $accidentId,
            'accident' => $accident,
            'suratKetetapanTentangPenetapanTersangkaDocument' => $suratKetetapanTentangPenetapanTersangkaDocument,
            'suratKetetapanTentangPenetapanTersangkaDocumentId' => $suratKetetapanTentangPenetapanTersangkaDocumentId,
        ];

        return view('docs.surat-ketetapan-tentang-penetapan-tersangka-document.show', $viewData);
    }

    public function edit($id)
    {
        // Get URL Parameter
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $suratKetetapanTentangPenetapanTersangkaDocumentId = $id;

        $vehicleListResponse = Http::withHeaders([
            "Key" => "16s08e23TBJ1hEXwAMSIH00eBI1F5BODfeLVlHMHnIZrNsDmtS=getdataKorbanirsmS",
            "Content-Type" => "application/json"
        ])
            ->get('https://irsms.korlantas.polri.go.id/irsmsapi/api/dataKorban', [
                'accident_id' => $accidentId
            ])
            ->json();
        $vehicleListCollection = collect($vehicleListResponse['result'] ?? []);
        
        $accidentTypes = AccidentType::where('is_active', true)
            ->get();
        $accidentTypesCollection = collect($accidentTypes);

        $vehicleTypes = VehicleType::where('is_active', true)
            ->get();
        $vehicleTypesCollection = collect($vehicleTypes);

        $accidentCauses = AccidentCause::where('is_active', true)
            ->get();
        $accidentCausesCollection = collect($accidentCauses);

        $identityTypes = IdentityType::where('is_active', true)
            ->orderBy('sort')
            ->get();
        $identityTypesCollection = collect($identityTypes);

        $drivingLicenseTypes = DrivingLicenseType::where('is_active', true)
            ->orderBy('sort')
            ->get();
        $drivingLicenseTypesCollection = collect($drivingLicenseTypes);
        
        $vehicleList = $vehicleListCollection->map(function ($vehicle) use ($accidentTypesCollection, $vehicleTypesCollection, $accidentCausesCollection, $identityTypesCollection, $drivingLicenseTypesCollection) {
            $accidentTypeMatch = $accidentTypesCollection->firstWhere('irsms_id', $vehicle['jenis_kecelakaan']);
            if($accidentTypeMatch){
                $vehicle['accident_type_id'] = $accidentTypeMatch['id'];
                $vehicle['accident_type_name'] = $accidentTypeMatch['name'];
            }

            $vehicleTypeMatch = $vehicleTypesCollection->firstWhere('irsms_id', $vehicle['jenis_ranmor']);
            if($vehicleTypeMatch){
                $vehicle['vehicle_type_id'] = $vehicleTypeMatch['id'];
                $vehicle['vehicle_type_name'] = $vehicleTypeMatch['name'];
            }

            $accidentCauseMatch = $accidentCausesCollection->firstWhere('irsms_id', $vehicle['penyebab']);
            if($accidentCauseMatch){
                $vehicle['accident_cause_id'] = $accidentCauseMatch['id'];
                $vehicle['accident_cause_name'] = $accidentCauseMatch['name'];
            }

            $identityTypeMatch = $identityTypesCollection->firstWhere('irsms_id', $vehicle['tipe_identitas']);
            if($identityTypeMatch){
                $vehicle['identity_type_id'] = $identityTypeMatch['id'];
                $vehicle['identity_type_name'] = $identityTypeMatch['name'];
            }

            $drivingLicenseTypeMatch = $drivingLicenseTypesCollection->firstWhere('irsms_id', $vehicle['jenis_sim'] ?? '-');
            if($drivingLicenseTypeMatch){
                $vehicle['driving_license_type_id'] = $drivingLicenseTypeMatch['id'];
                $vehicle['driving_license_type_name'] = $drivingLicenseTypeMatch['name'];
            }

            return $vehicle;
        });

        $suratKetetapanTentangPenetapanTersangkaDocument = SuratKetetapanTentangPenetapanTersangkaDocument::with(['suratKetetapanTentangPenetapanTersangkaDocumentOfficers', 'suspect'])->where('id', $suratKetetapanTentangPenetapanTersangkaDocumentId)->first();
        $accident = Accident::where('id',$accidentId)->first();
       
        $policeId = $accident->polres_id;

        $laporanHasilGelarPerkaraDocumentSuspectDeterminations = LaporanHasilGelarPerkaraDocument::whereHas('caseDegreeType', function($query){
                $query->where('id', '1');
        })
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->where('accident_id', $accidentId)
            ->get();
        
        $suspects = [];

        if($suratKetetapanTentangPenetapanTersangkaDocument->suspect_source_id == '4'){
            $suspects = Suspect::with(['vehicleAssociatedSuspect'])->where('accident_id', $accidentId)->get();

        }elseif($suratKetetapanTentangPenetapanTersangkaDocument->suspect_source_id == '5'){
            $suspects = Suspect::with(['vehicleAssociatedSuspect'])
            ->whereHas('laporanHasilGelarPerkaraDocuments', function($query) use ($suratKetetapanTentangPenetapanTersangkaDocument){
                $query->where('laporan_hasil_gelar_perkara_documents.id', $suratKetetapanTentangPenetapanTersangkaDocument->laporan_hasil_gelar_perkara_document_id);
            })->where('accident_id', $accidentId)->get();
        }

        $ranks = Rank::where('is_active', true)->wherePolri()->orderBy('sort')->get();

        $positions = Position::where('is_active', true)->orderBy('sort')->get();

        $getOldNewPolresIds = $this->getOldNewPolresIds($policeId);

        $authorizedSignatories = Officer::withRelated()
            ->selectFullName()
            ->where('police_id', $getOldNewPolresIds)
            ->whereHasUserActive()
            ->hasDataComplete()
            ->signatory()
            ->active()
            ->valid()
            ->orderBy('first_name')
            ->get();

        $timezones = Timezone::where('is_active', true)
                            ->orderBy('sort')
                            ->get();

        $suspectSources = SuspectSource::where('is_active', true)
                                        ->orderBy('sort')
                                        ->get();

        $genders = Gender::where('is_active', true)
                        ->orderBy('sort')
                        ->get();

        $ethnics = Ethnic::where('is_active', true)
            ->orderBy('sort')
            ->get();

        $jobs = Job::where('is_active', true)
                    ->orderBy('sort')
                    ->get();

        $religions = Religion::where('is_active', true)
                            ->orderBy('sort')
                            ->get();

        $educations = Education::where('is_active', true)
                            ->orderBy('sort')
                            ->get();

        $maritalStatuses = MaritalStatus::where('is_active', true)
                                    ->orderBy('sort')
                                    ->get();

        $countries = Location::where('is_active', true)
                            ->where('class', 'COUNTRY')
                            ->orderBy('sort')
                            ->get();

        $prosecutors = Prosecutor::whereHas('polices', function($query) use ($policeId){
                $query->where('lib.polices.id', $policeId);
            })
            ->active()
            ->orderBy('sort')
            ->get();

        $suspectSources = SuspectSource::where('group', 'SURAT_KETETAPAN_TENTANG_PENETAPAN_TERSANGKA')
                                ->where('is_active', true)
                                ->orderBy('sort')
                                ->get();
                                
        $suratPerintahPenyidikanDocuments = SuratPerintahPenyidikanDocument::where('accident_id', $accidentId)
            ->whereIn('status_id', $this->docService->requiredDocumentStatusIds)
            ->get();

        $viewData =[
            'authorizedSignatories' => $authorizedSignatories,
            'accidentId' => $accidentId,
            'accident' => $accident,
            'suratKetetapanTentangPenetapanTersangkaDocument' => $suratKetetapanTentangPenetapanTersangkaDocument,
            'suratKetetapanTentangPenetapanTersangkaDocumentId' => $suratKetetapanTentangPenetapanTersangkaDocumentId,
            'ranks' => $ranks,
            'positions' => $positions,
            'officers' => $suratKetetapanTentangPenetapanTersangkaDocument->suratKetetapanTentangPenetapanTersangkaDocumentOfficers,
            'suratPerintahPenyidikanDocuments' => $suratPerintahPenyidikanDocuments,

            'timezones' => $timezones,
            'suspectSources' => $suspectSources,
            'identityTypes' => $identityTypes,
            'genders' => $genders,
            'ethnics' => $ethnics,
            'jobs' => $jobs,
            'religions' => $religions,
            'educations' => $educations,
            'maritalStatuses' => $maritalStatuses,
            'countries' => $countries,
            'prosecutors' => $prosecutors,
            'suspectSources' => $suspectSources,
            'laporanHasilGelarPerkaraDocumentSuspectDeterminations' => $laporanHasilGelarPerkaraDocumentSuspectDeterminations,
            'suratKetetapanTentangPenetapanTersangkaDocumentOfficers' => $suratKetetapanTentangPenetapanTersangkaDocument->suratKetetapanTentangPenetapanTersangkaDocumentOfficers,
            'suspects' => $suspects,
            'vehicleList' => $vehicleList,
        ];

        return view('docs.surat-ketetapan-tentang-penetapan-tersangka-document.edit', $viewData);
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
        $suratKetetapanTentangPenetapanTersangkaDocumentId = $id;

        // Define & Sanitize Text Input
        $user = Auth::user();
        $documentDate = htmlspecialchars($request->documentDate);
        $documentNumber = htmlspecialchars($request->documentNumber);
        $suratPerintahPenyidikanDocumentId = htmlspecialchars($request->suratPerintahPenyidikanDocument);
        $prosecutorId = htmlspecialchars($request->prosecutor);
        $signatoryId = htmlspecialchars($request->signatory);
        $suspectSourceId = htmlspecialchars($request->suspectSource);
        $laporanHasilGelarPerkaraDocumentSuspectDeterminationId = ($request->laporanHasilGelarPerkaraDocumentSuspectDetermination) ? htmlspecialchars($request->laporanHasilGelarPerkaraDocumentSuspectDetermination) : NULL;
        $resumeSuspectDeterminationDate = ($request->resumeSuspectDeterminationDate) ? htmlspecialchars($request->resumeSuspectDeterminationDate) : NULL;
        $suspectId = $request->suspect;

        $identityTypeIdSuspect = $request->identityTypeFieldSuspect;
        $identityNumberSuspect = htmlspecialchars($request->identityNumberFieldSuspect);
        $nameSuspect = htmlspecialchars($request->nameFieldSuspect);
        $genderIdSuspect = htmlspecialchars($request->genderFieldSuspect);
        $birthPlaceSuspect = htmlspecialchars($request->birthPlaceFieldSuspect);
        $birthDateSuspect = htmlspecialchars($request->birthDateFieldSuspect);
        $fatherSuspect = htmlspecialchars($request->fatherFieldSuspect);
        $motherSuspect = htmlspecialchars($request->motherFieldSuspect);
        $nationalitySuspect = htmlspecialchars($request->nationalityFieldSuspect);
        $ethnicIdSuspect = htmlspecialchars($request->ethnicFieldSuspect);
        $jobIdSuspect = htmlspecialchars($request->jobFieldSuspect);
        $religionIdSuspect = htmlspecialchars($request->religionFieldSuspect);
        $educationIdSuspect = htmlspecialchars($request->educationFieldSuspect);
        $maritalStatusIdSuspect = htmlspecialchars($request->maritalStatusFieldSuspect);
        $phoneNumberSuspect = htmlspecialchars($request->phoneNumberFieldSuspect);
        $emailSuspect = htmlspecialchars($request->emailFieldSuspect);
        $countryIdSuspect = htmlspecialchars($request->countryFieldSuspect);
        $provinceIdSuspect = htmlspecialchars($request->provinceFieldSuspect);
        $regencyIdSuspect = htmlspecialchars($request->regencyFieldSuspect);
        $districtIdSuspect = htmlspecialchars($request->districtFieldSuspect);
        $villageIdSuspect = htmlspecialchars($request->villageFieldSuspect);
        $addressSuspect = htmlspecialchars($request->addressFieldSuspect);
        
        $isUnknownGenderSuspect = $request->isUnknownGenderFieldSuspect;
        $isUnknownBirthPlaceSuspect = $request->isUnknownBirthPlaceFieldSuspect;
        $isUnknownBirthDateSuspect = $request->isUnknownBirthDateFieldSuspect;
        $isUnknownFatherSuspect = $request->isUnknownFatherFieldSuspect;
        $isUnknownMotherSuspect = $request->isUnknownMotherFieldSuspect;
        $isUnknownNationalitySuspect = $request->isUnknownNationalityFieldSuspect;
        $isUnknownMaritalStatusSuspect = $request->isUnknownMaritalStatusFieldSuspect;
        $isExistsPhoneNumberSuspect = $request->isExistsPhoneNumberFieldSuspect;
        $isExistsEmailSuspect = $request->isExistsEmailFieldSuspect;
        $isAvailablePhoneNumberSuspect = $request->isAvailablePhoneNumberFieldSuspect;
        $isAvailableEmailSuspect = $request->isAvailableEmailFieldSuspect;
        $isUnknownAddressSuspect = $request->isUnknownAddressFieldSuspect;

        $isLegacy = filter_var($request->isLegacy, FILTER_VALIDATE_BOOLEAN);

        $vehicleSuspect = htmlspecialchars($request->vehicleFieldSuspect);

        // Check if document number already exist
        $suratKetetapanTentangPenetapanTersangkaDocument = SuratKetetapanTentangPenetapanTersangkaDocument::findOrFail($id);
        $oldDocumentNumber = $suratKetetapanTentangPenetapanTersangkaDocument->document_number;
        if (strtolower($oldDocumentNumber) != strtolower($documentNumber)) {
            $exists = SuratKetetapanTentangPenetapanTersangkaDocument::where('accident_id', $request->input('accident_id'))
                ->where('document_number', 'ILIKE', $documentNumber)
                ->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'Dokumen ' . $documentNumber . ' Sudah Anda Buat Sebelumnya.');
            }
        }

        DB::beginTransaction();
        try{
            $vehicleListResponse = Http::withHeaders([
                "Key" => "16s08e23TBJ1hEXwAMSIH00eBI1F5BODfeLVlHMHnIZrNsDmtS=getdataKorbanirsmS",
                "Content-Type" => "application/json"
            ])
                ->get('https://irsms.korlantas.polri.go.id/irsmsapi/api/dataKorban', [
                    'accident_id' => $accidentId
                ])
                ->json();
            $vehicleListCollection = collect($vehicleListResponse['result'] ?? []);
            
            $accidentTypes = AccidentType::where('is_active', true)
                ->get();
            $accidentTypesCollection = collect($accidentTypes);
    
            $vehicleTypes = VehicleType::where('is_active', true)
                ->get();
            $vehicleTypesCollection = collect($vehicleTypes);
    
            $accidentCauses = AccidentCause::where('is_active', true)
                ->get();
            $accidentCausesCollection = collect($accidentCauses);
    
            $identityTypes = IdentityType::where('is_active', true)
                ->orderBy('sort')
                ->get();
            $identityTypesCollection = collect($identityTypes);

            $drivingLicenseTypes = DrivingLicenseType::where('is_active', true)
                ->orderBy('sort')
                ->get();
            $drivingLicenseTypesCollection = collect($drivingLicenseTypes);
            
            $vehicleList = $vehicleListCollection->map(function ($vehicle) use ($accidentTypesCollection, $vehicleTypesCollection, $accidentCausesCollection, $identityTypesCollection, $drivingLicenseTypesCollection) {
                $accidentTypeMatch = $accidentTypesCollection->firstWhere('irsms_id', $vehicle['jenis_kecelakaan']);
                if($accidentTypeMatch){
                    $vehicle['accident_type_id'] = $accidentTypeMatch['id'];
                    $vehicle['accident_type_name'] = $accidentTypeMatch['name'];
                }
    
                $vehicleTypeMatch = $vehicleTypesCollection->firstWhere('irsms_id', $vehicle['jenis_ranmor']);
                if($vehicleTypeMatch){
                    $vehicle['vehicle_type_id'] = $vehicleTypeMatch['id'];
                    $vehicle['vehicle_type_name'] = $vehicleTypeMatch['name'];
                }
    
                $accidentCauseMatch = $accidentCausesCollection->firstWhere('irsms_id', $vehicle['penyebab']);
                if($accidentCauseMatch){
                    $vehicle['accident_cause_id'] = $accidentCauseMatch['id'];
                    $vehicle['accident_cause_name'] = $accidentCauseMatch['name'];
                }
    
                $identityTypeMatch = $identityTypesCollection->firstWhere('irsms_id', $vehicle['tipe_identitas']);
                if($identityTypeMatch){
                    $vehicle['identity_type_id'] = $identityTypeMatch['id'];
                    $vehicle['identity_type_name'] = $identityTypeMatch['name'];
                }

                $drivingLicenseTypeMatch = $drivingLicenseTypesCollection->firstWhere('irsms_id', $vehicle['jenis_sim'] ?? '-');    
                if($drivingLicenseTypeMatch){
                    $vehicle['driving_license_type_id'] = $drivingLicenseTypeMatch['id'];
                    $vehicle['driving_license_type_name'] = $drivingLicenseTypeMatch['name'];
                }
    
                return $vehicle;
            });
            $vehicleAssociatedSuspect = $vehicleList->firstWhere('nopol', $vehicleSuspect);

            // Update to database
            $suratKetetapanTentangPenetapanTersangkaDocument = SuratKetetapanTentangPenetapanTersangkaDocument::where('id', $suratKetetapanTentangPenetapanTersangkaDocumentId)->first();

            $suratKetetapanTentangPenetapanTersangkaDocument->update([
                'accident_id'=> $accidentId,
                'surat_perintah_penyidikan_document_id'=> $suratPerintahPenyidikanDocumentId,
                'document_number'=> $documentNumber,
                'document_date'=> $documentDate,
                'suspect_source_id'=> $suspectSourceId,
                'laporan_hasil_gelar_perkara_document_id'=> $laporanHasilGelarPerkaraDocumentSuspectDeterminationId,
                'resume_suspect_determination_date'=> $resumeSuspectDeterminationDate,
                'prosecutor_id'=> $prosecutorId,
                'is_legacy' => $isLegacy,
            ]);

            $suratKetetapanTentangPenetapanTersangkaDocumentId = $suratKetetapanTentangPenetapanTersangkaDocument->id;

            // SIGNATORY
            $signatory = Officer::where('id',$signatoryId)->first();
            $suratKetetapanTentangPenetapanTersangkaDocument->suratKetetapanTentangPenetapanTersangkaDocumentOfficers()
                ->updateOrCreate(
                    [
                        'class' => SuratKetetapanTentangPenetapanTersangkaDocumentOfficer::getEnumOption('class', 'SIGNATORY'),
                    ],
                    [
                        'surat_ketetapan_tentang_penetapan_tersangka_document_id' => $suratKetetapanTentangPenetapanTersangkaDocumentId,
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
                        'status' => SuratKetetapanTentangPenetapanTersangkaDocumentOfficer::getEnumOption('status', 'PRESENT'),
                        'class' => SuratKetetapanTentangPenetapanTersangkaDocumentOfficer::getEnumOption('class', 'SIGNATORY'),
                        'flag' => SuratKetetapanTentangPenetapanTersangkaDocumentOfficer::getEnumOption('flag', 'INTERNAL'),
                        'insert_method' => SuratKetetapanTentangPenetapanTersangkaDocumentOfficer::getEnumOption('flag', 'IMPORT'),
                    ]
                );

            // SUSPECT
            $suspect = Suspect::where('id', $suspectId)->first();
        
            $suspect->update([
                'accident_id' => $accidentId,
                'identity_number' => $identityNumberSuspect,
                'name' => $nameSuspect,
                'birth_place' => $birthPlaceSuspect,
                'birth_date' => $birthDateSuspect,
                'phone_number' => $phoneNumberSuspect,
                'email_address' => $emailSuspect,
                'father_name' => $fatherSuspect,
                'mother_name' => $motherSuspect,
                'nationality' => $nationalitySuspect,
    
                'address' => $addressSuspect,
                'identity_type_id' => ($identityTypeIdSuspect) ? $identityTypeIdSuspect : null,
                'gender_id' => ($genderIdSuspect) ? $genderIdSuspect : null,
                'ethnic_id' => ($ethnicIdSuspect) ? $ethnicIdSuspect : null,
                'job_id' => ($jobIdSuspect) ? $jobIdSuspect : null,
                'religion_id' => ($religionIdSuspect) ? $religionIdSuspect : null,
                'education_id' => ($educationIdSuspect) ? $educationIdSuspect : null,
                'marital_status_id' => ($maritalStatusIdSuspect) ? $maritalStatusIdSuspect : null,

                'flag' => Suspect::getEnumOption('flag', 'TERSANGKA'),
                'class' => Suspect::getEnumOption('class', 'DETERMINATION'),
                'group' => Suspect::getEnumOption('group', 'SURAT_KETETAPAN_TENTANG_PENETAPAN_TERSANGKA'),

                'country_id' => ($countryIdSuspect) ? $countryIdSuspect : null,
                'province_id' => ($provinceIdSuspect) ? $provinceIdSuspect : null,
                'regency_id' => ($regencyIdSuspect) ? $regencyIdSuspect : null,
                'district_id' => ($districtIdSuspect) ? $districtIdSuspect : null,
                'village_id' => ($villageIdSuspect) ? $villageIdSuspect : null,

                'properties' => [
                    'is_exists_phone_number' => ($isExistsPhoneNumberSuspect == 'null' || $isExistsPhoneNumberSuspect == 'undefined' || $isExistsPhoneNumberSuspect == null) ? null : filter_var($isExistsPhoneNumberSuspect, FILTER_VALIDATE_BOOLEAN),
                    'is_available_phone_number' => ($isAvailablePhoneNumberSuspect == 'null' || $isAvailablePhoneNumberSuspect == 'undefined' || $isAvailablePhoneNumberSuspect == null) ? null : filter_var($isAvailablePhoneNumberSuspect, FILTER_VALIDATE_BOOLEAN),
                    'is_exists_email' => ($isExistsEmailSuspect == 'null' || $isExistsEmailSuspect == 'undefined' || $isExistsEmailSuspect == null) ? null : filter_var($isExistsEmailSuspect, FILTER_VALIDATE_BOOLEAN),
                    'is_available_email' => ($isAvailableEmailSuspect == 'null' || $isAvailableEmailSuspect == 'undefined' || $isAvailableEmailSuspect == null) ? null : filter_var($isAvailableEmailSuspect, FILTER_VALIDATE_BOOLEAN),
                    'is_unknown_gender' => ($isUnknownGenderSuspect == 'null' || $isUnknownGenderSuspect == 'undefined' || $isUnknownGenderSuspect == null) ? null : filter_var($isUnknownGenderSuspect, FILTER_VALIDATE_BOOLEAN),
                    'is_unknown_birth_place' => ($isUnknownBirthPlaceSuspect == 'null' || $isUnknownBirthPlaceSuspect == 'undefined' || $isUnknownBirthPlaceSuspect == null) ? null : filter_var($isUnknownBirthPlaceSuspect, FILTER_VALIDATE_BOOLEAN),
                    'is_unknown_birth_date' => ($isUnknownBirthDateSuspect == 'null' || $isUnknownBirthDateSuspect == 'undefined' || $isUnknownBirthDateSuspect == null) ? null : filter_var($isUnknownBirthDateSuspect, FILTER_VALIDATE_BOOLEAN),
                    'is_unknown_father' => ($isUnknownFatherSuspect == 'null' || $isUnknownFatherSuspect == 'undefined' || $isUnknownFatherSuspect == null) ? null : filter_var($isUnknownFatherSuspect, FILTER_VALIDATE_BOOLEAN),
                    'is_unknown_mother' => ($isUnknownMotherSuspect == 'null' || $isUnknownMotherSuspect == 'undefined' || $isUnknownMotherSuspect == null) ? null : filter_var($isUnknownMotherSuspect, FILTER_VALIDATE_BOOLEAN),
                    'is_unknown_nationality' => ($isUnknownNationalitySuspect == 'null' || $isUnknownNationalitySuspect == 'undefined' || $isUnknownNationalitySuspect == null) ? null : filter_var($isUnknownNationalitySuspect, FILTER_VALIDATE_BOOLEAN),
                    'is_unknown_marital_status' => ($isUnknownMaritalStatusSuspect == 'null' || $isUnknownMaritalStatusSuspect == 'undefined' || $isUnknownMaritalStatusSuspect == null) ? null : filter_var($isUnknownMaritalStatusSuspect, FILTER_VALIDATE_BOOLEAN),
                    'is_unknown_address' => ($isUnknownAddressSuspect == 'null' || $isUnknownAddressSuspect == 'undefined' || $isUnknownAddressSuspect == null) ? null : filter_var($isUnknownAddressSuspect, FILTER_VALIDATE_BOOLEAN),
                ],
            ]);
 
            $suratKetetapanTentangPenetapanTersangkaDocument->suspect()->detach();
            $suratKetetapanTentangPenetapanTersangkaDocument->suspect()->attach($suspect->id);
        
            VehicleAssociatedSuspect::where('suspect_id', $suspect->id)->update([
                'suspect_id' => $suspect->id,
                'accident_id' => $accidentId,
    
                'accident_type_id' => $vehicleAssociatedSuspect['accident_type_id'] ?? null,
                'vehicle_type_id' => $vehicleAssociatedSuspect['vehicle_type_id'] ?? null,
                'identity_type_id' => $vehicleAssociatedSuspect['identity_type_id'] ?? null,
                'accident_cause_id' => $vehicleAssociatedSuspect['accident_cause_id'] ?? null,
                'driving_license_type_id' => $vehicleAssociatedSuspect['driving_license_type_id'] ?? null,
                'identity_number' => $vehicleAssociatedSuspect['nomor_identitas'] ?? null,
                'identity_type' => $vehicleAssociatedSuspect['tipe_identitas'] ?? null,
                'driving_license_type' => $vehicleAssociatedSuspect['jenis_sim'] ?? null,
                'driver_name' => $vehicleAssociatedSuspect['nama_pengemudi'] ?? null,
                'accident_number' => $vehicleAssociatedSuspect['no_lp'] ?? null,
                'vehicle_type' => $vehicleAssociatedSuspect['jenis_ranmor'] ?? null,
                'plate_number' => $vehicleAssociatedSuspect['nopol'] ?? null,
                'accident_location' => $vehicleAssociatedSuspect['lokasi_kejadian'] ?? null,
                'accident_date' => $vehicleAssociatedSuspect['tgl_kejadian'] ?? null,
                'accident_type' => $vehicleAssociatedSuspect['jenis_kecelakaan'] ?? null,
                'accident_cause' => $vehicleAssociatedSuspect['penyebab'] ?? null,
                'total_victim' => $vehicleAssociatedSuspect['total_korban'] ?? null,
                'total_material_loss' => $vehicleAssociatedSuspect['total_kermat'] ?? null,
                'latitude' => $vehicleAssociatedSuspect['latitude'] ?? null,
                'longitude' => $vehicleAssociatedSuspect['longtitude'] ?? null,
            ]);

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
        $suratKetetapanTentangPenetapanTersangkaDocumentId = $id;

        DB::beginTransaction();
        try{
            // Delete from database
            $suratKetetapanTentangPenetapanTersangkaDocument = SuratKetetapanTentangPenetapanTersangkaDocument::where('id', $suratKetetapanTentangPenetapanTersangkaDocumentId)->first();
            $suratKetetapanTentangPenetapanTersangkaDocument->delete();

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
        $suratKetetapanTentangPenetapanTersangkaDocumentId = $id;
        
        $suratKetetapanTentangPenetapanTersangkaDocument = SuratKetetapanTentangPenetapanTersangkaDocument::with(['suratKetetapanTentangPenetapanTersangkaDocumentOfficers', 'laporanHasilGelarPerkaraDocument', 'suspect', 'suratPerintahPenyidikanDocument'])->where('id', $suratKetetapanTentangPenetapanTersangkaDocumentId)->first();
        $signatory = $suratKetetapanTentangPenetapanTersangkaDocument->suratKetetapanTentangPenetapanTersangkaDocumentOfficers->where('class','=', SuratKetetapanTentangPenetapanTersangkaDocumentOfficer::getEnumOption('class', 'SIGNATORY'))->first();
        $suspect = $suratKetetapanTentangPenetapanTersangkaDocument->suspect->first();
      
        $accident = Accident::with(['polres'])->where('id', $accidentId)->first();

        $signatoryHeadText = [
            'KAPOLRES' => 'KEPALA KEPOLISIAN RESOR ' . $accident->polres->full_name,
            'NO_KAPOLRES' => 'a.n. KEPALA KEPOLISIAN RESOR ' . $accident->polres->full_name,
            'NO_DIRLANTAS' => 'a.n. DIREKTUR LALU LINTAS POLDA ' . $accident->polres->polda->full_name,
        ];

        $signatoryPositionHeadText = [
            'NO_KAPOLRES' => $signatory->position->positionCluster->alias_name ?? '',
            'NO_DIRLANTAS' => $signatory->position->positionCluster->alias_name ?? '',
        ];

        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('word-template/surat_ketetapan_tentang_penetapan_tersangka.docx');

        if(isset($signatory->position)){
            if($signatory->position->position_cluster_id == '1'){
                $templateProcessor->setValue('signatoryHeadText', $signatoryHeadText['KAPOLRES']);
                $templateProcessor->setValue('signatoryPositionHeadText', '');
            }else if($signatory->position->position_cluster_id == '9'){
                $templateProcessor->setValue('signatoryHeadText', $signatoryHeadText['NO_DIRLANTAS']);
                $templateProcessor->setValue('signatoryPositionHeadText', $signatoryPositionHeadText['NO_DIRLANTAS']);
            }else{
                $templateProcessor->setValue('signatoryHeadText', $signatoryHeadText['NO_KAPOLRES']);
                $templateProcessor->setValue('signatoryPositionHeadText', $signatoryPositionHeadText['NO_KAPOLRES']);
            }
        }

        $documentDate = Carbon::parse($suratKetetapanTentangPenetapanTersangkaDocument->document_date)->locale('id')->translatedFormat('d F Y');
        $documentNumber = $suratKetetapanTentangPenetapanTersangkaDocument->document_number;
        
        $accidentNumber = $accident->no_lp;
        $accidentDate = Carbon::parse($accident->accident_date)->locale('id')->translatedFormat('d F Y');
        $reportDate = Carbon::parse($accident->report_date)->locale('id')->translatedFormat('d F Y');
        
        $prosecutor = $suratKetetapanTentangPenetapanTersangkaDocument->prosecutor;
        $prosecutorName = $prosecutor->name;

        $suspectName = $suspect->name ?? '';
        $suspectIdentityNumber = $suspect->identity_number ?? '';
        $suspectNationality = $suspect->nationality ?? '';
        $suspectBirthPlace = $suspect->birth_place ?? '';
        $suspectBirthDate = (!empty($suspect->birth_date)) ? Carbon::parse($suspect->birth_date)->locale('id')->translatedFormat('d F Y') : '-';
        $suspectGender = $suspect->gender()->first() ?? '';
        $suspectGenderName = $suspectGender->name ?? '';
        $suspectJob = $suspect->job()->first() ?? '';
        $suspectJobName = $suspectJob->name ?? '';
        $suspectReligion = $suspect->religion()->first() ?? '';
        $suspectReligionName = $suspectReligion->name ?? '';

        $suspectCountry = $suspect->country()->first() ?? '';
        $suspectCountryName = $suspectCountry->name ?? '';
        $suspectProvince = $suspect->province()->first() ?? '';
        $suspectProvinceName = ($suspectProvince) ? ', ' . $suspectProvince->name : '';
        $suspectRegency = $suspect->regency()->first() ?? '';
        $suspectRegencyName = ($suspectRegency) ? ', ' . $suspectRegency->name : '';
        $suspectDistrict = $suspect->district()->first() ?? '';
        $suspectDistrictName = ($suspectDistrict) ? ', ' . $suspectDistrict->name : '';
        $suspectVillage = $suspect->village()->first() ?? '';
        $suspectVillageName = ($suspectVillage) ? ', ' . $suspectVillage->name : '';
        $suspectAddress = $suspect->address ?? '';
        $suspectFullAddress = ucwords(strtolower($suspectAddress . $suspectVillageName . $suspectDistrictName . $suspectRegencyName . $suspectProvinceName));
        
        $suratPerintahPenyidikanDocument = $suratKetetapanTentangPenetapanTersangkaDocument->suratPerintahPenyidikanDocument;
        $suratPerintahPenyidikanDocumentNumber = $suratPerintahPenyidikanDocument->document_number;
        $suratPerintahPenyidikanDocumentDocumentDate = Carbon::parse($suratPerintahPenyidikanDocument->document_date)->locale('id')->translatedFormat('d F Y');
        
        $laporanHasilGelarPerkaraDocument = $suratKetetapanTentangPenetapanTersangkaDocument->laporanHasilGelarPerkaraDocument;
        $laporanHasilGelarPerkaraDocumentDate = Carbon::parse($laporanHasilGelarPerkaraDocument->date)->locale('id')->translatedFormat('d F Y');

        $daerahPolice = $accident->polres->polda;
        $daerahPoliceFullName = $daerahPolice->full_name;
        
        $resorPolice = $accident->polres;
        $resorPoliceAddress = $resorPolice->address . ', ' . $resorPolice->polres_zipcode;
        $resorPoliceFullName = (in_array($resorPolice->id, ['1114'])) ? 'DIREKTORAT LALU LINTAS' : 'RESOR ' . strtoupper($resorPolice->full_name);
        $resorPoliceProvinceName = $resorPolice->polres_province;
        
        $documentLocation = $resorPoliceProvinceName;

        $signatoryName = PeopleNameHelper::getFullName($signatory->first_title, $signatory->first_name, $signatory->last_name, $signatory->last_title);
        $signatoryRankName = $signatory->rank->name ?? '';
        $signatoryRegisterNumber = $signatory->register_number;

        //===============================================================

        $templateProcessor->setValue('documentDate', $documentDate);
        $templateProcessor->setValue('documentNumber', $documentNumber);
        $templateProcessor->setValue('documentLocation', $documentLocation);
       
        $templateProcessor->setValue('accidentNumber', $accidentNumber);
        $templateProcessor->setValue('accidentDate', $accidentDate);
        $templateProcessor->setValue('reportDate', $reportDate);

        $templateProcessor->setValue('prosecutorName', $prosecutorName);

        $templateProcessor->setValue('suspectName', $suspectName);
        $templateProcessor->setValue('suspectIdentityNumber', $suspectIdentityNumber);
        $templateProcessor->setValue('suspectNationality', $suspectNationality);
        $templateProcessor->setValue('suspectBirthPlace', $suspectBirthPlace);
        $templateProcessor->setValue('suspectBirthDate', $suspectBirthDate);
        $templateProcessor->setValue('suspectGenderName', $suspectGenderName);
        $templateProcessor->setValue('suspectJobName', $suspectJobName);
        $templateProcessor->setValue('suspectReligionName', $suspectReligionName);
        $templateProcessor->setValue('suspectFullAddress', $suspectFullAddress);
       
        $templateProcessor->setValue('suratPerintahPenyidikanDocumentNumber', $suratPerintahPenyidikanDocumentNumber);
        $templateProcessor->setValue('suratPerintahPenyidikanDocumentDocumentDate', $suratPerintahPenyidikanDocumentDocumentDate);
       
        $templateProcessor->setValue('laporanHasilGelarPerkaraDocumentDate', $laporanHasilGelarPerkaraDocumentDate);
       
        $templateProcessor->setValue('daerahPoliceFullName', strtoupper($daerahPoliceFullName));
       
        $templateProcessor->setValue('resorPoliceAddress', $resorPoliceAddress);
        $templateProcessor->setValue('resorPoliceFullName', strtoupper($resorPoliceFullName));
       
        $templateProcessor->setValue('signatoryName', $signatoryName);
        $templateProcessor->setValue('signatoryRankName', strtoupper($signatoryRankName));
        $templateProcessor->setValue('signatoryRegisterNumber', $signatoryRegisterNumber);

        $filename = 'generate/' . Str::uuid() . ' - Surat Ketetapan Tentang Penetapan Tersangka - Resor ' . $accident->polres->full_name;
        $templateProcessor->saveAs($filename.'.docx');
        return response()->download($filename.'.docx')->deleteFileAfterSend(true);
    }

    // =====( API )=====
    public function getSuspects(Request $request){
        // Get URL Parameter
        $accidentId = htmlspecialchars(request()->query('accident_id'));

        $class = $request->class;
        $flag = $request->flag;
        $notFlag = $request->notFlag;
        $group = $request->group;
        $laporanHasilGelarPerkaraDocumentId = $request->laporanHasilGelarPerkaraDocumentId;
        
        $suspects = Suspect::with(['identityType'])->where('accident_id', $accidentId)->get();
        
        if($laporanHasilGelarPerkaraDocumentId){
            $suspects = Suspect::with(['identityType'])->where('accident_id', $accidentId)
                ->whereHas('laporanHasilGelarPerkaraDocuments', function($query) use ($laporanHasilGelarPerkaraDocumentId){
                    $query->where('laporan_hasil_gelar_perkara_documents.id', $laporanHasilGelarPerkaraDocumentId);
                })->get();
        }

        if($class){
            $suspects = $suspects->where('class', $class);
        }
        
        if($flag){
            $suspects = $suspects->where('flag', $flag);
        }
       
        if($notFlag){
            $suspects = $suspects->where('flag', '!=', $notFlag);
        }
    
        if($group){
            $suspects = $suspects->where('group', $group);
        }


        return response()->json([
            'status' => 'success',
            'code' => 200,
            'data' => $suspects
        ], 200);
    }

    public function getLocations(Request $request){
        $class = $request->class;
        $parent_id = $request->parent_id;

        $locations = Location::where('is_active', true)
                        ->where('parent_id', $parent_id)
                        ->where('class', $class)
                        ->get();

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'data' => $locations
        ], 200);
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
            'documentNumber' => 'required | min:5 | max:255 | regex:/^(?=.*[a-zA-Z])(?=.*[0-9])(?=.*\/).+$/',
            'documentDate' => 'required',
            'suratPerintahPenyidikanDocument' => 'required',

            'suspectSource' => 'required',
            'resumeSuspectDeterminationDate' => 'required_if:suspectSource,"4"',
            'laporanHasilGelarPerkaraDocumentSuspectDetermination' => 'required_if:suspectSource,"5"',

            'prosecutor' => 'required',
            'signatory' => 'required',

            'suspect' => 'required',

            'identityTypeFieldSuspect' => 'required_if:suspectSource,"5"',
            'identityNumberFieldSuspect' => 'required_if:suspectSource,"5"|numeric',
            'nameFieldSuspect' => 'required_if:suspectSource,"5"|max:255',
            'genderFieldSuspect' => 'required_if:suspectSource,"5"',
            'birthPlaceFieldSuspect' => 'required_if:suspectSource,"5"|max:255',
            'birthDateFieldSuspect' => 'required_if:suspectSource,"5"',
            'fatherFieldSuspect' => [
                Rule::requiredIf(function () use ($request) {
                    return $request->suspectSource == '5' && $request->isUnknownFatherFieldSuspect != 'true';
                })
            ],
            'motherFieldSuspect' => [
                Rule::requiredIf(function () use ($request) {
                    return $request->suspectSource == '5' && $request->isUnknownMotherFieldSuspect != 'true';
                })
            ],
            'nationalityFieldSuspect' => 'required_if:suspectSource,"5"',
            'ethnicFieldSuspect' => 'required_if:suspectSource,"5"',
            'jobFieldSuspect' => 'required_if:suspectSource,"5"',
            'religionFieldSuspect' => 'required_if:suspectSource,"5"',
            'educationFieldSuspect' => 'required_if:suspectSource,"5"',
            'maritalStatusFieldSuspect' => 'required_if:suspectSource,"5"',
            'addressFieldSuspect' => 'required_if:suspectSource,"5"',
            
            'countryFieldSuspect' => 'required_if:suspectSource,"5"',
            'provinceFieldSuspect' => 'required_if:countryFieldSuspect,C101',
            'regencyFieldSuspect' => 'required_if:countryFieldSuspect,C101',
            'districtFieldSuspect' => 'required_if:countryFieldSuspect,C101',
            'villageFieldSuspect' => 'required_if:countryFieldSuspect,C101',

            'vehicleFieldSuspect' => [
                Rule::requiredIf(function () use ($request) {
                    return $request->caseFlag != 'JATANLIN';
                })
            ],
        ], [
            'suratPerintahPenyidikanDocument.required' => 'No Surat Perintah Penyidikan harus diisi',

            'documentDate.required' => 'Tanggal Dokumen harus diisi',
            'documentNumber.required' => 'No Dokumen harus diisi',
            'documentNumber.max' => 'No Dokumen maksimal 255 karakter',
            'documentNumber.min' => 'No Dokumen harus lengkap',
            'documentNumber.regex' => 'No Dokumen harus lengkap',

            'suspectSource.required' => 'Sumber Tersangka harus diisi',
            'resumeSuspectDeterminationDate.required_if' => 'Tanggal Resume Penetapan Tersangka harus diisi',
            'laporanHasilGelarPerkaraDocumentSuspectDetermination.required_if' => 'Tanggal Gelar Perkara Penetapan Tersangka harus diisi',
            
            'prosecutor.required' => 'Kejaksaan harus diisi',
            'signatory.required' => 'Yang Menandatangani harus diisi',

            'suspect.required' => 'Tersangka harus diisi',

            'nameFieldSuspect.required_if' => 'Nama Tersangka harus diisi',
            'nameFieldSuspect.max' => 'Nama Tersangka maksimal 255 karakter',
            'genderFieldSuspect.required_if' => 'Jenis Kelamin Tersangka harus diisi',
            'birthPlaceFieldSuspect.required_if' => 'Tempat Lahir Tersangka harus diisi',
            'birthPlaceFieldSuspect.max' => 'Tempat Lahir Tersangka maksimal 255 karakter',
            'birthDateFieldSuspect.required_if' => 'Tanggal Lahir Tersangka harus diisi',
            'identityTypeFieldSuspect.required_if' => 'Jenis Identitas Tersangka harus diisi',
            'identityNumberFieldSuspect.required_if' => 'Nomor Identitas Tersangka harus diisi',
            'identityNumberFieldSuspect.numeric' => 'Nomor Identitas Tersangka harus berupa angka',
            'fatherFieldSuspect.required' => 'Nama Ayah Tersangka harus diisi, atau Pilih Tidak tahu',
            'motherFieldSuspect.required' => 'Nama Ibu Tersangka harus diisi, atau Pilih Tidak tahu',
            'nationalityFieldSuspect.required_if' => 'Kebangsaan Tersangka harus diisi',
            'ethnicFieldSuspect.required_if' => 'Suku Tersangka harus diisi',
            'jobFieldSuspect.required_if' => 'Pekerjaan Tersangka harus diisi',
            'religionFieldSuspect.required_if' => 'Agama Tersangka harus diisi',
            'educationFieldSuspect.required_if' => 'Pendidikan Tersangka harus diisi',
            'maritalStatusFieldSuspect.required_if' => 'Status Kawin Tersangka harus diisi',
            'addressFieldSuspect.required_if' => 'Alamat Tersangka harus diisi',

            'countryFieldSuspect.required_if' => 'Negara harus diisi',
            'provinceFieldSuspect.required_if' => 'Provinsi harus diisi',
            'regencyFieldSuspect.required_if' => 'Kabupaten/Kota harus diisi',
            'districtFieldSuspect.required_if' => 'Kecamatan harus diisi',
            'villageFieldSuspect.required_if' => 'Desa/Kelurahan harus diisi',

            'vehicleFieldSuspect.required' => 'Kendaraan harus diisi',
        ]);
    }
}
