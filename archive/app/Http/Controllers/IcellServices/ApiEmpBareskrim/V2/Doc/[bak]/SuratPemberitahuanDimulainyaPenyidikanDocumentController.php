<?php

namespace App\Http\Controllers\IcellServices\ApiEmpBareskrim\V2\Doc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Helpers\PeopleNameHelper;

use App\Models\Doc\SuratPemberitahuanDimulainyaPenyidikanDocument\SuratPemberitahuanDimulainyaPenyidikanDocument;

class SuratPemberitahuanDimulainyaPenyidikanDocumentController extends Controller
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
            $suratPemberitahuanDimulainyaPenyidikanDocuments = SuratPemberitahuanDimulainyaPenyidikanDocument::with([
                'accident', 
                'suratPemberitahuanDimulainyaPenyidikanDocumentOfficers',
                'suratPerintahPenyidikanDocument',
                'suratPerintahTugasDocument',
                'prosecutor',
                'court',
                'suspects',
            ])
            ->whereHas('accident.suratPemberitahuanDimulainyaPenyidikanDocuments', function($query){
                $query->whereIn('status_id', ['86']);
            })
            ->whereIn('status_id', ['86'])
            ->orderBy('document_date', 'ASC');

            // Filter data
            if (!empty($startDocumentDate) && !empty($endDocumentDate)) {
                $suratPemberitahuanDimulainyaPenyidikanDocuments = $suratPemberitahuanDimulainyaPenyidikanDocuments
                    ->whereBetween('document_date', [date('Y-m-d H:i:s', strtotime($startDocumentDate)), date('Y-m-d H:i:s', strtotime($endDocumentDate))]);
            } else if (!empty($startDocumentDate)) {
                $suratPemberitahuanDimulainyaPenyidikanDocuments = $suratPemberitahuanDimulainyaPenyidikanDocuments
                    ->where('document_date', '>=', date('Y-m-d H:i:s', strtotime($startDocumentDate)));
            } else if (!empty($endDocumentDate)) {
                $suratPemberitahuanDimulainyaPenyidikanDocuments = $suratPemberitahuanDimulainyaPenyidikanDocuments
                    ->where('document_date', '<=', date('Y-m-d H:i:s', strtotime($endDocumentDate)));
            }

            $suratPemberitahuanDimulainyaPenyidikanDocuments = $suratPemberitahuanDimulainyaPenyidikanDocuments->paginate($perPage, ['*'], 'page', $page);
            
            $suratPemberitahuanDimulainyaPenyidikanDocumentsTotal = $suratPemberitahuanDimulainyaPenyidikanDocuments->total();
            $suratPemberitahuanDimulainyaPenyidikanDocumentsTotalPage = $suratPemberitahuanDimulainyaPenyidikanDocuments->lastPage();
                                            
            // Packing data
            $arrayKey = 0;
            foreach($suratPemberitahuanDimulainyaPenyidikanDocuments as $suratPemberitahuanDimulainyaPenyidikanDocument){
                $documentSignatory = $suratPemberitahuanDimulainyaPenyidikanDocument
                    ->suratPemberitahuanDimulainyaPenyidikanDocumentOfficers
                    ->where('class', 'SIGNATORY')
                    ->first();

                $createdBy = $suratPemberitahuanDimulainyaPenyidikanDocument->createdByUser ?? null;
                $createdByRegisterNumber = $createdBy->register_number ?? null;

                $prosecutor = $suratPemberitahuanDimulainyaPenyidikanDocument->prosecutor ?? null;
                $prosecutorEmpId = $prosecutor->emp_id ?? null;

                $court = $suratPemberitahuanDimulainyaPenyidikanDocument->court ?? null;
                $courtEmpId = $court->emp_id ?? null;

                $carbonCopies = $suratPemberitahuanDimulainyaPenyidikanDocument->carbon_copies ?? null;
                // $carbonCopiesArray = ($carbonCopies) ? $carbonCopies : null;
                $carbonCopiesText = ($carbonCopies) ? implode(', ', $carbonCopies) : null;

                $suspects = [];
                foreach ($suratPemberitahuanDimulainyaPenyidikanDocument->suspects as $suspect) {
                    $suspects[] = [
                        "SKetetapanPenetapanTersangkaId"=> $suspect->suratKetetapanTentangPenetapanTersangkaDocument->first()->id ?? null,
                        "Id"=> $suspect->id ?? null,
                        "DORSId"=> $suratPemberitahuanDimulainyaPenyidikanDocument->accident->dors_id ?? null,
                        "SPDPId"=> $suratPemberitahuanDimulainyaPenyidikanDocument->suratPerintahPenyidikanDocument->id ?? null,
                        "NamaTersangka"=> $suspect->name ?? null,
                        "JenisKelaminTersangka"=> $suspect->gender->name ?? null,
                        "TempatLahirTersangka"=> $suspect->birth_place ?? null,
                        "TanggalLahirTersangka"=> $suspect->birth_date ?? null,
                        "PekerjaanIdTersangka"=> $suspect->job->emp_id ?? null,
                        "AlamatTersangka"=> $suspect->address ?? null,
                        "NegaraIdTersangka"=> $suspect->country->emp_id ?? null,
                        "AgamaIdTersangka"=> $suspect->religion->emp_id ?? null,
                        "CreatedDate" => ($suratPemberitahuanDimulainyaPenyidikanDocument->created_at) ? date('Y-m-d H:i:s', strtotime($suratPemberitahuanDimulainyaPenyidikanDocument->created_at)) : null,
                        "CreatedBy" => $createdByRegisterNumber,
                        "CreatedLocation" => $suratPemberitahuanDimulainyaPenyidikanDocument->ip_addresses['created_ip'] ?? null,
                    ];
                }
       
                $responseData[$arrayKey]  = [
                    "Id" => $suratPemberitahuanDimulainyaPenyidikanDocument->id,
                    "DORSId" => ($suratPemberitahuanDimulainyaPenyidikanDocument->accident) ? strval($suratPemberitahuanDimulainyaPenyidikanDocument->accident->dors_id) : null,
                    "SprindikId" => ($suratPemberitahuanDimulainyaPenyidikanDocument->suratPerintahPenyidikanDocument) ? strval($suratPemberitahuanDimulainyaPenyidikanDocument->suratPerintahPenyidikanDocument->id) : null,
                    "SPTugasId"=> ($suratPemberitahuanDimulainyaPenyidikanDocument->suratPerintahTugasDocument) ? strval($suratPemberitahuanDimulainyaPenyidikanDocument->suratPerintahTugasDocument->id) : null,
                    "NoSurat" => $suratPemberitahuanDimulainyaPenyidikanDocument->document_number,
                    "Tanggal" => ($suratPemberitahuanDimulainyaPenyidikanDocument->document_date) ? date('Y-m-d H:i:s', strtotime($suratPemberitahuanDimulainyaPenyidikanDocument->document_date)) : null,
                    "KejaksaanId" => $prosecutorEmpId,
                    "PengadilanId" => $courtEmpId,
                    "Tembusan" => $carbonCopiesText,
                    "KategoriKejaksaan" => null,

                    "AdaTersangka" => ($suratPemberitahuanDimulainyaPenyidikanDocument->suspects->count() > 0) ? "Ada" : "Tidak Ada Tersangka",

                    "NamaPelapor" => null,
                    "TempatLahirPelapor" => null,
                    "TanggalLahirPelapor" => null,

                    "NamaTerlapor" => null,
                    "TempatLahirTerlapor" => null,
                    "TanggalLahirTerlapor" => null,

                    "Nama" => (!empty($documentSignatory)) ? PeopleNameHelper::getFullName($documentSignatory->first_title, $documentSignatory->first_name, $documentSignatory->last_name, $documentSignatory->last_title) : null,
                    "Nrp" => $documentSignatory->register_number ?? null,
                    "JabatanPenandatangan" => $documentSignatory->position->name ?? null,
                    "CreatedDate" => ($suratPemberitahuanDimulainyaPenyidikanDocument->created_at) ? date('Y-m-d H:i:s', strtotime($suratPemberitahuanDimulainyaPenyidikanDocument->created_at)) : null,
                    "CreatedBy" => $createdByRegisterNumber,
                    "CreatedLocation" => $suratPemberitahuanDimulainyaPenyidikanDocument->ip_addresses['created_ip'] ?? null,

                    "Tersangka_SPDP" => $suspects,
                ];

                // Update last_synced_at
                $suratPemberitahuanDimulainyaPenyidikanDocumentId = $suratPemberitahuanDimulainyaPenyidikanDocument->id;
                DB::table($this->tableSchemaName . 'surat_pemberitahuan_dimulainya_penyidikan_documents')
                    ->where('id', $suratPemberitahuanDimulainyaPenyidikanDocumentId)
                    ->update([
                        'last_synced_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    ]);

                $arrayKey++;
            }
            
            // Check if data array is empty result
            if ($suratPemberitahuanDimulainyaPenyidikanDocuments->isEmpty()) {
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
                    "TotalData" => $suratPemberitahuanDimulainyaPenyidikanDocumentsTotal,
                    "TotalPage" => $suratPemberitahuanDimulainyaPenyidikanDocumentsTotalPage,
                    "TotalDataSent" => count($responseData)
                ],
                "data" => $responseData
            ], 200);

        } catch (\Exception $e) {
            DB::rollback();
            return $e;
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

