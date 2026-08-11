<?php

namespace App\Http\Controllers\CMS\CaseDocumentValidation\Module;

use App\Helpers\PeopleNameHelper;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

use App\Services\Whatsapp\WhatsappWebhookService;
use App\Services\DocValidation\DocValidationService;

use App\Models\Lib\Rank;
use App\Models\Lib\Position;
use App\Models\Officer;
use App\Models\Accident;
use App\Models\Suspect;
use App\Models\Informant;
use App\Models\Lib\Prosecutor;
use App\Models\Lib\Court;
use App\Models\Lib\SuspectSource;
use App\Models\Lib\DocumentClassification;
use App\Models\Opt\Status;

use App\Models\Doc\SuratPemberitahuanDimulainyaPenyidikanDocument\SuratPemberitahuanDimulainyaPenyidikanDocument;
use App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument;
use App\Models\Doc\SuratPerintahTugasDocument\SuratPerintahTugasDocument;
use App\Models\Doc\SuratPerintahPenyelidikanDocument\SuratPerintahPenyelidikanDocument;
use App\Models\Doc\LaporanHasilGelarPerkaraDocument\LaporanHasilGelarPerkaraDocument;
use App\Models\Doc\SuratKetetapanTentangPenetapanTersangkaDocument\SuratKetetapanTentangPenetapanTersangkaDocument;

