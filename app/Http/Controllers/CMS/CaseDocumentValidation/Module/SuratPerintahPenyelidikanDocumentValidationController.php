<?php

namespace App\Http\Controllers\CMS\CaseDocumentValidation\Module;

use App\Helpers\PeopleNameHelper;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

use App\Services\DocValidation\DocValidationService;

use App\Models\Lib\Rank;
use App\Models\Lib\Position;
use App\Models\Officer;
use App\Models\Accident;
use App\Models\Lib\CaseKeyword;
use App\Models\Opt\Status;

use App\Models\Doc\SuratPerintahPenyelidikanDocument\SuratPerintahPenyelidikanDocument;

class SuratPerintahPenyelidikanDocumentValidationController extends Controller
{
    private $timestampNow;
    private $userAuth;

    public function __construct()
    {
        $this->timestampNow = Carbon::now();
        $this->middleware(function ($request, $next) {
            $this->userAuth = auth()->user();
    
            return $next($request);
        });
    }

    public function validation($id)
    {
        // Get URL Parameter
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $suratPerintahPenyelidikanDocumentId = $id;

        $suratPerintahPenyelidikanDocument = SuratPerintahPenyelidikanDocument::with(['suratPerintahPenyelidikanDocumentOfficers', 'suratPerintahPenyelidikanDocumentCaseKeywords', 'suratPerintahPenyelidikanDocumentAttachment', 'documentCategory'])->where('id', $suratPerintahPenyelidikanDocumentId)->first();
        $accident = Accident::where('id', $accidentId)->first();

        $ranks = Rank::where('is_active', true)->wherePolri()->orderBy('sort')->get();

        $positions = Position::where('is_active', true)->orderBy('sort')->get();

        $authorizedSignatories = Officer::withRelated()
            ->selectFullName()
            ->where('police_id', $accident->polres_id)
            ->whereHasUserActive()
            ->hasDataComplete()
            ->signatory()
            ->active()
            ->valid()
            ->orderBy('first_name')
            ->get();

        $caseKeywords = CaseKeyword::where('is_active', true)->orderBy('id')->get();

        $leaderOfficers = Officer::withRelated()
            ->selectFullName()
            ->where('police_id', $accident->polres_id)
            ->whereHasUserActive()
            ->hasDataComplete()
            ->member()
            ->active()
            ->valid()
            ->orderBy('first_name')
            ->get();

        $suratPerintahPenyelidikanDocuments = SuratPerintahPenyelidikanDocument::where('accident_id', $accidentId)->get();

        $rejectStatusOptions = Status::where('is_active', true)->orderBy('sort');

        if($suratPerintahPenyelidikanDocument->documentCategory->is_digital_signature == true){
            $rejectStatusOptions = $rejectStatusOptions->whereIn('id', ['6']);
        } else {
            $rejectStatusOptions = $rejectStatusOptions->whereIn('id', ['4', '5']);
        }

        $rejectStatusOptions = $rejectStatusOptions->get();

        $viewData = [
            'authorizedSignatories' => $authorizedSignatories,
            'accidentId' => $accidentId,
            'accident' => $accident,
            'suratPerintahPenyelidikanDocuments' => $suratPerintahPenyelidikanDocuments,
            'suratPerintahPenyelidikanDocument' => $suratPerintahPenyelidikanDocument,
            'suratPerintahPenyelidikanDocumentId' => $suratPerintahPenyelidikanDocumentId,
            'ranks' => $ranks,
            'positions' => $positions,
            'caseKeywords' => $caseKeywords,
            'leaderOfficers' => $leaderOfficers,
            'officers' => $suratPerintahPenyelidikanDocument
                ->suratPerintahPenyelidikanDocumentOfficers()
                ->withRelated()
                ->get(),
            'rejectStatusOptions' => $rejectStatusOptions,
        ];

        return view('cms.case-document-validation.modules.surat-perintah-penyelidikan-document-validation.validation', $viewData);
    }

