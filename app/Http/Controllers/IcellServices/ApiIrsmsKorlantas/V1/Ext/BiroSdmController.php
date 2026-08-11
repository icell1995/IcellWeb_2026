<?php

namespace App\Http\Controllers\IcellServices\ApiIrsmsKorlantas\V1\Ext;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

use App\Models\History\CheckOfficerDataHistory;

class BiroSdmController extends Controller
{
    public function getOfficer(Request $request)
    {
        $registerNumber = htmlspecialchars($request->register_number);

        if($registerNumber == null) {
            return $this->errorResponse('BAD_REQUEST', 'Register number is required', 400);
        }

        DB::beginTransaction();
        try{
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
                    'created_by_name' => "IRSMS",
                ]);
            }

            DB::commit();
        
            return $this->successResponse(
                message: 'Success',
                page: 1,
                totalData: 1,
                totalPage: 1,
                totalDataSent: 1,
                data: $result,
            );
        } catch(\Exception $e){
            DB::rollBack();
            $exceptionMessage = "an error has occurred";
            $status = 'Error';
            $code = 500;

            return $this->errorResponse(
                status: $status, 
                message: $exceptionMessage,
                code: $code
            );
        }
    }

   /**
     * Generate a success response.
     *
     * @param  mixed  $data
     * @param  string  $message
     * @param  int  $page
     * @param  int  $totalData
     * @param  int  $totalPage
     * @param  int  $totalDataSent
     * @return \Illuminate\Http\Response
     */
    private function successResponse($data, $message, $page, $totalData, $totalPage, $totalDataSent)
    {
        return response()->json([
            'code' => 200,
            'status' => 'OK',
            'message' => $message,
            'pagination' => [
                'Page' => $page,
                'TotalData' => $totalData,
                'TotalPage' => $totalPage,
                'TotalDataSent' => $totalDataSent,
            ],
            'data' => $data,
        ]);
    }

    /**
     * Generate an error response.
     *
     * @param  string  $status
     * @param  string  $message
     * @param  int  $code
     * @return \Illuminate\Http\Response
     */
    private function errorResponse($status, $message, $code)
    {
        return response()->json([
            'code' => $code,
            'status' => $status,
            'message' => $message,
            'pagination' => [
                'Page' => 1,
                'TotalData' => 0,
                'TotalPage' => 1,
                'TotalDataSent' => 0,
            ],
            'data' => [],
        ]);
    }
}
