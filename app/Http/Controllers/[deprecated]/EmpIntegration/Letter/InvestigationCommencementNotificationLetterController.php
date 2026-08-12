<?php

namespace App\Http\Controllers\EmpIntegration\Letter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

use App\Models\Letters\InvestigationCommencementNotificationLetter\InvestigationCommencementNotificationLetter;

// SURAT PEMBERITAHUAN DIMULAINYA PENYIDIKAN (SPDP)
class InvestigationCommencementNotificationLetterController extends Controller
{
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
            // Get data from database
            $investigationCommencementNotificationLetters = InvestigationCommencementNotificationLetter::with(['accident', 'accident.suspect', 'accident.polres', 'authorizedSignatory'])
                                                                ->orderBy('spdp_date', 'asc');

            // Filter data
            if (!empty($startDocumentDate) && !empty($endDocumentDate)) {
                $investigationCommencementNotificationLetters = $investigationCommencementNotificationLetters->whereBetween('spdp_date', [date('Y-m-d H:i:s', strtotime($startDocumentDate)), date('Y-m-d H:i:s', strtotime($endDocumentDate))]);
            } else if (!empty($startDocumentDate)) {
                $investigationCommencementNotificationLetters = $investigationCommencementNotificationLetters->where('spdp_date', '>=', date('Y-m-d H:i:s', strtotime($startDocumentDate)));
            } else if (!empty($endDocumentDate)) {
                $investigationCommencementNotificationLetters = $investigationCommencementNotificationLetters->where('spdp_date', '<=', date('Y-m-d H:i:s', strtotime($endDocumentDate)));
            }
            $investigationCommencementNotificationLetters =  $investigationCommencementNotificationLetters->paginate($perPage, ['*'], 'page', $page);

            $investigationCommencementNotificationLettersTotal = $investigationCommencementNotificationLetters->total();
            $investigationCommencementNotificationLettersTotalPage = $investigationCommencementNotificationLetters->lastPage();

            // Packing data
            $arrayKey = 0;

            foreach($investigationCommencementNotificationLetters as $investigationCommencementNotificationLetter){

                $investigationCommencementNotificationLetterArray = $investigationCommencementNotificationLetter->toArray();
                
                $accident = (!empty($investigationCommencementNotificationLetterArray['accident'])) ? $investigationCommencementNotificationLetterArray['accident'] : null;
                $suspects = (!empty($accident)) ? ((!empty($accident['suspect'])) ? $accident['suspect'] : null) : null;
                $authorizedSignatory = (!empty($investigationCommencementNotificationLetterArray['authorized_signatory'])) ? $investigationCommencementNotificationLetterArray['authorized_signatory'] : null;
                $resortPolice = (!empty($accident)) ? ((!empty($accident['polres'])) ? $accident['polres'] : null) : null;

                foreach($suspects as $suspect){
                    $documentSuspect[] = [
                        "Nama" => $suspect['name'], 
                        "TempatLahir" => $suspect['birth_place'], 
                        "TanggalLahir" => ($suspect['birth_date']) ? date('Y-m-d H:i:s', strtotime($suspect['birth_date'])) : null, 
                        "IdJenisKelamin" => $suspect['gender'], 
                        "Alamat" => $suspect['address'], 
                        "IdPendidikan" => $suspect['education'], 
                        "IdPekerjaan" => $suspect['occupation'], 
                        "GelarDepan" => null, 
                        "GelarBelakang" => null, 
                        "NamaBapak" => $suspect['father_name'], 
                        "NamaIbu" => $suspect['mother_name'], 
                        "IdAgama" => $suspect['religion'], 
                        "IdStatusPerkawinan" => $suspect['marital_status'], 
                        "IdKewarganegaraan" => null, 
                        "IdJenisIdentitas" => $suspect['identity_type'], 
                        "NomorIdentitas" => strval($suspect['identity_number']), 
                        "UmurSaatLK" => ($suspect['birth_date']) ? Carbon::parse($suspect['birth_date'])->diffInYears(Carbon::now()) : null, 
                        "NamaAlias" => null, 
                        "Status" => "2",
                    ];
                }
                
                $documentSignatory[0] = [
                    "Nama" => ($authorizedSignatory) ? $authorizedSignatory['first_title'] . " " . strtoupper($authorizedSignatory['first_name'] . " " . $authorizedSignatory['last_name']) . ", " . $authorizedSignatory['last_title'] : null,
                    "Pangkat" => ($authorizedSignatory) ? strtoupper($authorizedSignatory['rank_id']) : null,
                    "NRP" => ($authorizedSignatory) ? strval($authorizedSignatory['register_number']) : null, 
                    "Jabatan" => ($authorizedSignatory) ? strtoupper($authorizedSignatory['position_id']) : null,
                ];

                $responseData[$arrayKey]  = [
                    "Id" => $investigationCommencementNotificationLetter->id,
                    "Id_Sprindik" => $investigationCommencementNotificationLetter->id_sprindik,
                    "LaporanPolisiID" => ($accident) ? $accident['id'] : null,
                    "DorsID" => ($accident) ? strval($accident['dors_id']) : null,
                    "NoSurat" => $investigationCommencementNotificationLetter->no_spdp,
                    "Tanggal" => ($investigationCommencementNotificationLetter->spdp_date) ? date('Y-m-d H:i:s', strtotime($investigationCommencementNotificationLetter->spdp_date)) : null,
                    "KejaksaanId" => $investigationCommencementNotificationLetter->kejaksaan_id,
                    "Data_Tersangka" => $documentSuspect,
                    "NamaPenerima" => $investigationCommencementNotificationLetter->endorsee_name,
                    "Lampiran" => $investigationCommencementNotificationLetter->lampiran,
                    "Tembusan" => [
                        $investigationCommencementNotificationLetter->tembusan,
                    ],
                    "LokasiDibuat" => $resortPolice['spptti_id'],
                    "PengadilanId" => $investigationCommencementNotificationLetter->pengadilan_id,
                    "Id_Springas" => $investigationCommencementNotificationLetter->id_springas,
                    "PejabatPenandatanganDokumen" => $documentSignatory,
                    "Attachment" => null,
                    "AttachmentMimeType" => "application/pdf",
                    "AttachmentExtension" => ".pdf",
                    "CreatedDate" => ($investigationCommencementNotificationLetter->created_at) ? date('Y-m-d H:i:s', strtotime($investigationCommencementNotificationLetter->created_at)) : null,
                    "CreatedBy" => ($investigationCommencementNotificationLetter->Created_By) ? strval($investigationCommencementNotificationLetter->Created_By) : null,
                    "UpdatedDate" => ($investigationCommencementNotificationLetter->updated_at) ? date('Y-m-d H:i:s', strtotime($investigationCommencementNotificationLetter->updated_at)) : null,
                    "UpdatedBy" => ($investigationCommencementNotificationLetter->Updated_By) ? strval($investigationCommencementNotificationLetter->Updated_By) : null,
                ];

                // Update is_itegrated and integrated_at
                $investigationCommencementNotificationLetterId = $investigationCommencementNotificationLetter->id;
                DB::table('spdpp')
                    ->where('id', $investigationCommencementNotificationLetterId)
                    ->update([
                        'integrated_at' => date('Y-m-d H:i:s'),
                ]);

                $arrayKey++;
            }

