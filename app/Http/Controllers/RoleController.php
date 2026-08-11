<?php

namespace App\Http\Controllers;


use App\Models\Permission;
use App\Models\RolePermission;
use App\Models\Lib\Role;
use App\Models\User;
use App\Classes\Authority;
use DB;
use Form, Html;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Auth;


class RoleController extends Controller
{

    // protected $_module = 'role';
	// protected $_view   = 'role';
	// protected $_title  = 'Role Management';

    // function __construct(Role $_roleModel)
	// {
    //     $this->_roleModel       = $_roleModel;
    //     view()->share('_title', $this->_title);
    //     view()->share('_module', $this->_module);
	// }
    protected $_title  = 'Role Management';

    function __construct()
    {
        view()->share('_title', $this->_title);
    }

    public function index()
    {
        $perPage = 10;
		// $_data['data'] = Role::orderBy('id','asc')->paginate($perPage);
        // $data = Role::orderBy('id','asc')->paginate($perPage);
        // $role = Role::find(1);
        // dd($role->permissions);
        $user=Auth::getUser();
        $data = Role::with(['permissions'])->get();
        // dd($data);
        // return view($this->_view.'/role-index',$_data);
        return view('role.role-index',compact('data','user'));
    }


    public function add_role()
    {
        $permission = Permission::all();
        return view('role.role-add',compact('permission'));
    }

    public function submit_add_role(Request $request)
    {
        $cek_role = Role::where('name','=',$request->name)->first();
        // dd($cek_role);
        if($cek_role==null){
            $permission    = $request->permission == null ? [] : $request->permission;

            // dd($permission);
            // dd($request->permission);
            // $data= $request->all();
            // dd($data);
            $role = Role::create(['name'=>$request->name,'state'=>1]);
            $get_role = Role::where('name','=',$request->name)->get();
            $get=$get_role[0]->id;
            $roles=Role::find($get);
            // $get=$roles[0]->id;
            // dd($get);
            $roles->permissions()->attach($permission);
            // $role->permissions()->attach($request->permission[$this->DEFAULT_LANGUAGE]);
            return redirect('role')->with('message', 'Sukses menambahkan Role Akses!');
        }
        else {
            return redirect('role/role-add')->withErrors(['', 'Nama Level sudah dipakai'])->withInput();
        }

    }


    // public function getEdit($id)
    // {
    //     return $this->edit($id);
    // }

    public function edit($id)
    {
        if($id) {
            $data2 = Role::find($id);
            //  $_data2['data2'] = $data2;
            // return $data2;
        }

        // $data = DB::table('users')
        //     ->select('users.id as id','lib.roles.name as role_name','permissions.id','permissions.name','permission_role.role_id','permission_role.permission_id')
        //     ->join('lib.roles','users.role_id','lib.roles.id')
        //     ->join('permission_role','lib.roles.id','permission_role.role_id')
        //     ->join('permissions','permission_role.permission_id','permissions.id')
        //     ->where('permission_role.role_id','=',$id)
        //     ->get();

        // $roles = Role::with('permissions')->where('id',$id)->get();
        // dd($roles);
        // $permissions=Permission::all();
        // $temp = array();
        // foreach($permissions as $key => $value) {
        //     array_push($temp, $value['name']);
        // }
        // $data= User::with('roles')->where('role_id',$id)->get();
        // dd($data);
        // dd($data);
        // dd($data);
        // $_data['data'] = $data;

        // dd($_data2);
        // return $permission;
        // return view('role.role-edit',compact('roles','data2','permissions','temp'));
        // $roles = Role::with(['permissions'])->where('id',$id)->get();
        // $permissions=Permission::all();
        // dd($roles);

        // $roles=Role::where('id',$id)->get();

        // $userRole = $roles[0]->name;

        // $rolePermission= Role::with('allRolePermissions',$id)->get();
        $user=Auth::getUser();
        $roles=Role::where('id',$id)->get();
        $permissions = Permission::all();
        // dd($permissions);

        return view('role.role-edit')->with([ 'roles' => $roles,
        'permissions' => $permissions,'data2'=>$data2, 'user'=>$user]);
    }

    // public function save(Request $request)
    // {
    //     // $test = $request->illness; // returns an array
    //     // if(count($illness_arr) > 0) {
    //     //     $new_record = new Illness();
    //     //     $new_record->column_name = json_encode($new_record); // pushes as an array into the column of the table
    //     //     $new_record->save(); // saves the record into the table
    //     // }

    //     // $input = $request->all();
    //     // $input['have_access'] = $request->input('have_acces');
    //     // RolePermission::update($input);
    //     // return redirect()->route('posts.index');
    //     dd('test');

