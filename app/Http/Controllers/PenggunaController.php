<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Lib\Role;
use App\Models\Polda;
use App\Models\Polres;
use App\Models\Ref;
use App\Services\Regional\RegionalScopeService;
use DB;
use Session;

class PenggunaController extends Controller{
    protected $_title  = 'Daftar Pengguna';

    function __construct()
    {
        view()->share('_title', $this->_title);
    }

    public function index(){
        // $pengguna=User::all();
        $user = Auth::user();
        $roleData=Auth::user()->role_id;
        switch ($roleData) {
            case 2:
                    $pengguna = User::with(['role', 'polda', 'polres'])
                    ->where('username','<>',$user->username)
                    ->where('role_id','>',1)
                    ->where('polda_id','=',$user->polda_id)
                    ->orderBy('created_at', 'asc')->paginate(10);
                    $role=Role::where('id','!=','1')->where('id','!=','2')->get();
                    $polda=Polda::where('id','=',$user->polda_id)->get();
                    $polres=Polres::where('polda_id','=',$user->polda_id)->get();

                break;
                case 3:
                    $pengguna = User::with(['role', 'polda', 'polres'])
                    ->where('username','<>',$user->username)
                    ->where('role_id','>',2)
                    ->where('polres_id','=',$user->polres_id)
                    ->orderBy('created_at', 'asc')->paginate(10);
                    $role=Role::where('id','!=','3')->where('id','!=','1')->where('id','!=','2')->get();
                    $polda=Polda::where('id','=',$user->polda_id)->get();
                    $polres=Polres::where('id','=',$user->polres_id)->get();

                break;
            default:
                $pengguna = User::with(['role', 'polda', 'polres'])->where('username','<>',$user->username)
                ->orderBy('role_id','asc')
                ->orderBy('polres_id')
                ->orderBy('id')
                ->paginate(10);
                // dd($pengguna);
                $role=Role::all();
                $polda=Polda::all();
                //set default agar tidak muncul semua
                // $polres=Polres::where('polda_id','=','01')->get();
                $polres= Polres::all();
                break;
        }
        $rank=Ref::where('grp_id','=','RANK')->orderBy('sort')->get();
        // dd($pengguna);
        return view('pengguna.pengguna-index',compact('user','pengguna','role','polres','polda','rank'));
    }

    public function add()
    {
        $user=Auth::user();
        switch ($user->role_id) {
                case 2:
                $role=Role::where('id','!=','1')->where('id','!=','2')->get();
                $polda=Polda::where('id','=',$user->polda_id)->get();
                $polres=Polres::where('polda_id','=',$user->polda_id)->get();
                break;
            default:
                // $pengguna = User::with('role')->where('username','<>',$user)->orderBy('id')->get();
                $role=Role::all();
                $polda=Polda::all();
                $polres=Polres::all();
                break;
        }
        return view('pengguna.pengguna-add',compact('user','role','polda','polres'));
    }


