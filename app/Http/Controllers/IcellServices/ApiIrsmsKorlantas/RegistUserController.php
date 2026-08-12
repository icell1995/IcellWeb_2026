<?php

namespace App\Http\Controllers\IcellServices\ApiIrsmsKorlantas;

use App\Http\Controllers\Controller;
use App\Models\Lib\Police;
use App\Models\Lib\Rank;
use App\Models\Officer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class RegistUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function storeIRSMS(Request $request)
    {
        $officerOldRegisterNumber = Officer::where('register_number', $request->oldRegisterNumber)
            ->pluck('register_number')->first();
        $userOldEmail = User::where('register_number', $request->oldRegisterNumber)
            ->pluck('email')->first();

        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'rank' => 'required',
            'birth_date' => 'required',
            'register_number' => [
                'required',
                'numeric',
                'regex:/^\d{8}$|^\d{16}$/',
                Rule::unique('officers', 'register_number')->ignore($officerOldRegisterNumber, 'register_number'),
            ],
            'gender' => 'required',
            'religion' => 'required',
            'police_id' => 'required',
            'positon' => 'required',
            'education' => 'required',
            'phone_number' => 'required|max:20',
            'email' => [
                'required',
                'max:255',
                'email',
                Rule::unique('users', 'email')->ignore($userOldEmail, 'email'),
            ],
            'password' => 'required|min:6|max:255|confirmed:password_confirmation|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[-_#@$!%*?&])[A-Za-z\d\-_#@$!%*?&]+$/',
            'password_confirmation' => 'required',
        ]);

        $name = $request->first_name. ' ' . $request->last_name;
        $rankId = htmlspecialchars($request->rank);
        $birthDate = htmlspecialchars($request->birth_date);
        $register_number = htmlspecialchars($request->register_number);
        $genderId = htmlspecialchars($request->gender);
        $religonId = htmlspecialchars($request->religion);
        $policeId = htmlspecialchars($request->police_id);
        $positionId = htmlspecialchars($request->positon);
        $educationId = htmlspecialchars($request->education);
        $phoneNumber = htmlspecialchars($request->phone_number);
        $email = htmlspecialchars($request->email);
        $password = $request->password;

        try{
            $police = Police::with('parent')->where('id', $policeId)->first();
            $rank = Rank::where('id',$rankId)->first();

            $lastUser = User::max('id');

            $user = User::updateOrcreate(
                [
                    'register_number' => $register_number,
                ],
                [
                    'id' => $lastUser + 1,
                    'first_name' => $name,
                    'last_name' => null,
                    'last_title' => null,
                    'officer_id' => $register_number,
                    'role_id' => 4,
                    'pangkat' => $rankId,
                    'polda_id' => ($police->class == 'DAERAH') ? $police->id : (($police->class == 'RESOR' && !empty($police->parent)) ? $police->parent->id : null),
                    'polres_id' => ($police->class == 'RESOR') ? $police->id : 0, 
                    'email' => $email,
                    'phone' => $phoneNumber,
                    'avatar' => 'user.png',
                    'state' => 1,
                    
                ]
            );
        }catch(\Exception $e){

        }
    }
}
