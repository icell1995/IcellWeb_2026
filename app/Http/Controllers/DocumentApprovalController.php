<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

use App\Models\Doc\SuratPerintahPenyelidikanDocument\SuratPerintahPenyelidikanDocument;
use App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument;
use App\Models\Doc\SuratPerintahTugasDocument\SuratPerintahTugasDocument;
use App\Models\Doc\LaporanHasilGelarPerkaraDocument\LaporanHasilGelarPerkaraDocument;
use App\Models\Doc\SuratKetetapanTentangPenetapanTersangkaDocument\SuratKetetapanTentangPenetapanTersangkaDocument;
use App\Models\Doc\SuratPemberitahuanDimulainyaPenyidikanDocument\SuratPemberitahuanDimulainyaPenyidikanDocument;
use App\Models\Doc\SuratKesepakatanDiversiDocument\SuratKesepakatanDiversiDocument;
use App\Models\Doc\SuratPerintahPenahananDocument\SuratPerintahPenahananDocument;
use App\Models\Doc\SuratPerintahPenangguhanPenahananDocument\SuratPerintahPenangguhanPenahananDocument;
use App\Models\Doc\SuratPemberitahuanPenghentianPenyidikanDocument\SuratPemberitahuanPenghentianPenyidikanDocument;
use App\Models\Doc\SuratPemberitahuanDimulainyaPenyidikanPusiknasDocument\SuratPemberitahuanDimulainyaPenyidikanPusiknasDocument as SpdpPusiknasDocument;
use App\Models\Doc\Tahap1Document\Tahap1Document;
use App\Models\Doc\Tahap2Document\Tahap2Document;

use App\Traits\DocsOfficersTraits;

class DocumentApprovalController extends Controller
{
    use DocsOfficersTraits;

    public function index()
    {
        $user = Auth::user();

        $statusIds = ['3'];
        $documentsCollection = $this->getDocumentsByStatus($user, $statusIds);

        $documents = $documentsCollection->sortByDesc('updated_at');

        $viewData = [
            'documents' => $documents
        ];

        return view('document-approval.index', $viewData);
    }

    public function view()
    {
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

        return view('document-approval.view', $viewData);
    }

    public function save(Request $request)
    {
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $documentId = htmlspecialchars(request()->query('document_id'));
        $documentCategoryId = htmlspecialchars(request()->query('document_category_id'));

        $isApproved = htmlspecialchars($request->isApproved);
        $message = htmlspecialchars($request->message);

        DB::beginTransaction();
        try{
            $document = $this->getDocumentRouter($documentCategoryId, $documentId, $accidentId);

            if(!empty($document)){
                if(!in_array($document->status_id, [3])){
                    abort(419);
                }

                if(filter_var($isApproved, FILTER_VALIDATE_BOOLEAN) == true){
                    $document->status_id = '5';
                    $document->timestamps = [
                        'approved_at' => now(),
                    ];
                }else{
                    $document->status_id = '4';
                    $document->messages = [
                        'reason_approval_rejected' => $message,
                    ];
                    $document->timestamps = [
                        'rejected_at' => now(),
                    ];
                }

                $document->save();

                DB::commit();
            }
        }catch(\Exception $e){
            DB::rollback();
            return redirect()->route('document-approval.index');
        }

        return redirect()->route('document-approval.index');
    }

    public function uploadIndex()
    {
        $user = Auth::user();

        $statusIds = ['7'];
        $documentsCollection = $this->getDocumentsByStatus($user, $statusIds);

        $documents = $documentsCollection->sortByDesc('updated_at');

        $viewData = [
            'documents' => $documents
        ];

        return view('document-approval.upload.index', $viewData);
    }

    public function uploadView()
    {
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

        return view('document-approval.upload.view', $viewData);
    }