    // }


    public function update(Request $request )
    {
        // dd($request->all());
        // $this->validate($request,[
        //     'username' => 'required|min:8|max:20',
        //     'first_name' => 'required|max:20',
        //     'last_name' => 'required|max:20',
        //     'role_id' => 'required|numeric',
        //     'pangkat' => 'required',
        //     // 'password' => 'min:8|required_with:password-confirm|same:password-confirm',
        //     // 'password'=>'required| min:8| max:12 |confirmed',
        //     'email' => 'required|string|email|max:255',
        //     // 'password-confirm'=>'required',

        // ]);

        // $email =   User::select('email')->where('email','<>',''.$request->email.'');
        // // dd($email);
        // User::where('username', $request->username)
        // ->update(['first_name'=>$request->first_name,
        // 'last_name'=>$request->last_name,
        // 'email'=>$request->email,
        // 'role_id'=>$request->role_id,
        // 'pangkat'=>$request->pangkat ]);

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
            // return view('role.role-index');


            $roles = Role::find($request->id);

            // dd($roles);

            $permission    = $request->permissions == null ? [] : $request->permissions;
        //    dd($permission);
            $roles->permissions()->sync($permission);
            return redirect('role')->with('message', 'Update Sukses!');
        }


    // private function _parsing_input_authorities($input)
    // {
    //     $authorities = [];

    //     foreach ($input['authority_access'] as $key => $value) {

    //         $authorities["{$value}"]['have_access'] = isset($input['access'][$key]) ? TRUE : FALSE;

            // if(! isset($input['access'][$key])) {
            //     continue;
            // }

            // $authorities["{$value}"]['authority_read']     = isset($input['read'][$key]) ? TRUE : FALSE;
            // $authorities["{$value}"]['authority_create']   = isset($input['create'][$key]) ? TRUE : FALSE;
            // $authorities["{$value}"]['authority_update']   = isset($input['update'][$key]) ? TRUE : FALSE;
            // $authorities["{$value}"]['authority_delete']   = isset($input['delete'][$key]) ? TRUE : FALSE;
            // $authorities["{$value}"]['authority_import']   = isset($input['import'][$key]) ? TRUE : FALSE;
            // $authorities["{$value}"]['authority_export']   = isset($input['export'][$key]) ? TRUE : FALSE;
            // $authorities["{$value}"]['authority_download'] = isset($input['download'][$key]) ? TRUE : FALSE;
            // $authorities["{$value}"]['authority_upload']   = isset($input['upload'][$key]) ? TRUE : FALSE;
            // $authorities["{$value}"]['authority_data']     = isset($input['data'][$key]) ? $input['data'][$key] : 0;
    //     }

    //     return $authorities;
    // }




    // protected $_module = 'role';
	// protected $_view   = 'role';
	// protected $_title  = 'Role Management';

    // protected $_roleModel;
    // protected $_permissionModel;



    // protected $_opt_authority_data = [];

	// function __construct(Role $_roleModel, Permission $_permissionModel)
	// {
	// 	$this->_roleModel       = $_roleModel;
	// 	$this->_permissionModel = $_permissionModel;

    //     view()->share('_title', $this->_title);
    //     view()->share('_module', $this->_module);
	// }

	// public function index()
	// {
    //     $perPage = 10;
	// 	$_data['data'] = $this->_roleModel->orderBy('id','asc')->paginate($perPage);

	// 	return view($this->_view .'/role-index', $_data);
	// }

	// public function getAdd($id = null)
    // {
    //     $pageTitle = 'Add Role';

    //     $currentAccess = [];

    //     if($id) {

    //         $this->_authorities($id);

    //         $pageTitle = 'Update Role';

    //         $data = $this->_roleModel->find($id);
    //         $_data['data'] = $data;
    //     }

    //     $_data['id'] = $id;
    //     $_data['pageTitle'] = $pageTitle;
    //     $_data['optRoleData'] = [
    //         '0' => '-',
    //         '1' => 'All',
    //         '2' => 'Polda',
    //         '3' => 'Polres'
    //     ];

    //     $_data['authority'] =  $this->_parsingData();

    //     return view($this->_view.'/role-edit', $_data);
    // }

    // public function getEdit($id)
    // {
    //     return $this->getAdd($id);
    // }

    // public function postSave(Request $request, $id = null)
    // {
    //     DB::beginTransaction();
    //     try {

    //         $input   = $request->all();

    //         if($request->has('special_access')) {
    //             $input['special_access'] = json_encode($request->input('special_access', []));
    //         }

    //         $message = null;

