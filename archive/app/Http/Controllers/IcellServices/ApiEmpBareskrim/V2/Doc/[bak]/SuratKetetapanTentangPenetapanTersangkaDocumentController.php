<?php

namespace App\Http\Controllers\IcellServices\ApiEmpBareskrim\V2\Doc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Helpers\PeopleNameHelper;

use App\Models\Doc\SuratKetetapanTentangPenetapanTersangkaDocument\SuratKetetapanTentangPenetapanTersangkaDocument;

class SuratKetetapanTentangPenetapanTersangkaDocumentController extends Controller
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
            $suratKetetapanTentangPenetapanTersangkaDocuments = SuratKetetapanTentangPenetapanTersangkaDocument::with([
                'accident', 
                'suratPerintahPenyidikanDocument',
                'suratKetetapanTentangPenetapanTersangkaDocumentOfficers',
                'laporanHasilGelarPerkaraDocument',
                'prosecutor',
                'suspect',
            ])
            ->whereHas('accident.suratPemberitahuanDimulainyaPenyidikanDocuments', function($query){
                $query->whereIn('status_id', ['86']);
            })
            ->whereIn('status_id', ['86'])
            ->orderBy('document_date', 'ASC');

            // Filter data
            if (!empty($startDocumentDate) && !empty($endDocumentDate)) {
                $suratKetetapanTentangPenetapanTersangkaDocuments = $suratKetetapanTentangPenetapanTersangkaDocuments->whereBetween('document_date', [date('Y-m-d H:i:s', strtotime($startDocumentDate)), date('Y-m-d H:i:s', strtotime($endDocumentDate))]);
            } else if (!empty($startDocumentDate)) {
                $suratKetetapanTentangPenetapanTersangkaDocuments = $suratKetetapanTentangPenetapanTersangkaDocuments->where('document_date', '>=', date('Y-m-d H:i:s', strtotime($startDocumentDate)));
            } else if (!empty($endDocumentDate)) {
                $suratKetetapanTentangPenetapanTersangkaDocuments = $suratKetetapanTentangPenetapanTersangkaDocuments->where('document_date', '<=', date('Y-m-d H:i:s', strtotime($endDocumentDate)));
            }
            $suratKetetapanTentangPenetapanTersangkaDocuments = $suratKetetapanTentangPenetapanTersangkaDocuments->paginate($perPage, ['*'], 'page', $page);
            
            $suratKetetapanTentangPenetapanTersangkaDocumentsTotal = $suratKetetapanTentangPenetapanTersangkaDocuments->total();
            $suratKetetapanTentangPenetapanTersangkaDocumentsTotalPage = $suratKetetapanTentangPenetapanTersangkaDocuments->lastPage();
                                     
            // Packing data
            $arrayKey = 0;
            foreach($suratKetetapanTentangPenetapanTersangkaDocuments as $suratKetetapanTentangPenetapanTersangkaDocument){
                $documentSignatory = $suratKetetapanTentangPenetapanTersangkaDocument
                ->suratKetetapanTentangPenetapanTersangkaDocumentOfficers
                ->where('class', 'SIGNATORY')
                ->first();

                $createdBy = $suratKetetapanTentangPenetapanTersangkaDocument->createdByUser ?? null;
                $createdByRegisterNumber = $createdBy->register_number ?? null;

                $police = $suratKetetapanTentangPenetapanTersangkaDocument->accident->police ?? null;
                $policeEmpId = $police->emp_id ?? null;

                $prosecutor = $suratKetetapanTentangPenetapanTersangkaDocument->prosecutor ?? null;
                $prosecutorEmpId = $prosecutor->emp_id ?? null;
                
                $suspect = $suratKetetapanTentangPenetapanTersangkaDocument->suspect ?? null;
                $suspect = $suspect->first();

                $responseData[$arrayKey]  = [
                    "Id" => $suratKetetapanTentangPenetapanTersangkaDocument->id,
                    "DORSId" => ($suratKetetapanTentangPenetapanTersangkaDocument->accident) ? strval($suratKetetapanTentangPenetapanTersangkaDocument->accident->dors_id) : null,
                    "SprindikId" => ($suratKetetapanTentangPenetapanTersangkaDocument->suratPerintahPenyidikanDocument) ? strval($suratKetetapanTentangPenetapanTersangkaDocument->suratPerintahPenyidikanDocument->id) : null,
                    "LHGPId"=> ($suratKetetapanTentangPenetapanTersangkaDocument->laporanHasilGelarPerkaraDocument) ? strval($suratKetetapanTentangPenetapanTersangkaDocument->laporanHasilGelarPerkaraDocument->id) : null,
                    "NomorSurat" => $suratKetetapanTentangPenetapanTersangkaDocument->document_number,
                    "TanggalSurat" => ($suratKetetapanTentangPenetapanTersangkaDocument->document_date) ? date('Y-m-d H:i:s', strtotime($suratKetetapanTentangPenetapanTersangkaDocument->document_date)) : null,
                    "KejaksaanId" => $prosecutorEmpId,
                    "LokasiID" => $policeEmpId,

                    "JenisIdentitasTersangka" => $suspect->identityType->name ?? null,
                    "NomorIdentitasTersangka" => $suspect->identity_number ?? null,
                    "NamaTersangka" => $suspect->name,
                    "JenisKelaminTersangka" => $suspect->gender->name ?? null,
                    "AlamatTersangka" => $suspect->address ?? null,
                    "TempatLahirTersangka" => $suspect->birth_place ?? null,
                    "TanggalLahirTersangka" => ($suspect->birth_date) ? date('Y-m-d', strtotime($suspect->birth_date)) : null,
                    "SukuIdTersangka" => $suspect->ethnic->emp_id ?? null,
                    "PekerjaanIdTersangka" => $suspect->job->emp_id ?? null,
                    "AgamaIdTersangka" => $suspect->religion->emp_id ?? null,
                    "PendidikanIdTersangka" => $suspect->education->emp_id ?? null,
                    "NegaraIdTersangka" => $suspect->country->emp_id ?? null,
                    "NomorTeleponTersangka" => $suspect->phone_number ?? null,
                    "UmurTersangka" => $suspect->age ?? null,
                    "EmailTersangka" => $suspect->email_address ?? null,
                    "PropinsiIdTersangka" => $suspect->province->emp_id ?? null,
                    "KotaIdTersangka" => $suspect->regency->emp_id ?? null,
                    "KecamatanIdTersangka" => $suspect->district->emp_id ?? null,
                    "KelurahanIdTersangka" => $suspect->village->emp_id ?? null,
                    "NamaAlias" => null,
                    "IbuKandung" => $suspect->mother_name ?? null,
                    "BapakKandung" => $suspect->father_name ?? null,
                    "StatusKawin" => (($suspect->maritalStatus) ? (($suspect->maritalStatus->id == 1) ? 0 : (($suspect->maritalStatus->id == 2) ? 1 : null)) : null),

                    "Nama" => (!empty($documentSignatory)) ? PeopleNameHelper::getFullName($documentSignatory->first_title, $documentSignatory->first_name, $documentSignatory->last_name, $documentSignatory->last_title) : null,
                    "Nrp" => $documentSignatory->register_number ?? null,
                    "JabatanPenandatangan" => $documentSignatory->position->name ?? null,
                    "CreatedDate" => ($suratKetetapanTentangPenetapanTersangkaDocument->created_at) ? date('Y-m-d H:i:s', strtotime($suratKetetapanTentangPenetapanTersangkaDocument->created_at)) : null,
                    "CreatedBy" => $createdByRegisterNumber,
                    "CreatedLocation" => $suratKetetapanTentangPenetapanTersangkaDocument->ip_addresses['created_ip'] ?? null,
                ];

                // Update last_synced_at
                $suratKetetapanTentangPenetapanTersangkaDocumentId = $suratKetetapanTentangPenetapanTersangkaDocument->id;
                DB::table($this->tableSchemaName . 'surat_ketetapan_tentang_penetapan_tersangka_documents')
                    ->where('id', $suratKetetapanTentangPenetapanTersangkaDocumentId)
                    ->update([
                        'last_synced_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    ]);

                $arrayKey++;
            }
            
            // Check if data array is empty result
            if ($suratKetetapanTentangPenetapanTersangkaDocuments->isEmpty()) {
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
                    "TotalData" => $suratKetetapanTentangPenetapanTersangkaDocumentsTotal,
                    "TotalPage" => $suratKetetapanTentangPenetapanTersangkaDocumentsTotalPage,
                    "TotalDataSent" => count($responseData)
                ],
                "data" => $responseData
            ], 200);

        } catch (\Exception $e) {
            DB::rollback(); return $e;
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

