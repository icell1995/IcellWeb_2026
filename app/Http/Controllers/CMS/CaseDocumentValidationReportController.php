<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

use App\Models\Log\CaseDocumentValidation;

class CaseDocumentValidationReportController extends Controller
{
    public function index(Request $request)
    {
        $startApprovedDate = $request->query('startApprovedDate');
        $endApprovedDate = $request->query('endApprovedDate');

        $reports = CaseDocumentValidation::where('approved_at', '!=', null)
            ->orderBy('created_at', 'desc');
        $rangeDate = Carbon::now()->locale('id')->translatedFormat('d F Y') . ' - ' . Carbon::now()->locale('id')->translatedFormat('d F Y');

        if(!empty($startApprovedDate) && !empty($endApprovedDate)){
            $reports = $reports->whereBetween('approved_at', [$startApprovedDate.' 00:00:00', $endApprovedDate.' 23:59:59']);
            $rangeDate = Carbon::parse($startApprovedDate)->locale('id')->translatedFormat('d F Y') . ' - ' . Carbon::parse($endApprovedDate)->locale('id')->translatedFormat('d F Y');
        }else{
            $reports = $reports->whereBetween('approved_at', [date('Y-m-d').' 00:00:00', date('Y-m-d').' 23:59:59']);
        }
        
        $reports = $reports->get();
        
        $urlParameters = [
            'startApprovedDate' => $startApprovedDate,
            'endApprovedDate' => $endApprovedDate,
        ];

        $viewData = [
            'reports' => $reports,
            'rangeDate' => $rangeDate,
            'urlParameters' => $urlParameters
        ];

        return view('cms.case-document-validation-report.index', $viewData);
    }
}