    public function approveValidation(Request $request, $id)
    {
        $docValidationService = new DocValidationService();
        
        $isApproved = $request->input('isApproved');
        $isLegacy = $request->input('isLegacy');

        $isApprovedBoolean = filter_var($isApproved, FILTER_VALIDATE_BOOLEAN);
        $isLegacyBoolean = filter_var($isLegacy, FILTER_VALIDATE_BOOLEAN);

        DB::beginTransaction();
        try{
            $suratPerintahPenyelidikanDocument = SuratPerintahPenyelidikanDocument::with(['accident', 'documentCategory', 'status'])
                ->where('id', $id)
                ->first();
            
            if($isApprovedBoolean == true) {
                $suratPerintahPenyelidikanDocument->status_id = '86';
                $suratPerintahPenyelidikanDocument->released_at = $this->timestampNow;

                if($isLegacyBoolean == true) {
                    $suratPerintahPenyelidikanDocument->is_legacy = true;
                }else{
                    $suratPerintahPenyelidikanDocument->is_legacy = false;
                }
            } else {
                $suratPerintahPenyelidikanDocument->status_id = '4';
            }
            
            $status = Status::where('id', '86')->first();
            $docValidationService->logging([
                'accident_id' => $suratPerintahPenyelidikanDocument->accident_id,
                'document_id' => $suratPerintahPenyelidikanDocument->id,
                'document_category_id' => $suratPerintahPenyelidikanDocument->document_category_id,
                'updated_status_id' => $status->id,
                'approved_by_id' => $this->userAuth->id,
                'accident_number' => $suratPerintahPenyelidikanDocument->accident->no_lp ?? '-',
                'document_number' => $suratPerintahPenyelidikanDocument->document_number,
                'document_category_name' => $suratPerintahPenyelidikanDocument->documentCategory->name ?? '-',
                'model_class' => get_class(new SuratPerintahPenyelidikanDocument()),
                'updated_status_name' => $status->name,
                'approved_by_name' => PeopleNameHelper::getFullName($this->userAuth->first_title, $this->userAuth->first_name, $this->userAuth->last_name, $this->userAuth->last_title),
                'approved_at' => $this->timestampNow
            ]);

            $suratPerintahPenyelidikanDocument->approved_at = $this->timestampNow;

            $suratPerintahPenyelidikanDocument->save();

            DB::commit();

            return redirect()->route('cms.case-document-validation.index')->with('success', 'Surat Perintah Penyelidikan berhasil disetujui.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan. Surat Perintah Penyelidikan gagal disetujui.');
        }
    }
    
    public function rejectValidation(Request $request, $id)
    {
        $docValidationService = new DocValidationService();

        $isRejected = $request->input('isRejected');
        $rejectStatusOption = $request->input('rejectStatusOption');
        $rejectReason = htmlspecialchars($request->input('rejectReason'));

        $isRejectedBoolean = filter_var($isRejected, FILTER_VALIDATE_BOOLEAN);

        if($isRejectedBoolean == true) {
            DB::beginTransaction();
            try{
                $suratPerintahPenyelidikanDocument = SuratPerintahPenyelidikanDocument::with(['accident', 'documentCategory', 'status'])
                    ->where('id', $id)
                    ->first();
                $suratPerintahPenyelidikanDocument->status_id = $rejectStatusOption;

                if($rejectStatusOption == '4'){
                    $suratPerintahPenyelidikanDocument->messages = [
                        'reason_approval_rejected' => $rejectReason,
                    ];
                }else if($rejectStatusOption == '5'){
                    $suratPerintahPenyelidikanDocument->messages = [
                        'reason_approval_file_rejected' => $rejectReason,
                    ];
                }

                $status = Status::where('id', $rejectStatusOption)->first();
                $docValidationService->logging([
                    'accident_id' => $suratPerintahPenyelidikanDocument->accident_id,
                    'document_id' => $suratPerintahPenyelidikanDocument->id,
                    'document_category_id' => $suratPerintahPenyelidikanDocument->document_category_id,
                    'updated_status_id' => $status->id,
                    'rejected_by_id' => $this->userAuth->id,
                    'reject_reason' => $rejectReason,
                    'accident_number' => $suratPerintahPenyelidikanDocument->accident->no_lp ?? '-',
                    'document_number' => $suratPerintahPenyelidikanDocument->document_number,
                    'document_category_name' => $suratPerintahPenyelidikanDocument->documentCategory->name ?? '-',
                    'model_class' => get_class(new SuratPerintahPenyelidikanDocument()),
                    'updated_status_name' => $status->name,
                    'rejected_by_name' => PeopleNameHelper::getFullName($this->userAuth->first_title, $this->userAuth->first_name, $this->userAuth->last_name, $this->userAuth->last_title),
                    'rejected_at' => $this->timestampNow
                ]);

                $suratPerintahPenyelidikanDocument->rejected_at = $this->timestampNow;
    
                $suratPerintahPenyelidikanDocument->save();

                DB::commit();

                return redirect()->route('cms.case-document-validation.index')->with('success', 'Surat Perintah Penyelidikan berhasil ditolak.');
            } catch (\Exception $e) {
                DB::rollback();
                return redirect()->back()->with('error', 'Terjadi kesalahan. Surat Perintah Penyelidikan gagal ditolak.');
            }
        }else{
            return redirect()->back()->with('error', 'Terjadi kesalahan. Surat Perintah Penyelidikan gagal ditolak.');
        }
    }
}
