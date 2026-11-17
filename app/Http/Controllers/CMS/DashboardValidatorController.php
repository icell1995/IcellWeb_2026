<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardValidatorController extends Controller
{
    public function index(Request $request)
    {
        // Hanya izinkan filter 'today', 'week', atau custom range
        $range = $request->range ?? 'week';

        $startDate = null;
        $endDate = null;

        if ($request->has('startDate') && $request->has('endDate')) {
            // Custom range filter
            $startDate = Carbon::parse($request->startDate)->startOfDay();
            $endDate = Carbon::parse($request->endDate)->endOfDay();
            $range = 'custom';
        } else {
            // Standar filter (today atau week)
            switch ($range) {
                case 'today':
                    $startDate = Carbon::today();
                    $endDate = Carbon::today()->endOfDay();
                    break;
                case 'week':
                default:
                    $startDate = Carbon::now()->startOfWeek();
                    $endDate = Carbon::now()->endOfWeek();
                    break;
            }
        }

        // Total validations for the week and today
        $totalValidationWeek = DB::table('log_case_document_validations')
            ->whereNotNull('approved_at')
            ->whereBetween('approved_at', [$startDate, $endDate])
            ->distinct('accident_id') // Count distinct accidents
            ->count('accident_id'); // Count distinct accidents

        $totalValidationToday = DB::table('log_case_document_validations')
            ->whereNotNull('approved_at')
            ->whereDate('approved_at', Carbon::today())
            ->distinct('accident_id')
            ->count('accident_id');

        // Pending validations - Menggunakan data dari log dengan status pending
        // Asumsi: dokumen yang belum divalidasi memiliki approved_at null dan rejected_at null
        $rejectedValidation = DB::table('log_case_document_validations')
            ->whereNotNull('rejected_at')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->distinct('accident_id') // Count distinct accidents that were rejected
            ->count('accident_id'); // Count distinct accidents that were rejected

        $pendingValidationToday = DB::table('doc.surat_pemberitahuan_dimulainya_penyidikan_documents')
            ->where('status_id', '12')
            // ->whereDate('created_at', Carbon::today())
            ->distinct('accident_id') // Count distinct accidents that are pending today
            ->count('accident_id'); // Count distinct accidents that are pending today

        // Calculate percentages
        $totalDocWeek = $totalValidationWeek + $rejectedValidation;
        $totalDocToday = $totalValidationToday + $pendingValidationToday;

        $rejectedValidationPercentage = $totalDocWeek > 0 ? round(($rejectedValidation / $totalDocWeek) * 100) : 0;
        $pendingValidationTodayPercentage = $totalDocToday > 0 ? round(($pendingValidationToday / $totalDocToday) * 100) : 0;

        // Calculate trends - Bandingkan dengan periode sebelumnya (kemarin untuk today, minggu lalu untuk week)
        if ($range === 'today') {
            $previousStartDate = Carbon::yesterday();
            $previousEndDate = Carbon::yesterday()->endOfDay();
        } else { // 'week'
            $previousStartDate = Carbon::now()->subWeek()->startOfWeek();
            $previousEndDate = Carbon::now()->subWeek()->endOfWeek();
        }

        $previousValidationCount = DB::table('log_case_document_validations')
            ->whereNotNull('approved_at')
            ->whereBetween('approved_at', [$previousStartDate, $previousEndDate])
            ->distinct('accident_id')
            ->count('accident_id');

        $weekTrend = $previousValidationCount > 0
            ? round((($totalValidationWeek - $previousValidationCount) / $previousValidationCount) * 100)
            : 100;

        $yesterdayCount = DB::table('log_case_document_validations')
            ->whereNotNull('approved_at')
            ->whereDate('approved_at', Carbon::yesterday())
            ->distinct('accident_id')
            ->count('accident_id');

        $todayTrend = $yesterdayCount > 0
            ? round((($totalValidationToday - $yesterdayCount) / $yesterdayCount) * 100)
            : 100;

        // Get top validators
        $topValidators = DB::table('log_case_document_validations')
            ->select(
                'approved_by_id as id',
                'approved_by_name as name',
            DB::raw('COUNT(DISTINCT accident_id) as validation_count')
            )
            ->whereNotNull('approved_at')
            ->whereBetween('approved_at', [$startDate, $endDate])
            ->groupBy('approved_by_id', 'approved_by_name')
            ->orderBy('validation_count', 'desc')
            ->limit(10)
            ->get();

        // Get validation trend data
        $validationTrend = $this->getValidationTrendData($range, $startDate, $endDate);

        // Get document type statistics
        $documentTypeStats = DB::table('log_case_document_validations')
            ->select(
                'document_category_name',
            DB::raw('COUNT(DISTINCT accident_id) as count')
            )
            ->whereNotNull('approved_at')
            ->whereBetween('approved_at', [$startDate, $endDate])
            ->groupBy('document_category_name')
            ->orderBy('count', 'desc')
            ->get();

        // Format date range for display dengan format yang lebih lengkap
        if ($range === 'custom') {
            $startDateFormat = Carbon::parse($request->startDate)->format('d M Y');
            $endDateFormat = Carbon::parse($request->endDate)->format('d M Y');
            $rangeDisplay = $startDateFormat . ' - ' . $endDateFormat;
        } else if ($range === 'today') {
            $rangeDisplay = 'Hari Ini';
        } else {
            $weekStart = Carbon::now()->startOfWeek()->format('d');
            $weekEnd = Carbon::now()->endOfWeek()->format('d M Y');
            $rangeDisplay = 'Minggu Ini';
        }

        // Untuk request AJAX, kembalikan JSON
        if ($request->ajax() || $request->has('ajax')) {
            return response()->json([
                'totalValidationWeek' => $totalValidationWeek,
                'totalValidationToday' => $totalValidationToday,
                'rejectedValidation' => $rejectedValidation,
                'pendingValidationToday' => $pendingValidationToday,
                'rejectedValidationPercentage' => $rejectedValidationPercentage,
                'pendingValidationTodayPercentage' => $pendingValidationTodayPercentage,
                'weekTrend' => $weekTrend,
                'todayTrend' => $todayTrend,
                'topValidators' => $topValidators,
                'validationTrend' => $validationTrend,
                'documentTypeStats' => $documentTypeStats,
                'range' => $range,
                'rangeDisplay' => $rangeDisplay
            ]);
        }

        // Untuk request normal, kembalikan view
        return view('cms.dashboard-validator.index', compact(
            'totalValidationWeek',
            'totalValidationToday',
            'rejectedValidation',
            'pendingValidationToday',
            'rejectedValidationPercentage',
            'pendingValidationTodayPercentage',
            'weekTrend',
            'todayTrend',
            'topValidators',
            'validationTrend',
            'documentTypeStats',
            'range',
            'rangeDisplay'
        ));
    }

    public function getLeaderboard(Request $request)
    {
        // Hanya izinkan filter 'today' dan 'week'
        $range = $request->range ?? 'week';
        if (!in_array($range, ['today', 'week'])) {
            $range = 'week';
        }

        // Set date range hanya untuk today atau week
        switch ($range) {
            case 'today':
                $startDate = Carbon::today();
                $endDate = Carbon::today()->endOfDay();
                break;
            case 'week':
            default:
                $startDate = Carbon::now()->startOfWeek();
                $endDate = Carbon::now()->endOfWeek();
                break;
        }

        // Get top validators menggunakan kolom yang ada pada tabel
        $topValidators = DB::table('log_case_document_validations')
            ->select(
                'approved_by_id as id',
                'approved_by_name as name',
            DB::raw('COUNT(DISTINCT accident_id) as validation_count')
            )
            ->whereNotNull('approved_at')
            ->whereBetween('approved_at', [$startDate, $endDate])
            ->groupBy('approved_by_id', 'approved_by_name')
            ->orderBy('validation_count', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'topValidators' => $topValidators
        ]);
    }

    // Method untuk mendapatkan data trend sesuai range filter
    private function getValidationTrendData(string $range, $startDate, $endDate)
    {
        // Format sesuai dengan range yang dipilih
        if ($range === 'today') {
            // Untuk hari ini, tampilkan data per jam dengan 1 grouped query per tabel
            $hours = [];
            $validatedCounts = [];
            $pendingCounts = [];

            $dayStart = Carbon::today()->startOfDay();
            $dayEnd = Carbon::today()->endOfDay();

            $validatedByHour = DB::table('log_case_document_validations')
                ->selectRaw('EXTRACT(HOUR FROM approved_at)::int as hour_num, COUNT(*) as total')
                ->whereNotNull('approved_at')
                ->whereBetween('approved_at', [$dayStart, $dayEnd])
                ->groupByRaw('EXTRACT(HOUR FROM approved_at)::int')
                ->pluck('total', 'hour_num');

            $pendingByHour = DB::table('doc.surat_pemberitahuan_dimulainya_penyidikan_documents')
                ->selectRaw('EXTRACT(HOUR FROM created_at)::int as hour_num, COUNT(*) as total')
                ->where('status_id', '12')
                ->whereBetween('created_at', [$dayStart, $dayEnd])
                ->groupByRaw('EXTRACT(HOUR FROM created_at)::int')
                ->pluck('total', 'hour_num');

            for ($hour = 0; $hour < 24; $hour++) {
                $hours[] = sprintf('%02d:00', $hour);
                $validatedCounts[] = (int) ($validatedByHour[$hour] ?? 0);
                $pendingCounts[] = (int) ($pendingByHour[$hour] ?? 0);
            }

            return [
                'labels' => $hours,
                'validated' => $validatedCounts,
                'pending' => $pendingCounts
            ];
        } else {
            // Untuk minggu ini, tampilkan data per hari dengan 1 grouped query per tabel
            $days = [];
            $validatedCounts = [];
            $pendingCounts = [];

            $weekStart = Carbon::now()->startOfWeek()->startOfDay();
            $weekEnd = Carbon::now()->startOfWeek()->addDays(6)->endOfDay();

            $validatedByDate = DB::table('log_case_document_validations')
                ->selectRaw('DATE(approved_at) as date_key, COUNT(*) as total')
                ->whereNotNull('approved_at')
                ->whereBetween('approved_at', [$weekStart, $weekEnd])
                ->groupByRaw('DATE(approved_at)')
                ->pluck('total', 'date_key');

            $pendingByDate = DB::table('doc.surat_pemberitahuan_dimulainya_penyidikan_documents')
                ->selectRaw('DATE(created_at) as date_key, COUNT(*) as total')
                ->where('status_id', '12')
                ->whereBetween('created_at', [$weekStart, $weekEnd])
                ->groupByRaw('DATE(created_at)')
                ->pluck('total', 'date_key');

            for ($day = 0; $day < 7; $day++) {
                $currentDate = Carbon::now()->startOfWeek()->addDays($day);
                $dateKey = $currentDate->toDateString();

                $days[] = $currentDate->locale('id')->isoFormat('ddd');
                $validatedCounts[] = (int) ($validatedByDate[$dateKey] ?? 0);
                $pendingCounts[] = (int) ($pendingByDate[$dateKey] ?? 0);
            }

            return [
                'labels' => $days,
                'validated' => $validatedCounts,
                'pending' => $pendingCounts
            ];
        }
    }
}
