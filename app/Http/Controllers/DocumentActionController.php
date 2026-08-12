<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use PDF;

use App\Models\Accident;
use App\Models\Officer;

use App\Models\Doc\SuratPerintahPenyelidikanDocument\SuratPerintahPenyelidikanDocument;
use App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument;
use App\Models\Doc\SuratPerintahTugasDocument\SuratPerintahTugasDocument;
use App\Models\Doc\LaporanHasilGelarPerkaraDocument\LaporanHasilGelarPerkaraDocument;
use App\Models\Doc\SuratKetetapanTentangPenetapanTersangkaDocument\SuratKetetapanTentangPenetapanTersangkaDocument;
use App\Models\Doc\SuratPemberitahuanDimulainyaPenyidikanDocument\SuratPemberitahuanDimulainyaPenyidikanDocument;
// use App\Models\Doc\SuratKetetapanPenghentianPenyelidikanDocument\SuratKetetapanPenghentianPenyelidikanDocument;
use App\Models\Doc\Tahap1Document\Tahap1Document;
use App\Models\Doc\SuratKetetapanPenghentianPenyidikanDocument\SuratKetetapanPenghentianPenyidikanDocument;
use App\Models\Doc\SuratPerintahPenahananDocument\SuratPerintahPenahananDocument;
use App\Models\Doc\PermintaanPerpanjanganPenahananDocument\PermintaanPerpanjanganPenahananDocument;
use App\Models\Doc\PerpanjanganLanjutanDocument\PerpanjanganLanjutanDocument;
use App\Models\Doc\SuratPerintahPenangkapanDocument\SuratPerintahPenangkapanDocument;

