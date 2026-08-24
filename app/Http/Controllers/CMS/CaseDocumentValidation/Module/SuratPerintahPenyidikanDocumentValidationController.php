<?php

namespace App\Http\Controllers\CMS\CaseDocumentValidation\Module;

use App\Helpers\PeopleNameHelper;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

use App\Services\DocValidation\DocValidationService;

use App\Models\Officer;
use App\Models\Accident;
use App\Models\Lib\Rank;
use App\Models\Lib\Position;
use App\Models\Lib\CaseKeyword;
use App\Models\Lib\CaseClassification;
use App\Models\Lib\CrimeType;
use App\Models\Lib\CrimeClass;
use App\Models\Lib\CrimeConstitution;
use App\Models\Opt\Status;

use App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument;

class SuratPerintahPenyidikanDocumentValidationController extends Controller
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
        $suratPerintahPenyidikanDocumentId = $id;

        $suratPerintahPenyidikanDocument = SuratPerintahPenyidikanDocument::with(['suratPerintahPenyidikanDocumentOfficers', 'suratPerintahPenyidikanDocumentCaseKeywords', 'suratPerintahPenyidikanDocumentLaws', 'suratPerintahPenyidikanDocumentAttachment'])
                                                ->where('id', $suratPerintahPenyidikanDocumentId)
                                                ->first();
    
        $accident = Accident::where('id',$accidentId)->first();

        $ranks = Rank::where('is_active', true)->wherePolri()->orderBy('sort')->get();

        $positions = Position::where('is_active', true)->orderBy('sort')->get();

        $crimeTypes = CrimeType::withRelated()
            ->active()
            ->orderBy('sort')
            ->get();
        $crimeClasses = CrimeClass::withRelated()
            ->active()
            ->orderBy('sort')
            ->get();
        $crimeConstitutions = CrimeConstitution::withRelated()
            ->active()
            ->orderBy('sort')
            ->get();

        $caseClassifications = CaseClassification::where('is_active', true)->orderBy('sort')->get();

        $authorizedSignatories = Officer::withRelated()
            ->selectFullName()
            ->whereHasUserActive()
            ->hasDataComplete()
            ->where('police_id', $accident->polres_id)
            ->signatory()
            ->active()
            ->valid()
            ->orderBy('first_name')
            ->get();

        $caseKeywords = CaseKeyword::where('is_active', true)->orderBy('id')->get();

        $leaderOfficers = Officer::withRelated()
            ->selectFullName()
            ->whereHasUserActive()
            ->hasDataComplete()
            ->where('police_id', $accident->polres_id)
            ->member()
            ->valid()
            ->active()
            ->orderBy('first_name')
            ->get();

        $suratPerintahPenyidikanDocuments = SuratPerintahPenyidikanDocument::where('accident_id', $accidentId)->get();

        $rejectStatusOptions = Status::where('is_active', true)->orderBy('sort');

        if($suratPerintahPenyidikanDocument->documentCategory->is_digital_signature == true){
            $rejectStatusOptions = $rejectStatusOptions->whereIn('id', ['6']);
        } else {
            $rejectStatusOptions = $rejectStatusOptions->whereIn('id', ['4', '5']);
        }

        $rejectStatusOptions = $rejectStatusOptions->get();

        $viewData =[
            'authorizedSignatories' => $authorizedSignatories,
            'accidentId' => $accidentId,
            'accident' => $accident,
            'suratPerintahPenyidikanDocuments' => $suratPerintahPenyidikanDocuments,
            'suratPerintahPenyidikanDocument' => $suratPerintahPenyidikanDocument,
            'suratPerintahPenyidikanDocumentId' => $suratPerintahPenyidikanDocumentId,
            'ranks' => $ranks,
            'positions' => $positions,
            'caseKeywords' => $caseKeywords,
            'leaderOfficers' => $leaderOfficers,
            'crimeTypes' => $crimeTypes,
            'crimeClasses' => $crimeClasses,
            'crimeConstitutions' => $crimeConstitutions,
            'caseClassifications' => $caseClassifications,
            'laws' => $suratPerintahPenyidikanDocument->suratPerintahPenyidikanDocumentLaws,
            'officers' => $suratPerintahPenyidikanDocument
                ->suratPerintahPenyidikanDocumentOfficers()
                ->withRelated()
                ->get(),
            'rejectStatusOptions' => $rejectStatusOptions,
        ];
 
        return view('cms.case-document-validation.modules.surat-perintah-penyidikan-document-validation.validation', $viewData);
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
            $suratPerintahPenyidikanDocument = SuratPerintahPenyidikanDocument::with(['accident', 'documentCategory', 'status'])
                ->where('id', $id)
                ->first();
            
            if($isApprovedBoolean == true) {
                $suratPerintahPenyidikanDocument->status_id = '86';
                $suratPerintahPenyidikanDocument->released_at = $this->timestampNow;

                if($isLegacyBoolean == true) {
                    $suratPerintahPenyidikanDocument->is_legacy = true;
                }else{
                    $suratPerintahPenyidikanDocument->is_legacy = false;
                }
            } else {
                $suratPerintahPenyidikanDocument->status_id = '4';
            }

            $status = Status::where('id', '86')->first();
            $docValidationService->logging([
                'accident_id' => $suratPerintahPenyidikanDocument->accident_id,
                'document_id' => $suratPerintahPenyidikanDocument->id,
                'document_category_id' => $suratPerintahPenyidikanDocument->document_category_id,
                'updated_status_id' => $status->id,
                'approved_by_id' => $this->userAuth->id,
                'accident_number' => $suratPerintahPenyidikanDocument->accident->no_lp ?? '-',
                'document_number' => $suratPerintahPenyidikanDocument->document_number,
                'document_category_name' => $suratPerintahPenyidikanDocument->documentCategory->name ?? '-',
                'model_class' => get_class(new SuratPerintahPenyidikanDocument()),
                'updated_status_name' => $status->name,
                'approved_by_name' => PeopleNameHelper::getFullName($this->userAuth->first_title, $this->userAuth->first_name, $this->userAuth->last_name, $this->userAuth->last_title),
                'approved_at' => $this->timestampNow
            ]);

            $suratPerintahPenyidikanDocument->approved_at = $this->timestampNow;

            $suratPerintahPenyidikanDocument->save();
            
            DB::commit();

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Surat Perintah Penyidikan berhasil disetujui.'
                ]);
            }

            return "
            <!DOCTYPE html>
            <html lang='id'>
            <head>
                <meta charset='UTF-8'>
                <title>Validasi Berhasil</title>
                <link rel='preconnect' href='https://fonts.googleapis.com'>
                <link rel='preconnect' href='https://fonts.gstatic.com' crossorigin>
                <link href='https://fonts.googleapis.com/css2?family=Open+Sans&display=swap' rel='stylesheet'>
                <link rel='stylesheet' href='" . asset('css/bootstrap1x.min.css') . "'>
                <link rel='stylesheet' href='" . asset('css/style2x.css') . "'>
                <script src='" . asset('libs/sweetalert/sweetalert2.all.min.js') . "'></script>
                <style>
                    body {
                        font-family: 'Open Sans', sans-serif;
                        background-color: #f4f6f9;
                    }
                </style>
            </head>
            <body>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Surat Perintah Penyidikan berhasil disetujui.',
                            showConfirmButton: false,
                            timer: 1500,
                            timerProgressBar: true,
                            allowOutsideClick: false,
                            allowEscapeKey: false
                        }).then(() => {
                            window.close();
                        });
                    });
                </script>
            </body>
            </html>";
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan. Surat Perintah Penyidikan gagal disetujui.');
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
                $suratPerintahPenyidikanDocument = SuratPerintahPenyidikanDocument::with(['accident', 'documentCategory', 'status'])
                    ->where('id', $id)
                    ->first();
                $suratPerintahPenyidikanDocument->status_id = $rejectStatusOption;

                if($rejectStatusOption == '4'){
                    $suratPerintahPenyidikanDocument->messages = [
                        'reason_approval_rejected' => $rejectReason,
                    ];
                }else if($rejectStatusOption == '5'){
                    $suratPerintahPenyidikanDocument->messages = [
                        'reason_approval_file_rejected' => $rejectReason,
                    ];
                }
    
                $status = Status::where('id', $rejectStatusOption)->first();
                $docValidationService->logging([
                    'accident_id' => $suratPerintahPenyidikanDocument->accident_id,
                    'document_id' => $suratPerintahPenyidikanDocument->id,
                    'document_category_id' => $suratPerintahPenyidikanDocument->document_category_id,
                    'updated_status_id' => $status->id,
                    'rejected_by_id' => $this->userAuth->id,
                    'reject_reason' => $rejectReason,
                    'accident_number' => $suratPerintahPenyidikanDocument->accident->no_lp ?? '-',
                    'document_number' => $suratPerintahPenyidikanDocument->document_number,
                    'document_category_name' => $suratPerintahPenyidikanDocument->documentCategory->name ?? '-',
                    'model_class' => get_class(new SuratPerintahPenyidikanDocument()),
                    'updated_status_name' => $status->name,
                    'rejected_by_name' => PeopleNameHelper::getFullName($this->userAuth->first_title, $this->userAuth->first_name, $this->userAuth->last_name, $this->userAuth->last_title),
                    'rejected_at' => $this->timestampNow
                ]);

                $suratPerintahPenyidikanDocument->rejected_at = $this->timestampNow;
                
                $suratPerintahPenyidikanDocument->save();

                DB::commit();

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Surat Perintah Penyidikan berhasil dikembalikan.'
                ]);
            }

            return "
                <!DOCTYPE html>
                <html lang='id'>
                <head>
                    <meta charset='UTF-8'>
                    <title>Validasi Berhasil</title>
                    <script src='" . asset('libs/sweetalert/sweetalert2.all.min.js') . "'></script>
                </head>
                <body>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Surat Perintah Penyidikan berhasil dikembalikan.',
                            showConfirmButton: false,
                            timer: 1500,
                            timerProgressBar: true,
                            allowOutsideClick: false,
                            allowEscapeKey: false
                        }).then(() => {
                            window.close();
                        });
                        });
                    </script>
                </body>
                </html>";
            } catch (\Exception $e) {
                DB::rollback();
                return redirect()->back()->with('error', 'Terjadi kesalahan. Surat Perintah Penyidikan gagal ditolak.');
            }
        }else{
            return redirect()->back()->with('error', 'Terjadi kesalahan. Surat Perintah Penyidikan gagal ditolak.');
        }
    }
}
