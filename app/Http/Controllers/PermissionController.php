<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Permission;
use DB;

class PermissionController extends Controller
{
    //
    protected $_title  = 'Hak Akses';

    function __construct()
    {
        view()->share('_title', $this->_title);
    }

    public function index()
    {

        $permission = Permission::orderBy('id','asc')->get();
        return view('permission.permission-index',compact('permission'));
    }

    public function add(){
        return view('permission.permission-add');
    }

    public function add_permission(Request $request){

        $permission_name =   Permission::select('permissions')->where('name','=',''.$request->name.'')->first();

        if(!$permission_name){
            Permission::create([
                'name' => $request->name,
                'state'=>1,
                ]);
            // Session::flash('sukses','Ini notifikasi SUKSES');
            // return redirect('register')->withSucces('Sukses');
            return redirect('permission')->with('message', 'Sukses menambahkan permission!');
            }
            return redirect('permission/permission-add')->withErrors(['', 'Nama Permission sudah dipakai'])->withInput();
        }

    public function edit($id)
    {
	$permission = DB::table('permissions')->where('id',$id)->get();
    // $permission = Permission::all();
    return view('permission.permission-edit',compact('permission'));
    }

    public function edit_modal_permission(Request $request)
    {
        $id=$request->id;
        $get_permission= DB::select('select * from permissions where id = \''.$id.'\' ');
        $permission= $get_permission[0];
        $data=[
            'permisiion'=>$permission,
        ];
        return response()->json($data);
    }

    public function edit_permission(Request $request )
    {
        $getpermission = Permission::where('name',$request->name_edit);

        if(empty($permission)){
            Permission::where('id', $request->id_edit)
            ->update(['name'=>$request->name_edit]);
                return redirect('permission')->with('message', 'Sukses Update Permission!');
            }

        return redirect('permission.permission-edit')->with('message', 'Permission sudah ada');
    }


}
