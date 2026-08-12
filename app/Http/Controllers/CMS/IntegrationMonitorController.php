<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class IntegrationMonitorController extends Controller
{
    /**
     * Menampilkan view utama untuk monitor integrasi.
     */
    public function index()
    {
        return view('cms.integration-monitor.index');
    }

    /**
     * Mengambil data log spesifik per aplikasi menggunakan DataTables.
     */
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

        // Ambil data sesuai aplikasi yang dipilih
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

        // Format hasil untuk response DataTable
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

            // Normalisasi status name
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
}