    public function uploadSave(Request $request){
        $accidentId = htmlspecialchars(request()->query('accident_id'));
        $documentId = htmlspecialchars(request()->query('document_id'));
        $documentCategoryId = htmlspecialchars(request()->query('document_category_id'));

        $isApproved = htmlspecialchars($request->isApproved);
        $message = htmlspecialchars($request->message);

        DB::beginTransaction();
        try{
            $document = $this->getDocumentRouter($documentCategoryId, $documentId, $accidentId);

            if(!empty($document)){
                if(!in_array($document->status_id, [7])){
                    abort(419);
                }

                if(filter_var($isApproved, FILTER_VALIDATE_BOOLEAN) == true){
                    if(in_array($documentCategoryId, ['0101', '0201', '0702', '0706', '0213', '0215', '0601', '0603'])){
                        $document->status_id = '86';
                    }else{
                        $document->status_id = '11';
                    }

                    $document->timestamps = [
                        'uploaded_at' => now()
                    ];
                }else{
                    $document->status_id = '4';
                    $document->messages = [
                        'reason_approval_rejected' => $message,
                    ];
                    $document->timestamps = [
                        'rejected_at' => now(),
                    ];
                }

                $document->save();

                DB::commit();
            }
        }catch(\Exception $e){
            DB::rollback();
            return redirect()->route('document-approval.upload.index');
        }

        return redirect()->route('document-approval.upload.index');
    }

    //===================================================================================================

    private function getDocumentsByStatus($user, $statusIds) {
        $documentTypes = [
            SuratPerintahPenyelidikanDocument::class,
            SuratPerintahPenyidikanDocument::class,
            SuratPerintahTugasDocument::class,
            LaporanHasilGelarPerkaraDocument::class,
            SuratKetetapanTentangPenetapanTersangkaDocument::class,
            SuratPemberitahuanDimulainyaPenyidikanDocument::class,
            SuratKesepakatanDiversiDocument::class,
            SuratPerintahPenahananDocument::class,
            SuratPerintahPenangguhanPenahananDocument::class,
            SuratPemberitahuanPenghentianPenyidikanDocument::class,
            SpdpPusiknasDocument::class,
            Tahap1Document::class,
            Tahap2Document::class,
        ];

        $documentsCollection = Collection::make();

        foreach ($documentTypes as $documentType) {
            $documents = $documentType::with(['accident', 'documentCategory'])
                ->whereHas('accident', function ($query) use ($user) {
                    // $query->where('polres_id', $user->polres_id);
                    $query->whereIn('polres_id', $this->getOldNewPolresIds($user->polres_id));
                })
                ->whereIn('status_id', $statusIds)
                ->get();

            if (!$documents->isEmpty()) {
                $documentsCollection = $documentsCollection->merge($documents);
            }
        }

        return $documentsCollection;
    }

    private function getDocumentRouter($documentCategoryId, $documentId, $accidentId)
    {
        $documentModels = [
            '0101' => SuratPerintahPenyelidikanDocument::class,
            '0201' => SuratPerintahPenyidikanDocument::class,
            '0204' => [
                SuratPemberitahuanDimulainyaPenyidikanDocument::class,
                \App\Models\Doc\SuratPemberitahuanDimulainyaPenyidikanPusiknasDocument\SuratPemberitahuanDimulainyaPenyidikanPusiknasDocument::class
            ],
            '0213' => SuratKesepakatanDiversiDocument::class,
            '0215' => SuratKetetapanTentangPenetapanTersangkaDocument::class,
            '0702' => SuratPerintahTugasDocument::class,
            '0706' => LaporanHasilGelarPerkaraDocument::class,
            '0601' => SuratPerintahPenahananDocument::class,
            '0603' => SuratPerintahPenangguhanPenahananDocument::class,
            // Add more document types here
            '0216' => SuratPemberitahuanPenghentianPenyidikanDocument::class,
            '0805' => Tahap1Document::class,
            '0806' => Tahap1Document::class,
            '0807' => Tahap2Document::class,
        ];

        if (array_key_exists($documentCategoryId, $documentModels)) {
            $models = (array) $documentModels[$documentCategoryId];
            
            foreach ($models as $modelClass) {
                $document = $modelClass::with(['accident', 'documentCategory', 'attachment'])
                    ->where('id', $documentId)
                    ->first();
                
                if ($document) {
                    return $document;
                }
            }
        }
        
        return redirect()->route('document-approval.index');
    }
}