class DocumentActionController extends Controller
{
    public function requestApprovalRequest()
    {
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $formType = htmlspecialchars(request()->query('form_type'));
        $documentId = htmlspecialchars(request()->query('document_id'));
        $documentCategoryId = htmlspecialchars(request()->query('document_category_id'));

        $accident = Accident::where('id', $accidentId)->first();

        $signatories = Officer::withRelated()
            ->selectFullName()
            ->where('police_id', $accident->polres_id)
            ->where('class', 'SIGNATORY')
            ->where('is_valid', true)
            ->where('is_active', true)
            ->orderBy('first_name')
            ->get();

        $document = $this->getDocumentRouter($documentCategoryId, $documentId, $accidentId);
        if (! $document) {
            return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId])
                ->with('error', 'Dokumen tidak ditemukan.');
        }
        $documentSignatory = $document->signatory;

        $viewData = [
            'accidentId' => $accidentId,
            'formType' => $formType,
            'documentId' => $documentId,
            'documentCategoryId' => $documentCategoryId,
            'document' => $document,
            'signatories' => $signatories,
            'documentSignatory' => $documentSignatory
        ];

        return view('document-action.request-approval.request', $viewData);
    }

    public function requestApprovalRequestSave(Request $request)
    {
        //validation
        $request->validate([
            'documentNumber' => 'required_if:formType,1|max:255',
            'documentDate' => 'required'
        ],[
            'documentNumber.required_if' => 'Nomor dokumen wajib diisi',
            'documentNumber.max' => 'Nomor dokumen maksimal 255 karakter',
            'documentDate.required' => 'Tanggal dokumen wajib diisi'
        ]);

        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $formType = htmlspecialchars(request()->query('form_type'));
        $documentId = htmlspecialchars(request()->query('document_id'));
        $documentCategoryId = htmlspecialchars(request()->query('document_category_id'));

        // $signatoryId = $request->signatory;
        $documentNumber = $request->documentNumber;
        $documentDate = $request->documentDate;

        DB::beginTransaction();
        try{
            $document = $this->getDocumentRouter($documentCategoryId, $documentId, $accidentId);

            if(!empty($document)){
                if(!in_array($document->status_id, [2, 4])){
                    abort(419);
                }

                //$this->generatePreview($document->id);

                if($document->documentCategory->is_digital_signature == true){
                    $document->status_id = '6';
                }else{
                    $document->status_id = '3';
                }

                $document->document_date = $documentDate;

                if($formType == '1'){
                    $document->document_number = $documentNumber;
                }

                $document->save();

            }

            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
            return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId]);
        }

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId]);
    }

    public function uploadDocumentUpload()
    {
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $formType = htmlspecialchars(request()->query('form_type'));
        $documentId = htmlspecialchars(request()->query('document_id'));
        $documentCategoryId = htmlspecialchars(request()->query('document_category_id'));

        $accident = Accident::where('id', $accidentId)->first();

        $viewData = [
            'accidentId' => $accidentId,
            'formType' => $formType,
            'documentId' => $documentId,
            'documentCategoryId' => $documentCategoryId,
            'accident' => $accident
        ];

        return view('document-action.upload-document.upload', $viewData);
    }

    public function uploadDocumentUploadSave(Request $request)
    {
        $formType = htmlspecialchars(request()->query('form_type'));
        $requiredFileType = $request->requiredFileType;

        //validate
        $request->validate([
            'file' => 'required|max:10000'
        ],[
            'file.required' => 'File wajib diisi',
            'file.max' => 'File maksimal 10MB'
        ]);

        if($requiredFileType == 'PDF'){
            $request->validate([
                'file' => 'mimetypes:application/pdf'
            ],[
                'file.mimetypes' => 'File harus berformat pdf'
            ]);
        }elseif($requiredFileType == 'WORD'){
            $request->validate([
                'file' => 'mimetypes:application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document'
            ],[
                'file.mimetypes' => 'File harus berformat doc atau docx (word)'
            ]);
        }else{
            return redirect()->back()->with('error', 'Tipe file tidak tersedia');
        }

        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $documentId = htmlspecialchars(request()->query('document_id'));
        $documentCategoryId = htmlspecialchars(request()->query('document_category_id'));
        $file = $request->file('file');

        $accident = Accident::where('id', $accidentId)->first();

        DB::beginTransaction();
        try{
            $document = $this->getDocumentRouter($documentCategoryId, $documentId, $accidentId);
            $documentId = $document->id;

            if(!empty($document)){
                if(!in_array($document->status_id, [5, 6])){
                    abort(419);
                }

                # upload file
                $fileExtension = $file->getClientOriginalExtension();
                $fileOriginalName = $file->getClientOriginalName();
                $fileSize = $file->getSize();
                $fileHashName = $file->hashName();
                $fileMimeType = $file->getMimeType();

                $documentAttachment = $document->attachment()->first();

                $attachmentData = [
                    'original_name' => $fileOriginalName,
                    'name'          => $fileHashName,
                    'extension'     => $fileExtension,
                    'size'          => $fileSize,
                    'mimetype'      => $fileMimeType,
                    'type'          => 'DOCUMENT',
                    'path'          => 'documents/attachments/' . $fileHashName,
                ];

                if ($documentAttachment) {
                    // Update existing attachment record
                    $documentAttachment->update($attachmentData);
                } else {
                    // Create new attachment — foreign key diisi otomatis oleh relasi hasOne
                    $document->attachment()->create($attachmentData);
                }

                //move file to public
                $file->move(public_path('documents/attachments/'), $fileHashName);

                //remove old file
                if(!empty($documentAttachment)){
                    $oldFile = public_path('documents/attachments/') . $documentAttachment->name;
                    if(file_exists($oldFile)){
                        unlink($oldFile);
                    }
                }

                // Audit Trail
                $timestamps = $document->timestamps;
                if (is_string($timestamps)) {
                    $timestamps = json_decode($timestamps, true) ?: [];
                }
                if (!is_array($timestamps)) {
                    $timestamps = [];
                }

                $timestamps[] = [
                    'status_id' => ($document->documentCategory->is_digital_signature == true) ? '8' : '7',
                    'updated_at' => Carbon::now()->toDateTimeString(),
                    'updated_by' => Auth::id(),
                    'message' => 'Dokumen fisik diunggah'
                ];
                $document->timestamps = $timestamps;

                $ipAddresses = $document->ip_addresses;
                if (is_string($ipAddresses)) {
                    $ipAddresses = json_decode($ipAddresses, true) ?: [];
                }
                if (!is_array($ipAddresses)) {
                    $ipAddresses = [];
                }

                if (!in_array($request->ip(), $ipAddresses)) {
                    $ipAddresses[] = $request->ip();
                }
                $document->ip_addresses = $ipAddresses;

                if($document->documentCategory->is_digital_signature == true){
                    $document->status_id = '8';
                }else{
                    $document->status_id = '7';
                }

                $document->save();

                DB::commit();
            }
        }catch(\Exception $e){
            DB::rollback();
            return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId]);
        }

        return redirect()->route('view_produktivitas_accident', ['accident_id' => $accidentId]);
    }

    public function documentPreviewView(){
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $documentId = htmlspecialchars(request()->query('document_id'));
        $documentCategoryId = htmlspecialchars(request()->query('document_category_id'));

        $document = $this->getDocumentRouter($documentCategoryId, $documentId, $accidentId);

        $viewData = [
            'accidentId' => $accidentId,
            'documentId' => $documentId,
            'documentCategoryId' => $documentCategoryId,
            'document' => $document
        ];

        return view('document-action.document-preview.view', $viewData);
    }

    //===================================================================================================================================

    private function getDocumentRouter($documentCategoryId, $documentId, $accidentId)
    {
        $documentModels = [
            '0101' => SuratPerintahPenyelidikanDocument::class,
            // '0112' => SuratKetetapanPenghentianPenyelidikanDocument::class,
            '0201' => SuratPerintahPenyidikanDocument::class,
            '0204' => SuratPemberitahuanDimulainyaPenyidikanDocument::class,
            '0215' => SuratKetetapanTentangPenetapanTersangkaDocument::class,
            '0601' => SuratPerintahPenahananDocument::class,
            '0603' => PermintaanPerpanjanganPenahananDocument::class,
            '0604' => PerpanjanganLanjutanDocument::class,
            '0301' => SuratPerintahPenangkapanDocument::class,
            '0702' => SuratPerintahTugasDocument::class,
            '0706' => LaporanHasilGelarPerkaraDocument::class,
            '0206' => SuratKetetapanPenghentianPenyidikanDocument::class,
            '0805' => Tahap1Document::class,
            // Add more document types here
        ];

        if (array_key_exists($documentCategoryId, $documentModels)) {
            $document = $documentModels[$documentCategoryId]::with(['accident', 'documentCategory', 'signatory'])
                ->where('id', $documentId)
                ->first();
        } else {
            return null;
        }

        return $document;
    }

    /*
    private function generatePreview($id)
    {
        // Get URL Parameter
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $suratPerintahPenyelidikanDocumentId = $id;

        // Get data from database
        $suratPerintahPenyelidikanDocument = SuratPerintahPenyelidikanDocument::with(['suratPerintahPenyelidikanDocumentOfficers', 'suratPerintahPenyelidikanDocumentCaseKeywords'])->where('id', $suratPerintahPenyelidikanDocumentId)->first();
        $officers = $suratPerintahPenyelidikanDocument->suratPerintahPenyelidikanDocumentOfficers->where('class', '!=', 'SIGNATORY')->sortBy('class');
        $leader = $suratPerintahPenyelidikanDocument->suratPerintahPenyelidikanDocumentOfficers->where('class', '=', 'LEADER')->first();
        $signatory = $suratPerintahPenyelidikanDocument->suratPerintahPenyelidikanDocumentOfficers->where('class', '=', 'SIGNATORY')->first();

        $accident = Accident::with(['polres', 'polres.polda'])->where('id', $accidentId)->first();

        $no = 1;
        $blockOfficers = [];
        foreach ($officers as $officer) {
            $blockOfficers[] = [
                'number' => $no,
                'first_name'   => ($officer->first_title) ? $officer->first_title . ' ' . $officer->first_name : $officer->first_name,
                'last_name'  => ($officer->last_title) ? $officer->last_name . ', ' . $officer->last_title : $officer->last_name,
                'rank_id' => $officer->rank->name ?? '',
                'officer_id' => $officer->register_number,
                'position' => $officer->position->name ?? '',
            ];
            $no++;
        }

        $signatureTitleText = [
            'KAPOLRES' => 'KEPALA KEPOLISIAN RESOR ' . $accident->polres->full_name,
            'NO_KAPOLRES' => 'a.n. KEPALA KEPOLISIAN RESOR ' . $accident->polres->full_name . '</w:t><w:p/><w:t>' . $signatory->position->name ?? '',
            'NO_DIRLANTAS' => 'a.n. DIREKTUR LALU LINTAS POLDA ' . $accident->polres->polda->full_name . '</w:t><w:p/><w:t>' . $signatory->position->name ?? '',
        ];

        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('word-template/surat_perintah_penyelidikan.docx');

        if (isset($signatory->position)) {
            if ($signatory->position->position_cluster_id == '1') {
                $templateProcessor->setValue('officer_signature_title_text', $signatureTitleText['KAPOLRES']);
            } else if ($signatory->position->position_cluster_id == '9') {
                $templateProcessor->setValue('officer_signature_title_text', $signatureTitleText['NO_DIRLANTAS']);
            } else {
                $templateProcessor->setValue('officer_signature_title_text', $signatureTitleText['NO_KAPOLRES']);
            }
        }

        $templateProcessor->cloneBlock('block_officers', 2, true, false, $blockOfficers);
        $templateProcessor->setValue('letter_number',  $suratPerintahPenyelidikanDocument->document_number);
        $templateProcessor->setValue('letter_end_date', ($suratPerintahPenyelidikanDocument->end_date != NULL) ? 'tanggal ' . Carbon::parse($suratPerintahPenyelidikanDocument->end_date)->locale('id')->translatedFormat('d F Y') : 'selesai');
        $templateProcessor->setValue('issued_date', Carbon::parse($suratPerintahPenyelidikanDocument->document_date)->locale('id')->translatedFormat('d F Y'));
        $templateProcessor->setValue('accident_day', Carbon::parse($accident->accident_date)->locale('id')->translatedFormat('l'));
        $templateProcessor->setValue('accident_date', Carbon::parse($accident->accident_date)->locale('id')->translatedFormat('d F Y'));
        $templateProcessor->setValue('accident_time', Carbon::parse($accident->accident_time)->locale('id')->translatedFormat('H:i'));
        $templateProcessor->setValue('polda_full_name', $accident->polres->polda->full_name);
        $templateProcessor->setValue('polda_name', $accident->polres->polda->full_name);
        $templateProcessor->setValue('polres_name', $accident->polres->full_name);
        $templateProcessor->setValue('polres_alamat', ucwords(strtolower($accident->polres->address . ', ' . $accident->polres->polres_district . ', ' . $accident->polres->polres_zipcode)));
        $templateProcessor->setValue('road_name', $accident->road_name);
        $templateProcessor->setValue('no_lp', $accident->no_lp);
        $templateProcessor->setValue('officer_signature_sebagai_kepala', strtoupper($signatory->position->name ?? ''));
        $templateProcessor->setValue('officer_signature_rank', strtoupper($signatory->rank->name ?? ''));
        $templateProcessor->setValue('officer_signature_nrp', $signatory->register_number);
        $templateProcessor->setValue('officer_signature_name', (($signatory->first_title) ? $signatory->first_title . ' ' . $signatory->first_name : $signatory->first_name) . ' ' . (($signatory->last_title) ? $signatory->last_name . ', ' . $signatory->last_title : $signatory->last_name));
        $templateProcessor->setValue('officer_assign_rank', strtoupper($leader->rank->name ?? ''));
        $templateProcessor->setValue('officer_assign_nrp', $leader->register_number);
        $templateProcessor->setValue('officer_assign_name', (($leader->first_title) ? $leader->first_title . ' ' . $leader->first_name : $leader->first_name) . ' ' . (($leader->last_title) ? $leader->last_name . ', ' . $leader->last_title : $leader->last_name));
        $templateProcessor->setValue('location_created', ucwords(strtolower($accident->polres->polres_district)));

        $filePath = storage_path('documents/' . $suratPerintahPenyelidikanDocument->documentCategory->alt_code . '/attachments/' . $suratPerintahPenyelidikanDocument->id . ' - Surat Perintah Penyelidikan - ' . $accident->polres->full_name . '.docx');
        $templateProcessor->saveAs($filePath);

        //update to database attachment
        $suratPerintahPenyelidikanDocument->attachment()->updateOrCreate(
            [
                'surat_perintah_penyelidikan_document_id' => $suratPerintahPenyelidikanDocument->id,
            ],
            [
                'surat_perintah_penyelidikan_document_id' => $suratPerintahPenyelidikanDocument->id,
                'name' => $suratPerintahPenyelidikanDocument->id . ' - Surat Perintah Penyelidikan - ' . $accident->polres->full_name . '.docx',
                'original_name' => $suratPerintahPenyelidikanDocument->id . ' - Surat Perintah Penyelidikan - ' . $accident->polres->full_name . '.docx',
                'extension' => 'docx',
                'mimetype' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'size' => filesize($filePath),
                'type' => 'DOCUMENT',
            ]
        );
    }*/
}
