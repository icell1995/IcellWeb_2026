<?php

namespace App\Http\Controllers\Case\Participant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Models\Lib\IdentityType;
use App\Models\Lib\Gender;
use App\Models\Lib\Ethnic;
use App\Models\Lib\Job;
use App\Models\Lib\Religion;
use App\Models\Lib\Education;
use App\Models\Lib\MaritalStatus;
use App\Models\Lib\Location;
use App\Models\Lib\Nationality;
use App\Models\ReportedPerson;
use App\Models\ReportingPerson;

class ParticipantPersonController extends Controller
{
    private function getLibData()
    {
        return [
            'identityTypes' => IdentityType::active()->orderBy('sort')->get(),
            'genders'       => Gender::active()->orderBy('sort')->get(),
            'ethnics'       => Ethnic::active()->orderBy('sort')->get(),
            'jobs'          => Job::active()->orderBy('sort')->get(),
            'religions'     => Religion::active()->orderBy('sort')->get(),
            'educations'    => Education::active()->orderBy('sort')->get(),
            'maritalStatuses' => MaritalStatus::active()->orderBy('sort')->get(),
            'countries'     => Location::active()->where('class', 'COUNTRY')->orderBy('sort')->get(),
            'nationalities' => Nationality::active()->orderBy('sort')->get(),
        ];
    }

    public function create($accidentId)
    {
        $libData = $this->getLibData();

        return view('case.participant.person.create', array_merge(
            ['accidentId' => $accidentId],
            $libData
        ));
    }

    public function store(Request $request, $accidentId)
    {
        $validator = $this->validateForm($request);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $jenisPihak = $request->jenisPihak; // 'PELAPOR' or 'TERLAPOR'

        $identityType         = htmlspecialchars($request->identityType);
        $identityNumber       = htmlspecialchars($request->identityNumber);
        $name                 = htmlspecialchars($request->name);
        $aliasName            = htmlspecialchars($request->aliasName);
        $isUnknownGender      = filter_var($request->isUnknownGender, FILTER_VALIDATE_BOOLEAN);
        $gender               = (!$isUnknownGender) ? htmlspecialchars($request->gender) : NULL;
        $birthPlace           = htmlspecialchars($request->birthPlace);
        $isUnknownBirthPlace  = filter_var($request->isUnknownBirthPlace, FILTER_VALIDATE_BOOLEAN);
        $isUnknownBirthDate   = filter_var($request->isUnknownBirthDate, FILTER_VALIDATE_BOOLEAN);
        $birthDate            = (!$isUnknownBirthDate) ? htmlspecialchars($request->birthDate) : NULL;
        $father               = htmlspecialchars($request->father);
        $isUnknownFather      = filter_var($request->isUnknownFather, FILTER_VALIDATE_BOOLEAN);
        $mother               = htmlspecialchars($request->mother);
        $isUnknownMother      = filter_var($request->isUnknownMother, FILTER_VALIDATE_BOOLEAN);
        $nationality          = htmlspecialchars($request->nationality);
        $ethnic               = htmlspecialchars($request->ethnic);
        $job                  = htmlspecialchars($request->job);
        $religion             = htmlspecialchars($request->religion);
        $education            = htmlspecialchars($request->education);
        $isUnknownMaritalStatus = filter_var($request->isUnknownMaritalStatus, FILTER_VALIDATE_BOOLEAN);
        $maritalStatus        = (!$isUnknownMaritalStatus) ? htmlspecialchars($request->maritalStatus) : NULL;
        $phoneNumber          = htmlspecialchars($request->phoneNumber);
        $isExistsPhoneNumber  = filter_var($request->isExistsPhoneNumber, FILTER_VALIDATE_BOOLEAN);
        $isAvailablePhoneNumber = filter_var($request->isAvailablePhoneNumber, FILTER_VALIDATE_BOOLEAN);
        $email                = htmlspecialchars($request->email);
        $isExistsEmail        = filter_var($request->isExistsEmail, FILTER_VALIDATE_BOOLEAN);
        $isAvailableEmail     = filter_var($request->isAvailableEmail, FILTER_VALIDATE_BOOLEAN);
        $country              = htmlspecialchars($request->country);
        $province             = ($country == 'C101') ? htmlspecialchars($request->province) : NULL;
        $regency              = ($country == 'C101') ? htmlspecialchars($request->regency) : NULL;
        $district             = ($country == 'C101') ? htmlspecialchars($request->district) : NULL;
        $village              = ($country == 'C101') ? htmlspecialchars($request->village) : NULL;
        $subVillage           = ($country == 'C101') ? htmlspecialchars($request->subVillage) : NULL;
        $address              = htmlspecialchars($request->address);
        $isUnknownAddress     = filter_var($request->isUnknownAddress, FILTER_VALIDATE_BOOLEAN);

        $payload = [
            'accident_id'              => $accidentId,
            'identity_type_id'         => $identityType,
            'identity_number'          => $identityNumber,
            'name'                     => $name,
            'gender_id'                => $gender,
            'is_unknown_gender'        => $isUnknownGender,
            'birth_place'              => $birthPlace,
            'is_unknown_birth_place'   => $isUnknownBirthPlace,
            'birth_date'               => $birthDate,
            'is_unknown_birth_date'    => $isUnknownBirthDate,
            'father_name'              => $father,
            'is_unknown_father'        => $isUnknownFather,
            'mother_name'              => $mother,
            'is_unknown_mother'        => $isUnknownMother,
            'nationality_id'           => $nationality,
            'ethnic_id'                => $ethnic,
            'job_id'                   => $job,
            'religion_id'              => $religion,
            'education_id'             => $education,
            'marital_status_id'        => $maritalStatus,
            'is_unknown_marital_status'=> $isUnknownMaritalStatus,
            'phone_number'             => $phoneNumber,
            'is_exists_phone_number'   => $isExistsPhoneNumber,
            'is_available_phone_number'=> $isAvailablePhoneNumber,
            'email'                    => $email,
            'is_exists_email'          => $isExistsEmail,
            'is_available_email'       => $isAvailableEmail,
            'country_id'               => $country,
            'province_id'              => $province,
            'regency_id'               => $regency,
            'district_id'              => $district,
            'village_id'               => $village,
            'sub_village'              => $subVillage,
            'address'                  => $address,
            'is_unknown_address'       => $isUnknownAddress,
        ];

        DB::beginTransaction();
        try {
            // Simpan ke tabel sesuai pilihan
            if ($jenisPihak === 'PELAPOR') {
                $payload['alias_name'] = $aliasName;
                unset($payload['name_alias']);
                ReportingPerson::create($payload);
            } else {
                $payload['name_alias'] = $aliasName;
                unset($payload['alias_name']);
                ReportedPerson::create($payload);
            }

            DB::commit();

            return redirect()
                ->route('view_produktivitas_accident', ['accident_id' => $accidentId, 'page' => 'participants'])
                ->with('success', 'Data berhasil disimpan');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('ParticipantPersonController@store : ', [$e->getMessage()]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data');
        }
    }

    public function getLocations(Request $request, $accidentId)
    {
        $locations = Location::where('is_active', true)
            ->where('parent_id', $request->parent_id)
            ->where('class', $request->class)
            ->get();

        return response()->json(['status' => 'success', 'code' => 200, 'data' => $locations], 200);
    }

    public function validateRequestForm(Request $request, $accidentId)
    {
        try {
            $validator = $this->validateForm($request);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'code' => 422, 'errors' => $validator->errors()], 422);
            }