class SuratPemberitahuanDimulainyaPenyidikanDocumentValidationController extends Controller
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
        $suratPemberitahuanDimulainyaPenyidikanDocumentId = $id;

        $suratPemberitahuanDimulainyaPenyidikanDocument = SuratPemberitahuanDimulainyaPenyidikanDocument::with(['suratPemberitahuanDimulainyaPenyidikanDocumentOfficers', 'suratPerintahPenyidikanDocument', 'suratPemberitahuanDimulainyaPenyidikanDocumentAttachment'])->where('id', $suratPemberitahuanDimulainyaPenyidikanDocumentId)->first();
        $accident = Accident::where('id', $accidentId)->first();

        $selectedSuspects = $suratPemberitahuanDimulainyaPenyidikanDocument->suspects()->get()->pluck('id')->toArray();

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

        $suratPerintahPenyidikanDocuments = SuratPerintahPenyidikanDocument::where('accident_id', $accidentId)->get();

        $suratPerintahTugasDocuments = SuratPerintahTugasDocument::where('accident_id', $accidentId)
            ->whereHasMorph('related', 'App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument')
            ->get();

        $suspectSources = SuspectSource::where('is_active', true)
            ->orderBy('sort')
            ->get();

        $prosecutors = Prosecutor::where('is_active', true)
            ->orderBy('sort')
            ->get();

        $courts = Court::where('is_active', true)
            ->orderBy('sort')
            ->get();

        $suspectSources = SuspectSource::where('group', 'SURAT_KETETAPAN_TENTANG_PENETAPAN_TERSANGKA')
            ->where('is_active', true)
            ->orderBy('sort')
            ->get();

        $documentClassifications = DocumentClassification::where('group', 'SURAT_PEMBERITAHUAN_DIMULAINYA_PENYIDIKAN')
            ->where('is_active', true)
            ->orderBy('sort')
            ->get();

        $suspects = Suspect::where('accident_id', $accidentId)
            ->where('flag', Suspect::getEnumOption('flag', 'TERSANGKA'))
            ->where('class', Suspect::getEnumOption('class', 'DETERMINATION'))
            ->where('group', Suspect::getEnumOption('group', 'SURAT_KETETAPAN_TENTANG_PENETAPAN_TERSANGKA'))
            ->get();

        $informants = Informant::where('accident_id', $accidentId)->get();

        $reportedSuspects = Suspect::where('accident_id', $accidentId)
            ->where('flag', Suspect::getEnumOption('flag', 'TERLAPOR'))
            ->get();

        $rejectStatusOptions = Status::where('is_active', true)->orderBy('sort');
        if($suratPemberitahuanDimulainyaPenyidikanDocument->documentCategory->is_digital_signature == true){
            $rejectStatusOptions = $rejectStatusOptions->whereIn('id', ['4', '6']);
        } else {
            $rejectStatusOptions = $rejectStatusOptions->whereIn('id', ['4', '5']);
        }
        $rejectStatusOptions = $rejectStatusOptions->get();

        $suratPerintahPenyidikanDocument = $suratPemberitahuanDimulainyaPenyidikanDocument->suratPerintahPenyidikanDocument;
        $suratPerintahPenyidikanDocumentLaws = $suratPerintahPenyidikanDocument->suratPerintahPenyidikanDocumentLaws;
        $crimeConstitutionText = '';
        $countSuratPerintahPenyidikanDocumentLaw = $suratPerintahPenyidikanDocumentLaws->count();
        $suratPerintahPenyidikanDocumentLawIteration = 1;
        foreach($suratPerintahPenyidikanDocumentLaws as $suratPerintahPenyidikanDocumentLaw){
            $coma = ($suratPerintahPenyidikanDocumentLawIteration == $countSuratPerintahPenyidikanDocumentLaw) ? '' : ', ';

            if($suratPerintahPenyidikanDocumentLaw->flag == 'MAIN'){
                $crimeConstitution = $suratPerintahPenyidikanDocumentLaw->crimeConstitution ?? '';
                $crimeConstitutionChapter = $suratPerintahPenyidikanDocumentLaw->constitution_chapter ?? '';
                $crimeConstitutionName = $crimeConstitution->name ?? '';
                $crimeConstitutionText .= $crimeConstitutionChapter . $coma;
            }elseif($suratPerintahPenyidikanDocumentLaw->flag == 'ADDITIONAL'){
                $crimeConstitution = $suratPerintahPenyidikanDocumentLaw->constitution ?? '';
                $crimeConstitutionText .= $crimeConstitution . $coma;
            }

            $suratPerintahPenyidikanDocumentLawIteration++;
        }

        //generating preview
        $filePreviewUrl = NULL;
        if(isset($suratPemberitahuanDimulainyaPenyidikanDocument->suratPemberitahuanDimulainyaPenyidikanDocumentAttachment->name)){
            /*$response = Http::attach(
                    'attachment', 
                    File::get(public_path('documents/attachments/' . $suratPemberitahuanDimulainyaPenyidikanDocument->suratPemberitahuanDimulainyaPenyidikanDocumentAttachment->name)), 
                    $suratPemberitahuanDimulainyaPenyidikanDocument->suratPemberitahuanDimulainyaPenyidikanDocumentAttachment->name
                )
                ->post(env('DISCORD_WEBHOOK_URL_ATTACHMENT_PREVIEW'));
    
            $responseDecoded = json_decode($response, true);

            if(isset($responseDecoded["attachments"][0]["url"])){
                $filePreviewUrl = 'https://view.officeapps.live.com/op/view.aspx?src=' . $responseDecoded["attachments"][0]["url"];
            }*/

            $path = Storage::disk('s3')->putFileAs(
                'AttachmentPreview', 
                public_path('documents/attachments/' . $suratPemberitahuanDimulainyaPenyidikanDocument->suratPemberitahuanDimulainyaPenyidikanDocumentAttachment->name),
                $suratPemberitahuanDimulainyaPenyidikanDocument->suratPemberitahuanDimulainyaPenyidikanDocumentAttachment->name,
                'public'
            );

            $url = Storage::disk('s3')->url($path);

            $filePreviewUrl = 'https://view.officeapps.live.com/op/view.aspx?src=' . $url;
        }

        $viewData = [
            'authorizedSignatories' => $authorizedSignatories,
            'accidentId' => $accidentId,
            'accident' => $accident,
            'suratPemberitahuanDimulainyaPenyidikanDocument' => $suratPemberitahuanDimulainyaPenyidikanDocument,
            'suratPemberitahuanDimulainyaPenyidikanDocumentId' => $suratPemberitahuanDimulainyaPenyidikanDocumentId,
            'suratPemberitahuanDimulainyaPenyidikanDocumentOfficers' => $suratPemberitahuanDimulainyaPenyidikanDocument->suratPemberitahuanDimulainyaPenyidikanDocumentOfficers,
            'ranks' => $ranks,
            'positions' => $positions,
            'officers' => $suratPemberitahuanDimulainyaPenyidikanDocument->suratPemberitahuanDimulainyaPenyidikanDocumentOfficers,
            'suratPerintahPenyidikanDocuments' => $suratPerintahPenyidikanDocuments,
            'suspectSources' => $suspectSources,
            'prosecutors' => $prosecutors,
            'courts' => $courts,
            'suspectSources' => $suspectSources,
            'documentClassifications' => $documentClassifications,
            'suratPerintahTugasDocuments' => $suratPerintahTugasDocuments,
            'suspects' => $suspects,
            'informants' => $informants,
            'reportedSuspects' => $reportedSuspects,
            'selectedSuspects' => $selectedSuspects,
            'rejectStatusOptions' => $rejectStatusOptions,
            'filePreviewUrl' => $filePreviewUrl,
            'crimeConstitutionText' => $crimeConstitutionText,
        ];

        return view('cms.case-document-validation.modules.surat-pemberitahuan-dimulainya-penyidikan-document-validation.validation', $viewData);
    }

    public function approveValidation(Request $request, $id)
    {
        $docValidationService = new DocValidationService();
        
        $isApproved = $request->input('isApproved');
        $isLegacy = $request->input('isLegacy');
        $isApplyLeagcySettingToAllDocument = $request->input('isApplyLeagcySettingToAllDocument');
        $currentStatusId = $request->input('currentStatusId');

        $isApprovedBoolean = filter_var($isApproved, FILTER_VALIDATE_BOOLEAN);
        $isLegacyBoolean = filter_var($isLegacy, FILTER_VALIDATE_BOOLEAN);

        // $whatsappWebhookService = new WhatsappWebhookService();

        DB::beginTransaction();
        try{
            $suratPemberitahuanDimulainyaPenyidikanDocument = SuratPemberitahuanDimulainyaPenyidikanDocument::with([
                    'accident',
                    'documentCategory', 
                    'status',
                    'suratPemberitahuanDimulainyaPenyidikanDocumentOfficers',
                ])
                ->where('id', $id)
                ->lockForUpdate()
                ->first();
            
            $updatedStatusId = '86';

            if ($isApprovedBoolean == true && $currentStatusId !== null) {
                if ((string)$suratPemberitahuanDimulainyaPenyidikanDocument->status_id !== (string)$currentStatusId) {
                    DB::rollback();
                    return redirect()->back()->with('error', 'Dokumen ini sudah divalidasi.');
                }
            }

            if($isApprovedBoolean == true) {
                $documentSignatory = $suratPemberitahuanDimulainyaPenyidikanDocument
                    ->suratPemberitahuanDimulainyaPenyidikanDocumentOfficers
                    ->where('class', 'SIGNATORY')
                    ->first();

                $accidentPoliceId = $suratPemberitahuanDimulainyaPenyidikanDocument->accident->police_id;
                $accidentId = $suratPemberitahuanDimulainyaPenyidikanDocument->accident_id;
                $policeAdmins = Officer::with(['police'])
                    ->where('police_id', $accidentPoliceId)
                    ->where('is_active', true)
                    ->where('flag', 'ADMIN')
                    ->get();

                if($suratPemberitahuanDimulainyaPenyidikanDocument->status_id == '9'){

                    if($isLegacyBoolean == true){
                        $suratPemberitahuanDimulainyaPenyidikanDocument->status_id = '85';
                        $updatedStatusId = '85';
                    }else{
                        $suratPemberitahuanDimulainyaPenyidikanDocument->status_id = '9';
                        $updatedStatusId = '9';
                    }

                }elseif($suratPemberitahuanDimulainyaPenyidikanDocument->status_id == '12'){

                    if($isLegacyBoolean == true){
                        $suratPemberitahuanDimulainyaPenyidikanDocument->status_id = '85';
                        $updatedStatusId = '85';
                    }else{
                        $suratPemberitahuanDimulainyaPenyidikanDocument->status_id = '9';
                        $updatedStatusId = '9';
              
                        // Send whatsapp message to signatory
                        // if(!empty($documentSignatory->position->name)){
                        //     $officer = Officer::where('register_number', $documentSignatory->register_number)->first();

                        //     if(!empty($officer->phone_number)){
                        //         $whatsappWebhookService->sendMessageTemplate(
                        //             destinationPhoneNumber: $officer->phone_number, 
                        //             templateId: env('WHATSAPP_BOT_TEMPLATE_ID_DOC_READY_TO_SIGNED'),
                        //             props: [
                        //                 '{positionName}' => $documentSignatory->position->name,
                        //                 '{documentNumber}' => $suratPemberitahuanDimulainyaPenyidikanDocument->document_number,
                        //                 '{documentName}' => $suratPemberitahuanDimulainyaPenyidikanDocument->documentCategory->name ?? '',
                        //                 '{accidentNumber}' => $suratPemberitahuanDimulainyaPenyidikanDocument->accident->no_lp ?? '',
                        //             ]
                        //         );
                        //     }
                        // }
                    }

                    // Send whatsapp message to admin
                    // foreach($policeAdmins as $policeAdmin){
                    //     if(!empty($policeAdmin->phone_number)){
                    //         $policeName = (isset($policeAdmin->police->full_name)) ? $policeAdmin->police->full_name : '';
                    //         $whatsappWebhookService->sendMessageTemplate(
                    //             destinationPhoneNumber: $policeAdmin->phone_number,
                    //             templateId: env('WHATSAPP_BOT_TEMPLATE_ID_DOC_VALIDATED'),
                    //             props: [
                    //                 '{positionName}' => 'ADMIN ' . $policeName,
                    //                 '{documentNumber}' => $suratPemberitahuanDimulainyaPenyidikanDocument->document_number,
                    //                 '{documentName}' => $suratPemberitahuanDimulainyaPenyidikanDocument->documentCategory->name ?? '',
                    //                 '{accidentNumber}' => $suratPemberitahuanDimulainyaPenyidikanDocument->accident->no_lp ?? '',
                    //             ]
                    //         );
                    //     }
                    // }
                    
                }else{
                    $suratPemberitahuanDimulainyaPenyidikanDocument->status_id = '86';
                    $updatedStatusId = '86';
                }
                
                $suratPemberitahuanDimulainyaPenyidikanDocument->released_at = $this->timestampNow;

                if($isLegacyBoolean == true) {
                    $suratPemberitahuanDimulainyaPenyidikanDocument->is_legacy = true;

                    if($isApplyLeagcySettingToAllDocument == true){
                        SuratPerintahPenyelidikanDocument::where('accident_id', $accidentId)
                            ->whereIn('status_id', ['86'])
                            ->update([
                                'is_legacy' => true
                            ]);
                        
                        SuratPerintahPenyidikanDocument::where('accident_id', $accidentId)
                            ->whereIn('status_id', ['86'])
                            ->update([
                                'is_legacy' => true
                            ]);
    
                        SuratPerintahTugasDocument::where('accident_id', $accidentId)
                            ->whereIn('status_id', ['86'])
                            ->update([
                                'is_legacy' => true
                            ]);
    
                        LaporanHasilGelarPerkaraDocument::where('accident_id', $accidentId)
                            ->whereIn('status_id', ['86'])
                            ->update([
                                'is_legacy' => true
                            ]);
    
                        SuratKetetapanTentangPenetapanTersangkaDocument::where('accident_id', $accidentId)
                            ->whereIn('status_id', ['86'])
                            ->update([
                                'is_legacy' => true
                            ]);
                    }

                }else{
                    $suratPemberitahuanDimulainyaPenyidikanDocument->is_legacy = false;

                    if($isApplyLeagcySettingToAllDocument == true){
                        SuratPerintahPenyelidikanDocument::where('accident_id', $accidentId)
                            ->whereIn('status_id', ['86'])
                            ->update([
                                'is_legacy' => false
                            ]);
                        
                        SuratPerintahPenyidikanDocument::where('accident_id', $accidentId)
                            ->whereIn('status_id', ['86'])
                            ->update([
                                'is_legacy' => false
                            ]);
    
                        SuratPerintahTugasDocument::where('accident_id', $accidentId)
                            ->whereIn('status_id', ['86'])
                            ->update([
                                'is_legacy' => false
                            ]);
    
                        LaporanHasilGelarPerkaraDocument::where('accident_id', $accidentId)
                            ->whereIn('status_id', ['86'])
                            ->update([
                                'is_legacy' => false
                            ]);
    
                        SuratKetetapanTentangPenetapanTersangkaDocument::where('accident_id', $accidentId)
                            ->whereIn('status_id', ['86'])
                            ->update([
                                'is_legacy' => false
                            ]);
                    }
                }
            }else{
                $suratPemberitahuanDimulainyaPenyidikanDocument->status_id = '4';
                $updatedStatusId = '4';
            }

            $status = Status::where('id', $updatedStatusId)->first();
            $docValidationService->logging([
                'accident_id' => $suratPemberitahuanDimulainyaPenyidikanDocument->accident_id,
                'document_id' => $suratPemberitahuanDimulainyaPenyidikanDocument->id,
                'document_category_id' => $suratPemberitahuanDimulainyaPenyidikanDocument->document_category_id,
                'updated_status_id' => $status->id,
                'approved_by_id' => $this->userAuth->id,
                'accident_number' => $suratPemberitahuanDimulainyaPenyidikanDocument->accident->no_lp ?? '-',
                'document_number' => $suratPemberitahuanDimulainyaPenyidikanDocument->document_number,
                'document_category_name' => $suratPemberitahuanDimulainyaPenyidikanDocument->documentCategory->name ?? '-',
                'model_class' => get_class(new SuratPemberitahuanDimulainyaPenyidikanDocument()),
                'updated_status_name' => $status->name,
                'approved_by_name' => PeopleNameHelper::getFullName($this->userAuth->first_title, $this->userAuth->first_name, $this->userAuth->last_name, $this->userAuth->last_title),
                'approved_at' => $this->timestampNow
            ]);

            $suratPemberitahuanDimulainyaPenyidikanDocument->approved_at = $this->timestampNow;

            if ($isApprovedBoolean == true) {
                $affected = SuratPemberitahuanDimulainyaPenyidikanDocument::where('id', $id)
                    ->where('status_id', $currentStatusId)
                    ->update([
                        'status_id'   => $updatedStatusId,
                        'released_at' => $this->timestampNow,
                        'approved_at' => $this->timestampNow,
                        'is_legacy'   => $isLegacyBoolean,
                    ]);

                if ($affected === 0) {
                    DB::rollback();
                    return redirect()->back()->with('error', 'Dokumen ini sudah divalidasi oleh petugas lain. Silakan refresh halaman.');
                }
            } else {
                $suratPemberitahuanDimulainyaPenyidikanDocument->save();
            }

            DB::commit();

            return redirect()->route('cms.case-document-validation.index')->with('success', 'Surat Pemberitahuan Dimulainya Penyidikan disetujui.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan. Surat Pemberitahuan Dimulainya Penyidikan gagal disetujui.');
        }
    }
    
    public function rejectValidation(Request $request, $id)
    {
        $docValidationService = new DocValidationService();
        // $whatsappWebhookService = new WhatsappWebhookService();

        $isRejected = $request->input('isRejected');
        $rejectStatusOption = $request->input('rejectStatusOption');
        $rejectReason = htmlspecialchars($request->input('rejectReason'));

        $isRejectedBoolean = filter_var($isRejected, FILTER_VALIDATE_BOOLEAN);

        if($isRejectedBoolean == true) {
            DB::beginTransaction();
            try{
                $suratPemberitahuanDimulainyaPenyidikanDocument = SuratPemberitahuanDimulainyaPenyidikanDocument::with(['accident', 'documentCategory', 'status'])
                    ->where('id', $id)
                    ->first();
                $suratPemberitahuanDimulainyaPenyidikanDocument->status_id = $rejectStatusOption;

                if($rejectStatusOption == '4'){
                    $suratPemberitahuanDimulainyaPenyidikanDocument->messages = [
                        'reason_approval_rejected' => $rejectReason,
                    ];
                }else if($rejectStatusOption == '6'){
                    $suratPemberitahuanDimulainyaPenyidikanDocument->messages = [
                        'reason_approval_file_rejected' => $rejectReason,
                    ];
                }
                
                $accidentPoliceId = $suratPemberitahuanDimulainyaPenyidikanDocument->accident->police_id;
                $policeAdmins = Officer::with(['police'])
                    ->where('police_id', $accidentPoliceId)
                    ->where('is_active', true)
                    ->where('flag', 'ADMIN')
                    ->get();
                    
                // Send whatsapp message to admin
                // foreach($policeAdmins as $policeAdmin){
                //     if(!empty($policeAdmin->phone_number)){
                //         $policeName = (isset($policeAdmin->police->full_name)) ? $policeAdmin->police->full_name : '';
                //         $whatsappWebhookService->sendMessageTemplate(
                //             destinationPhoneNumber: $policeAdmin->phone_number,
                //             templateId: env('WHATSAPP_BOT_TEMPLATE_ID_DOC_VALIDATE_REJECTED'),
                //             props: [
                //                 '{positionName}' => 'ADMIN ' . $policeName,
                //                 '{documentNumber}' => $suratPemberitahuanDimulainyaPenyidikanDocument->document_number,
                //                 '{documentName}' => $suratPemberitahuanDimulainyaPenyidikanDocument->documentCategory->name ?? '',
                //                 '{accidentNumber}' => $suratPemberitahuanDimulainyaPenyidikanDocument->accident->no_lp ?? '',
                //                 '{rejectMessage}' => $rejectReason
                //             ]
                //         );
                //     }
                // }

                // $suratPemberitahuanDimulainyaPenyidikanDocument->id = Str::uuid();

                $status = Status::where('id', $rejectStatusOption)->first();
                $docValidationService->logging([
                    'accident_id' => $suratPemberitahuanDimulainyaPenyidikanDocument->accident_id,
                    'document_id' => $suratPemberitahuanDimulainyaPenyidikanDocument->id,
                    'document_category_id' => $suratPemberitahuanDimulainyaPenyidikanDocument->document_category_id,
                    'updated_status_id' => $status->id,
                    'rejected_by_id' => $this->userAuth->id,
                    'reject_reason' => $rejectReason,
                    'accident_number' => $suratPemberitahuanDimulainyaPenyidikanDocument->accident->no_lp ?? '-',
                    'document_number' => $suratPemberitahuanDimulainyaPenyidikanDocument->document_number,
                    'document_category_name' => $suratPemberitahuanDimulainyaPenyidikanDocument->documentCategory->name ?? '-',
                    'model_class' => get_class(new SuratPemberitahuanDimulainyaPenyidikanDocument()),
                    'updated_status_name' => $status->name,
                    'rejected_by_name' => PeopleNameHelper::getFullName($this->userAuth->first_title, $this->userAuth->first_name, $this->userAuth->last_name, $this->userAuth->last_title),
                    'rejected_at' => $this->timestampNow
                ]);
       
                $suratPemberitahuanDimulainyaPenyidikanDocument->rejected_at = $this->timestampNow;

                $suratPemberitahuanDimulainyaPenyidikanDocument->save();

                DB::commit();

                return redirect()->route('cms.case-document-validation.index')->with('success', 'Surat Pemberitahuan Dimulainya Penyidikan berhasil dikembalikan.');
            } catch (\Exception $e) {
                DB::rollback();
                return redirect()->back()->with('error', 'Terjadi kesalahan. Surat Pemberitahuan Dimulainya Penyidikan gagal ditolak.');
            }
        }else{
            return redirect()->back()->with('error', 'Terjadi kesalahan. Surat Pemberitahuan Dimulainya Penyidikan gagal ditolak.');
        }
    }
}
