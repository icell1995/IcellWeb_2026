<?php

namespace App\Http\Controllers\CMS\CaseDocumentValidation\Module;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

use App\Helpers\PeopleNameHelper;

use App\Services\DocValidation\DocValidationService;

use App\Models\Lib\Rank;
use App\Models\Lib\Position;
use App\Models\Officer;
use App\Models\Accident;
use App\Models\Lib\Gender;
use App\Models\Lib\Ethnic;
use App\Models\Lib\Job;
use App\Models\Lib\Religion;
use App\Models\Lib\Education;
use App\Models\Lib\MaritalStatus;
use App\Models\Lib\Location;
use App\Models\Lib\Timezone;
use App\Models\Lib\SuspectSource;
use App\Models\Lib\IdentityType;
use App\Models\Lib\CaseDegreeType;
use App\Models\Lib\Police;
use App\Models\Suspect;
use App\Models\Witness;
use App\Models\Opt\Status;

use App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument;
use App\Models\Doc\LaporanHasilGelarPerkaraDocument\LaporanHasilGelarPerkaraDocument;

class LaporanHasilGelarPerkaraDocumentValidationController extends Controller
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
        $accident = Accident::where('id',$accidentId)->first();
        $police = Police::with('parent')->where('id', $accident->polres_id)->first();
  
        $laporanHasilGelarPerkaraDocumentId = $id;
        $laporanHasilGelarPerkaraDocument = LaporanHasilGelarPerkaraDocument::with(['suspects', 'laporanHasilGelarPerkaraDocumentOfficers', 'laporanHasilGelarPerkaraDocumentFiles', 'laporanHasilGelarPerkaraDocumentAttachment'])->where('id', $laporanHasilGelarPerkaraDocumentId)->first();
        // dd($laporanHasilGelarPerkaraDocument->suspects);
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

        $ranks = Rank::where('is_active', true)
            ->wherePolri()
            ->orderBy('sort')
            ->get();

        $positions = Position::whereIn('police_id', [$police->id, $police->parent->id ?? null, $police->parent->parent->id ?? null])
            ->where('is_active', true)
            ->orderBy('sort')
            ->get();

        $caseDegreeTypes = CaseDegreeType::where('is_active', true)
            ->orderBy('sort')
            ->get();

        $timezones = Timezone::where('is_active', true)
                        ->orderBy('sort')
                        ->get();

        $suspectSources = SuspectSource::where('group', 'LAPORAN_HASIL_GELAR_PERKARA')
                            ->where('is_active', true)
                            ->orderBy('sort')
                            ->get();

        $identityTypes = IdentityType::where('is_active', true)
                            ->orderBy('sort')
                            ->get();

        $genders = Gender::where('is_active', true)
                        ->orderBy('sort')
                        ->get();

        $ethnics = Ethnic::where('is_active', true)
                        ->orderBy('sort')
                        ->get();

        $jobs = Job::where('is_active', true)
                        ->orderBy('sort')
                        ->get();

        $religions = Religion::where('is_active', true)
                            ->orderBy('sort')
                            ->get();

        $educations = Education::where('is_active', true)
                                ->orderBy('sort')
                                ->get();

        $maritalStatuses = MaritalStatus::where('is_active', true)
                                    ->orderBy('sort')
                                    ->get();

        $countries = Location::where('is_active', true)
                                ->where('class', 'COUNTRY')
                                ->orderBy('sort')
                                ->get();

        $suspects = Suspect::where('accident_id', $accidentId)->get();

        $witnesses = Witness::where('accident_id', $accidentId)->get();
        
        $suratPerintahPenyidikanDocuments = SuratPerintahPenyidikanDocument::where('accident_id', $accidentId)->get();

        $rejectStatusOptions = Status::where('is_active', true)->orderBy('sort');
        if($laporanHasilGelarPerkaraDocument->documentCategory->is_digital_signature == true){
            $rejectStatusOptions = $rejectStatusOptions->whereIn('id', ['6']);
        } else {
            $rejectStatusOptions = $rejectStatusOptions->whereIn('id', ['4', '5']);
        }
        $rejectStatusOptions = $rejectStatusOptions->get();

        $viewData =[
            'authorizedSignatories' => $authorizedSignatories,
            'accidentId' => $accidentId,
            'accident' => $accident,
            'laporanHasilGelarPerkaraDocument' => $laporanHasilGelarPerkaraDocument,
            'laporanHasilGelarPerkaraDocumentId' => $laporanHasilGelarPerkaraDocumentId,
            'ranks' => $ranks,
            'positions' => $positions,
            'officers' => $laporanHasilGelarPerkaraDocument->laporanHasilGelarPerkaraDocumentOfficers,
            'suratPerintahPenyidikanDocuments' => $suratPerintahPenyidikanDocuments,
            'timezones' => $timezones,
            'suspectSources' => $suspectSources,
            'identityTypes' => $identityTypes,
            'genders' => $genders,
            'ethnics' => $ethnics,
            'jobs' => $jobs,
            'religions' => $religions,
            'educations' => $educations,
            'maritalStatuses' => $maritalStatuses,
            'countries' => $countries,
            'caseDegreeTypes' => $caseDegreeTypes,
            'laporanHasilGelarPerkaraDocumentOfficers' => $laporanHasilGelarPerkaraDocument->laporanHasilGelarPerkaraDocumentOfficers,
            'suspects' => $suspects,
            'arrerstedSuspects' => $suspects->where('flag', Suspect::getEnumOption('flag', 'TERSANGKA')),
            'revocationSuspects' => $suspects->where('flag', Suspect::getEnumOption('flag', 'TERSANGKA')),
            'witnesses' => $witnesses,
            'rejectStatusOptions' => $rejectStatusOptions,
        ];
 
        return view('cms.case-document-validation.modules.laporan-hasil-gelar-perkara-document-validation.validation', $viewData);
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
            $laporanHasilGelarPerkaraDocument = LaporanHasilGelarPerkaraDocument::with(['accident', 'documentCategory', 'status'])
                ->where('id', $id)
                ->first();

            if($isApprovedBoolean == true) {
                $laporanHasilGelarPerkaraDocument->status_id = '86';
                $laporanHasilGelarPerkaraDocument->released_at = $this->timestampNow;

                if($isLegacyBoolean == true) {
                    $laporanHasilGelarPerkaraDocument->is_legacy = true;
                }else{
                    $laporanHasilGelarPerkaraDocument->is_legacy = false;
                }
            } else {
                $laporanHasilGelarPerkaraDocument->status_id = '4';

            }

            $status = Status::where('id', '86')->first();
            $docValidationService->logging([
                'accident_id' => $laporanHasilGelarPerkaraDocument->accident_id,
                'document_id' => $laporanHasilGelarPerkaraDocument->id,
                'document_category_id' => $laporanHasilGelarPerkaraDocument->document_category_id,
                'updated_status_id' => $status->id,
                'approved_by_id' => $this->userAuth->id,
                'accident_number' => $laporanHasilGelarPerkaraDocument->accident->no_lp ?? '-',
                'document_number' => $laporanHasilGelarPerkaraDocument->document_number,
                'document_category_name' => $laporanHasilGelarPerkaraDocument->documentCategory->name ?? '-',
                'model_class' => get_class(new LaporanHasilGelarPerkaraDocument()),
                'updated_status_name' => $status->name,
                'approved_by_name' => PeopleNameHelper::getFullName($this->userAuth->first_title, $this->userAuth->first_name, $this->userAuth->last_name, $this->userAuth->last_title),
                'approved_at' => $this->timestampNow
            ]);

            $laporanHasilGelarPerkaraDocument->approved_at = $this->timestampNow;
            
            $laporanHasilGelarPerkaraDocument->save();

            DB::commit();

            return redirect()->route('cms.case-document-validation.index')->with('success', 'Laporan Hasil Gelar Perkara berhasil disetujui.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan. Laporan Hasil Gelar Perkara gagal disetujui.');
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
                $laporanHasilGelarPerkaraDocument = LaporanHasilGelarPerkaraDocument::with(['accident', 'documentCategory', 'status'])
                    ->where('id', $id)
                    ->first();
                $laporanHasilGelarPerkaraDocument->status_id = $rejectStatusOption;

                if($rejectStatusOption == '4'){
                    $laporanHasilGelarPerkaraDocument->messages = [
                        'reason_approval_rejected' => $rejectReason,
                    ];
                }else if($rejectStatusOption == '5'){
                    $laporanHasilGelarPerkaraDocument->messages = [
                        'reason_approval_file_rejected' => $rejectReason,
                    ];
                }

                $status = Status::where('id', $rejectStatusOption)->first();
                $docValidationService->logging([
                    'accident_id' => $laporanHasilGelarPerkaraDocument->accident_id,
                    'document_id' => $laporanHasilGelarPerkaraDocument->id,
                    'document_category_id' => $laporanHasilGelarPerkaraDocument->document_category_id,
                    'updated_status_id' => $status->id,
                    'rejected_by_id' => $this->userAuth->id,
                    'reject_reason' => $rejectReason,
                    'accident_number' => $laporanHasilGelarPerkaraDocument->accident->no_lp ?? '-',
                    'document_number' => $laporanHasilGelarPerkaraDocument->document_number,
                    'document_category_name' => $laporanHasilGelarPerkaraDocument->documentCategory->name ?? '-',
                    'model_class' => get_class(new LaporanHasilGelarPerkaraDocument()),
                    'updated_status_name' => $status->name,
                    'rejected_by_name' => PeopleNameHelper::getFullName($this->userAuth->first_title, $this->userAuth->first_name, $this->userAuth->last_name, $this->userAuth->last_title),
                    'rejected_at' => $this->timestampNow
                ]);
                
                $laporanHasilGelarPerkaraDocument->rejected_at = $this->timestampNow;
    
                $laporanHasilGelarPerkaraDocument->save();

                DB::commit();

                return redirect()->route('cms.case-document-validation.index')->with('success', 'Laporan Hasil Gelar Perkara berhasil dikembalikan.');
            } catch (\Exception $e) {
                DB::rollback();
                return redirect()->back()->with('error', 'Terjadi kesalahan. Laporan Hasil Gelar Perkara gagal ditolak.');
            }
        }else{
            return redirect()->back()->with('error', 'Terjadi kesalahan. Laporan Hasil Gelar Perkara gagal ditolak.');
        }
    }
}
