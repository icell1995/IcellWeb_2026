<?php

   

namespace App\Http\Controllers\API;

   

use Illuminate\Http\Request;
use App\Http\Controllers\api\BaseController as BaseController;
use App\Models\User;
use App\Models\Polda;
use App\Models\Polres;
use Illuminate\Support\Facades\Auth;
use Validator;
use DB;

   

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

            'email' => 'required|email',

            'password' => 'required',

            // 'c_password' => 'required|same:password',

        ]);

   

        if($validator->fails()){

            return $this->sendError('Validation Error.', $validator->errors());       

        }

   

        $input = $request->all();
        // dd($input);

        $input['password'] = bcrypt($input['password']);

        $user = User::create($input);

        $success['token'] =  $user->createToken('MyApp')->accessToken;

        $success['name'] =  $user->name;

   

        return $this->sendResponse($success, 'User register successfully.');

    }

   

    /**

     * Login api

     *

     * @return \Illuminate\Http\Response

     */

    public function login(Request $request)

    {

        if(Auth::attempt(['username' => $request->username, 'password' => $request->password])){ 
            $space = ' ';
            $user = Auth::user(); 

            
            switch($user->role_id){
                case 1:
                    $laporan_terkirim = '0';
                    $laporan_proses = '0';
                    $laporan_selesai = '0';
                    $name = 'Admin Master';
        
                    //         $laporan_proses = DB::table('surat_penyidikan as sidik')
                    //         ->join('accidents as a', 'sidik.accident_id','=','a.id')
                    // ->selectraw('count(*) as laporan')
                    // ->where('sidik.officer_id', '=', $request->username)
                    // ->where('a.selra_flag','=','S0107')
                    // ->get();
                    // dd($user);
                    // dd($user->createToken('myApp')->accessToken);
                    $success['token'] =  $user->createToken('test')-> accessToken; 
                    $success['username'] =  $user->username;
                    $success['polda']=$user->polda_id;
                    $success['polres']=$user->polres_id;
                    $success['nrp']=$user->officer_id;
                    $success['name']=$name;
                    $success['laporan_terkirim'] = $laporan_terkirim;
                    $success['laporan_proses'] =  $laporan_proses;
                    $success['laporan_selesai'] = $laporan_selesai;
                    $success['avatar']= $user->avatar;
                    $success['role_id']=$user->role_id;
                break;
                case 2:
                    $laporan_terkirim = '0';
                    $laporan_proses = DB::select('select coalesce(count(*),0) as jumlah_proses from surat_penyidikan as sidik 
                                                  left join accidents as a on sidik.accident_id = a.id 
                                                  where sidik.officer_id = \''.$user->officer_id.'\' 
                                                    and a.selra_flag = \'S0107\' ');
                    $laporan_selesai = DB::select('select coalesce(count(*),0) as jumlah_selesai from surat_penyidikan as sidik 
                                                    left join accidents as a on sidik.accident_id = a.id 
                                                    where sidik.officer_id = \''.$user->officer_id.'\' 
                                                    and a.selra_flag <> \'S0107\' ');
                    $name = DB::select('select concat_ws(\''.$space.'\',first_name,last_name) as name from users where username = \''.$user->username.'\'');
                    $wilayah= DB::select('select name from polda where id = \''.$user->polda_id.'\' ');
                    //         $laporan_proses = DB::table('surat_penyidikan as sidik')
                    //         ->join('accidents as a', 'sidik.accident_id','=','a.id')
                    // ->selectraw('count(*) as laporan')
                    // ->where('sidik.officer_id', '=', $request->username)
                    // ->where('a.selra_flag','=','S0107')
                    // ->get();
                    // dd($user);
                    // dd($user->createToken('myApp')->accessToken);
                    $success['token'] =  $user->createToken('test')-> accessToken; 
                    $success['username'] =  $user->username;
                    $success['polda']=$user->polda_id;
                    $success['polres']=$user->polres_id;
                    $success['wilayah']=$wilayah[0]->name;
                    $success['nrp']=$user->officer_id;
                    $success['name']=$name[0]->name;
                    $success['laporan_terkirim'] = $laporan_terkirim;
                    $success['laporan_proses'] =  $laporan_proses[0]->jumlah_proses;
                    $success['laporan_selesai'] =  $laporan_selesai[0]->jumlah_selesai;
                    $success['avatar']= $user->avatar;
                    $success['role_id']=$user->role_id;
                break;
                case 3:
                    $laporan_terkirim = '0';
                    $laporan_proses = DB::select('select coalesce(count(*),0) as jumlah_proses from surat_penyidikan as sidik 
                                                  left join accidents as a on sidik.accident_id = a.id 
                                                  where sidik.officer_id = \''.$user->officer_id.'\' 
                                                    and a.selra_flag = \'S0107\' ');
                    $laporan_selesai = DB::select('select coalesce(count(*),0) as jumlah_selesai from surat_penyidikan as sidik 
                                                    left join accidents as a on sidik.accident_id = a.id 
                                                    where sidik.officer_id = \''.$user->officer_id.'\' 
                                                    and a.selra_flag <> \'S0107\' ');
                    $name = DB::select('select concat_ws(\''.$space.'\',first_name,last_name) as name from users where username = \''.$user->username.'\'');
                    $wilayah= DB::select('select name from polres where id = \''.$user->polres_id.'\' ');
                    //         $laporan_proses = DB::table('surat_penyidikan as sidik')
                    //         ->join('accidents as a', 'sidik.accident_id','=','a.id')
                    // ->selectraw('count(*) as laporan')
                    // ->where('sidik.officer_id', '=', $request->username)
                    // ->where('a.selra_flag','=','S0107')
                    // ->get();
                    // dd($user);
                    // dd($user->createToken('myApp')->accessToken);
                    $success['token'] =  $user->createToken('test')-> accessToken; 
                    $success['username'] =  $user->username;
                    $success['polda']=$user->polda_id;
                    $success['polres']=$user->polres_id;
                    $success['wilayah']=$wilayah[0]->name;
                    $success['nrp']=$user->officer_id;
                    $success['name']=$name[0]->name;
                    $success['laporan_terkirim'] = $laporan_terkirim;
                    $success['laporan_proses'] =  $laporan_proses[0]->jumlah_proses;
                    $success['laporan_selesai'] =  $laporan_selesai[0]->jumlah_selesai;
                    $success['avatar']= $user->avatar;
                    $success['role_id']=$user->role_id;
                break;
                case 4:
                    $laporan_terkirim = '0';
                    $laporan_proses = DB::select('select coalesce(count(*),0) as jumlah_proses from surat_penyidikan as sidik 
                                                  left join accidents as a on sidik.accident_id = a.id 
                                                  where sidik.officer_id = \''.$user->officer_id.'\' 
                                                    and a.selra_flag = \'S0107\' ');
                    $laporan_selesai = DB::select('select coalesce(count(*),0) as jumlah_selesai from surat_penyidikan as sidik 
                                                    left join accidents as a on sidik.accident_id = a.id 
                                                    where sidik.officer_id = \''.$user->officer_id.'\' 
                                                    and a.selra_flag <> \'S0107\' ');
                    $name = DB::select('select concat_ws(\''.$space.'\',first_name,last_name) as name from users where username = \''.$user->username.'\'');
                    $wilayah= DB::select('select name from polres where id = \''.$user->polres_id.'\' ');
                    //         $laporan_proses = DB::table('surat_penyidikan as sidik')
                    //         ->join('accidents as a', 'sidik.accident_id','=','a.id')
                    // ->selectraw('count(*) as laporan')
                    // ->where('sidik.officer_id', '=', $request->username)
                    // ->where('a.selra_flag','=','S0107')
                    // ->get();
                    // dd($user);
                    // dd($user->createToken('myApp')->accessToken);
                    $success['token'] =  $user->createToken('test')-> accessToken; 
                    $success['username'] =  $user->username;
                    $success['polda']=$user->polda_id;
                    $success['polres']=$user->polres_id;
                    $success['wilayah']=$wilayah[0]->name;
                    $success['nrp']=$user->officer_id;
                    $success['name']=$name[0]->name;
                    $success['laporan_terkirim'] = $laporan_terkirim;
                    $success['laporan_proses'] =  $laporan_proses[0]->jumlah_proses;
                    $success['laporan_selesai'] =  $laporan_selesai[0]->jumlah_selesai;
                    $success['avatar']= $user->avatar;
                    $success['role_id']=$user->role_id;
                break;
                
                default:
                
    
            }


           
            return $this->sendResponse($success, 'User login successfully.');

        } 

        else{ 
            return $this->sendError('Kombinasi username / password salah', ['error'=>'Unauthorised']);
        } 

    }


    public function logout() {
        $accessToken = Auth::user()->token();
        DB::table('oauth_access_tokens')
            ->where('id', $accessToken->id)
            ->update([
                'revoked' => true
            ]);

        $accessToken->revoke();
        return $this->sendResponse('a','a');
    }

}