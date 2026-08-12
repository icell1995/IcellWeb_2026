<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Polda;
use App\Models\Lib\Police;
use Illuminate\Support\Facades\DB;
use Validator;

class KPoldaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function grp($route){
        $data=explode("/",$route);
        $name=$data[0];
        $folder='katalog/polda/polda';
        $grp['index']=$folder.'-index';
        $grp['create']=$folder.'-create';
        $grp['edit']=$folder.'-edit';
        $grp['name']=$name;
        return $grp;
    }

    public function index(Request $request)
    {

        $grp=$this->grp($request->route()->uri);
        $data['polda']=Polda::orderBy("sort","asc")->paginate(10);
        $data['name']=$grp['name'];
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
        $find_data = Polda::whereNotNull('sort')->orderBy("sort","desc")->first();
        $data['last_sort'] = $find_data->sort + 1;
        $find_dataId = Polda::whereNotNull('id')->whereRaw('id::integer < 99')->orderBy("id","desc")->first();
        $data['newId'] = $find_dataId->id + 1;
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
        // $rules =[];
        $attribute=['id' => 'ID','nama_polda'=> 'Nama lengkap'];
        $customMessages = ['*.required' => ':attribute harus diisi.','*.unique'=> ':attribute sudah ada.','*.max' =>'Maximal 3 karakter'];
        $this->validate($request, $customMessages,$attribute);
        $grp=$this->grp($request->route()->uri);
        $validator = Validator::make($request->all(), [
            'id'=>'required','nama_polda'=>'required','timezone'=>'required|max:3',
        ]);
        if ($validator->fails()) {
            return redirect($grp['name'].'/create')
                        ->withErrors($validator)
                        ->withInput();
        }
        $polda=Polda::get()->last();

        DB::beginTransaction();
        try{
            $ref=new Polda;
            $ref->id=$request->input('id');
            $ref->name=$request->input('nama_polda');
            $ref->timezone=$request->input('timezone');
            $ref->state=(!empty($request->input('arsip')))?0:1;
            $ref->address=$request->input('alamat_polda');
            $ref->sort=$request->input('sort');
            $ref->save();
    
            // Insert To polices
            $police = DB::table('lib.polices')->where('id', $request->input('id'))->first();
            if(!empty($police)){
                // restore from delete
                DB::table('lib.polices')->where('id', $request->input('id'))->update(['deleted_at' => null]);
            }

            Police::updateOrCreate(
                [
                    'id' => $request->input('id'),
                ],
                [
                    'id' => $request->input('id'),
                    'parent_id' => '001',
                    'class' => Police::getEnumOption('class', 'DRH'),
                    'address' => $request->input('alamat_polda'),
                    'timezone' => $request->input('timezone'),
                    'name' => $request->input('nama_polda'),
                    'is_active' => (!empty($request->input('arsip')))? false : true,
                    'sort' => intval($request->input("sort")),
                ]
            );

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $e;
        }

        /* CREATE LOG - START */
        // app('App\Http\Controllers\LogsController')->logs_create_controller('polda','insert', $ref->id, '');
        /* CREATE LOG - END */
        return redirect($grp['name'])->with('message','Sukses Tambah Polda'.' '.$ref->name);;
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
        $data['name']=$grp['name'];
        $data['polda']=Polda::find($id);
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
        $rules =['nama_polda'=>'required','timezone'=>'required|max:3'];
        $attribute=['nama_polda'=> 'Nama lengkap','timezone'=>'Zona waktu'];
        $customMessages = ['*.required' => ':attribute harus diisi.','*.max' =>'Maximal 3 karakter'];
        $this->validate($request, $rules, $customMessages,$attribute);
        $grp=$this->grp($request->route()->uri);

        DB::beginTransaction();
        try{
            $ref=Polda::find($id);
            $ref->name=$request->input('nama_polda');
            $ref->state=(!empty($request->input('arsip')))?0:1;
            $ref->timezone=$request->input('timezone');
            $ref->address=$request->input('alamat_polda');
            $ref->sort=$request->input('sort');
            $ref->save();

            // Update To polices
            Police::where('id', $id)->update([
                'id' => $request->input('id'),
                'parent_id' => '001',
                'class' => Police::getEnumOption('class', 'DRH'),
                'address' => $request->input('alamat_polda'),
                'timezone' => $request->input('timezone'),
                'name' => $request->input('nama_polda'),
                'is_active' => (!empty($request->input('arsip')))? false : true,
                'sort' => intval($request->input("sort")),
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $e;
        }

        /* CREATE LOG - START */
        // app('App\Http\Controllers\LogsController')->logs_create_controller('polda','update', $ref->id, '');
        /* CREATE LOG - END */
        return redirect($grp['name'])->with('message','Sukses Update Polda'.' '.$ref->name);
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

        DB::beginTransaction();
        try{
            $ref=Polda::where('id','=',$id)->first();
            $ref->state = 0;
            $ref->delete();

            // Update To polices
            Police::where('id', $id)->update([
                'is_active' => false,
            ]);
            // Delete To polices
            Police::where('id', $id)->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $e;
        }

        return redirect($grp['name'])->with('message', 'Sukses Hapus Data'.' '.$ref['name']);
    }
}
