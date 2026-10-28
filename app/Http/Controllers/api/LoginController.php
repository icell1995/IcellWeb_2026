<?php

   

namespace App\Http\Controllers\api;

   

use Illuminate\Http\Request;
use App\Http\Controllers\api\BaseController as BaseController;
use App\Models\User;
use App\Models\Polda;
use App\Models\Polres;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class LoginController extends BaseController
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors()->toArray());       
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);

        /** @var mixed $user */
        $user = User::create($input);

        $tokenResult = method_exists($user, 'createToken') ? $user->createToken('MyApp') : null;
        $success = [
            'token' => $tokenResult ? ($tokenResult->plainTextToken ?? ($tokenResult->accessToken ?? '')) : '',
            'name'  => $user->name ?? $user->username,
        ];

        return $this->sendResponse($success, 'User register successfully.');
    }

    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        if (Auth::attempt(['username' => $request->username, 'password' => $request->password])) { 
            $space = ' ';
            /** @var mixed $user */
            $user = Auth::user(); 
            $success = [];
            $tokenResult = method_exists($user, 'createToken') ? $user->createToken('test') : null;
            $tokenString = $tokenResult ? ($tokenResult->plainTextToken ?? ($tokenResult->accessToken ?? '')) : '';

            switch ($user->role_id) {
                case 1:
                    $laporan_terkirim = '0';
                    $laporan_proses   = '0';
                    $laporan_selesai  = '0';
                    $name             = 'Admin Master';

                    $success['token']            = $tokenString; 
                    $success['username']         = $user->username;
                    $success['polda']            = $user->polda_id;
                    $success['polres']           = $user->polres_id;
                    $success['nrp']              = $user->officer_id;
                    $success['name']             = $name;
                    $success['laporan_terkirim'] = $laporan_terkirim;
                    $success['laporan_proses']   = $laporan_proses;
                    $success['laporan_selesai']  = $laporan_selesai;
                    $success['avatar']           = $user->avatar;
                    $success['role_id']          = $user->role_id;
                    break;
                case 2:
                    $laporan_terkirim = '0';
                    $laporan_proses = DB::select('select coalesce(count(*),0) as jumlah_proses from surat_penyidikan as sidik 
                                                  left join accidents as a on sidik.accident_id = a.id 
                                                  where sidik.officer_id = ? 
                                                    and a.selra_flag = ?', [$user->officer_id, 'S0107']);
                    $laporan_selesai = DB::select('select coalesce(count(*),0) as jumlah_selesai from surat_penyidikan as sidik 
                                                    left join accidents as a on sidik.accident_id = a.id 
                                                    where sidik.officer_id = ? 
                                                    and a.selra_flag <> ?', [$user->officer_id, 'S0107']);
                    $name = DB::select('select concat_ws(?, first_name, last_name) as name from users where username = ?', [$space, $user->username]);
                    $wilayah = DB::select('select name from polda where id = ?', [$user->polda_id]);

                    $success['token']            = $tokenString; 
                    $success['username']         = $user->username;
                    $success['polda']            = $user->polda_id;
                    $success['polres']           = $user->polres_id;
                    $success['wilayah']          = $wilayah[0]->name ?? '';
                    $success['nrp']              = $user->officer_id;
                    $success['name']             = $name[0]->name ?? '';
                    $success['laporan_terkirim'] = $laporan_terkirim;
                    $success['laporan_proses']   = $laporan_proses[0]->jumlah_proses ?? 0;
                    $success['laporan_selesai']  = $laporan_selesai[0]->jumlah_selesai ?? 0;
                    $success['avatar']           = $user->avatar;
                    $success['role_id']          = $user->role_id;
                    break;
                case 3:
                    $laporan_terkirim = '0';
                    $laporan_proses = DB::select('select coalesce(count(*),0) as jumlah_proses from surat_penyidikan as sidik 
                                                  left join accidents as a on sidik.accident_id = a.id 
                                                  where sidik.officer_id = ? 
                                                    and a.selra_flag = ?', [$user->officer_id, 'S0107']);
                    $laporan_selesai = DB::select('select coalesce(count(*),0) as jumlah_selesai from surat_penyidikan as sidik 
                                                    left join accidents as a on sidik.accident_id = a.id 
                                                    where sidik.officer_id = ? 
                                                    and a.selra_flag <> ?', [$user->officer_id, 'S0107']);
                    $name = DB::select('select concat_ws(?, first_name, last_name) as name from users where username = ?', [$space, $user->username]);
                    $wilayah = DB::select('select name from polres where id = ?', [$user->polres_id]);

                    $success['token']            = $tokenString; 
                    $success['username']         = $user->username;
                    $success['polda']            = $user->polda_id;
                    $success['polres']           = $user->polres_id;
                    $success['wilayah']          = $wilayah[0]->name ?? '';
                    $success['nrp']              = $user->officer_id;
                    $success['name']             = $name[0]->name ?? '';
                    $success['laporan_terkirim'] = $laporan_terkirim;
                    $success['laporan_proses']   = $laporan_proses[0]->jumlah_proses ?? 0;
                    $success['laporan_selesai']  = $laporan_selesai[0]->jumlah_selesai ?? 0;
                    $success['avatar']           = $user->avatar;
                    $success['role_id']          = $user->role_id;
                    break;
                case 4:
                    $laporan_terkirim = '0';
                    $laporan_proses = DB::select('select coalesce(count(*),0) as jumlah_proses from surat_penyidikan as sidik 
                                                  left join accidents as a on sidik.accident_id = a.id 
                                                  where sidik.officer_id = ? 
                                                    and a.selra_flag = ?', [$user->officer_id, 'S0107']);
                    $laporan_selesai = DB::select('select coalesce(count(*),0) as jumlah_selesai from surat_penyidikan as sidik 
                                                    left join accidents as a on sidik.accident_id = a.id 
                                                    where sidik.officer_id = ? 
                                                    and a.selra_flag <> ?', [$user->officer_id, 'S0107']);
                    $name = DB::select('select concat_ws(?, first_name, last_name) as name from users where username = ?', [$space, $user->username]);
                    $wilayah = DB::select('select name from polres where id = ?', [$user->polres_id]);

                    $success['token']            = $tokenString; 
                    $success['username']         = $user->username;
                    $success['polda']            = $user->polda_id;
                    $success['polres']           = $user->polres_id;
                    $success['wilayah']          = $wilayah[0]->name ?? '';
                    $success['nrp']              = $user->officer_id;
                    $success['name']             = $name[0]->name ?? '';
                    $success['laporan_terkirim'] = $laporan_terkirim;
                    $success['laporan_proses']   = $laporan_proses[0]->jumlah_proses ?? 0;
                    $success['laporan_selesai']  = $laporan_selesai[0]->jumlah_selesai ?? 0;
                    $success['avatar']           = $user->avatar;
                    $success['role_id']          = $user->role_id;
                    break;
                default:
                    $success['token']            = $tokenString;
                    $success['username']         = $user->username;
                    $success['polda']            = $user->polda_id;
                    $success['polres']           = $user->polres_id;
                    $success['nrp']              = $user->officer_id;
                    $success['name']             = $user->username;
                    $success['avatar']           = $user->avatar;
                    $success['role_id']          = $user->role_id;
                    break;
            }

            return $this->sendResponse($success, 'User login successfully.');
        } else { 
            return $this->sendError('Kombinasi username / password salah', ['error' => 'Unauthorised']);
        } 
    }

    public function logout()
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if ($user) {
            if (method_exists($user, 'currentAccessToken') && $user->currentAccessToken()) {
                $user->currentAccessToken()->delete();
            } elseif (method_exists($user, 'token') && $user->token()) {
                $accessToken = $user->token();
                DB::table('oauth_access_tokens')
                    ->where('id', $accessToken->id)
                    ->update(['revoked' => true]);
                $accessToken->revoke();
            }
        }
        return $this->sendResponse([], 'User logout successfully.');
    }
}