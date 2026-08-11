<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class IndeksGakkumController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->input('year', date('Y'));
        $quarter = $request->input('quarter', 'all');

        // Tentukan batas tanggal berdasarkan Triwulan (Kumulatif: Jan - Akhir Triwulan)
        $endMonth = 12;
        if ($quarter == '1') $endMonth = 3;
        if ($quarter == '2') $endMonth = 6;
        if ($quarter == '3') $endMonth = 9;
        
        $startDate = "$year-01-01";
        $endDate = Carbon::createFromDate($year, $endMonth, 1)->endOfMonth()->format('Y-m-d');

        // P21, SP3, Diversi, POM/TNI, SP2LID (tanpa S0106/RJ)
        $resolvedFlags = ['S0101', 'S0102', 'S0103', 'S0104', 'S0108'];

        // =============================================
        // TB: Total Laka Tahun Berjalan dari API IRSMS
        // =============================================
        $tb = 0;
        try {
            $apiResponse = Http::withHeaders([
                'Key' => 'Hy6d3K1d93LOHRfbeE0KKly1YK9t4YdGsbNDEvyxAYI=icell',
                'Content-Type' => 'application/json'
            ])->withQueryParameters([
                'start_date' => $startDate,
                'end_date'   => $endDate
            ])->get('https://irsms.korlantas.polri.go.id/irsmsapi/api/getTotalLaka')->json();

            $dataApi = $apiResponse['result'] ?? [];
            foreach ($dataApi as $item) {
                $tb += (int) ($item['jumlah_laka'] ?? 0);
            }
        } catch (\Exception $e) {
            // Jika API gagal, fallback ke database lokal
            $tb = DB::table('accidents')
                ->whereBetween('accident_date', [$startDate, $endDate])
                ->count();
        }

        // =============================================
        // KB: Selra Tahun Berjalan (Laka tahun ini, diselesaikan tahun ini)
        // Sumber: tabel accidents, laka terjadi di $year dan selra_flag resolved
        // =============================================
        $kbQuery = DB::table('accidents as a')
            ->join('accident_resolutions as ar', 'ar.accident_id', '=', 'a.id')
            ->whereBetween('a.accident_date', [$startDate, $endDate])
            ->whereIn('a.selra_flag', $resolvedFlags)
            ->whereBetween('ar.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->where('a.state', '<>', 0);

        $kb = $kbQuery->count(DB::raw('DISTINCT a.id'));

        // Rincian per jenis selra untuk KB
        $kbDetail = DB::table('accidents as a')
            ->join('accident_resolutions as ar', 'ar.accident_id', '=', 'a.id')
            ->whereBetween('a.accident_date', [$startDate, $endDate])
            ->whereIn('a.selra_flag', $resolvedFlags)
            ->whereBetween('ar.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->where('a.state', '<>', 0)
            ->select(DB::raw("
                COUNT(DISTINCT a.id) AS total,
                COUNT(DISTINCT CASE WHEN a.selra_flag = 'S0101' THEN a.id END) AS p21,
                COUNT(DISTINCT CASE WHEN a.selra_flag = 'S0102' THEN a.id END) AS sp3,
                COUNT(DISTINCT CASE WHEN a.selra_flag = 'S0103' THEN a.id END) AS diversi,
                COUNT(DISTINCT CASE WHEN a.selra_flag = 'S0104' THEN a.id END) AS pom_tni,
                COUNT(DISTINCT CASE WHEN a.selra_flag = 'S0108' THEN a.id END) AS sp2lid
            "))
            ->first();

        // =============================================
        // TS: Tunggakan Selra (Laka tahun sebelumnya, diselesaikan di tahun berjalan)
        // Sumber: query yang diberikan user (accidents JOIN accident_resolutions)
        // =============================================
        $previousYear = (int) $year - 1;

        $tsQuery = DB::table('accidents as a')
            ->join('accident_resolutions as ar', 'ar.accident_id', '=', 'a.id')
            ->whereRaw("date_part('year', a.accident_date) = ?", [$previousYear])
            ->whereIn('a.selra_flag', $resolvedFlags)
            ->whereBetween('ar.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->where('a.state', '<>', 0);

        $ts = $tsQuery->count(DB::raw('DISTINCT a.id'));

        // Rincian per jenis selra untuk TS
        $tsDetail = DB::table('accidents as a')
            ->join('accident_resolutions as ar', 'ar.accident_id', '=', 'a.id')
            ->whereRaw("date_part('year', a.accident_date) = ?", [$previousYear])
            ->whereIn('a.selra_flag', $resolvedFlags)
            ->whereBetween('ar.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->where('a.state', '<>', 0)
            ->select(DB::raw("
                COUNT(DISTINCT a.id) AS total,
                COUNT(DISTINCT CASE WHEN a.selra_flag = 'S0101' THEN a.id END) AS p21,
                COUNT(DISTINCT CASE WHEN a.selra_flag = 'S0102' THEN a.id END) AS sp3,
                COUNT(DISTINCT CASE WHEN a.selra_flag = 'S0103' THEN a.id END) AS diversi,
                COUNT(DISTINCT CASE WHEN a.selra_flag = 'S0104' THEN a.id END) AS pom_tni,
                COUNT(DISTINCT CASE WHEN a.selra_flag = 'S0108' THEN a.id END) AS sp2lid
            "))
            ->first();

        // =============================================
        // Hitung Clearance Rate C = (KB + TS) / (TB + TS) * 100%
        // =============================================
        $clearanceRate = 0;
        if (($tb + $ts) > 0) {
            $clearanceRate = round((($kb + $ts) / ($tb + $ts)) * 100, 2);
        }

        // Kinerja Level
        $kinerjaLevel = 4;
        if ($clearanceRate >= 86) {
            $kinerjaLevel = 1;
        } elseif ($clearanceRate >= 71) {
            $kinerjaLevel = 2;
        } elseif ($clearanceRate >= 55) {
            $kinerjaLevel = 3;
        }

        return view('cms.indeks-gakkum.index', compact(
            'year', 'quarter', 'tb', 'kb', 'ts',
            'kbDetail', 'tsDetail',
            'clearanceRate', 'kinerjaLevel'
        ));
    }
}
