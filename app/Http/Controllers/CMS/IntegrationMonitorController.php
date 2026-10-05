<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class IntegrationMonitorController extends Controller
{
    public function index()
    {
        return view('cms.integration-monitor.index');
    }

    public function getData(Request $request)
    {
        $appType = $request->input('app_type', 'tar'); // Default ke tar
        $filter = $request->input('filter', 'daily');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $now = Carbon::now();
        $dateStart = null;
        $dateEnd = null;

        if ($filter == 'daily') {
            $dateStart = $now->copy()->startOfDay();
            $dateEnd = $now->copy()->endOfDay();
        } elseif ($filter == 'weekly') {
            $dateStart = $now->copy()->startOfWeek();
            $dateEnd = $now->copy()->endOfWeek();
        } elseif ($filter == 'monthly') {
            $dateStart = $now->copy()->startOfMonth();
            $dateEnd = $now->copy()->endOfMonth();
        } elseif ($filter == 'custom' && $startDate && $endDate) {
            $dateStart = Carbon::createFromFormat('d-m-Y', $startDate)->startOfDay();
            $dateEnd = Carbon::createFromFormat('d-m-Y', $endDate)->endOfDay();
        } else {
            $dateStart = $now->copy()->startOfDay();
            $dateEnd = $now->copy()->endOfDay();
        }

        $logs = [];

        if ($appType === 'tar') {
            $logs = DB::table('log_api_tar_korlantas_transmit_accidents')
                ->selectRaw("'Success' as status, CONCAT('Transmit Accident ID: ', accident_id::text) as detail, ip_address, created_at")
                ->whereBetween('created_at', [$dateStart, $dateEnd])
                ->orderBy('created_at', 'desc')
                ->get();
        } elseif ($appType === 'irsms') {
            $logs = DB::table('log_api_irsms_korlantas_post_stg_dors_accidents')
                ->selectRaw("status, message as detail, ip_address, created_at")
                ->whereBetween('created_at', [$dateStart, $dateEnd])
                ->orderBy('created_at', 'desc')
                ->get();
        } elseif ($appType === 'divtik') {
            $logs = DB::table('log_api_divtik_polri')
                ->selectRaw("'Success' as status, 'Request Data ANEV' as detail, ip_address, created_at")
                ->whereBetween('created_at', [$dateStart, $dateEnd])
                ->orderBy('created_at', 'desc')
                ->get();
        } elseif ($appType === 'emp') {
            $logs = DB::table('history.document_api_sync_histories')
                ->selectRaw("'Success' as status, CONCAT('Sync for Accident ID: ', accident_id::text, ' (', COUNT(document_id), ' documents)') as detail, MAX(ip_address::text) as ip_address, MAX(created_at) as created_at")
                ->whereBetween('created_at', [$dateStart, $dateEnd])
                ->groupBy('accident_id')
                ->orderByRaw('MAX(created_at) desc')
                ->get();
        }

        $data = [];
        $no = 1;
        foreach ($logs as $log) {
            $badgeColor = 'success';
            $statusRaw = strtolower($log->status);
            
            if ($statusRaw === 'failed' || $statusRaw === 'error') {
                $badgeColor = 'danger';
            } elseif ($statusRaw !== 'success' && $log->status != '200') {
                $badgeColor = 'warning';
            }

            $statusName = $log->status;
            if ($statusName == '200') $statusName = 'Success';

            $data[] = [
                'no' => $no++,
                'status' => '<span class="badge bg-' . $badgeColor . ' px-3 rounded-pill shadow-sm">' . strtoupper($statusName) . '</span>',
                'detail' => $log->detail,
                'ip_address' => $log->ip_address ?? '-',
                'created_at' => Carbon::parse($log->created_at)->format('d M Y, H:i:s')
            ];
        }

        return response()->json([
            'data' => $data
        ]);
    }

    public function monthlyIndex()
    {
        return view('cms.integration-monitor.monthly');
    }

    public function getMonthlyData(Request $request)
    {
        $appType = $request->input('app_type', 'tar');
        $month = (int) $request->input('month', Carbon::now()->month);
        $year = (int) $request->input('year', Carbon::now()->year);

        $now = Carbon::now();

        if ($year == $now->year && $month > $now->month) {
            $month = $now->month;
        }
        $startOfMonth = Carbon::create($year, $month, 1)->startOfDay();
        $endOfPeriod = ($year == $now->year && $month == $now->month)
            ? $now->copy()->endOfDay()
            : $startOfMonth->copy()->endOfMonth()->endOfDay();

        $lastDay = ($year == $now->year && $month == $now->month)
            ? $now->day
            : $startOfMonth->daysInMonth;

        $aggregatedData = $this->getAggregatedLogs($appType, $startOfMonth, $endOfPeriod);

        $data = [];
        $totalLog = 0;
        $totalSuccess = 0;
        $totalFailed = 0;

        for ($day = 1; $day <= $lastDay; $day++) {
            $date = Carbon::create($year, $month, $day);
            $dateKey = $date->format('Y-m-d');

            $dayData = $aggregatedData->get($dateKey);

            $dayTotal = $dayData ? (int) $dayData->total_log : 0;
            $daySuccess = $dayData ? (int) $dayData->success_count : 0;
            $dayFailed = $dayData ? (int) $dayData->failed_count : 0;

            // Tentukan status
            if ($dayTotal === 0) {
                $status = 'NO DATA';
                $badgeClass = 'bg-secondary';
            } elseif ($dayFailed === 0) {
                $status = 'SUCCESS';
                $badgeClass = 'bg-success';
            } elseif ($daySuccess === 0) {
                $status = 'FAILED';
                $badgeClass = 'bg-danger';
            } else {
                $status = 'WARNING';
                $badgeClass = 'bg-warning text-dark';
            }

            $data[] = [
                'no' => $day,
                'tanggal' => $date->format('d M Y'),
                'total_log' => $dayTotal,
                'success' => $daySuccess,
                'failed' => $dayFailed,
                'status' => '<span class="badge ' . $badgeClass . ' px-3 rounded-pill shadow-sm">' . $status . '</span>',
                'status_raw' => $status,
            ];

            $totalLog += $dayTotal;
            $totalSuccess += $daySuccess;
            $totalFailed += $dayFailed;
        }

        $successRate = $totalLog > 0
            ? round(($totalSuccess / $totalLog) * 100, 2)
            : 0;

        return response()->json([
            'data' => $data,
            'summary' => [
                'total_hari' => $lastDay,
                'total_log' => $totalLog,
                'total_success' => $totalSuccess,
                'total_failed' => $totalFailed,
                'success_rate' => $successRate,
            ],
        ]);
    }

    private function getAggregatedLogs(string $appType, Carbon $startDate, Carbon $endDate)
    {
        $table = '';
        $statusCondition = '';

        switch ($appType) {
            case 'tar':
                $table = 'log_api_tar_korlantas_transmit_accidents';
                $statusCondition = "COUNT(*) as success_count, 0 as failed_count";
                break;

            case 'irsms':
                $table = 'log_api_irsms_korlantas_post_stg_dors_accidents';
                $statusCondition = "SUM(CASE WHEN LOWER(status) = 'success' OR status = '200' THEN 1 ELSE 0 END) as success_count, "
                    . "SUM(CASE WHEN LOWER(status) != 'success' AND status != '200' THEN 1 ELSE 0 END) as failed_count";
                break;

            case 'divtik':
                $table = 'log_api_divtik_polri';
                $statusCondition = "COUNT(*) as success_count, 0 as failed_count";
                break;

            case 'emp':
                $table = 'history.document_api_sync_histories';
                $statusCondition = "COUNT(*) as success_count, 0 as failed_count";
                break;

            default:
                return collect();
        }

        $results = DB::table($table)
            ->selectRaw("DATE(created_at) as log_date, COUNT(*) as total_log, {$statusCondition}")
            ->where('created_at', '>=', $startDate)
            ->where('created_at', '<=', $endDate)
            ->groupBy(DB::raw('DATE(created_at)'))
            ->get();

        return $results->keyBy('log_date');
    }
}
