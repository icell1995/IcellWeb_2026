<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Lib\Police;
use App\Models\Polda;

class CommanderWishController extends Controller
{
    public function index(Request $request)
    {
        $startAccidentDate = $request->query('startAccidentDate');
        $endAccidentDate = $request->query('endAccidentDate');
        $regionalPoliceId = $request->query('regionalPolice');
        $resortPoliceId = $request->query('resortPolice');

        $performances = [];
        if(!empty($startAccidentDate) && !empty($endAccidentDate)){
           
            $performances = DB::table('polda')
                ->selectRaw('polda.name AS nama_polda')
                ->selectRaw('MAX(COALESCE(total_p21.total, 0)) AS p21')
                ->selectRaw('MAX(COALESCE(total_sp3.total, 0)) AS sp3')
                ->selectRaw('MAX(COALESCE(total_diversi.total, 0)) AS diversi')
                ->selectRaw('MAX(COALESCE(total_pom_tni.total, 0)) AS pom_tni')
                ->selectRaw('MAX(COALESCE(total_sp2lid.total, 0)) AS sp2lid')
                ->selectRaw("ROUND((
                    CASE WHEN MAX(splidik.document_number) IS NOT NULL AND MAX(splidik.rejected_at) IS NULL AND MAX(splidik.status_id) = '86' THEN 1 ELSE 0 END +
                    CASE WHEN MAX(spsidik.document_number) IS NOT NULL AND MAX(spsidik.rejected_at) IS NULL AND MAX(spsidik.status_id) = '86' THEN 1 ELSE 0 END +
                    CASE WHEN MAX(sptugas.document_number) IS NOT NULL AND MAX(sptugas.rejected_at) IS NULL AND MAX(sptugas.status_id) = '86' THEN 1 ELSE 0 END +
                    CASE WHEN MAX(lhgp.case_degree_invite_reference) IS NOT NULL AND MAX(lhgp.rejected_at) IS NULL AND MAX(lhgp.status_id) = '86' THEN 1 ELSE 0 END +
                    CASE WHEN MAX(staptsk.document_number) IS NOT NULL AND MAX(staptsk.rejected_at) IS NULL AND MAX(staptsk.status_id) = '86' THEN 1 ELSE 0 END +
                    CASE WHEN MAX(spdp.document_number) IS NOT NULL AND MAX(spdp.rejected_at) IS NULL AND MAX(spdp.status_id) = '86' THEN 1 ELSE 0 END
                ) / 6.0 * 100, 2) AS persentase_keberhasilan")
                ->leftJoin('polres', 'polda.id', '=', 'polres.polda_id')
                ->leftJoin('accidents', 'polres.id', '=', 'accidents.polres_id')
                ->leftJoin('doc.surat_perintah_penyelidikan_documents as splidik', 'accidents.id', '=', 'splidik.accident_id')
                ->leftJoin('doc.surat_perintah_penyidikan_documents as spsidik', 'accidents.id', '=', 'spsidik.accident_id')
                ->leftJoin('doc.surat_perintah_tugas_documents as sptugas', 'accidents.id', '=', 'sptugas.accident_id')
                ->leftJoin('doc.laporan_hasil_gelar_perkara_documents as lhgp', 'accidents.id', '=', 'lhgp.accident_id')
                ->leftJoin('doc.surat_ketetapan_tentang_penetapan_tersangka_documents as staptsk', 'accidents.id', '=', 'staptsk.accident_id')
                ->leftJoin('doc.surat_pemberitahuan_dimulainya_penyidikan_documents as spdp', 'accidents.id', '=', 'spdp.accident_id')
                // Tambahkan LEFT JOIN dan subquery untuk menghitung total_p21, total_sp3, dst.
                // Sesuaikan subquery dengan selera_flag yang sesuai
                ->leftJoin(DB::raw("(SELECT polres.id AS id, COUNT(accidents.id) AS total
                    FROM polres
                    JOIN accidents ON polres.id = accidents.polres_id
                    WHERE accident_date BETWEEN '" . $startAccidentDate . "' AND '" . $endAccidentDate . "'
                    AND selra_flag = 'S0101'
                    AND polres.state <> 0
                    AND accidents.state <> 0
                    GROUP BY 1) AS total_p21"), 'polres.id', '=', 'total_p21.id')
                ->leftJoin(DB::raw("(SELECT polres.id AS id, COUNT(accidents.id) AS total
                    FROM polres
                    JOIN accidents ON polres.id = accidents.polres_id
                    WHERE accident_date BETWEEN '" . $startAccidentDate . "' AND '" . $endAccidentDate . "'
                    AND selra_flag = 'S0102'
                    AND polres.state <> 0
                    AND accidents.state <> 0
                    GROUP BY 1) AS total_sp3"), 'polres.id', '=', 'total_sp3.id')
                // Lanjutkan dengan LEFT JOIN dan subquery untuk total_diversi, total_pom_tni, total_sp2lid
                ->leftJoin(DB::raw("(SELECT polres.id AS id, COUNT(accidents.id) AS total
                    FROM polres
                    JOIN accidents ON polres.id = accidents.polres_id
                    WHERE accident_date BETWEEN '" . $startAccidentDate . "' AND '" . $endAccidentDate . "'
                    AND selra_flag = 'S0103'
                    AND polres.state <> 0
                    AND accidents.state <> 0
                    GROUP BY 1) AS total_diversi"), 'polres.id', '=', 'total_diversi.id')
                ->leftJoin(DB::raw("(SELECT polres.id AS id, COUNT(accidents.id) AS total
                    FROM polres
                    JOIN accidents ON polres.id = accidents.polres_id
                    WHERE accident_date BETWEEN '" . $startAccidentDate . "' AND '" . $endAccidentDate . "'
                    AND selra_flag = 'S0104'
                    AND polres.state <> 0
                    AND accidents.state <> 0
                    GROUP BY 1) AS total_pom_tni"), 'polres.id', '=', 'total_pom_tni.id')
                ->leftJoin(DB::raw("(SELECT polres.id AS id, COUNT(accidents.id) AS total
                    FROM polres
                    JOIN accidents ON polres.id = accidents.polres_id
                    WHERE accident_date BETWEEN '" . $startAccidentDate . "' AND '" . $endAccidentDate . "'
                    AND selra_flag = 'S0108'
                    AND polres.state <> 0
                    AND accidents.state <> 0
                    GROUP BY 1) AS total_sp2lid"), 'polres.id', '=', 'total_sp2lid.id')
                ->where('polda.state', '<>', 0)
                ->where('polres.state', '<>', 0)
                ->whereNotIn('polda.id', ['90', '99'])
                ->whereNotIn('accidents.selra_flag', ['S0107', 'S0108'])
                ->groupBy('polda.name')
                ->orderBy('polda.name', 'ASC');

            if(!empty($regionalPoliceId)){
                $performances = $performances->where('polda.id', $regionalPoliceId);
            }

            $performances = $performances->get();
        }
        
        $regionalPolices = Police::where('class', 'DAERAH')
        ->where('is_active', true)
        ->whereNotIn('id', ['90', '99', '80'])
        ->orderBy('sort', 'asc')
        ->get();

        $urlParameters = [
            'startAccidentDate' => $startAccidentDate,
            'endAccidentDate' => $endAccidentDate,
            'regionalPoliceId' => $regionalPoliceId,
            'resortPoliceId' => $resortPoliceId
        ];

        $viewData = [
            'performances' => $performances,
            'regionalPolices' => $regionalPolices,
            'urlParameters' => $urlParameters
        ];

        return view('commander-wish.index', $viewData);
    }

    public function getResortPolices(Request $request)
    {
        $regionalPoliceId = $request->regionalPoliceId;
        $resortPolices = Police::where('parent_id', $regionalPoliceId)
            ->where('class', 'RESOR')
            ->where('is_active', true)
            ->orderBy('sort', 'asc')
            ->get();

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'data' => $resortPolices
        ], 200);
    }
}
