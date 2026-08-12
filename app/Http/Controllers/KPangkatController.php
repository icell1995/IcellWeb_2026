<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RefGroup;
use App\Models\Ref;
use Auth;


class KPangkatController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function grp($route){
        $data=explode("/",$route);
        $name=$data[0];
        $folder='katalog/pangkat/pangkat';
        $grp['id']="RANK";
        $grp['index']=$folder.'-index';
        $grp['create']=$folder.'-create';
        $grp['edit']=$folder.'-edit';
        $grp['grp_name']=RefGroup::where([['id',$grp['id']]])->first()->name;
        $grp['name']=$name;
        return $grp;
    }
    public function filter(Request $request){
        $name=(!empty($request->input('filter_name')))?$request->input('filter_name'):null;
        $state=(!empty($request->input('filter_state')))?$request->input('filter_state'):null;
        switch($state){
            case 1:
            $status=1;
            break;
            case 2:
            $status=0;
            break;
        }
        $grp=$this->grp($request->route()->uri);
        if( (!empty($request->input('filter_name'))) && (empty($request->input('filter_state'))) ){
            $data['reference']=Ref::where([["grp_id",$grp['id']],['name','ILIKE','%'.$name.'%']])->paginate(10);
        }
        else if( (empty($request->input('filter_name'))) && (!empty($request->input('filter_state'))) ){
            $data['reference']=Ref::where([["grp_id",$grp['id']],['state',$status]])->paginate(10);
        }
        else if((!empty($request->input('filter_name'))) && (!empty($request->input('filter_state')))){
            $data['reference']=Ref::where([["grp_id",$grp['id']],['name','ILIKE','%'.$name.'%'],['state',$state]])->paginate(10);
        }
        else{
            $data['reference']=Ref::where([["grp_id",$grp['id']]])->paginate(10);
        }


        
        $data['name']=$grp['name'];
        $data['group']=$grp['grp_name'];
        $data['nama_filter']=$name;
        $data['status_filter']=$state;
        // dd($data);
        return view($grp['index'], $data);

    }
    public function index(Request $request)
    {
       
        
        $grp=$this->grp($request->route()->uri);
        $data['reference']=Ref::where([['grp_id',$grp['id']]])->orderBy('sort','asc')->paginate(10);
        $data['name']=$grp['name'];
        $data['group']=$grp['grp_name'];
        // dd($data['reference']);
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
        $data['name']=$grp['name'];
        $data['grp_id']=$grp['id'];
        $data['group']=$grp['grp_name'];
        $find_data = Ref::where('grp_id',$grp['id'])->orderBy('sort','desc')->first();
        if($find_data == null){
            $data['last_sort'] = 1;
        }else{
            $data['last_sort'] = $find_data->sort + 1;
        }
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
        $rules =['id'=>'required|unique:ref,id','name'=>'required',];
        $attribute=['id' => 'ID','name'=> 'Nama lengkap'];
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
        /* CREATE LOG - START */
        // app('App\Http\Controllers\LogsController')->logs_create_controller('ref','insert', $ref->id, '');
        /* CREATE LOG - END */
        return redirect($grp['name']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $grp=$this->grp($request->route()->uri);
        return redirect($grp['name'].'/'.$id.'/edit');
    }

    public function edit(Request $request, $id)
    {
        $grp=$this->grp($request->route()->uri);
        $data['name']=$grp['name'];
        $data['grp_id']=$grp['id'];
        $data['reference']=Ref::where([['grp_id',$grp['id']],['id',$id]])->first();
        $data['group']=$grp['grp_name'];
        if($data['reference']==null){
            return redirect($grp['name']);
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
        /* CREATE LOG - START */
        // app('App\Http\Controllers\LogsController')->logs_create_controller('ref','update', $ref->id, '');
        /* CREATE LOG - END */
        return redirect($grp['name']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        //
        $grp=$this->grp($request->route()->uri);
        $ref=Ref::where([['grp_id',$grp['id']],['id',$id]])->first();
        $ref->state = 0;
        $ref->delete();
        return redirect($grp['name'])->with('message', 'Sukses Hapus Data'.' '.$grp['name']);
        /* CREATE LOG - START */
        // app('App\Http\Controllers\LogsController')->logs_create_controller('ref','delete', $id, '');
        /* CREATE LOG - END */
    }

}
