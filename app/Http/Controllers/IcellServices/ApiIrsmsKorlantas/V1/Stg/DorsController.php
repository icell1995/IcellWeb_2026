<?php

namespace App\Http\Controllers\IcellServices\ApiIrsmsKorlantas\V1\Stg;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

use App\Models\Stg\DorsAccident;
use App\Models\Stg\DorsCrimeArticle;
use App\Models\Stg\DorsEvidence;
use App\Models\Stg\DorsReportedPerson;
use App\Models\Stg\DorsVictim;
use App\Models\Stg\DorsWitness;

use App\Services\IcellServices\ApiIrsmsKorlantas\LogService;
use App\Services\IcellServices\ApiIrsmsKorlantas\UtilityService;

class DorsController extends Controller
{
    public function store(Request $request)
    {
        $requestDecodeJson = json_decode(json_encode($request->all(), true), true);

        $logService = new LogService();
        $utilityService = new UtilityService();

        $mode = $request->mode;
        $requestPath = $request->path();
        $requestMethod = $request->method();
        
        DB::beginTransaction();
        try{
            $accident = $requestDecodeJson;
            $crimeArticles = $requestDecodeJson['tindak_pidana'];
            $evidences = $requestDecodeJson['barang_bukti'];
            $reportedPersons = $requestDecodeJson['terlapor'];
            $witnesses = $requestDecodeJson['saksi'];
            $victims = $requestDecodeJson['korban'];

            $dorsAccidentId = Str::uuid();

            //dors_accidents
            DorsAccident::updateOrCreate(
                [
                    'dors_id' => $accident['dors_id']
                ],
                [
                'id' => $dorsAccidentId,
    
                'no_lp' => $accident['no_lp'] ?? null,
                'nrp_pembuat' => $accident['nrp_pembuat'] ?? null,
                'nama_pembuat' => $accident['nama_pembuat'] ?? null,
                'pangkat_pembuat' => $accident['pangkat_pembuat'] ?? null,
                'nrp_penerima' => $accident['nrp_penerima'] ?? null,
                'id_polda' => $accident['id_polda'] ?? null,
                'id_polres' => $accident['id_polres'] ?? null,
                'id_polsek' => $accident['id_polsek'] ?? null,
                'tgl_laporan' =>  (!empty($accident['tgl_laporan'])) ? Carbon::parse($accident['tgl_laporan'])->format('Y-m-d H:i:s') : null,
                'waktu_kejadian' => (!empty($accident['waktu_kejadian'])) ? Carbon::parse($accident['waktu_kejadian'])->format('Y-m-d H:i:s') : null,
                'waktu_kejadian_faktual' => $accident['waktu_kejadian_faktual'] ?? null,
                'tempat_kejadian' => $accident['tempat_kejadian'] ?? null,
                'apa_terjadi' => $accident['apa_terjadi'] ?? null,
                'bagaimana_terjadi' => $accident['bagaimana_terjadi'] ?? null,
                'pasal_kamtibmas' => $accident['pasal_kamtibmas'] ?? null,
                'tkp_id_kota' => $accident['tkp_id_kota'] ?? null,
                'tkp_id_provinsi' => $accident['tkp_id_provinsi'] ?? null,
                'tkp_id_kecamatan' => $accident['tkp_id_kecamatan'] ?? null,
                'tkp_id_desa' => $accident['tkp_id_desa'] ?? null,
                'kerugian' => $accident['kerugian'] ?? null,
                'tindakan_diambil' => $accident['tindakan_diambil'] ?? null,
                'satuan' => $accident['satuan'] ?? null,
                'kategori_lokasi' => $accident['kategori_lokasi'] ?? null,
                'id_satker' => $accident['id_satker'] ?? null,
                'dors_id' => $accident['dors_id'] ?? null,
                'uraian_kejadian' => $accident['uraian_kejadian'] ?? null,
                'kesimpulan_sementara' => $accident['kesimpulan_sementara'] ?? null,
            ]);

            //log DorsAccident
            $logService->post(
                request: $request, 
                classModel: get_class(new DorsAccident()),
                code: 200, 
                status: 'Success',
                mode: $mode,
                endpoint: $requestPath,
                method: $requestMethod,
                message: 'Successfully inserted',
                data: [
                    'dors_id' => $accident['dors_id'] ?? null,
                    'accident_number' => $accident['no_lp'] ?? null
                ]
            );

            //dors_crime_articles
            DorsCrimeArticle::where('dors_id', $accident['dors_id'])->delete();
            foreach($crimeArticles as $crimeArticle){
                DorsCrimeArticle::create([
                    'id' => Str::uuid(),

                    'dors_accident_id' => $dorsAccidentId,
                    'id_uu' => $crimeArticle['id_uu'] ?? null,
                    'dors_id' => $crimeArticle['dors_id'] ?? null,
                    'pasal' => $crimeArticle['pasal'] ?? null,
                ]);
            }

            //log DorsCrimeArticle
            $logService->post(
                request: $request, 
                classModel: get_class(new DorsCrimeArticle()),
                code: 200, 
                status: 'Success',
                mode: $mode,
                endpoint: $requestPath,
                method: $requestMethod,
                message: 'Successfully inserted',
                data: [
                    'dors_id' => $accident['dors_id'] ?? null,
                    'accident_number' => $accident['no_lp'] ?? null
                ]
            );

            //dors_evidences
            DorsEvidence::where('dors_id', $accident['dors_id'])->delete();
            foreach($evidences as $evidence){
                DorsEvidence::create([
                    'id' => Str::uuid(),

                    'dors_accident_id' => $dorsAccidentId,
                    'kode' => $evidence['kode'] ?? null,
                    'kelompok' => $evidence['kelompok'] ?? null,
                    'jenis' => $evidence['jenis'] ?? null,
                    'bentuk' => $evidence['bentuk'] ?? null,
                    'satuan' => $evidence['satuan'] ?? null,
                    'jumlah' => $evidence['jumlah'] ?? null,
                    'keterangan' => $evidence['keterangan'] ?? null,
                    'ran_no_registrasi' => $evidence['ran_no_registrasi'] ?? null,
                    'ran_nama_pemilik' => $evidence['ran_nama_pemilik'] ?? null,
                    'ran_alamat' => $evidence['ran_alamat'] ?? null,
                    'ran_merk' => $evidence['ran_merk'] ?? null,
                    'ran_type' => $evidence['ran_type'] ?? null,
                    'ran_jenis' => $evidence['ran_jenis'] ?? null,
                    'ran_model' => $evidence['ran_model'] ?? null,
                    'ran_thn_pembuatan' => $evidence['ran_thn_pembuatan'] ?? null,
                    'ran_isi_silinder' => $evidence['ran_isi_silinder'] ?? null,
                    'ran_no_rangka' => $evidence['ran_no_rangka'] ?? null,
                    'ran_no_mesin' => $evidence['ran_no_mesin'] ?? null,
                    'ran_warna' => $evidence['ran_warna'] ?? null,
                    'ran_bahan_bakar' => $evidence['ran_bahan_bakar'] ?? null,
                    'ran_warna_tnkb' => $evidence['ran_warna_tnkb'] ?? null,
                    'ran_thn_registrasi' => $evidence['ran_thn_registrasi'] ?? null,
                    'ran_no_bpkb' => $evidence['ran_no_bpkb'] ?? null,
                    'dors_id' => $evidence['dors_id'] ?? null,
                ]);
            }

             //log DorsEvidence
             $logService->post(
                request: $request, 
                classModel: get_class(new DorsEvidence()),
                code: 200, 
                status: 'Success',
                mode: $mode,
                endpoint: $requestPath,
                method: $requestMethod,
                message: 'Successfully inserted',
                data: [
                    'dors_id' => $accident['dors_id'] ?? null,
                    'accident_number' => $accident['no_lp'] ?? null
                ]
            );

            //dors_reported_persons
            DorsReportedPerson::where('dors_id', $accident['dors_id'])->delete();
            foreach($reportedPersons as $reportedPerson){
                DorsReportedPerson::create([
                    'id' => Str::uuid(),

                    'dors_accident_id' => $dorsAccidentId,
                    'jenis_identitas' => $reportedPerson['jenis_identitas'] ?? null,
                    'nik' => $reportedPerson['nik'] ?? null,
                    'nama' => $reportedPerson['nama'] ?? null,
                    'pendidikan_terakhir' => $reportedPerson['pendidikan_terakhir'] ?? null,
                    'pekerjaan' => $reportedPerson['pekerjaan'] ?? null,
                    'suku' => $reportedPerson['suku'] ?? null,
                    'kewarganegaraan' => $reportedPerson['kewarganegaraan'] ?? null,
                    'alamat' => $reportedPerson['alamat'] ?? null,
                    'alamat_non_nkri' => $reportedPerson['alamat_non_nkri'] ?? null,
                    'no_hp' => $reportedPerson['no_hp'] ?? null,
                    'gender' => $reportedPerson['gender'] ?? null,
                    'tempat_lahir' => $reportedPerson['tempat_lahir'] ?? null,
                    'tgl_lahir' => (!empty($reportedPerson['tgl_lahir'])) ? Carbon::parse($reportedPerson['tgl_lahir'])->format('Y-m-d H:i:s') : null,
                    'agama' => $reportedPerson['agama'] ?? null,
                    'status_terlapor' => $reportedPerson['status_terlapor'] ?? null,
                    'dors_id' => $reportedPerson['dors_id'] ?? null,
                ]);
            }

            //log DorsReportedPerson
            $logService->post(
                request: $request, 
                classModel: get_class(new DorsReportedPerson()),
                code: 200, 
                status: 'Success',
                mode: $mode,
                endpoint: $requestPath,
                method: $requestMethod,
                message: 'Successfully inserted',
                data: [
                    'dors_id' => $accident['dors_id'] ?? null,
                    'accident_number' => $accident['no_lp'] ?? null
                ]
            );

            //dors_witnesses
            DorsWitness::where('dors_id', $accident['dors_id'])->delete();
            foreach($witnesses as $witness){
                DorsWitness::create([
                    'id' => Str::uuid('id'),

                    'dors_accident_id' => $dorsAccidentId,
                    'jenis_identitas' => $witness['jenis_identitas'] ?? null,
                    'nik' => $witness['nik'] ?? null,
                    'nama' => $witness['nama'] ?? null,
                    'pendidikan_terakhir' => $witness['pendidikan_terakhir'] ?? null,
                    'pekerjaan' => $witness['pekerjaan'] ?? null,
                    'suku' => $witness['suku'] ?? null,
                    'kewarganegaraan' => $witness['kewarganegaraan'] ?? null,
                    'alamat' => $witness['alamat'] ?? null,
                    'alamat_non_nkri' => $witness['alamat_non_nkri'] ?? null,
                    'no_hp' => $witness['no_hp'] ?? null,
                    'gender' => $witness['gender'] ?? null,
                    'tempat_lahir' => $witness['tempat_lahir'] ?? null,
                    'tgl_lahir' => (!empty($witness['tgl_lahir'])) ? Carbon::parse($witness['tgl_lahir'])->format('Y-m-d H:i:s') : null,
                    'agama' => $witness['agama'] ?? null,
                    'dors_id' => $witness['dors_id'] ?? null,
                ]);
            }

            //log DorsWitness
            $logService->post(
                request: $request, 
                classModel: get_class(new DorsWitness()),
                code: 200, 
                status: 'Success',
                mode: $mode,
                endpoint: $requestPath,
                method: $requestMethod,
                message: 'Successfully inserted',
                data: [
                    'dors_id' => $accident['dors_id'] ?? null,
                    'accident_number' => $accident['no_lp'] ?? null
                ]
            );

            //dors_victims
            DorsVictim::where('dors_id', $accident['dors_id'])->delete();
            foreach($victims as $victim){
                DorsVictim::create([
                    'id' => Str::uuid(),

                    'dors_accident_id' => $dorsAccidentId,
                    'jenis_korban' => $victim['jenis_korban'] ?? null,
                    'jenis_identitas' => $victim['jenis_identitas'] ?? null,
                    'nik' => $victim['nik'] ?? null,
                    'nama' => $victim['nama'] ?? null,
                    'pendidikan_terakhir' => $victim['pendidikan_terakhir'] ?? null,
                    'pekerjaan' => $victim['pekerjaan'] ?? null,
                    'suku' => $victim['suku'] ?? null,
                    'kewarganegaraan' => $victim['kewarganegaraan'] ?? null,
                    'alamat' => $victim['alamat'] ?? null,
                    'alamat_non_nkri' => $victim['alamat_non_nkri'] ?? null,
                    'no_hp' => $victim['no_hp'] ?? null,
                    'gender' => $victim['gender'] ?? null,
                    'tempat_lahir' => $victim['tempat_lahir'] ?? null,
                    'tgl_lahir' => (!empty($victim['tgl_lahir'])) ? Carbon::parse($victim['tgl_lahir'])->format('Y-m-d H:i:s') : null,
                    'agama' => $victim['agama'] ?? null,
                    'status_korban' => $victim['status_korban'] ?? null,
                    'nomor_visum' => $victim['nomor_visum'] ?? null,
                    'dors_id' => $victim['dors_id'] ?? null,
                ]);
            }

            //log DorsVictim
            $logService->post(
                request: $request, 
                classModel: get_class(new DorsVictim()),
                code: 200, 
                status: 'Success',
                mode: $mode,
                endpoint: $requestPath,
                method: $requestMethod,
                message: 'Successfully inserted',
                data: [
                    'dors_id' => $accident['dors_id'] ?? null,
                    'accident_number' => $accident['no_lp'] ?? null
                ]
            );

            DB::commit();

            return $this->successResponse(
                message: 'Data berhasil ditambahkan',
                totalDataReceived: 1,
                data: null
            );
        }catch(\Exception $e){
            DB::rollBack();

            $exceptionMessage = $e->getMessage();
            $status = 'Error';
            $code = 500;

            //log
            $logService->post(
                request: $request, 
                classModel: null,
                code: $code, 
                status: $status,
                mode: $mode,
                endpoint: $requestPath,
                method: $requestMethod,
                message: $exceptionMessage,
                data: [
                    'dors_id' => $requestDecodeJson['dors_id'] ?? null,
                    'accident_number' => $requestDecodeJson['no_lp'] ?? null
                ]
            );

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
    private function successResponse($data = [], $message, $totalDataReceived)
    {
        return response()->json([
            'code' => 200,
            'status' => 'OK',
            'message' => $message,
            'totalDataReceived' => $totalDataReceived,
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
            'data' => null,
        ]);
    }
}
