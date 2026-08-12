<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

use App\Models\User;
use App\Models\Officer;

use App\Models\Lib\Rank;
use App\Models\Lib\Gender;
use App\Models\Lib\Religion;
use App\Models\Lib\Education;
use App\Models\Lib\IdentityType;
use App\Models\Lib\Police;
use App\Models\Lib\Position;
use App\Models\Lib\PoliceDiktukEducation;
use App\Models\Lib\PoliceDikjurEducationMaterial;
use App\Models\Lib\PoliceDikjurEducationPlace;
use App\Models\Lib\PoliceDivision;
use App\Models\Lib\CertificateType;

class PersonnelController extends Controller
{
    private $policeId;
    private $userAuth;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $policeId = request()->policeId;
            $this->policeId = $policeId;

            $userAuth = Auth::user();
            $this->userAuth = $userAuth;

            $police = Police::with(['children' => function ($query) {
                $query->where('is_active', true);
            }])
                ->where('id', $userAuth->police_id)
                ->first();

            $policeChildrenIds = [];
            if (!empty($police->children)) {
                $policeChildrenIds = $police->children->pluck('id')->toArray();
            }

            // RBAC Check for Read Permission
            if (!$userAuth->hasPermission('personnel.R')) {
                return redirect()->route('home')->with('error', 'Akses data ditolak');
            }

            // User "level nasional" = super_admin ATAU tidak terikat satker (police_id null/kosong)
            // Level nasional bisa akses semua polda tanpa batasan wilayah
            $isNationalLevel = empty($userAuth->police_id);

            // Jika tidak ada policeId di URL dan user terikat satker, redirect ke satker sendiri
            if (empty($policeId) && !$isNationalLevel) {
                return redirect()->route('personnel.index', ['policeId' => $userAuth->police_id]);
            }

            // Jika ada policeId tapi bukan miliknya dan bukan level nasional, cek apakah child satker
            if (!empty($policeId) && !$isNationalLevel && $userAuth->police_id != $policeId) {
                if (empty($policeChildrenIds) || !in_array($policeId, $policeChildrenIds)) {
                    return redirect()->route('home')->with('error', 'Akses data ditolak');
                }
            }

