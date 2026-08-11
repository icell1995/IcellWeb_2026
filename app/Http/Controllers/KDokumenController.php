<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RefGroup;
use App\Models\Ref;
use Auth;

class KDokumenController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function grp($route){
        $data=explode("/",$route);
        // dd($data);
        $name=$data[0];
        // dd($name);
        $folder_KDL='katalog/dokumen/';
        switch($name){
            case "saksi":
            $grp['id']="D02";
            $folder=$folder_KDL.'saksi/saksi';
            break;
            case "tersangka":
            $grp['id']="D03";
            $folder=$folder_KDL.'tersangka/tersangka';
            break;
            case "penahanan":
            $grp['id']="D04";
            $folder=$folder_KDL.'penahanan/penahanan';
            break;
            case "penggeledahan":
            $grp['id']="D05";
            $folder=$folder_KDL.'penggeledahan/penggeledahan';
            break;
            case "penyitaan":
            $grp['id']="D06";
            $folder=$folder_KDL.'penyitaan/penyitaan';
            break;
            case "penyegelan":
            $grp['id']="D07";
            $folder=$folder_KDL.'penyegelan/penyegelan';
            break;
            case "labfor":
            $folder=$folder_KDL.'labfor/labfor';
            $grp['id']="D08";
            break;
            case "rekening-bank":
            $folder=$folder_KDL.'rekening-bank/rekening-bank';
            $grp['id']="D09";
            break;
            case "dpo-dpb":
            $folder=$folder_KDL.'dpo-dpb/dpo-dpb';
            $grp['id']="D10";
            break;
        }
        $grp['index']=$folder. '-index';
        $grp['create']=$folder.'-create';
        $grp['edit']=$folder.'-edit';
        $grp['grp_name']=RefGroup::find($grp['id'])->name;
        $grp['name']=$name;
        return $grp;
    }
    
    public function index(Request $request)
    {
        //
        $user = Auth::user();
        $grp=$this->grp($request->route()->uri);
        $data['reference']=Ref::where([["grp_id",$grp['id']]])->orderBy('sort', 'asc')->paginate(10);
        $data['name']=$grp['name'];
        $data['group']=$grp['grp_name'];
        $data['user']=Auth::user();
        // dd($data['user']);
        // dd($data);
        return view($grp['index'], $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $grp=$this->grp($request->route()->uri);
        // dd($grp);
        $data['name']=$grp['name'];
        $data['grp_id']=$grp['id'];
        $data['group']=$grp['grp_name'];
        $find_data = Ref::where('grp_id',$grp['id'])->orderBy('sort','desc')->first();
        // dd($find_data);
        $find_dataId = Ref::where('grp_id',$grp['id'])->orderBy('id','desc')->first();
        // dd($find_dataId);
        $data['last_sort'] = $find_data->sort + 1;
        // dd( $data['last_sort']);
        $idLast1 = substr(($find_dataId->id), 0,-2);
        // dd($idLast1);
        $idLast2 = substr(($find_dataId->id), (int)strlen($find_dataId->id) -2);
        // dd($idLast2);
        $numb=(int)$idLast2 +1;
        // dd($numb);
        $numb= ($numb<10)?$idLast1 . "0" . (string)$numb:$idLast1 . (string)$numb;
        // dd($numb);
        $data['last_id'] = $numb;

        return view($grp['create'], $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules          =['id'=>'required|unique:ref,id','name'=>'required',];
        $attribute      =['id'=> 'ID','name'=> 'Nama lengkap'];
        $customMessages = ['*.required' => ':attribute harus diisi.','*.unique'=> ':attribute sudah ada.'];
        $this->validate($request, $rules, $customMessages,$attribute);

        $grp=$this->grp($request->route()->uri);

        $find_data = Ref::where('grp_id',$grp['id'])->where('sort',$request->input('sort'))->first();
        if($find_data != null){
            $last_sort = Ref::where('grp_id',$grp['id'])->orderBy('sort','desc')->first();
            $last_sort = $last_sort->sort + 1;
            $update_sort_before = Ref::where('grp_id',$grp['id'])->where('sort',$request->input('sort'))->update([
                'sort'=> $last_sort
            ]);
        }

        $ref=new Ref;
        $ref->id=$request->input('id');
        $ref->name=$request->input('name');
        $ref->state=(!empty($request->input('aktif')))?0:1;
        $ref->grp_id=$grp['id'];
        $ref->sort=$request->input('sort');
        $ref->save();
        // $katalog_version = DB::table('tm_katalog_version')->first();
        // if($katalog_version){
        //   $old_version = $katalog_version->version + 1;
        //   DB::table('tm_katalog_version')->update([
        //       'version'=>$old_version
        //   ]);
        // }
        // /* CREATE LOG - START */
        // app('App\Http\Controllers\LogsController')->logs_create_controller('ref','insert', $ref->id, '');
        // /* CREATE LOG - END */
        return redirect($grp['name'])->with('message', 'Sukses Tambah Data');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $grp=$this->grp($request->route()->uri);
        // dd($grp);
        $data['name']=$grp['name'];
        // dd($data['name']);
        $data['grp_id']=$grp['id'];
        $data['reference']=Ref::where([['grp_id',$grp['id']],['id',$id]])->first();
        // dd($data['reference']);
        $data['group']=$grp['grp_name'];
        if($data['reference']==null){
            return redirect($grp['name'])->withErrors('Data Tidak ditemukan');
        }
        else{
            return view($grp['edit'], $data);
        }
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $rules =['name'=>'required'];
        $attribute=['name'=> 'Nama lengkap'];
        $customMessages = ['*.required' => ':attribute harus diisi.'];
        $this->validate($request, $rules, $customMessages,$attribute);
        $grp=$this->grp($request->route()->uri);
        $find_data = Ref::where('grp_id',$grp['id'])->where('sort',$request->input('sort'))->first();
        if($find_data != null){
            $get_sort_before = Ref::where('id',$id)->first();
            $get_sort_before = $get_sort_before->sort;
            Ref::where('grp_id',$grp['id'])->where('sort',$request->input('sort'))->update([
                'sort'=>$get_sort_before
            ]);
        }
        $ref=Ref::find($id);
        $ref->name=$request->input('name');
        $ref->state=(!empty($request->input('aktif')))?0:1;
        $ref->grp_id=$grp['id'];
        $ref->sort=$request->input('sort');
        $ref->save();
        // $katalog_version = DB::table('tm_katalog_version')->first();
        // if($katalog_version){
        //   $old_version = $katalog_version->version + 1;
        //   DB::table('tm_katalog_version')->update([
        //       'version'=>$old_version
        //   ]);
        // }
        // app('App\Http\Controllers\LogsController')->logs_create_controller('ref','update', $ref->id, '');

        return redirect($grp['name'])->with('message','Sukses update titik acuan');
        /* CREATE LOG - START */
        /* CREATE LOG - END */
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
