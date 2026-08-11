<?php

namespace App\Http\Controllers;

use App\Models\Suspect;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use DataTables;

class DaftarTersangkaController extends Controller
{
    public function store_tersangka(Request $request){
        // dd($request->input('identification'));
        if ($request->input('identification') == 'identification_1') {
            $tersangka=Suspect::updateOrCreate(['id'=>$request->tersangka_id],
            [
                'accident_id' => $request->accident_id,
                'identity_type' => 15,
                'identity_number' =>  '0',
                'name' =>  $request->name,
                'gender' =>  null,
                'birth_place' =>  'TIDAK DIKETAHUI',
                'birth_date' => null,
                'mother_name' => 'TIDAK DIKETAHUI',
                'father_name' => 'TIDAK DIKETAHUI',
                'ethnicity' => 'TIDAK DIKETAHUI',
                'occupation' => 1,
                'religion' => 1,
                'education' => 1,
                'country' => 'TIDAK DIKATAHUI',
                'marital_status' => null,
                'phone_number' => null,
                'email_address' => null,
                'province' => 'TIDAK DIKATAHUI',
                'city' => 'TIDAK DIKATAHUI',
                'district' => 'TIDAK DIKATAHUI',
                'sub_district' => 'TIDAK DIKATAHUI',
                'address' => 'TIDAK DIKATAHUI'
            ]);
        } else {
            $this->validate($request,[
                'identity_type' => 'required',
                'identity_number' => 'required',
                'name' => 'required',
                'gender' => 'required',
                'birth_place' => 'required',
                'birth_date' => 'required',
                'mother_name' => 'required',
                'father_name' => 'required',
                'ethnicity' => 'required',
                'occupation' => 'required',
                'religion' => 'required',
                'education' => 'required',
                'country' => 'required',
                'marital_status' => 'required',
                // 'phone_number' => 'required|numeric',
                // 'email_address' => 'required|email',
                'province' => 'required',
                'regency' => 'required',
                'village' => 'required',
                'address' => 'required',
            ]);
            $tersangka=Suspect::updateOrCreate(['id'=>$request->tersangka_id],
            [
                'accident_id' => $request->accident_id,
                'identity_type' =>  $request->identity_type,
                'identity_number' =>  $request->identity_number,
                'name' =>  $request->name,
                'gender' =>  $request->gender,
                'birth_place' =>  $request->birth_place,
                'birth_date' => Carbon::createFromFormat('d-m-Y',$request->birth_date)->format('Y-m-d'),
                'mother_name' => $request->mother_name,
                'father_name' => $request->father_name,
                'ethnicity' => $request->ethnicity,
                'occupation' => $request->occupation,
                'religion' => $request->religion,
                'education' => $request->education,
                'country' => $request->country,
                'marital_status' => $request->marital_status,
                'phone_number' => $request->phone_number,
                'email_address' => $request->email_address,
                'province' => $request->province,
                'city' => $request->regency,
                'district' => $request->district,
                'sub_district' => $request->village,
                'address' => $request->address
            ]);
        }
// dd($tersangka);

        // return response()->json(['success'=>'Added new records.']);
        return response()->json($tersangka);
    }

    public function read_tersangka(Request $request)
    {
         $accident = $request->accident_id;
            if ($request->ajax()) {
            $get_data = DB::select('select
                                    id as id,
                                    identity_number as identity_number,
                                    name as name,
                                    birth_place as birth_place,
                                    to_char(birth_date, \'DD-MM-YYYY\') as birth_date,
                                    (select identity_types.name from identity_types where identity_types.id=suspects.identity_type) as identity_type
                                    from suspects
                                    where accident_id = \''.$accident.'\' order by suspects.created_at');
            $data['tersangka']=$get_data;
            // $data = DaftarSaksi::where('accident_id',''.$accident.'')->get();
            return Datatables::of($data['tersangka'])
                    ->addIndexColumn()
                    ->addColumn('action', function($row){

                        //    $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Edit" class="edit btn btn-primary btn-sm editTersangka">Edit</a>';

                           $btn = ' <a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Delete" class="btn btn-danger btn-sm deleteTersangka">Delete</a>';

                            return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
    }

    public function destroy_tersangka(Request $request)
    {
        $tersangka = Suspect::find($request->id);
        $tersangka->delete();
        return response()->json('Sukses Menghapus Tersangka');
    }

}