    public function add_pengguna(Request $request )
    {
        // dd($request->polda_id);
        $this->validate($request,[
            'username_add' => 'required|min:5|max:20',
            'first_name_add' => 'required|max:20',
            // 'last_name' => 'required|max:20',
            'role_id_add' => 'required|numeric',
            'pangkat_add' => 'required',
            'nrp_add' => 'required|numeric',
            // 'password' => 'min:8|required_with:password-confirm_add|same:password-confirm_add',
            'password_add'=> [
                'required',
                'min:8',
                'regex:/^.*(?=.{1,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$#%]).*$/',
                'confirmed'
            ],
            'email_add' => 'required|string|email|max:255',
            'phone_add' => 'required|numeric',
            // 'password-confirm'=>'required',

        ]);

        $user = Auth::user();
        $level = $user->role_id;
        $cek_level = $request->role_id_add;

        if (!RegionalScopeService::canAssignRole($user, (int) $cek_level)) {
            abort(403, 'Anda tidak diizinkan menetapkan role tersebut.');
        }

        if($cek_level=="1") {
            $this->validate($request, [
                //isi validate
            ]);
            $polda_id = $polres_id = null;
            if ($level != '1'){
                return redirect('pengguna')->withErrors(['Anda Tidak Diizinkan Untuk Memilih Level 1'])->withInput();
            }
        } else if($cek_level=="2") {
            $polres_id = null;
            $this->validate($request, [
                'polda_id_add' => 'required|max:8',
            ]);
            $polres_id = null;
            $polda_id = $request->polda_id_add;
            if ($level == '3' || $level == '4'){
                return redirect('pengguna')->withErrors(['Anda Tidak Diizinkan Untuk Memilih Level 2'])->withInput();
            }
        } else if($cek_level=="3" || $cek_level=="4") {
            $this->validate($request, [
                'polres_id_add'    => 'required|max:8',
                'polda_id_add'     => 'required|max:8',
            ]);
            $polda_id = $request->polda_id_add;
            $polres_id = $request->polres_id_add;
            if ($level == 2 && (string) $polda_id !== (string) $user->polda_id) {
                abort(403, 'Akses lintas Polda ditolak.');
            }
            if ($level == 3 && (string) $polres_id !== (string) $user->polres_id) {
                abort(403, 'Akses lintas Polres ditolak.');
            }
        } else {
            $polda_id = $polres_id = $officer_id = $rank_id = null;
        }

        $username =   User::select('username')->where('username','=',$request->username_add)->first();
        $email =   User::select('email')->where('email','=',$request->email_add)->first();
        if(!$username){
            if(!$email){
                $user= User::create([
                'username' => $request->username_add,
                'first_name' =>  $request->first_name_add,
                'last_name' =>  $request->last_name_add,
                'officer_id' => $request->nrp_add,
                'role_id' =>  $request->role_id_add,
                'pangkat' =>  $request->pangkat_add,
                'polda_id' =>  $polda_id,
                'polres_id' =>  $polres_id,
                'police_id' => (!empty($polres_id)) ? $polres_id : $polda_id,
                // 'polres_id' =>  str_pad((string) $request->polres_id_add, 4, '0', STR_PAD_LEFT),
                'email' =>  $request->email_add,
                'phone' =>  $request->phone_add,
                'password' => Hash::make( $request->password_add),
                'avatar' => 'user.png',
                'state' => '1',
                ]);
            // Session::flash('sukses','Ini notifikasi SUKSES');
            // return redirect('register')->withSucces('Sukses');
            return redirect('pengguna')->with('message', 'Sukses menambahkan Petugas!');
            }
            else{
                return redirect('pengguna')->withErrors(['Email sudah dipakai'])->withInput();
                // return redirect()->back();
            }
       }
       else{
        return redirect('pengguna')->withErrors(['Username sudah dipakai'])->withInput();
        // return redirect()->back();
       }
    }

    public function polres_list($poldaId)
    {
        $user = Auth::user();
        $roleData=Auth::user()->role_id;
        switch ($roleData) {
            case 2:
                $polda = Polda::find($poldaId);
                $polres = $polda->polres()->get();
                break;
                case 3:
                    $polda = Polda::find($poldaId);
                    $polres = $polda->polres()->where('id','=',$user->polres_id)->get();
                break;
            default:
            $polda = Polda::find($poldaId);
            $polres = $polda->polres()->get();
                break;
        }
        return response()->json($polres);
    }


    public function edit($id)
    {
        // mengambil data pegawai berdasarkan id yang dipilih
        $user=Auth::getUser();
        $targetUser = User::findOrFail($id);
        if (!RegionalScopeService::authorizeTargetUser($user, $targetUser)) {
            abort(403, 'Akses lintas satker ditolak.');
        }
        $pengguna = DB::table('users')->where('id',$id)->get();
        $role=Role::all();
        $polda=Polda::all();
        $polres=Polres::all();
        return view('pengguna.pengguna-edit',compact('user','pengguna','role','polda','polres'));
    }

