<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Polres;
use App\Models\Polda;
use App\Models\Lib\Police;
use Auth;
// use App\Propinsi as Prop;
use Validator;

class KPolresController extends Controller
{
        /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function grp($route){
        $data=explode("/",$route);
        $name=$data[0];
        $folder='katalog/polres/polres';
        $grp['id']='G05';
        $grp['index']=$folder.'-index';
        $grp['create']=$folder.'-create';
        $grp['edit']=$folder.'-edit';
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
            $data['reference']=Polres::where([['name','ILIKE','%'.$name.'%']])->paginate(10);
        }
        else if( (empty($request->input('filter_name'))) && (!empty($request->input('filter_state'))) ){
            $data['reference']=Polres::where([['state',$status]])->paginate(10);
        }
        else if((!empty($request->input('filter_name'))) && (!empty($request->input('filter_state')))){
            $data['reference']=Polres::where([['name','ILIKE','%'.$name.'%'],['state',$status]])->paginate(10);
        }
        else{
            $data['reference']=Polres::paginate(10);
        }



        $data['name']=$grp['name'];
        $data['nama_filter']=$name;
        $data['status_filter']=$state;
        // dd($data);
        return view($grp['index'], $data);

    }
    public function index(Request $request)
    {

        $grp=$this->grp($request->route()->uri);
        $user = Auth::user();
        switch ($user->role_id) {
            case 2:
                $poldas=Polda::where('id','=',$user->polda_id)->get();
                $polress=Polres::where('polda_id','=',$user->polda_id)->get();
                $polda = $user->polda_id;
                $polres = '-';
                break;
            case 3:
                $poldas=Polda::where('id','=',$user->polda_id)->get();
                $polress=Polres::where('id','=',$user->polres_id)->get();
                    $polda = $user->polda_id;
                    $polres = $user->polres_id;
                break;
            case 4:
                $poldas=Polda::where('id','=',$user->polda_id)->get();
                $polress=Polres::where('id','=',$user->polres_id)->get();
                    $polda = $user->polda_id;
                    $polres = $user->polres_id;
                break;
            default:
            $poldas=Polda::all();
            $polress=Polres::all();
        }

        $data['polres']=Polres::orderBy("sort","asc")->paginate(10);
        $data['name']=$grp['name'];
        // dd($data);
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
        $data['polda']=Polda::all()->where('state','<>',0);
        // $data['propinsi']=Prop::all();
        $find_data = Polres::whereNotNull('sort')->orderBy("sort","desc")->first();
        $data['last_sort'] = $find_data->sort + 1;
        $data['last_id'] = $find_data->id + 1;
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
        $rules =['id'=>'required|unique:polres,id|min:2','nama_lengkap_id'=>'required','polda'=>'required'];
        $attribute=['id' => 'ID','nama_lengkap_id'=> 'Nama lengkap','polda'=>'Polda','propinsi'=>'Geografis provinsi'];
        $customMessages = ['*.required' => ':attribute harus diisi.','*.unique'=> ':attribute sudah ada.'];
        $this->validate($request, $rules, $customMessages,$attribute);
        $grp=$this->grp($request->route()->uri);

        DB::beginTransaction();
        try{
            $ref=new Polres;
            $ref->id=$request->input('id');
            $ref->polda_id=$request->input('polda');
            $ref->address = $request->input('alamat_polres');
            // $ref->propinsi_id=$request->input('propinsi');
            $ref->name=$request->input('nama_lengkap_id');
            $ref->state=(!empty($request->input('arsip')))?0:1;
            $ref->sort=$request->input("sort");
            $ref->save();

            // Insert To polices
            $police = DB::table('lib.polices')->where('id', $request->input('id'))->first();
            if(!empty($police)){
                // restore from delete
                DB::table('lib.polices')->where('id', $request->input('id'))->update(['deleted_at' => null]);
            }

            // Insert To polices
            Police::updateOrCreate(
                [
                    'id' => $request->input('id'),
                ],
                [
                    'id' => $request->input('id'),
                    'parent_id' => $request->input('polda'),
                    'class' => Police::getEnumOption('class', 'RES'),
                    'address' => $request->input('alamat_polres'),
                    'name' => $request->input('nama_lengkap_id'),
                    'is_active' => (!empty($request->input('arsip')))? false : true,
                    'sort' => intval($request->input("sort")),
                ]
            );

            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
            return $e;
        }

        /* CREATE LOG - START */
        // app('App\Http\Controllers\LogsController')->logs_create_controller('polres','insert', $ref->id, '');
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
        $data['polres']=Polres::find($id);
        $data['polda']=Polda::all()->where('state','<>',0)->sortBy('id');
        // $data['propinsi']=Prop::all();
        return view($grp['edit'], $data);
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
        $rules =['nama_lengkap_id'=>'required','polda'=>'required'];
        $attribute=['nama_lengkap_id'=> 'Nama lengkap','polda'=>'Polda','propinsi'=>'Geografis provinsi'];
        $customMessages = ['*.required' => ':attribute harus diisi.','*.unique'=> ':attribute sudah ada.'];
        $this->validate($request, $rules, $customMessages,$attribute);
        $grp=$this->grp($request->route()->uri);

        DB::beginTransaction();
        try{
            $ref=Polres::find($id);
            $ref->polda_id=$request->input('polda');
            $ref->address = $request->input('alamat_polres');
            $ref->id=$request->input('id');
            $ref->name=$request->input('nama_lengkap_id');
            $ref->state=(!empty($request->input('arsip')))?0:1;
            $ref->sort=$request->input("sort");
            $ref->save();

            // Update To Polices
            Police::where('id', $id)->update([
                'id' => $request->input('id'),
                'class' => Police::getEnumOption('class', 'RES'),
                'parent_id' => $request->input('polda'),
                'address' => $request->input('alamat_polres'),
                'name' => $request->input('nama_lengkap_id'),
                'is_active' => (!empty($request->input('arsip')))? false : true,
                'sort' => intval($request->input("sort")),
            ]);

            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
            return $e;
        }

        // echo (!empty($request->input('arsip')))?0:1;
        /* CREATE LOG - START */
        // app('App\Http\Controllers\LogsController')->logs_create_controller('polres','update', $ref->id, '');
        /* CREATE LOG - END */
        return redirect($grp['name']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function destroy(Request $request, $id)
    // {
    //     //
    //     $grp=$this->grp($request->route()->uri);
    //     $ref=Polres::where('id','=',$id)->first();
    //     $ref->state = 0;
    //     $ref->save();
    //     /* CREATE LOG - START */
    //     app('App\Http\Controllers\LogsController')->logs_create_controller('polres','delete', $id, '');
    //     /* CREATE LOG - END */
    // }

    public function destroy(Request $request, $id)
    {
        //
        $grp=$this->grp($request->route()->uri);

        DB::beginTransaction();
        try{
            $ref=Polres::where('id','=',$id)->first();
            $ref->state = 0;
            $ref->delete();

            // Update Is Active To Polices
            Police::where('id', $id)->update([
                'is_active' => false,
            ]);
            // Delete From Polices
            Police::where('id', $id)->delete();

            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
            return $e;
        }

        return redirect($grp['name'])->with('message', 'Sukses Hapus Data'.' '.$ref['name']);
        /* CREATE LOG - START */
        // app('App\Http\Controllers\LogsController')->logs_create_controller('ref','delete', $id, '');
        /* CREATE LOG - END */
    }

    public function logout()
    {
        UserLogin::setLogout();
        Sentinel::logout();

        return redirect('login');
    }
    public function search(Request $request){
        $find_dataId = Polres::where('polda_id',$request->input('grp_id'))->orderBy('id','desc')->first();
        if($find_dataId==null){
            $numb=$request->input('grp_id');
        }else{
            $idLast1 = substr(($find_dataId->id), 0, -2);
            $idLast2 = substr(($find_dataId->id), (int)strlen($find_dataId->id) -2);
            $numb=(int)$idLast2 +1;
            $numb= ($numb<10)?$idLast1 . "0" . (string)$numb:$idLast1 . (string)$numb;
        }
             return response()->json(['data' => $numb]);
    }
}
