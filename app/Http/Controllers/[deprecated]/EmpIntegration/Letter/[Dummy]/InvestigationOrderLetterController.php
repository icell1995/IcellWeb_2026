<?php

namespace App\Http\Controllers\EmpIntegration\Letter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Faker\Factory as Faker;

// SURAT PERINTAH PENYIDIKAN (SPRINDIK)
class InvestigationOrderLetterController extends Controller
{
    public function index(Request $request){
        // Get request data
        $startDocumentDate = $request->input('start_doc_date');
        $endDocumentDate = $request->input('end_doc_date');

        // Initialize variable
        $responseData = [];

        // Validate request data
        if (!empty($startDocumentDate) && date('Y-m-d', strtotime($startDocumentDate)) !== $startDocumentDate) {
            return response()->json([
                "code" => "400",
                "status" => "BAD_REQUEST",
                "message" => "Invalid Start document date or format. Date format should be YYYY-MM-DD.",
                "data" => []
            ], 400);
        }

        if (!empty($endDocumentDate) && date('Y-m-d', strtotime($endDocumentDate)) !== $endDocumentDate) {
            return response()->json([
                "code" => "400",
                "status" => "BAD_REQUEST",
                "message" => "Invalid End document date or format. Date format should be YYYY-MM-DD.",
                "data" => []
            ], 400);
        }

        try {
            // Get data from database
            $getData = $this->collectionData();

            // Filter data
            if (!empty($startDocumentDate) && !empty($endDocumentDate)) {
                $getData = $getData->whereBetween('TanggalSprindik', [date('Y-m-d H:i:s', strtotime($startDocumentDate)), date('Y-m-d H:i:s', strtotime($endDocumentDate))]);
            } else if (!empty($startDocumentDate)) {
                $getData = $getData->where('TanggalSprindik', '>=', date('Y-m-d H:i:s', strtotime($startDocumentDate)));
            } else if (!empty($endDocumentDate)) {
                $getData = $getData->where('TanggalSprindik', '<=', date('Y-m-d H:i:s', strtotime($endDocumentDate)));
            }

            $requestData = $getData->values();

            // Finalize data
            $responseData = $requestData;

            // Check if data array is empty result
            if ($responseData->isEmpty()) {
                return response()->json([
                    "code" => "404",
                    "status" => "NOT_FOUND",
                    "message" => "Data not found.",
                    "data" => []
                ], 404);
            }

            // Return Result JSON
            return response()->json([
                "code" => "200",
                "status" => "OK",
                "message" => "Success",
                "data" => $responseData
            ], 200);

        } catch (\Exception $e) {
            // If an exception occurs, return an error response
            return response()->json([
                "code" => "500",
                "status" => "INTERNAL_SERVER_ERROR",
                "message" => "An error occurred while processing your request.",
                "data" => []
            ], 500);
        }
    }

    private function collectionData(){
        $start_date = Carbon::create(2022, 1, 1);
        $end_date = Carbon::create(2022, 12, 31);
        $data = [];

        $no = 1;
        for ($date = $start_date; $date <= $end_date; $date->modify('+1 day')) {
            $data[] = [
                "Id" => Str::uuid(),
                "NoSurat" => "SURAT/SPRINDIK/" . $no . "/2022",
                "TanggalSprindik" => $date->format('Y-m-d H:i:s'),
                "LaporanPolisiID" => Str::uuid(),
                "DorsID" => strval(rand(1000000,9999999)),
                "LokasiDibuat" => 11,
                "TanggalMulai" => $date->format('Y-m-d H:i:s'),
                "TanggalBerakhir" => $date->modify('+1 day')->format('Y-m-d H:i:s'),
                "PejabatPenandatanganDokumen" => [
                    [
                        "Nama" => "SUGADRI S.I.K", 
                        "Pangkat" => "KOMPOL",
                        "NRP" => "77061000", 
                        "Jabatan" => "KASAT LANTAS",
                    ]
                ],
                "Personel_Sprindik" => [
                    [
                        "Nama" => "ISKANDAR, S.I.K", 
                        "Pangkat" => "AKP",
                        "NRP" => "73060000", 
                        "Jabatan" => "PENYIDIK",
                    ],
                    [
                        "Nama" => "SUKANDAR, S.I.K", 
                        "Pangkat" => "IPTU",
                        "NRP" => "73060111", 
                        "Jabatan" => "PENYIDIK PEMBANTU",
                    ]
                ],
                "Attachment" => "",
                "AttachmentMimeType" => "",
                "AttachmentExtension" => "",
                "CreatedDate" => $date->modify('-1 day')->format('Y-m-d H:i:s'),
                "CreatedBy" => "FIRMAN SANUSI",
                "UpdatedDate" => null,
                "UpdatedBy" => null,
            ];

            $no++;
        }

        return collect($data);
    }
}
