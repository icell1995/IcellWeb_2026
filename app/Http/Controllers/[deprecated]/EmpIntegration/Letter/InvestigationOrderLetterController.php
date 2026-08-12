<?php

namespace App\Http\Controllers\EmpIntegration\Letter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

use App\Models\Letters\InvestigationOrderLetter\InvestigationOrderLetter;

// SURAT PERINTAH PENYIDIKAN (SPRINDIK)
class InvestigationOrderLetterController extends Controller
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
                "data" => []
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
                "data" => []
            ], 400);
        }

        DB::beginTransaction();
        try {
            $investigationOrderLetters = InvestigationOrderLetter::with(['accident', 'accident.polres', 'authorizedSignatories', 'officers', 'leaderOfficers'])
                                            ->orderBy('issued_date', 'ASC');

            // Filter data
            if (!empty($startDocumentDate) && !empty($endDocumentDate)) {
                $investigationOrderLetters = $investigationOrderLetters->whereBetween('issued_date', [date('Y-m-d H:i:s', strtotime($startDocumentDate)), date('Y-m-d H:i:s', strtotime($endDocumentDate))]);
            } else if (!empty($startDocumentDate)) {
                $investigationOrderLetters = $investigationOrderLetters->where('issued_date', '>=', date('Y-m-d H:i:s', strtotime($startDocumentDate)));
            } else if (!empty($endDocumentDate)) {
                $investigationOrderLetters = $investigationOrderLetters->where('issued_date', '<=', date('Y-m-d H:i:s', strtotime($endDocumentDate)));
            }
            $investigationOrderLetters = $investigationOrderLetters->paginate($perPage, ['*'], 'page', $page);
            
            $investigationOrderLettersTotal = $investigationOrderLetters->total();
            $investigationOrderLettersTotalPage = $investigationOrderLetters->lastPage();
                                            
            // Packing data
            $arrayKey = 0;
            foreach($investigationOrderLetters as $investigationOrderLetter){

                $leaderOfficer = ($investigationOrderLetter->leaderOfficers) ? $investigationOrderLetter->leaderOfficers->first() : null;
                $officers = $investigationOrderLetter->officers;
                $authorizedSignatory = ($investigationOrderLetter->authorizedSignatories) ? $investigationOrderLetter->authorizedSignatories->first() : null;
                $resortPolice = ($investigationOrderLetter->accident) ? (($investigationOrderLetter->accident->polres) ? $investigationOrderLetter->accident->polres : null) : null;

                $documentOfficer = [];

                $documentOfficer[] = [
                    "Nama" => ($leaderOfficer) ? $leaderOfficer->first_name . " "  . $leaderOfficer->last_name : null,
                    "Pangkat" => ($leaderOfficer) ? strtoupper($leaderOfficer->rank_short_name) : null,
                    "NRP" => ($leaderOfficer) ? strval($leaderOfficer->id) : null, 
                    "Jabatan" => ($leaderOfficer) ? strtoupper($leaderOfficer->sebagai_kepala) : null,
                ];

                foreach($officers as $officer){
                    $documentOfficer[] = [
                        "Nama" => $officer->first_name . " "  . $officer->last_name,
                        "Pangkat" => strtoupper($officer->rank_short_name),
                        "NRP" => strval($officer->id), 
                        "Jabatan" => strtoupper($officer->position_short_name),
                    ];
                }
         
                $documentSignatory[0] = [
                    "Nama" => ($authorizedSignatory) ? $authorizedSignatory->first_title . " " . strtoupper($authorizedSignatory->first_name . " " . $authorizedSignatory->last_name) . ", " . $authorizedSignatory->last_title : null,
                    "Pangkat" => ($authorizedSignatory) ? strtoupper($authorizedSignatory->rank_id) : null,
                    "NRP" => ($authorizedSignatory) ? strval($authorizedSignatory->register_number) : null, 
                    "Jabatan" => ($authorizedSignatory) ? strtoupper($authorizedSignatory->position_id) : null,
                ];

                $responseData[$arrayKey]  = [
                    "Id" => $investigationOrderLetter->id,
                    "NoSurat" => $investigationOrderLetter->letter_number,
                    "TanggalSprindik" => ($investigationOrderLetter->issued_date) ? date('Y-m-d H:i:s', strtotime($investigationOrderLetter->issued_date)) : null,
                    "LaporanPolisiID" => ($investigationOrderLetter->accident) ? strval($investigationOrderLetter->accident->id) : null,
                    "DorsID" => ($investigationOrderLetter->accident) ? strval($investigationOrderLetter->accident->dors_id) : null,
                    "LokasiDibuat" => ($resortPolice) ? strval($resortPolice->spptti_id) : null,
                    "TanggalMulai" => ($investigationOrderLetter->start_date) ? date('Y-m-d H:i:s', strtotime($investigationOrderLetter->start_date)) : null,
                    "TanggalBerakhir" => ($investigationOrderLetter->end_date) ? date('Y-m-d H:i:s', strtotime($investigationOrderLetter->end_date)) : null,
                    "PejabatPenandatanganDokumen" => $documentSignatory,
                    "Personel_Sprindik" => $documentOfficer,
                    "Attachment" => null,
                    "AttachmentMimeType" => null,
                    "AttachmentExtension" => null,
                    "CreatedDate" => ($investigationOrderLetter->created_at) ? date('Y-m-d H:i:s', strtotime($investigationOrderLetter->created_at)) : null,
                    "CreatedBy" => ($investigationOrderLetter->created_by) ? $investigationOrderLetter->created_by : null,
                    "UpdatedDate" => ($investigationOrderLetter->updated_at) ? date('Y-m-d H:i:s', strtotime($investigationOrderLetter->updated_at)) : null,
                    "UpdatedBy" => ($investigationOrderLetter->updated_by) ? $investigationOrderLetter->updated_by : null,
                ];

                // Update is_itegrated and integrated_at
                $investigationOrderLetterId = $investigationOrderLetter->id;
                DB::table($this->tableSchemaName . 'investigation_order_letters')
                    ->where('id', $investigationOrderLetterId)
                    ->update([
                        'integrated_at' => date('Y-m-d H:i:s'),
                    ]);

                $arrayKey++;
            }
            
            // Check if data array is empty result
            if ($investigationOrderLetters->isEmpty()) {
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
                    "data" => []
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
                    "TotalData" => $investigationOrderLettersTotal,
                    "TotalPage" => $investigationOrderLettersTotalPage,
                    "TotalDataSent" => count($responseData)
                ],
                "data" => $responseData
            ], 200);

        } catch (\Exception $e) {
            DB::rollback();
            //If an exception occurs, return an error response
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