    public function edit_modal_pengguna(Request $request)
    {
        $id = $request->id;
        $users = User::find($id);
        if (!$users || !RegionalScopeService::authorizeTargetUser(Auth::user(), $users)) {
            abort(403, 'Akses lintas satker ditolak.');
        }
        $role = Role::all();
        $polda = Polda::all();
        $polres = Polres::all();
        $rank = Ref::where('grp_id', '=', 'RANK')->orderBy('sort')->get();
        $data = [
            'users' => $users,
            'role'=>$role,
            'polda'=>$polda,
            'polres'=>$polres,
        ];

        return response()->json($data);
    }
    public function edit_pengguna(Request $request )
    {
        $targetUser = User::findOrFail($request->id);
        if (!RegionalScopeService::authorizeTargetUser(Auth::user(), $targetUser)) {
            abort(403, 'Akses lintas satker ditolak.');
        }
        if (!RegionalScopeService::canAssignRole(Auth::user(), (int) $request->role_id_edit)) {
            abort(403, 'Anda tidak diizinkan menetapkan role tersebut.');
        }

        // dd($request->all());
        // if($request->role_id <> 2){
            $this->validate($request,[
                'username_edit' => 'required|min:8|max:20',
                'first_name_edit' => 'required|max:20',
                // 'last_name' => 'required|max:20',
                'role_id_edit' => 'required|numeric',
                'pangkat_edit' => 'required',
                // 'polda_id_edit' => 'required',
                // 'polres_id_edit' => 'required',
                // 'password' => 'min:8|required_with:password-confirm|same:password-confirm',
                // 'password_edit'=>'required| min:8| max:12 |confirmed',
                'email_edit' => 'required|string|email|max:255',
                'phone_edit' => 'required',
                // 'password-confirm'=>'required',
            ]);
        // }
        // dd($request->password_edit);
        if($request->password_edit != null){
            $this->validate($request,[
                'password_edit'=>'required| min:8| max:12 |confirmed',
            ]);
        }

        $user = Auth::user();
        $level = $user->role_id;
        $cek_level = $request->role_id_edit;

        if($cek_level=="1") {
        $this->validate($request, [
                //isi validate
            ]);
            $polda_id = $polres_id = null;
            if ($level != '1'){
                return redirect('pengguna')->withErrors(['Anda Tidak Diizinkan Untuk Memilih Level 1'])->withInput();
            }
        } else if($cek_level=="2") {
            $polres_id = null;
            $this->validate($request, [
                'polda_id_edit' => 'required|max:8',
            ]);
            $polres_id = null;
            $polda_id = $request->polda_id_edit;
            if ($level == '3' || $level == '4'){
                return redirect('pengguna')->withErrors(['Anda Tidak Diizinkan Untuk Memilih Level 2'])->withInput();
            }
        } else if($cek_level=="3" || $cek_level=="4") {
            $this->validate($request, [
                'polres_id_edit'    => 'required|max:8',
                'polda_id_edit'     => 'required|max:8',
            ]);
            $polda_id = $request->polda_id_edit;
            $polres_id = $request->polres_id_edit;
        } else {
            $polda_id = $polres_id = $officer_id = $rank_id = null;
        }

        // $cek_email_users =DB::table('users')->where('id','<>',$request->input('id'))->where('email','<>','')->whereNotNull('email')->where('email','=',$request->input('email_edit'))->count();

        // if($cek_email_users > 0) {
        //     return back()->withErrors(['Email sudah digunakan silahkan ganti alamat email anda'])->withInput();
        // }

        if($request->password_edit != null){
            User::where('id', $request->id)
            ->update([
            'username'=>$request->username_edit,
            'first_name'=>$request->first_name_edit,
            'last_name'=>$request->last_name_edit,
            'email'=>$request->email_edit,
            'phone'=>$request->phone_edit,
            'password' => Hash::make( $request->password_edit),
            'polda_id' =>  $polda_id,
            'polres_id' =>  $polres_id,
            'police_id' => (!empty($polres_id)) ? $polres_id : $polda_id,
            'role_id'=>$request->role_id_edit,
            'pangkat'=>$request->pangkat_edit
            ]);
        }else{
            User::where('id', $request->id)
            ->update([
            'username'=>$request->username_edit,
            'first_name'=>$request->first_name_edit,
            'last_name'=>$request->last_name_edit,
            'email'=>$request->email_edit,
            'phone'=>$request->phone_edit,
            'polda_id' =>  $polda_id,
            'polres_id' =>  $polres_id,
            'police_id' => (!empty($polres_id)) ? $polres_id : $polda_id,
            'role_id'=>$request->role_id_edit,
            'pangkat'=>$request->pangkat_edit
            ]);
        }
                // $user= User::update([
                // 'username' => $request->username,
                // 'first_name' =>  $request->first_name,
                // 'last_name' =>  $request->last_name,
                // 'role_id' =>  $request->role_id,
                // 'pangkat' =>  $request->pangkat,
                // 'email' =>  $request->email,
                // 'password' => Hash::make( $request->password),
                // 'state' => '1',
                // ]);
            // Session::flash('sukses','Ini notifikasi SUKSES');
            // return redirect('register')->withSucces('Sukses');
        return redirect('pengguna')->with('message', 'Sukses Update Data Petugas!');
    }


