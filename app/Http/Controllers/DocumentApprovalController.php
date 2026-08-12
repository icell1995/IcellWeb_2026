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
use App\Models\Doc\SuratKetetapanPenghentianPenyelidikanDocument\SuratKetetapanPenghentianPenyelidikanDocument;
use App\Models\Doc\SuratKetetapanPenghentianPenyidikanDocument\SuratKetetapanPenghentianPenyidikanDocument;
use App\Models\Doc\SuratPerintahPenahananDocument\SuratPerintahPenahananDocument;
use App\Models\Doc\Tahap1Document\Tahap1Document;
use App\Models\Doc\PermintaanPerpanjanganPenahananDocument\PermintaanPerpanjanganPenahananDocument;
use App\Models\Doc\PerpanjanganLanjutanDocument\PerpanjanganLanjutanDocument;
use App\Models\Doc\SuratPerintahPenangkapanDocument\SuratPerintahPenangkapanDocument;
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
                    if(in_array($documentCategoryId, ['0101', '0201', '0702', '0706', '0601', '0603', '0604', '0301'])){
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
            SuratPerintahPenahananDocument::class,
            SuratKetetapanPenghentianPenyidikanDocument::class,
            Tahap1Document::class,
            PermintaanPerpanjanganPenahananDocument::class,
            PerpanjanganLanjutanDocument::class,
            SuratPerintahPenangkapanDocument::class,
            // SuratKetetapanPenghentianPenyelidikanDocument::class,
        ];

        $documentsCollection = Collection::make();

        foreach ($documentTypes as $documentType) {

            // $getOldNewPolresIds = $this->getOldNewPolresIds($user->polres_id);

            $documents = $documentType::with(['accident', 'documentCategory'])
                ->whereHas('accident', function ($query) use ($user) {
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
            // '0112' => SuratKetetapanPenghentianPenyelidikanDocument::class,
            '0201' => SuratPerintahPenyidikanDocument::class,
            '0204' => SuratPemberitahuanDimulainyaPenyidikanDocument::class,
            '0205' => SuratKetetapanPenghentianPenyidikanDocument::class,
            '0215' => SuratKetetapanTentangPenetapanTersangkaDocument::class,
            '0601' => SuratPerintahPenahananDocument::class,
            '0603' => PermintaanPerpanjanganPenahananDocument::class,
            '0604' => PerpanjanganLanjutanDocument::class,
            '0301' => SuratPerintahPenangkapanDocument::class,
            '0702' => SuratPerintahTugasDocument::class,
            '0706' => LaporanHasilGelarPerkaraDocument::class,
            '0805' => Tahap1Document::class,
            // Add more document types here
        ];

        if (array_key_exists($documentCategoryId, $documentModels)) {
            $document = $documentModels[$documentCategoryId]::with(['accident','documentCategory', 'attachment'])
                ->where('id', $documentId)
                ->first();
        } else {
            return redirect()->route('document-approval.index');
        }

        return $document;
    }
}