            // Check if data array is empty result
            if ($investigationCommencementNotificationLetters->isEmpty()) {
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

            // Return Result JSON
            return response()->json([
                "code" => "200",
                "status" => "OK",
                "message" => "Success",
                "pagination" => [
                    "Page" => $page,
                    "TotalData" => $investigationCommencementNotificationLettersTotal,
                    "TotalPage" => $investigationCommencementNotificationLettersTotalPage,
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

    private function collectionData(){
        $start_date = Carbon::create(2022, 1, 1);
        $end_date = Carbon::create(2022, 12, 31);
        $data = [];

        $no = 1;
        for ($date = $start_date; $date <= $end_date; $date->modify('+1 day')) {
            $data[] = [
                "Id" => Str::uuid(),
                "Id_Sprindik" => Str::uuid(),
                "LaporanPolisiID" => Str::uuid(),
                "DorsID" => strval(rand(1000000,9999999)),
                "NoSurat" => "SURAT/SPDP/" . $no . "/2022",
                "Tanggal" => $date->format('Y-m-d H:i:s'),
                "KejaksaanId" => 4,
                "Data_Tersangka" => [
                    [
                        "Nama" => "Andi Santoso Candra",
                        "TempatLahir" => "Jakarta",
                        "TanggalLahir" => "1990-01-01",
                        "IdJenisKelamin" => 1,
                        "Alamat" => "Jl. Merdeka No. 1",
                        "IdPendidikan" => 3,
                        "IdPekerjaan" => 5,
                        "GelarDepan" => "",
                        "GelarBelakang" => "",
                        "NamaBapak" => "Budi Santoso",
                        "NamaIbu" => "Siti Vivi",
                        "IdAgama" => 2,
                        "IdStatusPerkawinan" => 2,
                        "IdKewarganegaraan" => 1,
                        "IdJenisIdentitas" => 1,
                        "NomorIdentitas" => "9283741234567890",
                        "UmurSaatLK" => 32,
                        "NamaAlias" => "ASC",
                        "Status" => 2,
                    ],
                    [
                        "Nama" => "Budi Raharjo",
                        "TempatLahir" => "Bandung",
                        "TanggalLahir" => "1985-05-15",
                        "IdJenisKelamin" => 1,
                        "Alamat" => "Jl. Kemuning No. 10",
                        "IdPendidikan" => 2,
                        "IdPekerjaan" => 3,
                        "GelarDepan" => "",
                        "GelarBelakang" => "",
                        "NamaBapak" => "Joko Tjandra",
                        "NamaIbu" => "Tuti Yulianti",
                        "IdAgama" => 1,
                        "IdStatusPerkawinan" => 1,
                        "IdKewarganegaraan" => 1,
                        "IdJenisIdentitas" => 1,
                        "NomorIdentitas" => "234567890",
                        "UmurSaatLK" => 36,
                        "NamaAlias" => "BR",
                        "Status" => 2,
                    ],
                ],
                "NamaPenerima" => "KEJAKSAAN NEGERI JAKARTA SELATAN",
                "Lampiran" => "1",
                "Tembusan" => [
                    "Ketua Pengadilan Negeri Jakarta Selatan",
                    "Ketua Kejaksaan Tinggi Jakarta",
                ],
                "LokasiDibuat" => 1,
                "PengadilanId" => 45,
                "Id_Springas" => "a163b4ec-1f38-4c56-88b3-e8b2d67e203e",
                "PejabatPenandatanganDokumen" => [
                    [
                        "Nama" => "SUGADRI S.I.K", 
                        "Pangkat" => "KOMPOL",
                        "NRP" => "77061000", 
                        "Jabatan" => "KASAT LANTAS",
                    ]
                ],
                "Attachment" => base64_encode(file_get_contents(public_path('file/hello_world.pdf'))),
                "AttachmentMimeType" => "application/pdf",
                "AttachmentExtension" => ".pdf",
                "CreatedDate" => $date->format('Y-m-d H:i:s'),
                "CreatedBy" => "ANDRE",
                "UpdatedDate" => null,
                "UpdatedBy" => null,
            ];

            $no++;
        }

        return collect($data);
    }
}