            return $next($request);
        });
    }

    public function index()
    {
        $pageParam = strtolower(request()->page);

        $userAuth = $this->userAuth;
        $policeId = $this->policeId;

        if ($pageParam == 'active' || empty($pageParam)) {
            return $this->indexActive($userAuth, $policeId);
        } else if ($pageParam == 'inactive') {
            return $this->indexInactive($userAuth, $policeId);
        } else if ($pageParam == 'verification') {
            return $this->indexVerification($userAuth, $policeId);
        } else if ($pageParam == 'signatory') {
            return $this->indexSignatory($userAuth, $policeId);
        } else {
            // back to dashboard
            return redirect()->route('home')->with('error', 'Akses data ditolak');
        }
    }

    private function indexActive($userAuth, $policeId)
    {
        $usersQuery = User::withRelated()
            ->selectFullNameExpression()
            ->where('is_active', true)
            ->whereHas('officer', function ($query) {
                $query->whereIn('status', ['PRESENT', 'ASSISTANCE'])
                    ->where('is_active', true);
            })
            ->groupBy('users.id')
            ->groupBy('users.police_id')
            ->orderBy('users.police_id');

        $policesQuery = Police::with(['children' => function ($query) {
            $query->where('is_active', true);
        }])
            ->where('is_active', true)
            ->orderBy('id', 'ASC');

        // User level nasional = super_admin atau tidak terikat satker (police_id null)
        $isNationalLevel = empty($userAuth->police_id);

        if (($userAuth->role_id == 3 && !empty($policeId)) || $userAuth->role_id == 3) {
            $policesQuery->where('id', $userAuth->police_id);
            $usersQuery->where('police_id', $policeId);
        }

        if ($isNationalLevel && !empty($policeId)) {
            $usersQuery->where('police_id', $policeId);
        }

        if ($isNationalLevel && empty($policeId)) {
            $policesQuery->where('class', 'DAERAH');
        }

        if ($userAuth->role_id == 5) {
            $usersQuery->whereHas('officer', function ($query) {
                $query->where('flag', 'ADMIN');
            })->where('police_id', $userAuth->police_id);
        }

        $users = $usersQuery->get();
        $polices = $policesQuery->get();

        $currentPolice = Police::where('id', $policeId)
            ->first();

        $viewData = [
            'users' => $users,
            'policeId' => $policeId,
            'currentPolice' => $currentPolice,
            'currentPoliceId' => $currentPolice->id ?? null,
            'polices' => $polices,
        ];

        return view('personnel.index-active', $viewData);
    }

    private function indexInactive($userAuth, $policeId)
    {
        $usersQuery = User::withRelated()
            ->selectFullNameExpression()
            ->whereHas('officer', function ($query) {
                $query->valid()
                    ->whereIn('status', ['RETIRE', 'EXIT']);
            })
            ->groupBy('users.id')
            ->groupBy('users.police_id')
            ->orderBy('users.police_id');

        $policesQuery = Police::with(['children' => function ($query) {
            $query->where('is_active', true);
        }])
            ->where('is_active', true)
            ->orderBy('id', 'ASC');

        // User level nasional = super_admin atau tidak terikat satker (police_id null)
        $isNationalLevel = empty($userAuth->police_id);

        if (($userAuth->role_id == 3 && !empty($policeId)) || $userAuth->role_id == 3) {
            $policesQuery->where('id', $userAuth->police_id);
            $usersQuery->where('police_id', $policeId);
        }

        if ($isNationalLevel && !empty($policeId)) {
            $usersQuery->where('police_id', $policeId);
        }

        if ($isNationalLevel && empty($policeId)) {
            $policesQuery->where('class', 'DAERAH');
        }

        if ($userAuth->role_id == 5) {
            $usersQuery->whereHas('officer', function ($query) {
                $query->where('flag', 'ADMIN');
            })->where('police_id', $userAuth->police_id);
        }

        $users = $usersQuery->get();
        $polices = $policesQuery->get();

        $currentPolice = Police::where('id', $policeId)
            ->first();

        $viewData = [
            'users' => $users,
            'policeId' => $policeId,
            'currentPolice' => $currentPolice,
            'currentPoliceId' => $currentPolice->id ?? null,
            'polices' => $polices,
        ];

        return view('personnel.index-inactive', $viewData);
    }

    private function indexVerification($userAuth, $policeId)
    {
        $usersQuery = User::withRelated()
            ->selectFullNameExpression()
            ->whereHas('officer', function ($query) {
                $query->where('is_valid', false);
            })
            ->groupBy('users.id')
            ->groupBy('users.police_id')
            ->orderBy('users.police_id');

        $policesQuery = Police::with(['children' => function ($query) {
            $query->where('is_active', true);
        }])
            ->where('is_active', true)
            ->orderBy('id', 'ASC');

        $isNationalLevel = empty($userAuth->police_id);

        if ($isNationalLevel && empty($policeId)) {
            $policesQuery->where('class', 'DAERAH');
        }

        $users = $usersQuery->get();

        $polices = $policesQuery->get();

        $currentPolice = Police::where('id', $policeId)
            ->first();

        $viewData = [
            'users' => $users,
            'policeId' => $policeId,
            'currentPolice' => $currentPolice,
            'currentPoliceId' => $currentPolice->id ?? null,
            'polices' => $polices,
        ];

        return view('personnel.index-verification', $viewData);
    }

    private function indexSignatory($userAuth, $policeId)
    {
        $usersQuery = User::withRelated()
            ->selectFullNameExpression()
            ->whereHas('officer', function ($query) {
                $query->where('passphrase', '!=', null);
            })
            ->groupBy('users.id')
            ->groupBy('users.police_id')
            ->orderBy('users.police_id');

        $policesQuery = Police::with(['children' => function ($query) {
            $query->where('is_active', true);
        }])
            ->where('is_active', true)
            ->orderBy('id', 'ASC');

        $isNationalLevel = empty($userAuth->police_id);

        if ($isNationalLevel && empty($policeId)) {
            $policesQuery->where('class', 'DAERAH');
        }

        $users = $usersQuery->get();

        $polices = $policesQuery->get();

        $currentPolice = Police::where('id', $policeId)
            ->first();

        $viewData = [
            'users' => $users,
            'policeId' => $policeId,
            'currentPolice' => $currentPolice,
            'currentPoliceId' => $currentPolice->id ?? null,
            'polices' => $polices,
        ];

        return view('personnel.index-signatory', $viewData);
    }

    public function validation($id)
    {
        $userAuth = $this->userAuth;
        $policeId = $this->policeId;

        if (!$userAuth->hasPermission('personnel.R')) {
            // back to dashboard
            return redirect()->route('home')->with('error', 'Akses data ditolak');
        }

        $user = User::withRelated()
            ->selectFullNameExpression()
            ->where('users.id', $id)
            ->first();

        $officer = $user->officer()->selectFullName()->first();

        if (empty($user)) {
            return redirect()->route('personnel.index', ['policeId' => $policeId])->with('error', 'Data tidak ditemukan');
        }

        //ranks based on officer employment type id
        $ranks = Rank::where('employment_type_id', $officer->employment_type_id ?? 1)
            ->where('is_active', true)
            ->orderBy('sort')
            ->get();

        $genders = Gender::where('is_active', true)
            ->orderBy('sort')
            ->get();

        $religions = Religion::where('is_active', true)
            ->orderBy('sort')
            ->get();

        $educations = Education::where('is_active', true)
            ->whereNotIn('id', ['0', '11'])
            ->orderBy('sort')
            ->get();

        $positions = Position::withRelated()
            ->where('is_active', true)
            ->where('police_id', $policeId)
            ->where('position_cluster_id', '!=', '6')
            ->orderBy('sort')
            ->get();

        $policeDiktukEducations = PoliceDiktukEducation::where('is_active', true)
            ->orderBy('sort')
            ->get();

        $policeDikjurEducationMaterials = PoliceDikjurEducationMaterial::where('is_active', true)
            ->orderBy('sort')
            ->get();

        $policeDikjurEducationPlaces = PoliceDikjurEducationPlace::where('is_active', true)
            ->orderBy('sort')
            ->get();

        $policeDivisions = PoliceDivision::where('is_active', true)
            ->orderBy('sort')
            ->get();

        $registerSignatoryIdentityTypes = IdentityType::where('is_active', true)
            ->where('id', '10')
            ->orderBy('sort')
            ->get();

        $viewData = [
            'officer' => $officer,
            'currentOfficer' => $officer,
            'ranks' => $ranks,
            'policeId' => $policeId,
            'genders' => $genders,
            'religions' => $religions,
            'educations' => $educations,
            'positions' => $positions,
            'policeDiktukEducations' => $policeDiktukEducations,
            'registerSignatoryIdentityTypes' => $registerSignatoryIdentityTypes,
            'policeDikjurEducationMaterials' => $policeDikjurEducationMaterials,
            'policeDikjurEducationPlaces' => $policeDikjurEducationPlaces,
            'policeDivisions' => $policeDivisions,
            'user' => $user,
            'currentUser' => $user,
        ];

        return view('personnel.validation', $viewData);
    }

    public function validationProcess(Request $request, $id)
    {
        $id = $request->id;
        $userAuth = $this->userAuth;
        $policeId = $this->policeId;

        $officer = Officer::where('id', $id)
            ->first();

        if (empty($officer)) {
            return redirect()->route('personnel.index', ['policeId' => $policeId])->with('error', 'Data tidak ditemukan');
        }

        $officer->update(
            [
                'is_valid' => true,
            ]
        );

        return redirect()->route('personnel.index', ['policeId' => $policeId])->with('success', 'Data berhasil disimpan');
    }

    public function signatory()
    {
        $userAuth = $this->userAuth;
        $policeId = $this->policeId;

        $usersQuery = User::withRelated()
            ->selectFullNameExpression()
            ->whereHas('officer', function ($query) {
                $query->where('passphrase', '!=', null);
            })
            ->groupBy('users.id')
            ->groupBy('users.police_id')
            ->orderBy('users.police_id');

        $isNationalLevel = empty($userAuth->police_id);

        if ($userAuth->role_id == 3 && !empty($policeId)) {
            $polices = Police::where('parent_id', $policeId)->orWhere('id', $policeId)->get();
            $polices = $polices->pluck('id');

            $usersQuery->whereIn('police_id', $polices);
        } elseif (empty($policeId) && !$isNationalLevel) {
            // Redirect ke policeId milik user sendiri jika bukan level nasional dan policeId kosong
            return redirect()->route('personnel.signatory', ['policeId' => $userAuth->police_id]);
        }

        $users = $usersQuery->get();

        $currentPolice = Police::where('id', $policeId)
            ->first();

        $viewData = [
            'users' => $users,
            'policeId' => $policeId,
            'currentPolice' => $currentPolice,
            'currentPoliceId' => $currentPolice->id ?? null,
        ];

        return view('personnel.signatory', $viewData);
    }

    public function certification()
    {
        $userAuth = $this->userAuth;
        $policeId = $this->policeId;

        $usersQuery = User::withRelated()
            ->selectFullNameExpression()
            ->whereHas('officer', function ($query) {
                $query->where('is_certificate_exists', true);
            })
            ->groupBy('users.id')
            ->groupBy('users.police_id')
            ->orderBy('users.police_id');

        $isNationalLevel = empty($userAuth->police_id);

        if ($userAuth->role_id == 3 && !empty($policeId)) {
            $usersQuery->where('police_id', $policeId);
        } elseif (empty($policeId) && !$isNationalLevel) {
            // Redirect ke policeId milik user sendiri jika bukan level nasional dan policeId kosong
            return redirect()->route('personnel.certification', ['policeId' => $userAuth->police_id]);
        }

        $users = $usersQuery->get();

        $currentPolice = Police::where('id', $policeId)
            ->first();

        $viewData = [
            'users' => $users,
            'policeId' => $policeId,
            'currentPolice' => $currentPolice,
            'currentPoliceId' => $currentPolice->id ?? null,
        ];

        return view('personnel.certification', $viewData);
    }

    public function create()
    {
        $policeId = $this->policeId;

        $ranks = Rank::wherePolri()
            ->where('is_active', true)
            ->orderBy('sort')
            ->get();

        $genders = Gender::where('is_active', true)
            ->orderBy('sort')
            ->get();

        $religions = Religion::where('is_active', true)
            ->orderBy('sort')
            ->get();

        $educations = Education::where('is_active', true)
            ->whereNotIn('id', ['0', '11'])
            ->orderBy('sort')
            ->get();

        $positions = Position::withRelated()
            ->where('is_active', true)
            ->where('police_id', $policeId)
            ->orderBy('sort')
            ->get();

        $policeDiktukEducations = PoliceDiktukEducation::where('is_active', true)
            ->orderBy('sort')
            ->get();

        $policeDikjurEducationMaterials = PoliceDikjurEducationMaterial::where('is_active', true)
            ->orderBy('sort')
            ->get();

        $policeDikjurEducationPlaces = PoliceDikjurEducationPlace::where('is_active', true)
            ->orderBy('sort')
            ->get();

        $policeDivisions = PoliceDivision::where('is_active', true)
            ->orderBy('sort')
            ->get();

        $registerSignatoryIdentityTypes = IdentityType::where('is_active', true)
            ->where('id', '10')
            ->orderBy('sort')
            ->get();

        $certificateTypes = CertificateType::where('is_active', true)
            ->orderBy('sort')
            ->get();

        $viewData = [
            'policeId' => $policeId,
            'ranks' => $ranks,
            'genders' => $genders,
            'religions' => $religions,
            'educations' => $educations,
            'positions' => $positions,
            'policeDiktukEducations' => $policeDiktukEducations,
            'registerSignatoryIdentityTypes' => $registerSignatoryIdentityTypes,
            'policeDikjurEducationMaterials' => $policeDikjurEducationMaterials,
            'policeDikjurEducationPlaces' => $policeDikjurEducationPlaces,
            'policeDivisions' => $policeDivisions,
            'certificateTypes' => $certificateTypes,
        ];

        return view('personnel.create', $viewData);
    }

    public function store(Request $request)
    {
        // Validation
        $validator = $this->validateForm($request);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $userAuth = $this->userAuth;
        $policeId = $this->policeId;

        $name = $request->name;
        $employmentTypeId = htmlspecialchars($request->employmentType);
        $rankId = htmlspecialchars($request->rank);
        $birthDate = htmlspecialchars($request->birthDate);
        $registerNumber = htmlspecialchars($request->registerNumber);
        $genderId = htmlspecialchars($request->gender);
        $religionId = htmlspecialchars($request->religion);

        $positionId = htmlspecialchars($request->position);
        $position = Position::with(['positionCluster'])->where('id', $positionId)->first();

        $isRegisterSignatory = htmlspecialchars($request->isRegisterSignatory);
        $isRegisterAdmin = htmlspecialchars($request->isRegisterAdmin);

        $educationId = htmlspecialchars($request->education);
        $educationInstitutionName = htmlspecialchars($request->educationInstitutionName);
        $phoneNumber = htmlspecialchars($request->phoneNumber);
        $email = htmlspecialchars($request->email);
        $policeDiktukEducationId = htmlspecialchars($request->policeDiktukEducation);
        $policeDiktukEducationGraduateYear = htmlspecialchars($request->policeDiktukEducationGraduateYear);

        $isExistsOfficerSkepPenyidik = htmlspecialchars($request->isExistsOfficerSkepPenyidik);
        $officerSkepPenyidikNumber = (filter_var($isExistsOfficerSkepPenyidik, FILTER_VALIDATE_BOOLEAN) == true) ? htmlspecialchars($request->officerSkepPenyidikNumber) : null;
        $roleId = (filter_var($isRegisterAdmin, FILTER_VALIDATE_BOOLEAN) == true) ? 3 : ((filter_var($isRegisterSignatory, FILTER_VALIDATE_BOOLEAN) == true) ? 5 : 4);

        $isRegisterSignatory = filter_var($request->isRegisterSignatory, FILTER_VALIDATE_BOOLEAN);
        $registerSignatoryIdentityTypeId = ($isRegisterSignatory) ? htmlspecialchars($request->registerSignatoryIdentityType) : null;
        $registerSignatoryIdentityNumber = ($isRegisterSignatory) ? htmlspecialchars($request->registerSignatoryIdentityNumber) : null;

        $isRegisterAdmin = filter_var($request->isRegisterAdmin, FILTER_VALIDATE_BOOLEAN);

        $isRegisterAdminCanEntryDocument = filter_var($request->isRegisterAdminCanEntryDocument, FILTER_VALIDATE_BOOLEAN);

        $isOfficerOperationControlAssistance = filter_var($request->isOfficerOperationControlAssistance, FILTER_VALIDATE_BOOLEAN);
        $officerOperationControlAssistanceNumber = ($isOfficerOperationControlAssistance == true) ? htmlspecialchars($request->officerOperationControlAssistanceNumber) : null;
        $officerOperationControlAssistanceDate = ($isOfficerOperationControlAssistance == true) ? htmlspecialchars($request->officerOperationControlAssistanceDate) : null;
        $officerOperationControlAssistanceOriginPoliceId = ($isOfficerOperationControlAssistance == true) ? htmlspecialchars($request->officerOperationControlAssistanceOriginPoliceId) : null;

        $careerHistoryPoliceDivisionIds = $request->careerHistoryPoliceDivisionIds ?? [];
        $careerHistoryPositionNames = $request->careerHistoryPositionNames ?? [];
        $careerHistoryYears = $request->careerHistoryYears ?? [];

        $policeDikjurEducationPlaceIds = $request->policeDikjurEducationPlaceIds ?? [];
        $policeDikjurEducationGraduateYears = $request->policeDikjurEducationGraduateYears ?? [];
        $policeDikjurEducationMaterialIds = $request->policeDikjurEducationMaterialIds ?? [];

        $isExistsOfficerCertificate = filter_var($request->isExistsOfficerCertificate, FILTER_VALIDATE_BOOLEAN);

        DB::beginTransaction();
        try {
            $police = Police::with('parent')->where('id', $policeId)
                ->first();
            $rank = Rank::where('id', $rankId)
                ->first();

            $lastUser = User::max('id');

            $user = User::updateOrCreate(
                [
                    'register_number' => $registerNumber,
                ],
                [
                    'id' => $lastUser + 1,
                    'first_name' => $name,
                    'last_name' => null,
                    'first_title' => null,
                    'last_title' => null,
                    'rank_id' => $rank->id,
                    'position_id' => $position->id,
                    'register_number' => $registerNumber,
                    'username' => $registerNumber,
                    'police_id' => $police->id,

                    'password' => Hash::make($registerNumber . '#' . $birthDate),
                    'is_password_changed' => false,
                    'phone' => $phoneNumber,
                    'email' => $email,
                    'state' => 1,
                    'is_active' => true,
                    'avatar' => 'user.png',
                    'role_id' => $roleId,

                    'polda_id' => ($police->class == 'DAERAH') ? $police->id : (($police->class == 'RESOR' && !empty($police->parent)) ? $police->parent->id : null),
                    'polres_id' => ($police->class == 'RESOR') ? $police->id : 0,
                    'pangkat' => $rank->name ?? null,
                    'officer_id' => $registerNumber,

                    'ip_addresses' => [
                        'created_ip' => $request->ip(),
                    ],

                    'properties' => [
                        'is_can_entry_document' => $isRegisterAdminCanEntryDocument,
                    ],
                ]
            );

            $isSignatoryClass = (filter_var($isRegisterSignatory, FILTER_VALIDATE_BOOLEAN) == true);
            if ((int)$educationId < 8) {
                $isSignatoryClass = false;
            }
            $class = $isSignatoryClass ? 'SIGNATORY' : 'MEMBER';
            $flag = (filter_var($isRegisterAdmin, FILTER_VALIDATE_BOOLEAN) == true) ? 'ADMIN' : null;
            $officer = Officer::updateOrCreate(
                [
                    'register_number' => $registerNumber,
                ],
                [
                    'id' => strval($registerNumber),
                    'user_id' => $user->id,
                    'first_name' => $name,
                    'last_name' => null,
                    'first_title' => null,
                    'last_title' => null,
                    'employment_type_id' => $employmentTypeId,
                    'rank_id' => $rank->id,
                    'birth_date' => $birthDate,
                    'register_number' => $registerNumber,
                    'gender_id' => $genderId,
                    'religion_id' => $religionId,
                    'position_id' => $position->id,
                    'education_id' => $educationId,
                    'education_institution_name' => $educationInstitutionName,
                    'phone_number' => $phoneNumber,
                    'email' => $email,
                    'police_diktuk_education_id' => $policeDiktukEducationId,
                    'police_diktuk_education_graduate_year' => $policeDiktukEducationGraduateYear,
                    'police_id' => $police->id,

                    'is_valid' => (filter_var($isRegisterSignatory, FILTER_VALIDATE_BOOLEAN) == true) ? false : true,
                    'is_active' => true,

                    'class' => $class,
                    'flag' => $flag,

                    'polda_id' => ($police->class == 'DAERAH') ? $police->id : (($police->class == 'RESOR' && !empty($police->parent)) ? $police->parent->id : null),
                    'polres_id' => ($police->class == 'RESOR') ? $police->id : 0,
                    'rank_short_name' => $rank->name ?? null,
                    'position_short_name' => $position->name ?? null,
                    'sebagai_kepala' => $position->name ?? '-',
                    'state' => 1,

                    'status' => 'PRESENT',
                    'identity_type_id' => $registerSignatoryIdentityTypeId,
                    'identity_number' => $registerSignatoryIdentityNumber,
                    'is_certificate_exists' => ($isExistsOfficerCertificate == true) ? true : false,
                ]
            );

            $officer->officerInvestigativeDetail()->updateOrCreate(
                [
                    'officer_id' => $officer->id,
                ],
                [
                    'officer_id' => $officer->id,
                    'is_skep_penyidik_exists' => filter_var($isExistsOfficerSkepPenyidik, FILTER_VALIDATE_BOOLEAN),
                    'skep_penyidik_number' => $officerSkepPenyidikNumber,
                ]
            );

            $officer->officerOperationControlAssistance()->updateOrCreate(
                [
                    'officer_id' => $officer->id,
                ],
                [
                    'officer_id' => $officer->id,
                    'is_operation_control_assistance' => $isOfficerOperationControlAssistance,
                    'letter_number' => $officerOperationControlAssistanceNumber,
                    'date' => $officerOperationControlAssistanceDate,
                    'origin_police_id' => (!empty($officerOperationControlAssistanceOriginPoliceId)) ? $officerOperationControlAssistanceOriginPoliceId : null,
                ]
            );

            //CAREER HISTORY
            $careerHistoryPoliceDivisionIdCollection = Collection::make($careerHistoryPoliceDivisionIds);
            $careerHistoryPositionNameCollection = Collection::make($careerHistoryPositionNames);
            $careerHistoryYearCollection = Collection::make($careerHistoryYears);

            $careerHistoryCollection = $careerHistoryPoliceDivisionIdCollection
                ->zip(
                    $careerHistoryPositionNameCollection,
                    $careerHistoryYearCollection
                )
                ->map(function ($careerHistory) {
                    return [
                        'police_division_id' => $careerHistory[0],
                        'position_name' => $careerHistory[1],
                        'year' => $careerHistory[2],
                    ];
                })->all();

            foreach ($careerHistoryCollection as $careerHistory) {
                $officer->officerCareerHistories()->create(
                    [
                        'officer_id' => $officer->id,
                        'police_division_id' => $careerHistory['police_division_id'],
                        'position_name' => $careerHistory['position_name'],
                        'year' => $careerHistory['year'],
                    ]
                );
            }

            //POLICE DIKJUR EDUCATION
            $policeDikjurEducationPlaceIdCollection = Collection::make($policeDikjurEducationPlaceIds);
            $policeDikjurEducationGraduateYearCollection = Collection::make($policeDikjurEducationGraduateYears);
            $policeDikjurEducationMaterialIdCollection = Collection::make($policeDikjurEducationMaterialIds);

            $policeDikjurEducationCollection = $policeDikjurEducationPlaceIdCollection
                ->zip(
                    $policeDikjurEducationGraduateYearCollection,
                    $policeDikjurEducationMaterialIdCollection
                )
                ->map(function ($policeDikjurEducation) {
                    return [
                        'police_dikjur_education_place_id' => $policeDikjurEducation[0],
                        'graduate_year' => $policeDikjurEducation[1],
                        'police_dikjur_education_material_id' => $policeDikjurEducation[2],
                    ];
                })->all();

            foreach ($policeDikjurEducationCollection as $policeDikjurEducation) {
                $officer->officerPoliceDikjurEducations()->create(
                    [
                        'officer_id' => $officer->id,
                        'police_dikjur_education_place_id' => $policeDikjurEducation['police_dikjur_education_place_id'],
                        'graduate_year' => $policeDikjurEducation['graduate_year'],
                        'police_dikjur_education_material_id' => $policeDikjurEducation['police_dikjur_education_material_id'],
                    ]
                );
            }

            //CERTIFICATE HISTORY
            if ($isExistsOfficerCertificate == true) {
                $certificateTypeIds = $request->certificateTypeIds ?? [];
                $certificateNumbers = $request->certificateNumbers ?? [];
                $certificateStartDates = $request->certificateStartDates ?? [];
                $certificateEndDates = $request->certificateEndDates ?? [];

                $certificateTypeIdCollection = Collection::make($certificateTypeIds);
                $certificateNumberCollection = Collection::make($certificateNumbers);
                $certificateStartDateCollection = Collection::make($certificateStartDates);
                $certificateEndDateCollection = Collection::make($certificateEndDates);

                $certificateCollection = $certificateTypeIdCollection
                    ->zip(
                        $certificateNumberCollection,
                        $certificateStartDateCollection,
                        $certificateEndDateCollection
                    )
                    ->map(function ($certificate) {
                        return [
                            'certificate_type_id' => $certificate[0],
                            'certificate_number' => $certificate[1],
                            'begin_date' => $certificate[2],
                            'expired_date' => $certificate[3],
                        ];
                    })->all();

                foreach ($certificateCollection as $certificate) {
                    $officer->officerCertificateHistories()->create(
                        [
                            'officer_id' => $officer->id,
                            'certificate_type_id' => $certificate['certificate_type_id'],
                            'certificate_number' => $certificate['certificate_number'],
                            'begin_date' => $certificate['begin_date'],
                            'expired_date' => $certificate['expired_date'],
                        ]
                    );
                }
            } else if ($isExistsOfficerCertificate == false) {
                $officer->officerCertificateHistories()->delete();
            }

            DB::commit();

            return redirect()->route('personnel.index', ['policeId' => $policeId])->with('success', 'Data berhasil disimpan');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('PersonnelController@store : ', $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data');
        }
    }

    public function show($id)
    {
        $userAuth = $this->userAuth;
        $policeId = $this->policeId;

        $user = User::withRelated()
            ->selectFullNameExpression()
            ->where('users.id', $id)
            ->first();

        if (empty($user)) {
            return redirect()->route('personnel.index', ['policeId' => $policeId])->with('error', 'Data tidak ditemukan');
        }

        //ranks based on officer employment type id
        $ranks = Rank::where('employment_type_id', $user->officer->employment_type_id ?? 1)
            ->where('is_active', true)
            ->orderBy('sort')
            ->get();

        $genders = Gender::where('is_active', true)
            ->orderBy('sort')
            ->get();

        $religions = Religion::where('is_active', true)
            ->orderBy('sort')
            ->get();

        $educations = Education::where('is_active', true)
            ->whereNotIn('id', ['0', '11'])
            ->orderBy('sort')
            ->get();

        $positions = Position::withRelated()
            ->where('is_active', true)
            ->where('police_id', $policeId)
            ->where('position_cluster_id', '!=', '6')
            ->orderBy('sort')
            ->get();

        $policeDiktukEducations = PoliceDiktukEducation::where('is_active', true)
            ->orderBy('sort')
            ->get();

        $policeDikjurEducationMaterials = PoliceDikjurEducationMaterial::where('is_active', true)
            ->orderBy('sort')
            ->get();

        $policeDikjurEducationPlaces = PoliceDikjurEducationPlace::where('is_active', true)
            ->orderBy('sort')
            ->get();

        $policeDivisions = PoliceDivision::where('is_active', true)
            ->orderBy('sort')
            ->get();

        $registerSignatoryIdentityTypes = IdentityType::where('is_active', true)
            ->where('id', '10')
            ->orderBy('sort')
            ->get();

        $certificateTypes = CertificateType::where('is_active', true)
            ->orderBy('sort')
            ->get();

        $viewData = [
            'officer' => $user->officer()->selectFullName()->first(),
            'currentOfficer' => $user->officer()->selectFullName()->first(),
            'user' => $user,
            'currentUser' => $user,
            'ranks' => $ranks,
            'policeId' => $policeId,
            'genders' => $genders,
            'religions' => $religions,
            'educations' => $educations,
            'positions' => $positions,
            'policeDiktukEducations' => $policeDiktukEducations,
            'registerSignatoryIdentityTypes' => $registerSignatoryIdentityTypes,
            'policeDikjurEducationMaterials' => $policeDikjurEducationMaterials,
            'policeDikjurEducationPlaces' => $policeDikjurEducationPlaces,
            'policeDivisions' => $policeDivisions,
            'certificateTypes' => $certificateTypes
        ];

        return view('personnel.show', $viewData);
    }

    public function edit($id)
    {
        $policeId = $this->policeId;

        $user = User::withRelated()
            ->selectFullNameExpression()
            ->where('users.id', $id)
            ->first();

        if (empty($user)) {
            return redirect()->route('personnel.index', ['policeId' => $policeId])->with('error', 'Data tidak ditemukan');
        }

        //ranks based on officer employment type id
        $ranks = Rank::where('employment_type_id', $user->officer->employment_type_id ?? 1)
            ->where('is_active', true)
            ->orderBy('sort')
            ->get();

        $genders = Gender::where('is_active', true)
            ->orderBy('sort')
            ->get();

        $religions = Religion::where('is_active', true)
            ->orderBy('sort')
            ->get();

        $educations = Education::where('is_active', true)
            ->whereNotIn('id', ['0', '11'])
            ->orderBy('sort')
            ->get();

        $positions = Position::withRelated()
            ->where('is_active', true)
            ->where('police_id', $policeId)
            ->orderBy('sort')
            ->get();

        $policeDiktukEducations = PoliceDiktukEducation::where('is_active', true)
            ->orderBy('sort')
            ->get();

        $policeDikjurEducationMaterials = PoliceDikjurEducationMaterial::where('is_active', true)
            ->orderBy('sort')
            ->get();

        $policeDikjurEducationPlaces = PoliceDikjurEducationPlace::where('is_active', true)
            ->orderBy('sort')
            ->get();

        $policeDivisions = PoliceDivision::where('is_active', true)
            ->orderBy('sort')
            ->get();

        $registerSignatoryIdentityTypes = IdentityType::where('is_active', true)
            ->where('id', '10')
            ->orderBy('sort')
            ->get();

        $certificateTypes = CertificateType::where('is_active', true)
            ->orderBy('sort')
            ->get();

        $viewData = [
            'user' => $user,
            'officer' => $user->officer()->selectFullName()->first(),
            'currentOfficer' => $user->officer()->selectFullName()->first(),
            'ranks' => $ranks,
            'policeId' => $policeId,
            'genders' => $genders,
            'religions' => $religions,
            'educations' => $educations,
            'positions' => $positions,
            'policeDiktukEducations' => $policeDiktukEducations,
            'registerSignatoryIdentityTypes' => $registerSignatoryIdentityTypes,
            'policeDikjurEducationMaterials' => $policeDikjurEducationMaterials,
            'policeDikjurEducationPlaces' => $policeDikjurEducationPlaces,
            'policeDivisions' => $policeDivisions,
            'certificateTypes' => $certificateTypes
        ];

        return view('personnel.edit', $viewData);
    }

    public function update(Request $request, $id)
    {
        // Validation
        $validator = $this->validateForm($request);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $userAuth = $this->userAuth;
        $policeId = $this->policeId;

        $name = $request->name;
        $employmentTypeId = htmlspecialchars($request->employmentType);
        $rankId = htmlspecialchars($request->rank);
        $birthDate = htmlspecialchars($request->birthDate);
        $registerNumber = htmlspecialchars($request->registerNumber);
        $genderId = htmlspecialchars($request->gender);
        $religionId = htmlspecialchars($request->religion);

        $positionId = htmlspecialchars($request->position);
        $position = Position::with(['positionCluster'])->where('id', $positionId)->first();

        $isRegisterSignatory = htmlspecialchars($request->isRegisterSignatory);

        $isRegisterAdmin = htmlspecialchars($request->isRegisterAdmin);
        $isRegisterAdminCanEntryDocument = filter_var($request->isRegisterAdminCanEntryDocument, FILTER_VALIDATE_BOOLEAN);

        $educationId = htmlspecialchars($request->education);
        $educationInstitutionName = htmlspecialchars($request->educationInstitutionName);
        $phoneNumber = htmlspecialchars($request->phoneNumber);
        $email = htmlspecialchars($request->email);
        $policeDiktukEducationId = htmlspecialchars($request->policeDiktukEducation);
        $policeDiktukEducationGraduateYear = htmlspecialchars($request->policeDiktukEducationGraduateYear);

        $isExistsOfficerSkepPenyidik = htmlspecialchars($request->isExistsOfficerSkepPenyidik);
        $officerSkepPenyidikNumber = (filter_var($isExistsOfficerSkepPenyidik, FILTER_VALIDATE_BOOLEAN) == true) ? htmlspecialchars($request->officerSkepPenyidikNumber) : null;
        $roleId = (filter_var($isRegisterAdmin, FILTER_VALIDATE_BOOLEAN) == true) ? 3 : ((filter_var($isRegisterSignatory, FILTER_VALIDATE_BOOLEAN) == true) ? 5 : 4);

        $isRegisterSignatory = filter_var($request->isRegisterSignatory, FILTER_VALIDATE_BOOLEAN);
        $registerSignatoryIdentityTypeId = ($request->registerSignatoryIdentityType) ? htmlspecialchars($request->registerSignatoryIdentityType) : null;
        $registerSignatoryIdentityNumber = htmlspecialchars($request->registerSignatoryIdentityNumber);

        $isRegisterAdmin = filter_var($request->isRegisterAdmin, FILTER_VALIDATE_BOOLEAN);

        $isOfficerOperationControlAssistance = filter_var($request->isOfficerOperationControlAssistance, FILTER_VALIDATE_BOOLEAN);
        $officerOperationControlAssistanceNumber = ($isOfficerOperationControlAssistance == true) ? htmlspecialchars($request->officerOperationControlAssistanceNumber) : null;
        $officerOperationControlAssistanceDate = ($isOfficerOperationControlAssistance == true) ? htmlspecialchars($request->officerOperationControlAssistanceDate) : null;
        $officerOperationControlAssistanceOriginPoliceId = ($isOfficerOperationControlAssistance == true) ? htmlspecialchars($request->officerOperationControlAssistanceOriginPoliceId) : null;

        $careerHistoryPoliceDivisionIds = $request->careerHistoryPoliceDivisionIds ?? [];
        $careerHistoryPositionNames = $request->careerHistoryPositionNames ?? [];
        $careerHistoryYears = $request->careerHistoryYears ?? [];

        $policeDikjurEducationPlaceIds = $request->policeDikjurEducationPlaceIds ?? [];
        $policeDikjurEducationGraduateYears = $request->policeDikjurEducationGraduateYears ?? [];
        $policeDikjurEducationMaterialIds = $request->policeDikjurEducationMaterialIds ?? [];

        $isExistsOfficerCertificate = filter_var($request->isExistsOfficerCertificate, FILTER_VALIDATE_BOOLEAN);

        DB::beginTransaction();
        try {
            $police = Police::with('parent')->where('id', $policeId)
                ->first();
            $rank = Rank::where('id', $rankId)
                ->first();

            $lastUserId = User::max('id');
            $currentUser = User::where('id', $id)
                ->first();

            $user = User::where('id', $id)->first();

            $user->update(
                [
                    'id' => (!empty($currentUser)) ? $currentUser->id : $lastUserId + 1,
                    'first_name' => $name,
                    'last_name' => null,
                    'first_title' => null,
                    'last_title' => null,
                    'rank_id' => $rank->id,
                    'position_id' => $position->id,
                    'register_number' => $registerNumber,
                    'username' => $registerNumber,
                    'police_id' => $police->id,

                    'phone' => $phoneNumber,
                    'email' => $email,
                    'state' => 1,
                    'role_id' => $roleId,

                    'polda_id' => ($police->class == 'DAERAH') ? $police->id : (($police->class == 'RESOR' && !empty($police->parent)) ? $police->parent->id : null),
                    'polres_id' => ($police->class == 'RESOR') ? $police->id : 0,
                    'pangkat' => $rank->name ?? null,
                    'officer_id' => $registerNumber,

                    'properties' => [
                        'is_can_entry_document' => $isRegisterAdminCanEntryDocument,
                    ],
                ]
            );

            $currentOfficer = Officer::where('user_id', $user->id)
                ->first();

            $isValidAsRegisterSignatory = false;
            if (!empty($currentOfficer)) {
                if (!empty($currentOfficer->identity_number)) {
                    $isValidAsRegisterSignatory = $currentOfficer->is_valid;
                } else {
                    $isValidAsRegisterSignatory = (filter_var($isRegisterSignatory, FILTER_VALIDATE_BOOLEAN) == true) ? false : true;
                }
            } else {
                $isValidAsRegisterSignatory = (filter_var($isRegisterSignatory, FILTER_VALIDATE_BOOLEAN) == true) ? false : true;
            }

            $officer = Officer::where('user_id', $user->id)
                ->first();
            $isSignatoryClass = (filter_var($isRegisterSignatory, FILTER_VALIDATE_BOOLEAN) == true);
            if ((int)$educationId < 8) {
                $isSignatoryClass = false;
            }
            $class = $isSignatoryClass ? 'SIGNATORY' : 'MEMBER';
            $flag = (filter_var($isRegisterAdmin, FILTER_VALIDATE_BOOLEAN) == true) ? 'ADMIN' : null;
            if ($officer->identity_number != $registerSignatoryIdentityNumber) {
                $isValidAsRegisterSignatory = false;
            }

            $officer->update(
                [
                    'id' => (!empty($currentOfficer)) ? $currentOfficer->id : $registerNumber,
                    'user_id' => $user->id,
                    'first_name' => $name,
                    'last_name' => NULL,
                    'first_title' => NULL,
                    'last_title' => NULL,
                    'employment_type_id' => $employmentTypeId,
                    'rank_id' => $rankId,
                    'birth_date' => $birthDate,
                    'register_number' => $registerNumber,
                    'gender_id' => $genderId,
                    'religion_id' => $religionId,
                    'position_id' => $positionId,
                    'education_id' => $educationId,
                    'education_institution_name' => $educationInstitutionName,
                    'phone_number' => $phoneNumber,
                    'email' => $email,
                    'police_diktuk_education_id' => $policeDiktukEducationId,
                    'police_diktuk_education_graduate_year' => $policeDiktukEducationGraduateYear,
                    'police_id' => $police->id,

                    'is_valid' => $isValidAsRegisterSignatory,
                    'class' => $class,
                    'flag' => $flag,

                    'polda_id' => ($police->class == 'DAERAH') ? $police->id : (($police->class == 'RESOR' && !empty($police->parent)) ? $police->parent->id : null),
                    'polres_id' => ($police->class == 'RESOR') ? $police->id : 0,
                    'rank_short_name' => $rank->name ?? null,
                    'position_short_name' => $position->name ?? null,
                    'state' => 1,

                    'status' => 'PRESENT',
                    'identity_type_id' => $registerSignatoryIdentityTypeId,
                    'identity_number' => $registerSignatoryIdentityNumber,
                    'is_certificate_exists' => ($isExistsOfficerCertificate == true) ? true : false,
                ]
            );

            // investigative detail
            $officer->officerInvestigativeDetail()->updateOrCreate(
                [
                    'officer_id' => $officer->id,
                ],
                [
                    'officer_id' => $officer->id,
                    'is_skep_penyidik_exists' => filter_var($isExistsOfficerSkepPenyidik, FILTER_VALIDATE_BOOLEAN), //convert string to boolean (true or false
                    'skep_penyidik_number' => $officerSkepPenyidikNumber,
                ]
            );

            // operation control assistance
            $officer->officerOperationControlAssistance()->updateOrCreate(
                [
                    'officer_id' => $officer->id,
                ],
                [
                    'officer_id' => $officer->id,
                    'is_operation_control_assistance' => $isOfficerOperationControlAssistance,
                    'letter_number' => $officerOperationControlAssistanceNumber,
                    'date' => $officerOperationControlAssistanceDate,
                    'origin_police_id' => (!empty($officerOperationControlAssistanceOriginPoliceId)) ? $officerOperationControlAssistanceOriginPoliceId : null,
                ]
            );

            //CAREER HISTORY
            $careerHistoryPoliceDivisionIdCollection = Collection::make($careerHistoryPoliceDivisionIds);
            $careerHistoryPositionNameCollection = Collection::make($careerHistoryPositionNames);
            $careerHistoryYearCollection = Collection::make($careerHistoryYears);

            $careerHistoryCollection = $careerHistoryPoliceDivisionIdCollection
                ->zip(
                    $careerHistoryPositionNameCollection,
                    $careerHistoryYearCollection
                )
                ->map(function ($careerHistory) {
                    return [
                        'police_division_id' => $careerHistory[0],
                        'position_name' => $careerHistory[1],
                        'year' => $careerHistory[2],
                    ];
                })->all();

            //delete all career history
            $officer->officerCareerHistories()->delete();

            foreach ($careerHistoryCollection as $careerHistory) {
                $officer->officerCareerHistories()->create(
                    [
                        'officer_id' => $officer->id,
                        'police_division_id' => $careerHistory['police_division_id'],
                        'position_name' => $careerHistory['position_name'],
                        'year' => $careerHistory['year'],
                    ]
                );
            }

            //POLICE DIKJUR EDUCATION
            $policeDikjurEducationPlaceIdCollection = Collection::make($policeDikjurEducationPlaceIds);
            $policeDikjurEducationGraduateYearCollection = Collection::make($policeDikjurEducationGraduateYears);
            $policeDikjurEducationMaterialIdCollection = Collection::make($policeDikjurEducationMaterialIds);

            $policeDikjurEducationCollection = $policeDikjurEducationPlaceIdCollection
                ->zip(
                    $policeDikjurEducationGraduateYearCollection,
                    $policeDikjurEducationMaterialIdCollection
                )
                ->map(function ($policeDikjurEducation) {
                    return [
                        'police_dikjur_education_place_id' => $policeDikjurEducation[0],
                        'graduate_year' => $policeDikjurEducation[1],
                        'police_dikjur_education_material_id' => $policeDikjurEducation[2],
                    ];
                })->all();

            //delete all police dikjur education
            $officer->officerPoliceDikjurEducations()->delete();

            foreach ($policeDikjurEducationCollection as $policeDikjurEducation) {
                $officer->officerPoliceDikjurEducations()->create(
                    [
                        'officer_id' => $officer->id,
                        'police_dikjur_education_place_id' => $policeDikjurEducation['police_dikjur_education_place_id'],
                        'graduate_year' => $policeDikjurEducation['graduate_year'],
                        'police_dikjur_education_material_id' => $policeDikjurEducation['police_dikjur_education_material_id'],
                    ]
                );
            }

            //CERTIFICATE HISTORY
            if ($isExistsOfficerCertificate == true) {
                $certificateTypeIds = $request->certificateTypeIds ?? [];
                $certificateNumbers = $request->certificateNumbers ?? [];
                $certificateStartDates = $request->certificateStartDates ?? [];
                $certificateEndDates = $request->certificateEndDates ?? [];

                $certificateTypeIdCollection = Collection::make($certificateTypeIds);
                $certificateNumberCollection = Collection::make($certificateNumbers);
                $certificateStartDateCollection = Collection::make($certificateStartDates);
                $certificateEndDateCollection = Collection::make($certificateEndDates);

                $certificateCollection = $certificateTypeIdCollection
                    ->zip(
                        $certificateNumberCollection,
                        $certificateStartDateCollection,
                        $certificateEndDateCollection
                    )
                    ->map(function ($certificate) {
                        return [
                            'certificate_type_id' => $certificate[0],
                            'certificate_number' => $certificate[1],
                            'begin_date' => $certificate[2],
                            'expired_date' => $certificate[3],
                        ];
                    })->all();

                //delete all certificate history
                $officer->officerCertificateHistories()->delete();

                foreach ($certificateCollection as $certificate) {
                    $officer->officerCertificateHistories()->create(
                        [
                            'officer_id' => $officer->id,
                            'certificate_type_id' => $certificate['certificate_type_id'],
                            'certificate_number' => $certificate['certificate_number'],
                            'begin_date' => $certificate['begin_date'],
                            'expired_date' => $certificate['expired_date'],
                        ]
                    );
                }
            } else if ($isExistsOfficerCertificate == false) {
                $officer->officerCertificateHistories()->delete();
            }

            DB::commit();

            return redirect()->route('personnel.index', ['policeId' => $policeId])->with('success', 'Data berhasil disimpan');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('PersonnelController@update : ', $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data');
        }
    }

    public function changePassword($id)
    {
        $userAuth = $this->userAuth;
        $policeId = $this->policeId;

        $user = User::withRelated()
            ->selectFullNameExpression()
            ->where('users.id', $id)
            ->first();

        if (empty($user)) {
            return redirect()->route('personnel.index', ['policeId' => $policeId]);
        }

        $viewData = [
            'id' => $id,
            'userAuth' => $userAuth,
            'policeId' => $policeId,
            'officer' => $user->officer()->selectFullName()->first(),
            'user' => $user,
        ];

        return view('personnel.change-password', $viewData);
    }

    public function updatePassword(Request $request, $id)
    {
        $userAuth = $this->userAuth;
        $policeId = $this->policeId;
        $id = $request->id;

        $user = User::withRelated()
            ->selectFullNameExpression()
            ->where('users.id', $id)
            ->first();

        if (empty($user)) {
            return redirect()->route('personnel.index', ['policeId', $policeId]);
        }

        $officer = $user->officer;

        $this->validate($request, [
            'password' => 'required|min:6|max:255|confirmed:password_confirmation|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[-_#@$!%*?&])[A-Za-z\d\-_#@$!%*?&]+$/
            ',
            'password_confirmation' => 'required',
        ], [
            'password.required' => 'Password tidak boleh kosong',
            'password.min' => 'Password minimal 6 karakter',
            'password.max' => 'Password maksimal 255 karakter',
            'password.confirmed' => 'Password tidak sama dengan konfirmasi password',
            'password.regex' => 'Password harus mengandung huruf besar, huruf kecil, angka, dan minimal satu karakter spesial ( - _ # @ $ ! % * ? & )',
        ]);

        $password = $request->password;

        DB::beginTransaction();
        try {
            $user->update([
                'password' => Hash::make($password),
                'is_password_changed' => true,
            ]);

            DB::commit();
            return redirect()->route('personnel.index', ['policeId' => $policeId])->with('success', 'Data berhasil disimpan');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat mengubah password');
        }
    }

    public function move($id)
    {
        $userAuth = $this->userAuth;
        $policeId = $this->policeId;

        $user = User::withRelated()
            ->selectFullNameExpression()
            ->where('users.id', $id)
            ->first();

        if (empty($user)) {
            return redirect()->route('personnel.index', ['policeId' => $policeId])->with('error', 'Data tidak ditemukan');
        }

        $regionalPolices = Police::where('class', 'DAERAH')
            ->where('is_active', true)
            ->orderBy('sort', 'asc')
            ->get();

        $viewData = [
            'id' => $id,
            'userAuth' => $userAuth,
            'policeId' => $policeId,
            'officer' => $user->officer()->selectFullName()->first(),
            'user' => $user,
            'regionalPolices' => $regionalPolices,
        ];

        return view('personnel.move', $viewData);
    }

    public function updateMove(Request $request, $id)
    {
        $request->validate([
            'id' => 'required',
            'mutationType' => 'required',

            'presentMutationTypePoliceName' => 'required_if:mutationType,PRESENT',
            'presentMutationTypePoliceId' => 'required_if:mutationType,PRESENT',

            'operationControlAssistanceNumber' => 'required_if:mutationType,ASSISTANCE',
            'operationControlAssistanceDate' => 'required_if:mutationType,ASSISTANCE',
            'operationControlAssistancePoliceName' => 'required_if:mutationType,ASSISTANCE',
            'operationControlAssistancePoliceId' => 'required_if:mutationType,ASSISTANCE',
        ], [
            'name.required' => 'Nama tidak boleh kosong',
            'name.max' => 'Nama maksimal 255 karakter',
            'mutationType.required' => 'Jenis mutasi tidak boleh kosong',

            'presentMutationTypePoliceName.required_if' => 'Satker Tujuan Mutasi tidak boleh kosong',
            'presentMutationTypePoliceId.required_if' => 'Satker Tujuan Mutasi tidak boleh kosong',

            'operationControlAssistanceNumber.required_if' => 'Nomor surat BKO tidak boleh kosong',
            'operationControlAssistanceDate.required_if' => 'Tanggal Penugasan BKO tidak boleh kosong',
            'operationControlAssistancePoliceName.required_if' => 'Satker Tujuan BKO tidak boleh kosong',
            'operationControlAssistancePoliceId.required_if' => 'Satker Tujuan BKO tidak boleh kosong',
        ]);

        $id = $request->id;
        $mutationType = $request->mutationType;
        $userAuth = $this->userAuth;
        $policeId = $this->policeId;

        $user = User::withRelated()
            ->selectFullNameExpression()
            ->where('users.id', $id)
            ->first();

        if (empty($user)) {
            return redirect()->route('personnel.index', ['policeId', $policeId]);
        }

        $officer = $user->officer;
        $presentMutationTypePoliceName = htmlspecialchars($request->presentMutationTypePoliceName);
        $presentMutationTypePoliceId = htmlspecialchars($request->presentMutationTypePoliceId);

        $operationControlAssistanceNumber = htmlspecialchars($request->operationControlAssistanceNumber);
        $operationControlAssistanceDate = htmlspecialchars($request->operationControlAssistanceDate);
        $operationControlAssistancePoliceName = htmlspecialchars($request->operationControlAssistancePoliceName);
        $operationControlAssistancePoliceId = htmlspecialchars($request->operationControlAssistancePoliceId);

        DB::beginTransaction();
        try {
            $officer->officerPoliceHistories()
                ->where('officer_id', $officer->id,)
                ->where('is_present', true)
                ->update(
                    [
                        'exit_date' => date('Y-m-d'),
                        'is_present' => false,
                    ]
                );

            if ($mutationType == 'PRESENT') {
                $police = Police::with(['parent', 'children'])->where('id', $presentMutationTypePoliceId)
                    ->first();
                $regionalPolice = ($police->class == 'DAERAH') ? $police->id : (($police->class == 'RESOR') ? $police->parent()->first()->id : null);
                $resortPolice = ($police->class == 'RESOR') ? $police->id : 0;

                $user->update([
                    'police_id' => $police->id,
                    'polda_id' => $regionalPolice,
                    'polres_id' => $resortPolice,
                    'is_active' => true,
                ]);

                $officer->update([
                    'police_id' => $police->id,
                    'polda_id' => $regionalPolice,
                    'polres_id' => $resortPolice,
                    'status' => $mutationType,
                    'position_id' => ($policeId == $police->id) ? $officer->position_id : null,
                    'is_active' => true,
                ]);

                $officer->officerPoliceHistories()->create(
                    [
                        'officer_id' => $officer->id,
                        'police_id' => $police->id,
                        'position_id' => $officer->position_id,
                        'rank_id' => $officer->rank_id,
                        'enter_date' => date('Y-m-d'),
                        'is_present' => true,
                    ]
                );
            } else if ($mutationType == 'EXIT') {
                $user->update([
                    'is_active' => false,
                ]);

                $officer->update([
                    'status' => $mutationType,
                    'is_active' => false,
                ]);
            } else if ($mutationType == 'RETIRE') {
                $user->update([
                    'is_active' => false,
                ]);

                $officer->update([
                    'status' => $mutationType,
                    'is_active' => false,
                ]);
            } else if ($mutationType == 'ASSISTANCE') {
                $police = Police::with(['parent', 'children'])->where('id', $operationControlAssistancePoliceId)
                    ->first();
                $regionalPolice = ($police->class == 'DAERAH') ? $police->id : (($police->class == 'RESOR') ? $police->parent()->first()->id : null);
                $resortPolice = ($police->class == 'RESOR') ? $police->id : 0;

                $user->update([
                    'police_id' => $police->id,
                    'polda_id' => $regionalPolice,
                    'polres_id' => $resortPolice,
                    'is_active' => true,
                ]);

                $officer->update([
                    'police_id' => $police->id,
                    'polda_id' => $regionalPolice,
                    'polres_id' => $resortPolice,
                    'status' => $mutationType,
                    'position_id' => null,
                    'is_active' => true,
                ]);

                $officer->officerOperationControlAssistance()->updateOrCreate(
                    [
                        'officer_id' => $officer->id,
                    ],
                    [
                        'officer_id' => $officer->id,
                        'is_operation_control_assistance' => true,
                        'letter_number' => $operationControlAssistanceNumber,
                        'date' => $operationControlAssistanceDate,
                        'origin_police_id' => $policeId,
                    ]
                );

                $officer->officerPoliceHistories()->create(
                    [
                        'officer_id' => $officer->id,
                        'police_id' => $police->id,
                        'position_id' => $officer->position_id,
                        'rank_id' => $officer->rank_id,
                        'enter_date' => date('Y-m-d'),
                        'is_present' => true,
                    ]
                );
            }

            DB::commit();

            return redirect()->route('personnel.index', ['policeId' => $policeId])->with('success', 'Data berhasil dipindahkan');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat memindahkan data');
        }
    }

    //=================================================================================================

    public function checkOfficer(Request $request)
    {
        $policeId = $request->policeId;
        $registerNumber = htmlspecialchars($request->registerNumber);

        try {
            $officer = Officer::withRelated()
                ->selectFullName()
                ->where('officers.register_number', $registerNumber)
                ->where('officers.user_id', '!=', NULL)
                ->first();

            if (empty($officer)) {
                return response()->json([
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'Data tidak ditemukan'
                ], 404);
            }

            $admins = Officer::withRelated()
                ->selectFullName()
                ->where('officers.police_id', $officer->police_id)
                ->whereHas('user', function ($query) {
                    $query->where('users.role_id', 3);
                })
                ->where('officers.flag', 'ADMIN')
                ->get();

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'data' => [
                    'officer' => $officer,
                    'admins' => $admins,
                ]
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

    public function getSearchPolices(Request $request)
    {
        $policeClass = $request->policeClass;
        $policeNameKeyword = htmlspecialchars($request->policeNameKeyword);
        $policeNameKeyword = strtoupper($policeNameKeyword);

        try {
            $polices = [];

            switch ($policeClass) {
                case 'PUSAT':
                    $polices = Police::with('parent')
                        ->where('is_active', true)
                        ->where('class', $policeClass)
                        ->where('full_name', 'like', '%' . $policeNameKeyword . '%')
                        ->orderBy('sort', 'asc')
                        ->get();
                    break;

                case 'DAERAH':
                    $polices = Police::with('parent')
                        ->where('is_active', true)
                        ->where('class', $policeClass)
                        ->where('full_name', 'like', '%' . $policeNameKeyword . '%')
                        ->orderBy('sort', 'asc')
                        ->get();
                    break;

                case 'RESOR':
                    $polices = Police::with('parent')
                        ->where('is_active', true)
                        ->where('class', $policeClass)
                        ->where('full_name', 'like', '%' . $policeNameKeyword . '%')
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

    public function getRanks()
    {
        $employmentTypeId = request()->employmentTypeId;
        $policeId = request()->policeId;

        try {
            $ranks = Rank::where('employment_type_id', $employmentTypeId)
                ->where('is_active', true)
                ->orderBy('sort', 'asc')
                ->get();

            if (empty($ranks)) {
                return response()->json([
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'Data tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'data' => $ranks
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getPositions()
    {
        $employmentTypeId = request()->employmentTypeId;
        $policeId = request()->policeId;

        try {
            $positions = Position::withRelated()
                ->where('employment_type_id', $employmentTypeId)
                ->where('police_id', $policeId)
                ->where('is_active', true)
                ->orderBy('sort', 'asc')
                ->get();

            if (empty($positions)) {
                return response()->json([
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'Data tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'data' => $positions
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => $e->getMessage()
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

            $policeId = $this->policeId;
            $oldIsRegisterAdminCanEntryDocument = $request->oldIsRegisterAdminCanEntryDocument;
            $registerNumber = $request->registerNumber;

            if (filter_var($request->isRegisterAdmin, FILTER_VALIDATE_BOOLEAN) == true) {
                $countUserAsWorkUnitAdmin = User::whereHas('officer', function ($query) use ($policeId, $oldIsRegisterAdminCanEntryDocument, $registerNumber) {
                    if (filter_var($oldIsRegisterAdminCanEntryDocument, FILTER_VALIDATE_BOOLEAN) == true) {
                        $query->where('flag', 'ADMIN')
                            ->where('police_id', $policeId)
                            ->where('register_number', '!=', $registerNumber)
                            ->where('is_active', true);
                    } else {
                        $query->where('flag', 'ADMIN')
                            ->where('police_id', $policeId)
                            ->where('is_active', true);
                    }
                })->count();

                // if more 2 then return error
                if ($countUserAsWorkUnitAdmin >= 2) {
                    return response()->json([
                        'success' => false,
                        'errors' => $validator->errors()->add('position', 'Jumlah Admin Satker maksimal 2 orang'),
                        'code' => 422,
                    ], 422);
                }
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
        $officerOldRegisterNumber = Officer::where('register_number', $request->oldRegisterNumber)
            ->pluck('register_number')->first();
        $userOldEmail = User::where('register_number', $request->oldRegisterNumber)
            ->pluck('email')->first();

        return Validator::make($request->all(), [
            'name' => 'required | max:255',
            'employmentType' => 'required',
            'rank' => 'required',
            'birthDate' => 'required',
            'registerNumber' => [
                'required',
                'numeric',
                'regex:/^\d{8}$|^\d{16}$/',
                Rule::unique('officers', 'register_number')->ignore($officerOldRegisterNumber, 'register_number'),
            ],
            'gender' => 'required',
            'religion' => 'required',
            'position' => 'required',
            'education' => 'required',
            'educationInstitutionName' => 'max:255',
            'phoneNumber' => 'required | max:255',
            'email' => [
                'required',
                'max:255',
                'email',
                Rule::unique('users', 'email')->ignore($userOldEmail, 'email'),
            ],
            'policeDiktukEducation' => 'required',
            'policeDiktukEducationGraduateYear' => 'required | max:255',

            'isExistsOfficerSkepPenyidik' => 'required',
            'officerSkepPenyidikNumber' => 'required_if:isExistsOfficerSkepPenyidik,true|max:255',

            'isExistsOfficerCertificate' => 'required',
            'certificateTypeIds' => 'required_if:isExistsOfficerCertificate,true',
            'certificateNumbers' => 'required_if:isExistsOfficerCertificate,true|max:255',
            'certificateStartDates' => 'required_if:isExistsOfficerCertificate,true',
            'certificateEndDates' => 'required_if:isExistsOfficerCertificate,true',

            'registerSignatoryIdentityType' => 'required_if:isRegisterSignatory,true',
            'registerSignatoryIdentityNumber' => 'required_if:isRegisterSignatory,true',

            'isOfficerOperationControlAssistance' => 'required',
            'officerOperationControlAssistanceNumber' => 'required_if:isOfficerOperationControlAssistance,true|max:255',
            'officerOperationControlAssistanceDate' => 'required_if:isOfficerOperationControlAssistance,true',
            'officerOperationControlAssistanceOriginPoliceId' => 'required_if:isOfficerOperationControlAssistance,true',
        ], [
            'name.required' => 'Nama harus diisi',
            'name.max' => 'Nama maksimal 255 karakter',
            'employmentType.required' => 'Status Kepegawaian harus diisi',
            'rank.required' => 'Pangkat harus diisi',
            'birthDate.required' => 'Tanggal Lahir harus diisi',

            'registerNumber.required' => 'NRP harus diisi',
            'registerNumber.regex' => 'NRP harus lengkap',
            'registerNumber.numeric' => 'NRP harus berupa angka',
            'registerNumber.unique' => 'NRP sudah terdaftar',

            'gender.required' => 'Jenis Kelamin harus diisi',
            'religion.required' => 'Agama harus diisi',
            'position.required' => 'Jabatan Struktural harus diisi',
            'education.required' => 'Jenjang Pendidikan Terakhir harus diisi',
            'educationInstitutionName.max' => 'Universitas / Perguruan Tinggi / Sekolah maksimal 255 karakter',
            'phoneNumber.required' => 'Nomor Telepon harus diisi',
            'phoneNumber.max' => 'Nomor Telepon maksimal 255 karakter',
            'email.required' => 'Email harus diisi',
            'email.max' => 'Email maksimal 255 karakter',
            'email.email' => 'Email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'policeDiktukEducation.required' => 'Pendidikan Diktuk harus diisi',
            'policeDiktukEducationGraduateYear.required' => 'Tahun Lulus Diktuk harus diisi',
            'policeDiktukEducationGraduateYear.max' => 'Tahun Lulus Diktuk maksimal 255 karakter',

            'isExistsOfficerSkepPenyidik.required' => 'Status Kepenyidikan harus diisi',
            'officerSkepPenyidikNumber.required_if' => 'Nomor Skep Penyidik harus diisi',

            'isExistsOfficerCertificate.required' => 'Status Sertifikat harus diisi',
            'certificateTypeIds.required_if' => 'Jenis Sertifikat harus diisi',
            'certificateNumbers.required_if' => 'Nomor Sertifikat harus diisi',
            'certificateNumbers.max' => 'Nomor Sertifikat maksimal 255 karakter',
            'certificateStartDates.required_if' => 'Tanggal Mulai Berlaku Sertifikat harus diisi',
            'certificateEndDates.required_if' => 'Tanggal Kadaluwarsa Sertifikat harus diisi',

            'registerSignatoryIdentityType.required_if' => 'Jenis Identitas harus diisi',
            'registerSignatoryIdentityNumber.required_if' => 'Nomor Identitas/NIK harus diisi',
            'registerSignatoryIdentityNumber.digits' => 'Nomor Identitas/NIK harus lengkap',
            'registerSignatoryIdentityNumber.numeric' => 'Nomor Identitas/NIK harus berupa angka',

            'isOfficerOperationControlAssistance.required' => 'Status BKO harus diisi',
            'officerOperationControlAssistanceNumber.required_if' => 'Nomor Surat BKO harus diisi',
            'officerOperationControlAssistanceNumber.max' => 'Nomor Surat BKO maksimal 255 karakter',
            'officerOperationControlAssistanceDate.required_if' => 'Tanggal BKO harus diisi',
            'officerOperationControlAssistanceOriginPoliceId.required_if' => 'BKO Asal Satker harus diisi',
        ]);
    }
}
