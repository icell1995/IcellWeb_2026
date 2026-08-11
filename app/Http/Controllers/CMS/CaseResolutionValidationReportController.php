<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Log\CaseResolutionValidation;

class CaseResolutionValidationReportController extends Controller
{
    public function index(Request $request)
    {
        $tz = config('app.timezone', 'Asia/Jakarta');

        $startApprovedDate = $request->query('startApprovedDate');
        $endApprovedDate   = $request->query('endApprovedDate');

        // Base query: hanya yang sudah approved
        $reports = CaseResolutionValidation::query()
            ->whereNotNull('approved_at')
            ->orderByDesc('approved_at');

        // Range tanggal untuk tampilan
        $rangeDate = Carbon::now($tz)->locale('id')->translatedFormat('d F Y') . ' - ' .
                     Carbon::now($tz)->locale('id')->translatedFormat('d F Y');

        if (!empty($startApprovedDate) && !empty($endApprovedDate)) {
            // Pakai rentang yang dikirim user
            $start = Carbon::parse($startApprovedDate, $tz)->startOfDay();
            $end   = Carbon::parse($endApprovedDate,   $tz)->endOfDay();

            $reports->whereBetween('approved_at', [$start, $end]);

            $rangeDate = $start->locale('id')->translatedFormat('d F Y') . ' - ' .
                         $end->locale('id')->translatedFormat('d F Y');
        } else {
            // Default: hari ini
            $todayStart = Carbon::now($tz)->startOfDay();
            $todayEnd   = Carbon::now($tz)->endOfDay();

            $reports->whereBetween('approved_at', [$todayStart, $todayEnd]);
        }

        $reports = $reports->get();

        $urlParameters = [
            'startApprovedDate' => $startApprovedDate,
            'endApprovedDate'   => $endApprovedDate,
        ];

        $viewData = [
            'reports'       => $reports,
            'rangeDate'     => $rangeDate,
            'urlParameters' => $urlParameters,
        ];

        // Buatkan folder view: resources/views/cms/case-resolution-validation-report/index.blade.php
        return view('cms.case-resolutions-validation-report.index', $viewData);
    }
}