    //         $authorities = $this->_parsing_input_authorities($input);

    //         if ($id) {

    //             $role = $this->_roleModel->find($id);
    //             $role->permission()->sync($authorities);
    //             $role->update($input);
	// 							app('App\Http\Controllers\LogsController')->logs_create_controller('role','update', '', '');
    //             $message = 'Authority Has been updated';

    //         } else {

    //             $role = $this->_roleModel->create($input);
    //             $role->permission()->attach($authorities);
	// 							app('App\Http\Controllers\LogsController')->logs_create_controller('role','create', '', '');
    //             $message = 'Authority has been created';
    //         }
    //         (new Authority)->forgetCache();
    //         DB::commit();



    //         return back()->with('success', $message);

    //     } catch (\Exception $e) {
    //         DB::rollback();

    //         if(env('APP_ENV') != 'production')
    //             throw $e;

    //         return back()->with('errorMessage', $e->getMessage());
    //     }
    // }

    // private function _parsing_input_authorities($input)
    // {
    //     $authorities = [];

    //     foreach ($input['authority_access'] as $key => $value) {

    //         $authorities["{$value}"]['have_access'] = isset($input['access'][$key]) ? TRUE : FALSE;

    //         if(! isset($input['access'][$key])) {
    //             continue;
    //         }

    //         // $authorities["{$value}"]['authority_read']     = isset($input['read'][$key]) ? TRUE : FALSE;
    //         // $authorities["{$value}"]['authority_create']   = isset($input['create'][$key]) ? TRUE : FALSE;
    //         // $authorities["{$value}"]['authority_update']   = isset($input['update'][$key]) ? TRUE : FALSE;
    //         // $authorities["{$value}"]['authority_delete']   = isset($input['delete'][$key]) ? TRUE : FALSE;
    //         // $authorities["{$value}"]['authority_import']   = isset($input['import'][$key]) ? TRUE : FALSE;
    //         // $authorities["{$value}"]['authority_export']   = isset($input['export'][$key]) ? TRUE : FALSE;
    //         // $authorities["{$value}"]['authority_download'] = isset($input['download'][$key]) ? TRUE : FALSE;
    //         // $authorities["{$value}"]['authority_upload']   = isset($input['upload'][$key]) ? TRUE : FALSE;
    //         // $authorities["{$value}"]['authority_data']     = isset($input['data'][$key]) ? $input['data'][$key] : 0;
    //     }

    //     return $authorities;
    // }

    // private function _authorities($key = NULL)
    // {
    //     $data = $this->_roleModel->find($key);

    //     foreach ($data->permission as $dt) {
    //         if ($dt->pivot->have_access == TRUE)
    //             $this->_authorities['authority_access'][] = $dt->id;
    //             // dd($dt);

    //         // if ($dt->pivot->authority_read == TRUE)
    //         //     $this->_authorities['authority_read'][] = $dt->id;
    //         // if ($dt->pivot->authority_create == TRUE)
    //         //     $this->_authorities['authority_create'][] = $dt->id;
    //         // if ($dt->pivot->authority_update == TRUE)
    //         //     $this->_authorities['authority_update'][] = $dt->id;
    //         // if ($dt->pivot->authority_delete == TRUE)
    //         //     $this->_authorities['authority_delete'][] = $dt->id;
    //         // if ($dt->pivot->authority_upload == TRUE)
    //         //     $this->_authorities['authority_upload'][] = $dt->id;
    //         // if ($dt->pivot->authority_download == TRUE)
    //         //     $this->_authorities['authority_download'][] = $dt->id;
    //         // if ($dt->pivot->authority_import == TRUE)
    //         //     $this->_authorities['authority_import'][] = $dt->id;
    //         // if ($dt->pivot->authority_export == TRUE)
    //         //     $this->_authorities['authority_export'][] = $dt->id;

    //         $this->_authorities['authority_data'][(string) $dt->id] = $dt->pivot->authority_data;
    //     }
    // }

    // private function _get_role_field($id, $feature)
    // {
    //     switch ($feature):
    //         // case 'R':
    //         //     return  Form::hidden('authority_read[' . $id . ']', $id) .  '<i class="fa fa-eye"></i>' .
    //         //             Form::checkbox('view[' . $id . ']', $id, in_array($id, $this->_authorities['authority_read']) ? TRUE : FALSE, ['class' => 'dd-roles', 'title' => 'Lihat']);
    //         //     break;
    //         // case 'C':
    //         //     return  Form::hidden('authority_create[' . $id . ']', $id)  . '<i class="fa fa-plus"></i>' .
    //         //             Form::checkbox('create[' . $id . ']', $id, in_array($id, $this->_authorities['authority_create']) ? TRUE : FALSE, ['class' => 'dd-roles', 'title' => 'Tambah']);
    //         //     break;
    //         // case 'U':
    //         //     return  Form::hidden('authority_update[' . $id . ']', $id)  . '<i class="fa fa-edit"></i>' .
    //         //             Form::checkbox('update[' . $id . ']', $id, in_array($id, $this->_authorities['authority_update']) ? TRUE : FALSE, ['class' => 'dd-roles', 'title' => 'Ubah']);
    //         //     break;
    //         // case 'D':

