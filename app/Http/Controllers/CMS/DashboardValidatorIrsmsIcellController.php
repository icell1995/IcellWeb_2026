<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use App\Models\AccidentResolution;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DashboardValidatorIrsmsIcellController extends Controller
{
    /**
     * Display dashboard index page
     */
    public function index(Request $request)
    {
        // Use fixed date range: January 1st of current year to today
        $startDate = Carbon::create(Carbon::now()->year, 1, 1)->startOfDay();
        $endDate = Carbon::today()->endOfDay();

        // Set range indicator for view
        $range = 'year_to_date';

        // Fetch data from IRSMS API
        $irsmsApiData = $this->fetchIrsmsApiData();

        // IRSMS data - only from API (no local fallback)
        $totalValidationIrsms = $irsmsApiData ? ($irsmsApiData['totalValidation'] ?? 0) : 0;
        $pendingValidationTodayIrsms = $irsmsApiData ? ($irsmsApiData['pendingValidationToday'] ?? 0) : 0;
        $rejectedValidationIrsms = $irsmsApiData ? ($irsmsApiData['rejectedValidation'] ?? 0) : 0;
        $totalValidationTodayIrsms = $irsmsApiData ? ($irsmsApiData['totalValidationToday'] ?? 0) : 0;
        $percentageValidate = $irsmsApiData ? round($irsmsApiData['percentageValidateToday'] ?? 0) : 0;

        // ICELL data - structured similar to DashboardValidatorController
        // Total validations for the selected period
        $totalValidationIcell = DB::table('log_case_document_validations')
            ->whereNotNull('approved_at')
            ->whereBetween('approved_at', [$startDate, $endDate])
            ->distinct('accident_id')
            ->count('accident_id');

        // Total validations for today only
        $totalValidationTodayIcell = DB::table('log_case_document_validations')
            ->whereNotNull('approved_at')
            ->whereDate('approved_at', Carbon::today())
            ->distinct('accident_id')
            ->count('accident_id');

        // Rejected validations count
        $rejectedValidationIcell = DB::table('log_case_document_validations')
            ->whereNotNull('rejected_at')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->distinct('accident_id')
            ->count('accident_id');

        // Pending validations today
        $pendingValidationTodayIcell = DB::table('doc.surat_pemberitahuan_dimulainya_penyidikan_documents')
            ->where('status_id', '12')
            ->count();

        // Calculate percentages for ICELL
        $totalDocTodayIcell = $totalValidationTodayIcell + $pendingValidationTodayIcell;
        $pendingValidationTodayPercentageIcell = $totalDocTodayIcell > 0 ?
            round(($pendingValidationTodayIcell / $totalDocTodayIcell) * 100) : 0;

        // SELRA Pending data
        $pendingSelraCount = $this->getPendingSelraCount('all');
        $pendingSelraTodayCount = $this->getPendingSelraCount('today');
        $returnedSelraCount = $this->getPendingSelraReturnedCount('all');
        $totalPendingSelra = $pendingSelraCount + $returnedSelraCount;

        // Get top validators for IRSMS from API only
        $topValidatorsIrsms = [];
        if ($irsmsApiData && isset($irsmsApiData['topValidators'])) {
            $topValidatorsIrsms = collect($irsmsApiData['topValidators'])
                ->map(function ($validator) {
                    return (object) [
                        'id' => $validator['id'] ?? null,
                        'name' => $validator['name'] ?? 'Unknown',
                    'image' => $validator['image'] ?? null,
                        'validation_count' => $validator['validationCount'] ?? $validator['total'] ?? 0,
                        'role' => $validator['role'] ?? 'Validator'
                    ];
                })
                ->take(10)
                ->values();
        }

        // Get top validators for ICELL - MINDIK ONLY
        $topValidatorsMindik = DB::table('log_case_document_validations')
            ->select(
                'approved_by_id as id',
                'approved_by_name as name',
            DB::raw('COUNT(DISTINCT accident_id) as validation_count')
            )
            ->whereNotNull('approved_at')
            ->whereNotNull('approved_by_id')
            ->whereBetween('approved_at', [$startDate, $endDate])
            ->groupBy('approved_by_id', 'approved_by_name')
            ->get();

        // Get top validators for ICELL - SELRA (Approved + Rejected)
        $topValidatorsSelra = $this->getTopValidatorsSelraWithRejected($startDate, $endDate);

        // Combine Mindik + SELRA validators
        $topValidatorsIcell = $this->combineValidators($topValidatorsMindik, $topValidatorsSelra);

        // Format date range for display
        $startDateFormat = $startDate->format('d M Y');
        $endDateFormat = $endDate->format('d M Y');
        $rangeDisplay = $startDateFormat . ' - ' . $endDateFormat;

        // For AJAX requests, return JSON
        if ($request->ajax() || $request->has('ajax')) {
            return response()->json([
                'totalValidationIrsms' => $totalValidationIrsms,
                'totalValidationTodayIrsms' => $totalValidationTodayIrsms,
                'rejectedValidationIrsms' => $rejectedValidationIrsms,
                'pendingValidationTodayIrsms' => $pendingValidationTodayIrsms,
                'percentageValidate' => $percentageValidate,

                'totalValidationIcell' => $totalValidationIcell,
                'totalValidationTodayIcell' => $totalValidationTodayIcell,
                'rejectedValidationIcell' => $rejectedValidationIcell,
                'pendingValidationTodayIcell' => $pendingValidationTodayIcell,
                'pendingValidationTodayPercentageIcell' => $pendingValidationTodayPercentageIcell,

                'pendingSelraCount' => $pendingSelraCount,
                'pendingSelraTodayCount' => $pendingSelraTodayCount,
                'returnedSelraCount' => $returnedSelraCount,
                'totalPendingSelra' => $totalPendingSelra,

                'topValidatorsIrsms' => $topValidatorsIrsms,
                'topValidatorsIcell' => $topValidatorsIcell,
                'rangeDisplay' => $rangeDisplay,
                'irsmsApiStatus' => $irsmsApiData ? 'connected' : 'api_error'
            ]);
        }

        // For normal requests, return view
        return view('cms.dashboard-validator-irsms-icell.index', compact(
            'totalValidationIrsms',
            'totalValidationTodayIrsms',
            'rejectedValidationIrsms',
            'pendingValidationTodayIrsms',
            'percentageValidate',
            'totalValidationIcell',
            'totalValidationTodayIcell',
            'rejectedValidationIcell',
            'pendingValidationTodayIcell',
            'pendingValidationTodayPercentageIcell',
            'pendingSelraCount',
            'pendingSelraTodayCount',
            'returnedSelraCount',
            'totalPendingSelra',
            'topValidatorsIrsms',
            'topValidatorsIcell',
            'rangeDisplay',
            'irsmsApiData'
        ));
    }

    /**
     * Fetch data from the IRSMS API with caching
     */
    private function fetchIrsmsApiData($dateFilter = null)
    {
        // $cacheKeySuffix = $dateFilter === 'today' ? '_today' : ($dateFilter === '7days' ? '_7days' : '_all');
        $cacheKeySuffix = $dateFilter === 'today' ? '_today' : ($dateFilter === '7days' ? '_7days' : ($dateFilter === 'this_month' ? '_this_month' : '_all'));
        $cacheKey = 'irsms_api_dashboard_data' . $cacheKeySuffix;

        if (Cache::has($cacheKey)) {
            $cachedData = Cache::get($cacheKey);
            $cachedData['fromCache'] = true;
            return $cachedData;
        }

        try {
            $baseUrl = 'https://irsms.korlantas.polri.go.id/irsmsapi/api/icell/dashboard';

            $queryParams = [];
            if ($dateFilter === 'today') {
                $queryParams['customDateReportHelpdesk'] = Carbon::today()->format('Y-m-d');
                $queryParams['customDateReportHelpdeskEnd'] = Carbon::today()->format('Y-m-d');
            } elseif ($dateFilter === '7days') {
                $queryParams['customDateReportHelpdesk'] = Carbon::today()->subDays(6)->format('Y-m-d');
                $queryParams['customDateReportHelpdeskEnd'] = Carbon::today()->format('Y-m-d');
            } elseif ($dateFilter === 'this_month') {
                $queryParams['customDateReportHelpdesk']    = Carbon::today()->copy()->startOfMonth()->format('Y-m-d');
                $queryParams['customDateReportHelpdeskEnd'] = Carbon::today()->format('Y-m-d'); // sampai hari ini
            }

            $response = Http::timeout(10)->withHeaders([
                'Accept' => 'application/json',
                'Key' => '9xEVPyQE26KTpo5h6DxwW34m2TkUGnqD243b1i4BNs3C7yg3dL4Ynql3ehRsfZbsQubzRR51BTudsPeMFRtkLBGiennZ0TojmmK9=icell-dashboard',
                'Content-Type' => 'application/json'
            ])->get($baseUrl, $queryParams);

            if ($response->successful()) {
                $data = $response->json();

                // PERBAIKAN: Mapping yang lebih konsisten untuk topValidators
                $topValidators = [];
                if (isset($data['reportHelpdesk']) && is_array($data['reportHelpdesk'])) {
                    $topValidators = array_map(function ($validator) {
                        // Pastikan mengambil nilai yang benar dari API
                        $validationCount = 0;

                        // Coba ambil dari berbagai kemungkinan field
                        if (isset($validator['total'])) {
                            $validationCount = (int) $validator['total'];
                        } elseif (isset($validator['validationCount'])) {
                            $validationCount = (int) $validator['validationCount'];
                        } elseif (isset($validator['count'])) {
                            $validationCount = (int) $validator['count'];
                        }

                        return [
                            'id' => $validator['id'] ?? null,
                            'name' => $validator['name'] ?? 'Unknown',
                            'image' => $validator['image'] ?? null,
                            'validationCount' => $validationCount,
                            'total' => $validationCount, 
                            'role' => $validator['role'] ?? 'Validator'
                        ];
                    }, $data['reportHelpdesk']);
                }

                $mappedData = [
                    'totalValidation' => $data['countValidateAll'] ?? 0,
                    'totalValidationToday' => $data['countValidateToday'] ?? 0,
                    'pendingValidationToday' => $data['countValidateToday'] ?? 0,
                    'rejectedValidation' => $data['countRefuse'] ?? 0,
                    'percentageValidateToday' => $data['percentageValidate'] ?? 0,
                    'rawApiResponse' => $data,
                    'dateFilter' => $dateFilter,
                    'validatedDocuments' => [],
                    'pendingDocuments' => [],
                    'topValidators' => $topValidators,
                    'validatorStats' => $topValidators,
                    'lastUpdate' => now()->toDateTimeString(),
                    'apiStatus' => 'connected'
                ];

                // Cache dengan waktu lebih pendek untuk data 7 hari
                $cacheDuration = $dateFilter === '7days' ? 180 : 300; // 3 menit untuk 7 hari, 5 menit untuk lainnya
                Cache::put($cacheKey, $mappedData, $cacheDuration);
                Cache::put($cacheKey . '_stale', $mappedData, 1440);

                return $mappedData;
            } else {
                Log::error('IRSMS API Error', [
                    'status' => $response->status(),
                    'filter' => $dateFilter,
                    'response' => $response->body()
                ]);
            }
        } catch (\Exception $e) {
            Log::warning('IRSMS API Connection Failed', [
                'error' => $e->getMessage(),
                'filter' => $dateFilter
            ]);

            if (Cache::has($cacheKey . '_stale')) {
                $staleData = Cache::get($cacheKey . '_stale');
                $staleData['fromStaleCache'] = true;
                $staleData['apiStatus'] = 'stale';
                return $staleData;
            }

            return null;
        }

        return null;
    }

    /**
     * Get leaderboard data (AJAX endpoint)
     */
    public function getLeaderboard(Request $request)
    {
        $system = $request->query('system', 'irsms');
        $range = $request->query('range', 'all');

        if ($range === 'today') {
            $startDate = Carbon::today()->startOfDay();
            $endDate = Carbon::today()->endOfDay();
        } elseif ($range === '7days') {
            $startDate = Carbon::today()->subDays(6)->startOfDay();
            $endDate = Carbon::today()->endOfDay();
        } elseif ($range === 'this_month') {
            $startDate = Carbon::today()->copy()->startOfMonth()->startOfDay();
            $endDate = Carbon::today()->endOfDay();
        } else {
            $startDate = Carbon::create(Carbon::now()->year, 1, 1)->startOfDay();
            $endDate = Carbon::today()->endOfDay();
        }

        if (strtolower($system) === 'irsms') {
            $dateFilter = null;
            if ($range === 'today') {
                $dateFilter = 'today';
            } elseif ($range === '7days') {
                $dateFilter = '7days';
            } elseif ($range === 'this_month') {
                $dateFilter = 'this_month';
            }

            $irsmsApiData = $this->fetchIrsmsApiData($dateFilter);

            if ($irsmsApiData && isset($irsmsApiData['topValidators'])) {
                // PERBAIKAN: Gunakan mapping yang konsisten
                $topValidators = collect($irsmsApiData['topValidators'])
                    ->map(function ($validator) {
                    // Ambil nilai validationCount dengan prioritas
                    $count = $validator['validationCount'] ?? $validator['total'] ?? 0;

                    return (object) [
                            'id' => $validator['id'] ?? null,
                        'name' => $validator['name'] ?? 'Unknown',
                        'validation_count' => (int) $count,
                        'role' => $validator['role'] ?? 'Validator',
                        'image' => $validator['image'] ?? null
                        ];
                    })
                    ->sortByDesc('validation_count') // PERBAIKAN: Sort ulang untuk memastikan urutan benar
                    ->take(10)
                    ->values();

                return response()->json([
                    'topValidators' => $topValidators,
                    'range' => $range,
                    'system' => $system,
                    'dataSource' => 'api',
                    'dateRange' => $startDate->format('d M Y') . ' - ' . $endDate->format('d M Y'),
                    'debug' => [ // Untuk debugging
                        'dateFilter' => $dateFilter,
                        'rawDataCount' => count($irsmsApiData['topValidators']),
                        'filteredCount' => $topValidators->count()
                    ]
                ]);
            }
        }

        // ...existing code untuk ICELL...
        $topValidatorsMindik = DB::table('log_case_document_validations')
            ->select(
                'approved_by_id as id',
                'approved_by_name as name',
            DB::raw('COUNT(DISTINCT accident_id) as validation_count')
            )
            ->whereNotNull('approved_at')
            ->whereNotNull('approved_by_id')
            ->whereBetween('approved_at', [$startDate, $endDate])
            ->groupBy('approved_by_id', 'approved_by_name')
            ->get();

        $topValidatorsSelra = $this->getTopValidatorsSelraWithRejected($startDate, $endDate);
        $topValidators = $this->combineValidators($topValidatorsMindik, $topValidatorsSelra);

        return response()->json([
            'topValidators' => $topValidators,
            'range' => $range,
            'system' => $system,
            'dataSource' => $system === 'irsms' ? 'local_fallback' : 'local',
            'dateRange' => $startDate->format('d M Y') . ' - ' . $endDate->format('d M Y')
        ]);
    }

    /**
     * Get pending IRSMS validations
     */
    public function getPendingIrsmsValidations(Request $request)
    {
        $irsmsApiData = $this->fetchIrsmsApiData();

        if ($irsmsApiData && isset($irsmsApiData['pendingDocuments'])) {
            return response()->json([
                'pendingValidations' => $irsmsApiData['pendingDocuments'],
                'source' => 'api'
            ]);
        }

        return response()->json([
            'pendingValidations' => [],
            'source' => 'api_unavailable'
        ]);
    }

    /**
     * Get pending ICELL validations
     */
    public function getPendingIcellValidations(Request $request)
    {
        $startDate = Carbon::create(Carbon::now()->year, 1, 1)->startOfDay();
        $endDate = Carbon::today()->endOfDay();

        $pendingValidations = DB::table('log_case_document_validations')
            ->whereNull('approved_at')
            ->whereNull('rejected_at')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select('id', 'document_id as document_number', 'document_date', 'created_at', 'updated_at')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'pendingValidations' => $pendingValidations
        ]);
    }

    /**
     * Get IRSMS validated documents
     */
    public function getIrsmsDocuments(Request $request)
    {
        $irsmsApiData = $this->fetchIrsmsApiData();

        if ($irsmsApiData && isset($irsmsApiData['validatedDocuments'])) {
            return response()->json([
                'validatedDocuments' => $irsmsApiData['validatedDocuments'],
                'source' => 'api'
            ]);
        }

        return response()->json([
            'validatedDocuments' => [],
            'source' => 'api_unavailable'
        ]);
    }

    /**
     * Get ICELL validated documents
     */
    public function getIcellDocuments(Request $request)
    {
        $startDate = Carbon::create(Carbon::now()->year, 1, 1)->startOfDay();
        $endDate = Carbon::today()->endOfDay();

        $validatedDocuments = DB::table('log_case_document_validations')
            ->whereNotNull('approved_at')
            ->whereBetween('approved_at', [$startDate, $endDate])
            ->select('id', 'document_id', 'document_category_name', 'document_number', 'document_date', 'approved_at', 'approved_by_name')
            ->orderBy('approved_at', 'desc')
            ->get();

        return response()->json([
            'validatedDocuments' => $validatedDocuments
        ]);
    }

    /**
     * Get IRSMS validator statistics
     */
    public function getIrsmsValidatorStats(Request $request)
    {
        $irsmsApiData = $this->fetchIrsmsApiData();

        if ($irsmsApiData && isset($irsmsApiData['validatorStats'])) {
            // PERBAIKAN: Mapping yang konsisten
            $stats = collect($irsmsApiData['validatorStats'])
                ->map(function ($validator) {
                    $count = $validator['validationCount'] ?? $validator['total'] ?? 0;

                    return [
                        'id' => $validator['id'] ?? null,
                        'name' => $validator['name'] ?? 'Unknown',
                        'validation_count' => (int) $count,
                        'avg_validation_time' => $validator['avgValidationTime'] ?? 0
                    ];
                })
                ->sortByDesc('validation_count')
                ->values();

            return response()->json([
                'validatorStats' => $stats,
                'source' => 'api'
            ]);
        } else if ($irsmsApiData && isset($irsmsApiData['topValidators'])) {
            $validatorStats = collect($irsmsApiData['topValidators'])
                ->map(function ($validator) {
                $count = $validator['validationCount'] ?? $validator['total'] ?? 0;

                return [
                        'id' => $validator['id'] ?? null,
                        'name' => $validator['name'] ?? 'Unknown',
                    'validation_count' => (int) $count,
                        'avg_validation_time' => $validator['avgValidationTime'] ?? 0
                    ];
                })
                ->sortByDesc('validation_count')
                ->values();

            return response()->json([
                'validatorStats' => $validatorStats,
                'source' => 'api'
            ]);
        }

        return response()->json([
            'validatorStats' => [],
            'source' => 'api_unavailable'
        ]);
    }

    /**
     * Get ICELL validator statistics
     */
    public function getIcellValidatorStats(Request $request)
    {
        $startDate = Carbon::create(Carbon::now()->year, 1, 1)->startOfDay();
        $endDate = Carbon::today()->endOfDay();

        $validatorStats = DB::table('log_case_document_validations')
            ->select(
                'approved_by_id as id',
                'approved_by_name as name',
                DB::raw('COUNT(id) as validation_count'),
            DB::raw('EXTRACT(EPOCH FROM AVG(approved_at - created_at))/60 as avg_validation_time')
            )
            ->whereNotNull('approved_at')
            ->whereBetween('approved_at', [$startDate, $endDate])
            ->groupBy('approved_by_id', 'approved_by_name')
            ->orderBy('validation_count', 'desc')
            ->get();

        return response()->json([
            'validatorStats' => $validatorStats
        ]);
    }

    /**
     * Get pending SELRA validations count
     */
    private function getPendingSelraCount($range = 'all')
    {
        if ($range === 'today') {
            $startDate = Carbon::today()->startOfDay();
            $endDate = Carbon::today()->endOfDay();
        } else {
            $tz = config('app.timezone', 'Asia/Jakarta');
            $startDate = Carbon::createFromFormat('Y-m-d', '2025-09-15', $tz)->startOfDay();
            $endDate = Carbon::now($tz)->endOfDay();
        }

        $logTable = (new \App\Models\Log\CaseResolutionValidation)->getTable();

        // Only count resolutions whose related accident has accident_date in year 2025
        $tz = config('app.timezone', 'Asia/Jakarta');
        $accidentYearStart = Carbon::createFromFormat('Y-m-d', '2025-01-01', $tz)->startOfDay();
        $accidentYearEnd = Carbon::now($tz)->endOfDay();

        return AccidentResolution::query()
            ->whereNull('approved_at')
            ->whereBetween('created_at', [$startDate, $endDate])
            // ensure related accident.accident_date is inside 2025
            ->whereExists(function ($q) use ($accidentYearStart, $accidentYearEnd) {
                $q->from('accidents as a')
                    ->selectRaw('1')
                    ->whereColumn('a.id', 'accident_resolutions.accident_id')
                    ->whereBetween(DB::raw('a.accident_date'), [$accidentYearStart, $accidentYearEnd]);
            })
            ->whereNotExists(function ($s) use ($logTable, $startDate, $endDate) {
                $s->from($logTable . ' as lg')
                    ->selectRaw('1')
                    ->whereColumn('lg.accident_id', 'accident_resolutions.accident_id')
                    ->where('lg.updated_status_name', 'rejected')
                    ->whereBetween(DB::raw('COALESCE(lg.rejected_at, lg.created_at)'), [$startDate, $endDate]);
            })
            ->count();
    }

    /**
     * Get returned SELRA validations count
     */
    private function getPendingSelraReturnedCount($range = 'all')
    {
        if ($range === 'today') {
            $startDate = Carbon::today()->startOfDay();
            $endDate = Carbon::today()->endOfDay();
        } else {
            $tz = config('app.timezone', 'Asia/Jakarta');
            $startDate = Carbon::createFromFormat('Y-m-d', '2025-09-15', $tz)->startOfDay();
            $endDate = Carbon::now($tz)->endOfDay();
        }

        $logTable = (new \App\Models\Log\CaseResolutionValidation)->getTable();

        // Only count resolutions whose related accident has accident_date in year 2025
        $tz = config('app.timezone', 'Asia/Jakarta');
        $accidentYearStart = Carbon::createFromFormat('Y-m-d', '2025-01-01', $tz)->startOfDay();
        $accidentYearEnd = Carbon::now($tz)->endOfDay();

        return AccidentResolution::query()
            ->whereNull('approved_at')
            ->whereBetween('created_at', [$startDate, $endDate])
            // ensure related accident.accident_date is inside 2025
            ->whereExists(function ($q) use ($accidentYearStart, $accidentYearEnd) {
                $q->from('accidents as a')
                    ->selectRaw('1')
                    ->whereColumn('a.id', 'accident_resolutions.accident_id')
                    ->whereBetween(DB::raw('a.accident_date'), [$accidentYearStart, $accidentYearEnd]);
            })
            ->whereExists(function ($s) use ($logTable, $startDate, $endDate) {
                $s->from($logTable . ' as lg')
                    ->selectRaw('1')
                    ->whereColumn('lg.accident_id', 'accident_resolutions.accident_id')
                    ->where('lg.updated_status_name', 'rejected')
                    ->whereBetween(DB::raw('COALESCE(lg.rejected_at, lg.created_at)'), [$startDate, $endDate]);
            })
            ->count();
    }

    /**
     * Get top validators from SELRA (Approved + Rejected)
     */
    private function getTopValidatorsSelraWithRejected($startDate, $endDate)
    {
        // Get approved SELRA validations
        $approvedValidations = DB::table('log_case_resolution_validations')
            ->select(
                'approved_by_id as id',
                'approved_by_name as name',
            DB::raw('COUNT(DISTINCT accident_id) as approved_count')
            )
            ->whereNotNull('approved_at')
            ->whereNotNull('approved_by_id')
            ->whereBetween('approved_at', [$startDate, $endDate])
            ->groupBy('approved_by_id', 'approved_by_name')
            ->get();

        // Get rejected SELRA validations
        $rejectedValidations = DB::table('log_case_resolution_validations')
            ->select(
                'rejected_by_id as id',
                'rejected_by_name as name',
                DB::raw('COUNT(DISTINCT accident_id) as rejected_count')
            )
            ->whereNotNull('rejected_at')
            ->whereNotNull('rejected_by_id')
            ->whereBetween('rejected_at', [$startDate, $endDate])
            ->groupBy('rejected_by_id', 'rejected_by_name')
            ->get();

        // Combine approved and rejected counts per validator
        $combined = [];

        // Add approved validations
        foreach ($approvedValidations as $validator) {
            $id = $validator->id;

            if (empty($id)) continue;

            $combined[$id] = [
                'id' => $id,
                'name' => $validator->name ?? 'Unknown',
                'approved_count' => $validator->approved_count ?? 0,
                'rejected_count' => 0,
                'total_count' => $validator->approved_count ?? 0
            ];
        }

        // Add rejected validations
        foreach ($rejectedValidations as $validator) {
            $id = $validator->id;

            if (empty($id)) continue;

            if (isset($combined[$id])) {
                $combined[$id]['rejected_count'] = $validator->rejected_count ?? 0;
                $combined[$id]['total_count'] += $validator->rejected_count ?? 0;

                // Update dengan nama yang lebih lengkap
                $currentName = $combined[$id]['name'];
                $newName = $validator->name ?? 'Unknown';
                if (strlen($newName) > strlen($currentName)) {
                    $combined[$id]['name'] = $newName;
                }
            } else {
                $combined[$id] = [
                    'id' => $id,
                    'name' => $validator->name ?? 'Unknown',
                    'approved_count' => 0,
                    'rejected_count' => $validator->rejected_count ?? 0,
                    'total_count' => $validator->rejected_count ?? 0
                ];
            }
        }

        // Convert to collection and return with proper structure
        return collect($combined)
            ->map(function ($item) {
                return (object) [
                    'id' => $item['id'],
                    'name' => $item['name'],
                    'validation_count' => $item['total_count'],
                    'selra_approved_count' => $item['approved_count'],
                    'selra_rejected_count' => $item['rejected_count']
                ];
            })
            ->sortByDesc('validation_count')
            ->values();
    }

    /**
     * Combine validators from Mindik and SELRA
     */
    private function combineValidators($mindikValidators, $selraValidators)
    {
        $combined = [];

        // Tambahkan Mindik validators
        foreach ($mindikValidators as $validator) {
            $id = $validator->id;

            if (empty($id)) {
                continue;
            }

            $combined[$id] = [
                'id' => $id,
                'name' => $this->normalizeValidatorName($validator->name ?? 'Unknown'),
                'mindik_count' => $validator->validation_count ?? 0,
                'selra_approved_count' => 0,
                'selra_rejected_count' => 0,
                'total_count' => $validator->validation_count ?? 0
            ];
        }

        // Tambahkan SELRA validators
        foreach ($selraValidators as $validator) {
            $id = $validator->id;

            if (empty($id)) {
                continue;
            }

            if (isset($combined[$id])) {
                $combined[$id]['selra_approved_count'] = $validator->selra_approved_count ?? 0;
                $combined[$id]['selra_rejected_count'] = $validator->selra_rejected_count ?? 0;
                $combined[$id]['total_count'] += $validator->validation_count ?? 0;

                // Update nama dengan yang lebih lengkap
                $currentName = $combined[$id]['name'];
                $newName = $this->normalizeValidatorName($validator->name ?? 'Unknown');

                if (strlen($newName) > strlen($currentName)) {
                    $combined[$id]['name'] = $newName;
                }
            } else {
                $combined[$id] = [
                    'id' => $id,
                    'name' => $this->normalizeValidatorName($validator->name ?? 'Unknown'),
                    'mindik_count' => 0,
                    'selra_approved_count' => $validator->selra_approved_count ?? 0,
                    'selra_rejected_count' => $validator->selra_rejected_count ?? 0,
                    'total_count' => $validator->validation_count ?? 0
                ];
            }
        }

        // Convert to collection and sort
        $sorted = collect($combined)
            ->sortByDesc('total_count')
            ->take(10); // Ambil 10 teratas dulu

        // PENTING: Re-index dengan values() untuk memastikan keys 0-9
        $result = $sorted->values()->map(function ($item, $index) {
            return (object) [
                'id' => $item['id'],
                'name' => $item['name'],
                'validation_count' => $item['total_count'],
                'mindik_count' => $item['mindik_count'],
                'selra_count' => $item['selra_approved_count'] + $item['selra_rejected_count'],
                'selra_approved_count' => $item['selra_approved_count'],
                'selra_rejected_count' => $item['selra_rejected_count'],
                'role' => 'Validator',
                '_debug_index' => $index // Untuk debugging
            ];
        });

        return $result;
    }

    /**
     * Normalize validator name
     */
    private function normalizeValidatorName($name)
    {
        if (empty($name) || $name === 'Unknown') {
            return 'Unknown';
        }

        // Trim whitespace
        $normalized = trim($name);

        // Remove multiple spaces
        $normalized = preg_replace('/\s+/', ' ', $normalized);

        // Remove role suffixes/prefixes
        $normalized = preg_replace('/\s*-\s*(Helpdesk|Validator|Admin|Staff|Petugas)$/i', '', $normalized);
        //$normalized = preg_replace('/^(Helpdesk|Validator|Admin|Staff|Petugas)\s*-\s*/i', '', $normalized);

        // Uppercase untuk konsistensi
        //$normalized = strtoupper(trim($normalized));

        return $normalized ?: 'Unknown';
    }
}
