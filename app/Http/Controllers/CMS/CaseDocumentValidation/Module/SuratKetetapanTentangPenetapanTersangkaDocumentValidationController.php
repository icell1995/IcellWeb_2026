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
use App\Models\Lib\Prosecutor;
use App\Models\Suspect;
use App\Models\Opt\Status;

use App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument;
use App\Models\Doc\LaporanHasilGelarPerkaraDocument\LaporanHasilGelarPerkaraDocument;
use App\Models\Doc\SuratKetetapanTentangPenetapanTersangkaDocument\SuratKetetapanTentangPenetapanTersangkaDocument;

class SuratKetetapanTentangPenetapanTersangkaDocumentValidationController extends Controller
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
        $suratKetetapanTentangPenetapanTersangkaDocumentId = $id;

        $suratKetetapanTentangPenetapanTersangkaDocument = SuratKetetapanTentangPenetapanTersangkaDocument::with(['suratKetetapanTentangPenetapanTersangkaDocumentOfficers', 'suspect', 'suratKetetapanTentangPenetapanTersangkaDocumentAttachment'])->where('id', $suratKetetapanTentangPenetapanTersangkaDocumentId)->first();
        $accident = Accident::where('id',$accidentId)->first();
       
        $policeId = $accident->polres_id;

        $laporanHasilGelarPerkaraDocumentSuspectDeterminations = LaporanHasilGelarPerkaraDocument::whereHas('caseDegreeType', function($query){
                $query->where('id', '1');
        })->where('accident_id', $accidentId)->get();
        
        $suspects = [];

        if($suratKetetapanTentangPenetapanTersangkaDocument->suspect_source_id == '4'){
            $suspects = Suspect::where('accident_id', $accidentId)->get();

        }elseif($suratKetetapanTentangPenetapanTersangkaDocument->suspect_source_id == '5'){
            $suspects = Suspect::whereHas('laporanHasilGelarPerkaraDocuments', function($query) use ($suratKetetapanTentangPenetapanTersangkaDocument){
                $query->where('laporan_hasil_gelar_perkara_documents.id', $suratKetetapanTentangPenetapanTersangkaDocument->laporan_hasil_gelar_perkara_document_id);
            })->where('accident_id', $accidentId)->get();
        }

        $ranks = Rank::where('is_active', true)->wherePolri()->orderBy('sort')->get();

        $positions = Position::where('is_active', true)->orderBy('sort')->get();

        $authorizedSignatories = Officer::withRelated()
            ->selectFullName()
            ->where('police_id', $policeId)
            ->whereHasUserActive()
            ->hasDataComplete()
            ->signatory()
            ->active()
            ->valid()
            ->orderBy('first_name')
            ->get();

        $timezones = Timezone::where('is_active', true)
                            ->orderBy('sort')
                            ->get();

        $suspectSources = SuspectSource::where('is_active', true)
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

        $prosecutors = Prosecutor::whereHas('polices', function($query) use ($policeId){
                $query->where('lib.polices.id', $policeId);
            })
            ->active()
            ->orderBy('sort')
            ->get();

        $suspectSources = SuspectSource::where('group', 'SURAT_KETETAPAN_TENTANG_PENETAPAN_TERSANGKA')
                                ->where('is_active', true)
                                ->orderBy('sort')
                                ->get();
                                
        $suratPerintahPenyidikanDocuments = SuratPerintahPenyidikanDocument::where('accident_id', $accidentId)->get();

        $rejectStatusOptions = Status::where('is_active', true)->orderBy('sort');
        if($suratKetetapanTentangPenetapanTersangkaDocument->documentCategory->is_digital_signature == true){
            $rejectStatusOptions = $rejectStatusOptions->whereIn('id', ['6']);
        } else {
            $rejectStatusOptions = $rejectStatusOptions->whereIn('id', ['4', '5']);
        }
        $rejectStatusOptions = $rejectStatusOptions->get();

        $viewData =[
            'authorizedSignatories' => $authorizedSignatories,
            'accidentId' => $accidentId,
            'accident' => $accident,
            'suratKetetapanTentangPenetapanTersangkaDocument' => $suratKetetapanTentangPenetapanTersangkaDocument,
            'suratKetetapanTentangPenetapanTersangkaDocumentId' => $suratKetetapanTentangPenetapanTersangkaDocumentId,
            'ranks' => $ranks,
            'positions' => $positions,
            'officers' => $suratKetetapanTentangPenetapanTersangkaDocument->suratKetetapanTentangPenetapanTersangkaDocumentOfficers,
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
            'prosecutors' => $prosecutors,
            'suspectSources' => $suspectSources,
            'laporanHasilGelarPerkaraDocumentSuspectDeterminations' => $laporanHasilGelarPerkaraDocumentSuspectDeterminations,
            'suratKetetapanTentangPenetapanTersangkaDocumentOfficers' => $suratKetetapanTentangPenetapanTersangkaDocument->suratKetetapanTentangPenetapanTersangkaDocumentOfficers,
            'suspects' => $suspects,
            'rejectStatusOptions' => $rejectStatusOptions,
        ];

        return view('cms.case-document-validation.modules.surat-ketetapan-tentang-penetapan-tersangka-document-validation.validation', $viewData);
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
            $suratKetetapanTentangPenetapanTersangkaDocument = SuratKetetapanTentangPenetapanTersangkaDocument::with(['accident', 'documentCategory', 'status'])
                ->where('id', $id)
                ->first();
            
            if($isApprovedBoolean == true) {
                $suratKetetapanTentangPenetapanTersangkaDocument->status_id = '86';
                $suratKetetapanTentangPenetapanTersangkaDocument->released_at = $this->timestampNow;

                if($isLegacyBoolean == true) {
                    $suratKetetapanTentangPenetapanTersangkaDocument->is_legacy = true;
                }else{
                    $suratKetetapanTentangPenetapanTersangkaDocument->is_legacy = false;
                }
            }else{
                $suratKetetapanTentangPenetapanTersangkaDocument->status_id = '4';
            }

            $status = Status::where('id', '86')->first();
            $docValidationService->logging([
                'accident_id' => $suratKetetapanTentangPenetapanTersangkaDocument->accident_id,
                'document_id' => $suratKetetapanTentangPenetapanTersangkaDocument->id,
                'document_category_id' => $suratKetetapanTentangPenetapanTersangkaDocument->document_category_id,
                'updated_status_id' => $status->id,
                'approved_by_id' => $this->userAuth->id,
                'accident_number' => $suratKetetapanTentangPenetapanTersangkaDocument->accident->no_lp ?? '-',
                'document_number' => $suratKetetapanTentangPenetapanTersangkaDocument->document_number,
                'document_category_name' => $suratKetetapanTentangPenetapanTersangkaDocument->documentCategory->name ?? '-',
                'model_class' => get_class(new SuratKetetapanTentangPenetapanTersangkaDocument()),
                'updated_status_name' => $status->name,
                'approved_by_name' => PeopleNameHelper::getFullName($this->userAuth->first_title, $this->userAuth->first_name, $this->userAuth->last_name, $this->userAuth->last_title),
                'approved_at' => $this->timestampNow
            ]);

            $suratKetetapanTentangPenetapanTersangkaDocument->approved_at = $this->timestampNow;

            $suratKetetapanTentangPenetapanTersangkaDocument->save();
            
            DB::commit();

            return redirect()->route('cms.case-document-validation.index')->with('success', 'Surat Ketetapan Tentang Penetapan Tersangka berhasil disetujui.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan. Surat Ketetapan Tentang Penetapan Tersangka gagal disetujui.');
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
                $suratKetetapanTentangPenetapanTersangkaDocument = SuratKetetapanTentangPenetapanTersangkaDocument::with(['accident', 'documentCategory', 'status'])
                    ->where('id', $id)
                    ->first();

                $suratKetetapanTentangPenetapanTersangkaDocument->status_id = $rejectStatusOption;

                if($rejectStatusOption == '4'){
                    $suratKetetapanTentangPenetapanTersangkaDocument->messages = [
                        'reason_approval_rejected' => $rejectReason,
                    ];
                }else if($rejectStatusOption == '5'){
                    $suratKetetapanTentangPenetapanTersangkaDocument->messages = [
                        'reason_approval_file_rejected' => $rejectReason,
                    ];
                }

                $status = Status::where('id', $rejectStatusOption)->first();
                $docValidationService->logging([
                    'accident_id' => $suratKetetapanTentangPenetapanTersangkaDocument->accident_id,
                    'document_id' => $suratKetetapanTentangPenetapanTersangkaDocument->id,
                    'document_category_id' => $suratKetetapanTentangPenetapanTersangkaDocument->document_category_id,
                    'updated_status_id' => $status->id,
                    'rejected_by_id' => $this->userAuth->id,
                    'reject_reason' => $rejectReason,
                    'accident_number' => $suratKetetapanTentangPenetapanTersangkaDocument->accident->no_lp ?? '-',
                    'document_number' => $suratKetetapanTentangPenetapanTersangkaDocument->document_number,
                    'document_category_name' => $suratKetetapanTentangPenetapanTersangkaDocument->documentCategory->name ?? '-',
                    'model_class' => get_class(new SuratKetetapanTentangPenetapanTersangkaDocument()),
                    'updated_status_name' => $status->name,
                    'rejected_by_name' => PeopleNameHelper::getFullName($this->userAuth->first_title, $this->userAuth->first_name, $this->userAuth->last_name, $this->userAuth->last_title),
                    'rejected_at' => $this->timestampNow
                ]);

                $suratKetetapanTentangPenetapanTersangkaDocument->rejected_at = $this->timestampNow;
    
                $suratKetetapanTentangPenetapanTersangkaDocument->save();

                DB::commit();

                return redirect()->route('cms.case-document-validation.index')->with('success', 'Surat Pemberitahuan Dimulainya Penyidikan dikembalikan.');
            } catch (\Exception $e) {
                DB::rollback();
                return redirect()->back()->with('error', 'Terjadi kesalahan. Surat Ketetapan Tentang Penetapan Tersangka gagal ditolak.');
            }
        }else{
            return redirect()->back()->with('error', 'Terjadi kesalahan. Surat Ketetapan Tentang Penetapan Tersangka gagal ditolak.');
        }
    }
}
