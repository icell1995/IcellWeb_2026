<?php

namespace App\Http\Controllers\IcellServices\ApiEmpBareskrim\V2\Doc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SuratPerintahPenyelidikanDocumentController extends Controller
{
    private $tableSchemaName = 'doc' . '.';

    public function index(Request $request){
        // Get request data
        $startDocumentDate = $request->input('start_doc_date');
        $endDocumentDate = $request->input('end_doc_date');
        $perPage = $request->query('perPage', 100); // Jumlah item per halaman
        $page = $request->query('page', 1); // Nomor halaman saat ini, default 1

        // Initialize variable
        $responseData = [];

        if (!is_numeric($page)) {
            // Jika $page bukan angka, berikan nilai default 1
            $page = 1;
        }

        if (!is_numeric($perPage)) {
            // Jika $page bukan angka, berikan nilai default 1
            $perPage = 100;
        }

        $page = intval($page);

        // Validate request data
        if (!empty($startDocumentDate) && date('Y-m-d', strtotime($startDocumentDate)) !== $startDocumentDate) {
            return response()->json([
                "code" => "400",
                "status" => "BAD_REQUEST",
                "message" => "Invalid Start document date or format. Date format should be YYYY-MM-DD.",
                "pagination" => [
                    "Page" => $page,
                    "TotalData" => 0,
                    "TotalPage" => 0,
                    "TotalDataSent" => 0
                ],
                "data" => [],
            ], 400);
        }

        if (!empty($endDocumentDate) && date('Y-m-d', strtotime($endDocumentDate)) !== $endDocumentDate) {
            return response()->json([
                "code" => "400",
                "status" => "BAD_REQUEST",
                "message" => "Invalid End document date or format. Date format should be YYYY-MM-DD.",
                "pagination" => [
                    "Page" => $page,
                    "TotalData" => 0,
                    "TotalPage" => 0,
                    "TotalDataSent" => 0
                ],
                "data" => [],
            ], 400);
        }
        
        DB::beginTransaction();
        try {
            $investigationWarrants = InvestigationWarrant::with(['accident', 'accident.polres', 'authorizedSignatories', 'officers', 'leaderOfficers'])
                ->orderBy('issued_date', 'ASC');
            
            // Filter data
            if (!empty($startDocumentDate) && !empty($endDocumentDate)) {
            $investigationWarrants = $investigationWarrants->whereBetween('issued_date', [date('Y-m-d H:i:s', strtotime($startDocumentDate)), date('Y-m-d H:i:s', strtotime($endDocumentDate))]);
            } else if (!empty($startDocumentDate)) {
                $investigationWarrants = $investigationWarrants->where('issued_date', '>=', date('Y-m-d H:i:s', strtotime($startDocumentDate)));
            } else if (!empty($endDocumentDate)) {
                $investigationWarrants = $investigationWarrants->where('issued_date', '<=', date('Y-m-d H:i:s', strtotime($endDocumentDate)));
            }
            $investigationWarrants = $investigationWarrants->paginate($perPage, ['*'], 'page', $page);

            $investigationWarrantsTotal = $investigationWarrants->total();
            $investigationWarrantsTotalPage = $investigationWarrants->lastPage();

            // Packing data
            $arrayKey = 0;
            foreach($investigationWarrants as $investigationWarrant){

                $leaderOfficer = ($investigationWarrant->leaderOfficers) ? $investigationWarrant->leaderOfficers->first() : null;
                $officers = $investigationWarrant->officers;
                $authorizedSignatory = ($investigationWarrant->authorizedSignatories) ? $investigationWarrant->authorizedSignatories->first() : null;
                $resortPolice = ($investigationWarrant->accident) ? (($investigationWarrant->accident->polres) ? $investigationWarrant->accident->polres : null) : null;
                $documentOfficer = [];

                $documentOfficer[] = [
                    "Nama" => ($leaderOfficer) ? $leaderOfficer->first_name . " "  . $leaderOfficer->last_name : null,
                    "Pangkat" => ($leaderOfficer) ? strtoupper($leaderOfficer->ranks_id) : null,
                    "NRP" => ($leaderOfficer) ? strval($leaderOfficer->id) : null, 
                    "Jabatan" => ($leaderOfficer) ? strtoupper($leaderOfficer->sebagai_kepala) : null,
                ];

                foreach($officers as $officer){
                    $documentOfficer[] = [
                        "Nama" => $officer->first_name . " "  . $officer->last_name,
                        "Pangkat" => strtoupper($officer->ranks_id),
                        "NRP" => strval($officer->id), 
                        "Jabatan" => strtoupper($officer->position),
                    ];
                }
         
                $documentSignatory[0] = [
                    "Nama" => ($authorizedSignatory) ? $authorizedSignatory->first_title . " " . strtoupper($authorizedSignatory->first_name . " " . $authorizedSignatory->last_name) . ", " . $authorizedSignatory->last_title : null,
                    "Pangkat" => ($authorizedSignatory) ? strtoupper($authorizedSignatory->ranks_id) : null,
                    "NRP" => ($authorizedSignatory) ? strval($authorizedSignatory->register_number) : null, 
                    "Jabatan" => ($authorizedSignatory) ? strtoupper($authorizedSignatory->position_id) : null,
                ];

                $responseData[$arrayKey]  = [
                    "Id" => $investigationWarrant->id,
                    "NoSurat" => $investigationWarrant->letter_number,
                    "TanggalSprinlidik" => ($investigationWarrant->issued_date) ? date('Y-m-d H:i:s', strtotime($investigationWarrant->issued_date)) : null,
                    "LaporanPolisiID" => ($investigationWarrant->accident) ? strval($investigationWarrant->accident->id) : null,
                    "DorsID" => ($investigationWarrant->accident) ? strval($investigationWarrant->accident->dors_id) : null,
                    "LokasiDibuat" => ($resortPolice) ? strval($resortPolice->spptti_id) : null,
                    "TanggalMulai" => ($investigationWarrant->start_date) ? date('Y-m-d H:i:s', strtotime($investigationWarrant->start_date)) : null,
                    "TanggalBerakhir" => ($investigationWarrant->end_date != null) ? date('Y-m-d H:i:s', strtotime($investigationWarrant->end_date)) : 'selesai',
                    "PejabatPenandatanganDokumen" => $documentSignatory,
                    "Personel_Sprinlidik" => $documentOfficer,
                    "Attachment" => null,
                    "AttachmentMimeType" => null,
                    "AttachmentExtension" => null,
                    "CreatedDate" => ($investigationWarrant->created_at) ? date('Y-m-d H:i:s', strtotime($investigationWarrant->created_at)) : null,
                    "CreatedBy" => ($investigationWarrant->created_by) ? $investigationWarrant->created_by : null,
                    "UpdatedDate" => ($investigationWarrant->updated_at) ? date('Y-m-d H:i:s', strtotime($investigationWarrant->updated_at)) : null,
                    "UpdatedBy" => ($investigationWarrant->updated_by) ? $investigationWarrant->updated_by : null,
                ];

                // Update is_itegrated and integrated_at
                $investigationWarrantId = $investigationWarrant->id;
                DB::table($this->tableSchemaName . 'investigation_warrants')
                    ->where('id', $investigationWarrantId)
                    ->update([
                        'integrated_at' => date('Y-m-d H:i:s'),
                    ]);

                $arrayKey++;
            }

            // Check if data array is empty result
            if ($investigationWarrants->isEmpty()) {
                return response()->json([
                    "code" => "404",
                    "status" => "NOT_FOUND",
                    "message" => "Data not found.",
                    "pagination" => [
                        "Page" => $page,
                        "TotalData" => 0,
                        "TotalPage" => 0,
                        "TotalDataSent" => 0
                    ],
                    "data" => [],
                ], 404);
            }
            
            // Commit transaction
            DB::commit();

            // Return Result JSON
            return response()->json([
                "code" => "200",
                "status" => "OK",
                "message" => "Success",
                "pagination" => [
                    "Page" => $page,
                    "TotalData" => $investigationWarrantsTotal,
                    "TotalPage" => $investigationWarrantsTotalPage,
                    "TotalDataSent" => $investigationWarrants->count()
                ],
                "data" => $responseData
            ], 200);

        } catch (\Exception $e) {
            // Rollback transaction
            DB::rollback();
            // If an exception occurs, return an error response
            return response()->json([
                "code" => "500",
                "status" => "INTERNAL_SERVER_ERROR",
                "message" => "An error occurred while processing your request.",
                "pagination" => [
                    "Page" => $page,
                    "TotalData" => 0,
                    "TotalPage" => 0,
                    "TotalDataSent" => 0
                ],
                "data" => []
            ], 500);
        }
    }
}