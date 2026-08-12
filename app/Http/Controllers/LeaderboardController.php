<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

use App\Models\Lib\Police;

class LeaderboardController extends Controller
{
    public function index()
    {
        $now = date('Y-m-d');

        $caseResolutions = DB::table('lib.polices as xpolices')->select('xpolices.id as polres_id', 'xpolices.name as polres_name', 'ypolices.name as polda_name')
            ->selectRaw("SUM(CASE WHEN accidents.selra_flag = 'S0101' AND accidents.accident_date BETWEEN '2022-01-01' AND '$now' THEN 1 ELSE 0 END) as p21")
            ->selectRaw("SUM(CASE WHEN accidents.selra_flag = 'S0102' AND accidents.accident_date BETWEEN '2022-01-01' AND '$now' THEN 1 ELSE 0 END) as sp3")
            ->selectRaw("SUM(CASE WHEN accidents.selra_flag = 'S0103' AND accidents.accident_date BETWEEN '2022-01-01' AND '$now' THEN 1 ELSE 0 END) as diversi")
            ->selectRaw("SUM(CASE WHEN accidents.selra_flag = 'S0108' AND accidents.accident_date BETWEEN '2022-01-01' AND '$now' THEN 1 ELSE 0 END) as sp2lid")
            ->leftJoin('accidents', 'xpolices.id', '=', 'accidents.police_id')
            ->join('lib.polices as ypolices', 'xpolices.parent_id', '=', 'ypolices.id')
            ->where('xpolices.is_active', true)
            ->where('xpolices.class', 'RESOR')
            ->groupBy('xpolices.id', 'xpolices.name', 'ypolices.name')
            ->orderBy('xpolices.id', 'asc')
            ->get();

        $responseCases = Http::withHeaders([
            'Key' => 'Hy6d3K1d93LOHRfbeE0KKly1YK9t4YdGsbNDEvyxAYI=icell',
            'Content-Type' => 'application/json'
        ])
        ->withQueryParameters([
            'start_date' => '2022-01-01',
            'end_date' => $now
        ])
            ->get('https://irsms.korlantas.polri.go.id/irsmsapi/api/getTotalLaka')
            ->json();
        $cases = $responseCases['result'];
        
        $caseCollections = collect($cases);
        $caseResolutionCollections = collect($caseResolutions);

        $leaderboardItems = $caseCollections->map(function ($item) use ($caseResolutionCollections) {
            $match = $caseResolutionCollections->firstWhere('polres_id', $item['polres']);
            
            if ($match) {
                $p21 = $match->p21 ?? 0;
                $sp3 = $match->sp3 ?? 0;
                $sp2lid = $match->sp2lid ?? 0;
                $diversi = $match->diversi ?? 0;
                $polda_name = $match->polda_name;
                $polres_name = $match->polres_name;

                $total = $p21 + $sp3 + $sp2lid + $diversi;
                $jumlah_laka = $item['jumlah_laka'];
                
                return array_merge($item, [
                    'polda_name' => $polda_name,
                    'polres_name' => $polres_name,
                    'p21' => $p21 ?? 0,
                    'sp3' => $sp3 ?? 0,
                    'sp2lid' => $sp2lid ?? 0,
                    'diversi' => $diversi ?? 0,
                    'total' => $total,
                    'percentage_p21' => ($jumlah_laka != 0) ? (($p21/$jumlah_laka) * 100) : 0,
                    'percentage_sp3' => ($jumlah_laka != 0) ? (($sp3/$jumlah_laka) * 100) : 0,
                    'percentage_sp2lid' => ($jumlah_laka != 0) ? (($sp2lid/$jumlah_laka) * 100) : 0,
                    'percentage_diversi' => ($jumlah_laka != 0) ? (($diversi/$jumlah_laka) * 100) : 0,
                    'percentage_total' => ($jumlah_laka != 0) ? (($total/$jumlah_laka) * 100) : 0,
                ]);
            }
            
            return $item;
        });

        $viewData = [
            'leaderboardItems' => $leaderboardItems,
        ];

        return view('leaderboard.index', $viewData);
    }
}