    public function delete_pengguna($id){
        $targetUser = User::findOrFail($id);
        if (!RegionalScopeService::authorizeTargetUser(Auth::user(), $targetUser)) {
            abort(403, 'Akses lintas satker ditolak.');
        }
        $pengguna = DB::table('users')->where('id',$id)->get();
        if($pengguna[0]->state == 1){
        User::where('id', $pengguna[0]->id)
        ->update(['state'=>0 ]);
        return redirect('pengguna')->with('message', 'Sukses menonaktifkan Data Petugas!');
        }
        User::where('id', $pengguna[0]->id)
        ->update(['state'=>1 ]);
        return redirect('pengguna')->with('message', 'Sukses mengaktifkan Data Petugas!');
    }

    public function search_user(Request $request)
    {
        $search = $request->input('search');
        $user = Auth::user();
        $polres_input = $request->input('polres');
        $polda_input = $request->input('polda');
        $status = $request->state;
        if($status == null){
            $status = 99;
        }

        $roleData = Auth::user()->role_id;
        $applySearchFilters = function ($query, $poldaFilter, $polresFilter, $statusFilter, $searchFilter) {
            return $query
                ->when(!empty($poldaFilter) && $poldaFilter !== '-', fn($q) => $q->where('polda_id', $poldaFilter))
                ->when(!empty($polresFilter) && $polresFilter !== '-', fn($q) => $q->where('polres_id', $polresFilter))
                ->when((string) $statusFilter !== '99' && $statusFilter !== '-', fn($q) => $q->where('state', $statusFilter))
                ->when(!empty($searchFilter) && $searchFilter !== '-', function ($q) use ($searchFilter) {
                    $term = '%' . $searchFilter . '%';
                    $q->where(function ($sub) use ($term) {
                        $sub->where('first_name', 'ilike', $term)
                            ->orWhere('last_name', 'ilike', $term)
                            ->orWhere('username', 'ilike', $term);
                    });
                });
        };

        switch ($roleData) {
            case 2:
                $polda_input = $user->polda_id;
                if ($polres_input == null) {
                    $polres_input = '-';
                }
                $pengguna = $applySearchFilters(
                    User::with(['role', 'polda', 'polres'])->where('users.id', '>', 1)->where('username', '<>', $user->username),
                    $polda_input,
                    $polres_input,
                    $status,
                    $search
                )->orderBy('created_at', 'asc')->paginate(10);
                $role = Role::where('id', '!=', '1')->where('id', '!=', '2')->get();
                $polda = Polda::where('id', '=', $user->polda_id)->get();
                $polres = Polres::where('polda_id', '=', $user->polda_id)->get();
                break;

            case 3:
                $polda_input = $user->polda_id;
                $polres_input = $user->polres_id;
                $pengguna = $applySearchFilters(
                    User::with(['role', 'polda', 'polres'])->where('username', '<>', $user->username)->where('users.id', '>', 2),
                    $polda_input,
                    $polres_input,
                    $status,
                    $search
                )->orderBy('created_at', 'asc')->paginate(10);
                $role = Role::where('id', '>', '3')->get();
                $polda = Polda::where('id', '=', $user->polda_id)->get();
                $polres = Polres::where('id', '=', $user->polres_id)->get();
                break;

            case 4:
                $polda_input = $user->polda_id;
                $polres_input = $user->polres_id;
                $pengguna = $applySearchFilters(
                    User::with(['role', 'polda', 'polres'])->where('username', '<>', $user->username)->where('users.id', '>', 3),
                    $polda_input,
                    $polres_input,
                    $status,
                    $search
                )->orderBy('created_at', 'asc')->paginate(10);
                $role = Role::where('id', '>', '3')->get();
                $polda = Polda::where('id', '=', $user->polda_id)->get();
                $polres = Polres::where('id', '=', $user->polres_id)->get();
                break;

            default:
                if ($polda_input == null) {
                    $polda_input = '-';
                }
                if ($polres_input == null) {
                    $polres_input = '-';
                }
                $pengguna = $applySearchFilters(
                    User::with(['role', 'polda', 'polres'])->where('username', '<>', $user->username),
                    $polda_input,
                    $polres_input,
                    $status,
                    $search
                )
                    ->orderBy('role_id', 'asc')
                    ->orderBy('polres_id')
                    ->orderBy('id')
                    ->paginate(10);
                $role = Role::all();
                $polda = Polda::all();
                $polres = Polres::all();
                break;
        }
        $rank = Ref::where('grp_id', '=', 'RANK')->orderBy('sort')->get();
        $pengguna->appends($request->all());
        return view('pengguna.pengguna-index', compact('user', 'pengguna', 'role', 'polres', 'polda', 'rank'));
    }


}
