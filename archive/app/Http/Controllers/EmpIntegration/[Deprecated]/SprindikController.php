<?php

namespace App\Http\Controllers\EmpIntegration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Letters\InvestigationOrderLetter\InvestigationOrderLetter;
use App\Models\Ref;

class SprindikController extends Controller
{
    public function index(Request $request){

        return response()->json([
            'code' => 200,
            'status' => "OK",
            'data' => []
        ], 200);
}

    // public function index(Request $request)
    // {
    //     $investigationOrderLetters = InvestigationOrderLetter::with(['officers', 'signatoryOfficer', 'officers.rank', 'signatoryOfficer.rank'])
    //         ->where('is_integrated', false)
    //         ->get();
       
    //     // Parse data
    //     $outputData = $investigationOrderLetters->map(function($letter) {
    //         return [
    //             'Id' => $letter->id,
    //             'NoSurat' => $letter->letter_number,
    //             'TanggalSprindik' => $letter->issued_date,
    //             'LokasiDibuat' => $letter->location_created,
    //             'TanggalMulai' => $letter->start_date,
    //             'TanggalBerakhir' => $letter->end_date,
    //             'Attachment' => $letter->attachment,
    //             'CreatedDate' => $letter->created_at,
    //             'CreatedBy' => $letter->created_by,
    //             'UpdatedDate' => $letter->updated_at,
    //             'UpdatedBy' => $letter->updated_by,
    //             'PejabatPenandatanganDokumen' => $letter->signatoryOfficer->map(function($officer) {
    //                 return [
    //                     'Nama'=> $officer->first_name . ' ' . $officer->last_name,
    //                     'NRP' => "$officer->id",
    //                     'Pangkat' => $officer->rank->name,
    //                     'Jabatan' => $officer->sebagai_kepala,
    //                 ];
    //             }), 
    //             'Personel_Sprindik' => $letter->officers->map(function($officer) {
    //                 return [
    //                     'Nama'=> $officer->first_name . ' ' . $officer->last_name,
    //                     'NRP' => "$officer->id",
    //                     'Pangkat' => $officer->rank->name,
    //                     'Jabatan' => $officer->position_short_name,
    //                 ];
    //             }),
    //         ];
    //     });

    //     // Update is_integrated to true and integrated_at to current date
    //     $currentTimestamp = date('Y-m-d H:i:s');
    //     $investigationOrderLetters->map(function($letter) use ($currentTimestamp) {
    //         // $letter->is_integrated = true;
    //         $letter->integrated_at = $currentTimestamp;
    //         $letter->save();
    //     });

    //     return response()->json([
    //         'code' => 200,
    //         'status' => 'success',
    //         'data' => $outputData
    //     ], 200);
    // }
}
