<?php

namespace App\Http\Controllers\IcellServices\ApiEmpBareskrim\V2\Doc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Helpers\PeopleNameHelper;

use App\Models\Doc\SuratPerintahTugasDocument\SuratPerintahTugasDocument;

class SuratPerintahTugasDocumentController extends Controller
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
                    "Page" => intval($page),
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
                    "Page" => intval($page),
                    "TotalData" => 0,
                    "TotalPage" => 0,
                    "TotalDataSent" => 0
                ],
                "data" => []
            ], 400);
        }

        DB::beginTransaction();
        try {
            $suratPerintahTugasDocuments = SuratPerintahTugasDocument::with([
                'accident', 
                'related',
                'suratPerintahTugasDocumentOfficers',
            ])
            ->whereHas('accident.suratPemberitahuanDimulainyaPenyidikanDocuments', function($query){
                $query->whereIn('status_id', ['86']);
            })
            ->whereIn('status_id', ['86'])
            ->where('related_type', 'App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument')
            ->orderBy('document_date', 'ASC');

            // Filter data
            if (!empty($startDocumentDate) && !empty($endDocumentDate)) {
                $suratPerintahTugasDocuments = $suratPerintahTugasDocuments
                    ->whereBetween('document_date', [date('Y-m-d H:i:s', strtotime($startDocumentDate)), date('Y-m-d H:i:s', strtotime($endDocumentDate))]);
            } else if (!empty($startDocumentDate)) {
                $suratPerintahTugasDocuments = $suratPerintahTugasDocuments
                    ->where('document_date', '>=', date('Y-m-d H:i:s', strtotime($startDocumentDate)));
            } else if (!empty($endDocumentDate)) {
                $suratPerintahTugasDocuments = $suratPerintahTugasDocuments
                    ->where('document_date', '<=', date('Y-m-d H:i:s', strtotime($endDocumentDate)));
            }

            $suratPerintahTugasDocuments = $suratPerintahTugasDocuments->paginate($perPage, ['*'], 'page', $page);
            
            $suratPerintahTugasDocumentsTotal = $suratPerintahTugasDocuments->total();
            $suratPerintahTugasDocumentsTotalPage = $suratPerintahTugasDocuments->lastPage();
                                            
            // Packing data
            $arrayKey = 0;
            foreach($suratPerintahTugasDocuments as $suratPerintahTugasDocument){
                $documentSignatory = $suratPerintahTugasDocument
                    ->suratPerintahTugasDocumentOfficers
                    ->where('class', 'SIGNATORY')
                    ->first();

                $createdBy = $suratPerintahTugasDocument->createdByUser ?? NULL;
                $createdByRegisterNumber = $createdBy->register_number ?? null;

                $police = $suratPerintahTugasDocument->accident->police ?? null;
                $policeEmpId = $police->emp_id ?? null;

                $officers = [];
                foreach($suratPerintahTugasDocument->suratPerintahTugasDocumentOfficers as $suratPerintahTugasDocumentOfficer){
                    $officers[] = [
                        "Id" => $suratPerintahTugasDocumentOfficer->id,
                        "DORSId" => ($suratPerintahTugasDocumentOfficer->accident) ? strval($suratPerintahTugasDocumentOfficer->accident->dors_id) : null,
                        "SprindikId" => $suratPerintahTugasDocument->related_id,
                        "SPTugasId" => $suratPerintahTugasDocumentOfficer->surat_perintah_tugas_document_id,
                        "NamaPersonel" => PeopleNameHelper::getFullName($suratPerintahTugasDocumentOfficer->first_title, $suratPerintahTugasDocumentOfficer->first_name, $suratPerintahTugasDocumentOfficer->last_name, $suratPerintahTugasDocumentOfficer->last_title),
                        "Nrp" => $suratPerintahTugasDocumentOfficer->register_number,
                        "CreatedDate" => ($suratPerintahTugasDocument->created_at) ? date('Y-m-d H:i:s', strtotime($suratPerintahTugasDocument->created_at)) : null,
                        "CreatedBy" => $createdByRegisterNumber,
                        "CreatedLocation" => $suratPerintahTugasDocument->ip_addresses['created_ip'] ?? null,
                    ];
                }
                
                $responseData[$arrayKey]  = [
                    "Id" => $suratPerintahTugasDocument->id,
                    "DORSId" => ($suratPerintahTugasDocument->accident) ? strval($suratPerintahTugasDocument->accident->dors_id) : null,
                    "SprindikId" => ($suratPerintahTugasDocument->related) ? strval($suratPerintahTugasDocument->related->id) : null,
                    "NoSurat" => $suratPerintahTugasDocument->document_number,
                    "Tanggal" => ($suratPerintahTugasDocument->document_date) ? date('Y-m-d H:i:s', strtotime($suratPerintahTugasDocument->document_date)) : null,
                    "TanggalMulai" => ($suratPerintahTugasDocument->start_date) ? date('Y-m-d H:i:s', strtotime($suratPerintahTugasDocument->start_date)) : null,
                    "TanggalBerakhir" => ($suratPerintahTugasDocument->end_date) ? date('Y-m-d H:i:s', strtotime($suratPerintahTugasDocument->end_date)) : 'Selesai',
                    "Tugas" => $suratPerintahTugasDocument->task_description,
                    "LokasiID" => $policeEmpId,
                    "Nama" => (!empty($documentSignatory)) ? PeopleNameHelper::getFullName($documentSignatory->first_title, $documentSignatory->first_name, $documentSignatory->last_name, $documentSignatory->last_title) : null,
                    "Nrp" => $documentSignatory->register_number ?? null,
                    "JabatanPenandatangan" => $documentSignatory->position->name ?? null,
                    "CreatedDate" => ($suratPerintahTugasDocument->created_at) ? date('Y-m-d H:i:s', strtotime($suratPerintahTugasDocument->created_at)) : null,
                    "CreatedBy" => $createdByRegisterNumber,
                    "CreatedLocation" => $suratPerintahTugasDocument->ip_addresses['created_ip'] ?? null,

                    "DP_SPTugas" => $officers,
                ];

                // Update last_synced_at
                $suratPerintahTugasDocumentId = $suratPerintahTugasDocument->id;
                DB::table($this->tableSchemaName . 'surat_perintah_tugas_documents')
                    ->where('id', $suratPerintahTugasDocumentId)
                    ->update([
                        'last_synced_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    ]);

                $arrayKey++;
            }
            
            // Check if data array is empty result
            if ($suratPerintahTugasDocuments->isEmpty()) {
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
                    "TotalData" => $suratPerintahTugasDocumentsTotal,
                    "TotalPage" => $suratPerintahTugasDocumentsTotalPage,
                    "TotalDataSent" => count($responseData)
                ],
                "data" => $responseData
            ], 200);

        } catch (\Exception $e) {
            DB::rollback();
            // If an exception occurs, return an error response
            return response()->json([
                "code" => "500",
                "status" => "INTERNAL_SERVER_ERROR",
                "message" => "An error occurred while processing your request.",
                "pagination" => [
                    "Page" => intval($page),
                    "TotalData" => 0,
                    "TotalPage" => 0,
                    "TotalDataSent" => 0
                ],
                "data" => []
            ], 500);
        }
    }
}