            return response()->json(['success' => true, 'code' => 200, 'message' => 'Silahkan menunggu proses simpan data'], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'errors' => 'Terjadi kesalahan pada sistem.', 'code' => 500], 500);
        }
    }

    private function validateForm(Request $request)
    {
        return Validator::make($request->all(), [
            'jenisPihak'    => 'required|in:PELAPOR,TERLAPOR',
            'identityType'  => 'required',
            'identityNumber'=> 'required|max:255',
            'name'          => 'required|max:255',
            'aliasName'     => 'max:255',
            'gender'        => 'required_unless:isUnknownGender,true',
            'birthPlace'    => 'required|max:255',
            'birthDate'     => 'required_unless:isUnknownBirthDate,true',
            'father'        => 'required|max:255',
            'mother'        => 'required|max:255',
            'nationality'   => 'required',
            'ethnic'        => 'required',
            'job'           => 'required',
            'religion'      => 'required',
            'education'     => 'required',
            'maritalStatus' => 'required_unless:isUnknownMaritalStatus,true',
            'phoneNumber'   => 'required|max:255',
            'email'         => 'required|max:255',
            'country'       => 'required',
            'province'      => 'required_if:country,C101',
            'regency'       => 'required_if:country,C101',
            'district'      => 'required_if:country,C101',
            'village'       => 'required_if:country,C101',
            'subVillage'    => 'max:255',
            'address'       => 'required|max:255',
        ], [
            'jenisPihak.required'       => 'Jenis Pihak harus dipilih',
            'jenisPihak.in'             => 'Jenis Pihak tidak valid',
            'identityType.required'     => 'Jenis Identitas harus diisi',
            'identityNumber.required'   => 'No Identitas harus diisi',
            'name.required'             => 'Nama harus diisi',
            'gender.required_unless'    => 'Jenis Kelamin harus diisi',
            'birthPlace.required'       => 'Tempat Lahir harus diisi',
            'birthDate.required_unless' => 'Tgl. Lahir harus diisi',
            'nationality.required'      => 'Kewarganegaraan harus diisi',
            'father.required'           => 'Ayah harus diisi',
            'mother.required'           => 'Ibu harus diisi',
            'ethnic.required'           => 'Suku harus diisi',
            'job.required'              => 'Pekerjaan harus diisi',
            'religion.required'         => 'Agama harus diisi',
            'education.required'        => 'Pendidikan harus diisi',
            'maritalStatus.required_unless' => 'Status Perkawinan harus diisi',
            'phoneNumber.required'      => 'No. Telepon harus diisi',
            'email.required'            => 'Email harus diisi',
            'country.required'          => 'Negara harus diisi',
            'province.required_if'      => 'Provinsi harus diisi',
            'regency.required_if'       => 'Kabupaten/Kota harus diisi',
            'district.required_if'      => 'Kecamatan harus diisi',
            'village.required_if'       => 'Desa/Kelurahan harus diisi',
            'address.required'          => 'Alamat harus diisi',
        ]);
    }
}
