<?php

namespace App\Http\Controllers\IcellServices\ApiEmpBareskrim\V2\Doc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use App\Helpers\PeopleNameHelper;

use App\Models\Doc\LaporanHasilGelarPerkaraDocument\LaporanHasilGelarPerkaraDocument;

class LaporanHasilGelarPerkaraDocumentController extends Controller
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
            $laporanHasilGelarPerkaraDocuments = LaporanHasilGelarPerkaraDocument::with([
                'accident',
                'laporanHasilGelarPerkaraDocumentOfficers',
                'laporanHasilGelarPerkaraDocumentFiles',
                'suratPerintahPenyidikanDocument',
                'caseDegreeType',
                'timezone',
                'suspects',
            ])
            ->whereHas('accident.suratPemberitahuanDimulainyaPenyidikanDocuments', function($query){
                $query->whereIn('status_id', ['86']);
            })
            ->whereIn('status_id', ['86'])
            ->orderBy('document_date', 'ASC');

            // Filter data
            if (!empty($startDocumentDate) && !empty($endDocumentDate)) {
                $laporanHasilGelarPerkaraDocuments = $laporanHasilGelarPerkaraDocuments
                    ->whereBetween('document_date', [date('Y-m-d H:i:s', strtotime($startDocumentDate)), date('Y-m-d H:i:s', strtotime($endDocumentDate))]);
            } else if (!empty($startDocumentDate)) {
                $laporanHasilGelarPerkaraDocuments = $laporanHasilGelarPerkaraDocuments
                    ->where('document_date', '>=', date('Y-m-d H:i:s', strtotime($startDocumentDate)));
            } else if (!empty($endDocumentDate)) {
                $laporanHasilGelarPerkaraDocuments = $laporanHasilGelarPerkaraDocuments
                    ->where('document_date', '<=', date('Y-m-d H:i:s', strtotime($endDocumentDate)));
            }

            $laporanHasilGelarPerkaraDocuments = $laporanHasilGelarPerkaraDocuments->paginate($perPage, ['*'], 'page', $page);
            
            $laporanHasilGelarPerkaraDocumentsTotal = $laporanHasilGelarPerkaraDocuments->total();
            $laporanHasilGelarPerkaraDocumentsTotalPage = $laporanHasilGelarPerkaraDocuments->lastPage();
                                            
            // Packing data
            $arrayKey = 0;
            foreach($laporanHasilGelarPerkaraDocuments as $laporanHasilGelarPerkaraDocument){
                $dorsId = $laporanHasilGelarPerkaraDocument->accident->dors_id ?? null;
                $caseDegreeType = $laporanHasilGelarPerkaraDocument->caseDegreeType ?? null;
                $caseDegreeTypeEmpId = $caseDegreeType->emp_id ?? null;

                $timezone = $laporanHasilGelarPerkaraDocument->timezone ?? null;
                $timezoneName = $timezone->name ?? null;

                $documentSignatory = $laporanHasilGelarPerkaraDocument
                    ->laporanHasilGelarPerkaraDocumentOfficers
                    ->where('class', 'SIGNATORY')
                    ->first();

                $createdBy = $laporanHasilGelarPerkaraDocument->createdByUser ?? null;
                $createdByRegisterNumber = $createdBy->register_number ?? null;

                $police = $laporanHasilGelarPerkaraDocument->accident->police ?? null;
                $policeEmpId = $police->emp_id ?? null;

                $suspects = [];
                foreach($laporanHasilGelarPerkaraDocument->suspects as $suspect){
                    $suspects[] = [
                        "JenisIdentitasTersangka" => $suspect->identityType->name ?? null,
                        "NomorIdentitasTersangka" => $suspect->identity_number ?? null,
                        "NamaTersangka" => $suspect->name,
                        "JenisKelaminTersangka" => $suspect->gender->name ?? null,
                        "AlamatTersangka" => $suspect->address ?? null,
                        "TempatLahirTersangka" => $suspect->birth_place ?? null,
                        "TanggalLahirTersangka" => $suspect->birth_date ?? null,
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
                        "IbuKandung" => $suspect->mother_name,
                        "BapakKandung" => $suspect->father_name,
                        "StatusKawin" => (($suspect->maritalStatus) ? (($suspect->maritalStatus->id == 1) ? 0 : (($suspect->maritalStatus->id == 2) ? 1 : null)) : null),

                        "CreatedDate" => ($laporanHasilGelarPerkaraDocument->created_at) ? date('Y-m-d H:i:s', strtotime($laporanHasilGelarPerkaraDocument->created_at)) : null,
                        "CreatedBy" => $createdByRegisterNumber,
                        "CreatedLocation" => $laporanHasilGelarPerkaraDocument->ip_addresses['created_ip'] ?? null,
                    ];
                }

                $files = [];
                foreach($laporanHasilGelarPerkaraDocument->laporanHasilGelarPerkaraDocumentFiles as $laporanHasilGelarPerkaraDocumentFile){
                    $filePath = public_path('documents/laporan-hasil-gelar-perkara-document/images/' . $laporanHasilGelarPerkaraDocumentFile->name);

                    $files[] = [
                        "Id" => $laporanHasilGelarPerkaraDocumentFile->id,
                        "DORSId" => strval($dorsId),
                        "LHGPId" => $laporanHasilGelarPerkaraDocumentFile->laporan_hasil_gelar_perkara_document_id,
                        "BinaryFile" => base64_encode(File::get($filePath)),
                        "NamaFile" => $laporanHasilGelarPerkaraDocumentFile->original_name,
                        "ContentType" => $laporanHasilGelarPerkaraDocumentFile->mimetype,
                        "ContentLength" => $laporanHasilGelarPerkaraDocumentFile->size,
                        "CreatedDate" => ($laporanHasilGelarPerkaraDocument->created_at) ? date('Y-m-d H:i:s', strtotime($laporanHasilGelarPerkaraDocument->created_at)) : null,
                        "CreatedBy" => $createdByRegisterNumber,
                        "CreatedLocation" => $laporanHasilGelarPerkaraDocument->ip_addresses['created_ip'] ?? null,
                    ];
                }
                
                
                $responseData[$arrayKey]  = [
                    "Id" => $laporanHasilGelarPerkaraDocument->id,
                    "DORSId" => strval($dorsId),
                    "SprindikId" => ($laporanHasilGelarPerkaraDocument->suratPerintahPenyidikanDocument) ? strval($laporanHasilGelarPerkaraDocument->suratPerintahPenyidikanDocument->id) : null,
                    "SuratUGP" => $laporanHasilGelarPerkaraDocument->case_degree_invite_reference,
                    "TanggalUGP" => $laporanHasilGelarPerkaraDocument->case_degree_invite_date,
                    "IdListPermasalahan"=> $caseDegreeTypeEmpId,
                    "JenisLhgp" => $laporanHasilGelarPerkaraDocument->document_type,
                    "TanggalPelaksanaan" => $laporanHasilGelarPerkaraDocument->date,
                    "Waktu" => $laporanHasilGelarPerkaraDocument->time,
                    "ZonaWaktu" => $timezoneName,
                    "TempatPelaksanaan" => $laporanHasilGelarPerkaraDocument->place,
                    "PimpinanGelarPerkara" => $laporanHasilGelarPerkaraDocument->case_degree_leader,
                    "JumlahPeserta" => $laporanHasilGelarPerkaraDocument->attendees,
                    "Pembahasan" => $laporanHasilGelarPerkaraDocument->discussion,
                    "Penutupan" => $laporanHasilGelarPerkaraDocument->closing,
                    "Kesimpulan" => $laporanHasilGelarPerkaraDocument->conclusion,

                    "TempatKejadianPerkara" => null,
                    "MulaiTanggalKejadianPerkara" => null,
                    "MulaiWaktuKejadianPerkara" => null,
                    "MulaiZonaWaktuKejadianPerkara" => null,
                    "AkhirTanggalKejadianPerkara" => null,
                    "AkhirWaktuKejadianPerkara" => null,
                    "AkhirZonaWaktuKejadianPerkara" => null,
                    "DugaanTindakPidana" => null,
                    "Pasal" => null,

                    "LokasiID" => $policeEmpId,
                    "Nama" => (!empty($documentSignatory)) ? PeopleNameHelper::getFullName($documentSignatory->first_title, $documentSignatory->first_name, $documentSignatory->last_name, $documentSignatory->last_title) : null,
                    "Nrp" => $documentSignatory->register_number ?? null,
                    "JabatanPenandatangan" => $documentSignatory->position->name ?? null,
                    "CreatedDate" => ($laporanHasilGelarPerkaraDocument->created_at) ? date('Y-m-d H:i:s', strtotime($laporanHasilGelarPerkaraDocument->created_at)) : null,
                    "CreatedBy" => $createdByRegisterNumber,
                    "CreatedLocation" => $laporanHasilGelarPerkaraDocument->ip_addresses['created_ip'] ?? null,

                    "LaporanHasilgelarPerkara_ListTersangka" => $suspects,

                    "LaporanHasilGelarPerkara_File" => $files,
                ];

                // Update last_synced_at
                $laporanHasilGelarPerkaraDocumentId = $laporanHasilGelarPerkaraDocument->id;
                DB::table($this->tableSchemaName . 'laporan_hasil_gelar_perkara_documents')
                    ->where('id', $laporanHasilGelarPerkaraDocumentId)
                    ->update([
                        'last_synced_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    ]);

                $arrayKey++;
            }
            
            // Check if data array is empty result
            if ($laporanHasilGelarPerkaraDocuments->isEmpty()) {
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
                    "TotalData" => $laporanHasilGelarPerkaraDocumentsTotal,
                    "TotalPage" => $laporanHasilGelarPerkaraDocumentsTotalPage,
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