    //         //     return  Form::hidden('authority_delete[' . $id . ']', $id) . '<i class="fa fa-trash-o"></i>' .
    //         //             Form::checkbox('delete[' . $id . ']', $id, in_array($id, $this->_authorities['authority_delete']) ? TRUE : FALSE, ['class' => 'dd-roles', 'title' => 'Hapus']);
    //         // case 'I':
    //         //     return  Form::hidden('authority_import[' . $id . ']', $id) . '<i class="fa fa-download"></i>' .
    //         //             Form::checkbox('import[' . $id . ']', $id, in_array($id, $this->_authorities['authority_import']) ? TRUE : FALSE, ['class' => 'dd-roles', 'title' => 'Import']);
    //         //     break;
    //         // case 'E':
    //         //     return  Form::hidden('authority_export[' . $id . ']', $id) . '<i class="fa fa-download"></i>' .
    //         //             Form::checkbox('export[' . $id . ']', $id, in_array($id, $this->_authorities['authority_export']) ? TRUE : FALSE, ['class' => 'dd-roles', 'title' => 'Import']);
    //         //     break;
    //         // case 'DN':
    //         //     return  Form::hidden('authority_download[' . $id . ']', $id) . '<i class="fa fa-upload"></i>' .
    //         //             Form::checkbox('download[' . $id . ']', $id, in_array($id, $this->_authorities['authority_download']) ? TRUE : FALSE, ['class' => 'dd-roles', 'title' => 'Export']);
    //         //     break;
    //         // case 'UP':
    //         //     return  Form::hidden('authority_upload[' . $id . ']', $id) . '<i class="fa fa-upload"></i>' .
    //         //             Form::checkbox('export[' . $id . ']', $id, in_array($id, $this->_authorities['authority_upload']) ? TRUE : FALSE, ['class' => 'dd-roles', 'title' => 'Export']);
    //         //     break;

    //         // case 'DT':
    //         //    return '<label> &nbsp; &nbsp;</label>' . Form::select('data[' . $id . ']', $this->_opt_authority_data, isset($this->_authorities['authority_data'][$id]) ? $this->_authorities['authority_data'][$id] : NULL, ['class' => 'dd-roles', 'id' => 'opt-authority-data-' . $id, 'data-target' => 'dd-roles-' . $id, 'title' => 'Otoritas Data']);
    //         //     break;
    //         case 'ACCESS':
    //             return  Form::hidden('authority_access[' . $id . ']', $id) .  '<i class="fa fa-key"></i>' .
    //                     Form::checkbox('access[' . $id . ']', $id, in_array($id, $this->_authorities['authority_access']) ? TRUE : FALSE, ['class' => 'dd-roles', 'title' => 'Have Access']);
    //             break;
    //     endswitch;
    // }

    // private function _parsingData()
    // {
    //     $query = $this->_permissionModel->get();
    //     // dd($query);

    //     foreach ($query as $dt) {
    //         $dt->feature = json_decode($dt->feature);
    //         $dt->authority = (Object) $this->_authorityByPermissionId($dt->id);
    //     }

    //     return $query;
    // }

    // private function _authorityByPermissionId($id)
    // {
    //     return (Object) [
    //         'access'   => in_array($id, $this->_authorities['authority_access']),
    //         // 'read'     => in_array($id, $this->_authorities['authority_read']),
    //         // 'create'   => in_array($id, $this->_authorities['authority_create']),
    //         // 'update'   => in_array($id, $this->_authorities['authority_update']),
    //         // 'delete'   => in_array($id, $this->_authorities['authority_delete']),
    //         // 'import'   => in_array($id, $this->_authorities['authority_import']),
    //         // 'export'   => in_array($id, $this->_authorities['authority_export']),
    //         // 'download' => in_array($id, $this->_authorities['authority_download']),
    //         // 'upload'   => in_array($id, $this->_authorities['authority_upload']),
    //         // 'data'     => isset($this->_authorities['authority_data'][$id]) ? $this->_authorities['authority_data'][$id] : NULL
    //     ];
    // }


}
