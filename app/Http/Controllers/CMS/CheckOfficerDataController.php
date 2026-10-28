<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Yajra\DataTables\Facades\DataTables;

use App\Models\History\CheckOfficerDataHistory;

class CheckOfficerDataController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $query = CheckOfficerDataHistory::with(['createdByUser'])
                ->orderBy('created_at', 'desc');
   
            return DataTables::of($query)->make(true);
        }

        $viewData = [
        ];

        return view('cms.check-officer-data.index', $viewData);
    }  

    public function getOfficerData(Request $request){
        $registerNumber = htmlspecialchars($request->registerNumber);

        $response = Http::withHeaders(
            [
                'Authorization' => env('ESIGNATURE_API_TOKEN'),
            ]
        )->get(env('ESIGNATURE_API_HOST') . '/api/values/CekDataPersonelICELL', [
            'nrp' => $registerNumber,
        ]);

        $result = json_decode($response->getBody(), true);
        
        if(!empty($result['Data'])){
            CheckOfficerDataHistory::create([
                'register_number' => $result['Data']['nrp'],
                'name' => $result['Data']['nama'],
                'rank_name' => $result['Data']['pangkat'],
                'position_name' => $result['Data']['jabatan'],
                'unit_name' => $result['Data']['satuan'],
                'phone_number' => $result['Data']['handphone'],
                'gender_name' => $result['Data']['jenis_kelamin'],
                'work_email' => $result['Data']['email_dinas'],
                'investigator_certificate' => $result['Data']['sertifikasi_penyidikan'],
                'investigator_number' => $result['Data']['nomor_penyidik'],
                'work_units' => [
                    'unit1' => $result['Data']['satuan1'],
                    'unit2' => $result['Data']['satuan2'],
                    'unit3' => $result['Data']['satuan3'],
                    'unit4' => $result['Data']['satuan4'],
                ],
                'created_by_user_id' => auth()->user()->id,
            ]);

        }
        
        return response()->json($result);
    }
}
