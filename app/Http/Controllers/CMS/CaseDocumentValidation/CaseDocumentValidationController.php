<?php

namespace App\Http\Controllers\CMS\CaseDocumentValidation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

use App\Models\Accident;

class CaseDocumentValidationController extends Controller
{
    public function index()
    {
        $statusIds = ['9', '12', '11', '10'];
        $accidents = $this->getAccidentsByStatus();

        $viewData = [
            'accidents' => $accidents
        ];

        return view('cms.case-document-validation.index', $viewData);
    }
    
    public function getDocuments(Request $request)
    {
        $accidentId = $request->accidentId;
       
        $documents = $this->getDocumentsByAccident($accidentId);

        $viewData = [
            'documents' => $documents
        ];

        return view('cms.case-document-validation.components.documents-table-tbody', $viewData);
    }



    //===================================================================================================

    private function getAccidentsByStatus() {
        $accidents = Accident::with([
            'suratPemberitahuanDimulainyaPenyidikanDocuments' => function($query) {
                $query->orderBy('updated_at', 'asc');
            },
            'suratPemberitahuanDimulainyaPenyidikanPusiknasDocuments' => function($query) {
                $query->orderBy('updated_at', 'asc');
            },
            'suratKetetapanTentangPenetapanTersangkaDocuments',
            'laporanHasilGelarPerkaraDocuments',
            'suratPerintahTugasDocuments',
            'suratPerintahPenyelidikanDocuments',
            'suratPerintahPenyidikanDocuments',
            'police'
            ])
            ->where(function($q) {
                $q->whereHas('suratPemberitahuanDimulainyaPenyidikanDocuments', function($query){
                    $query->whereIn('status_id', ['12']);
                })->orWhereHas('suratPemberitahuanDimulainyaPenyidikanPusiknasDocuments', function($query){
                    $query->whereIn('status_id', ['12']);
                });
            })
            ->get();

        return $accidents;
    }

    private function getDocumentsByAccident($accidentId) {
        $documentTypes = [
            "App\Models\Doc\SuratPerintahPenyelidikanDocument\SuratPerintahPenyelidikanDocument",
            "App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument",
            "App\Models\Doc\SuratPerintahTugasDocument\SuratPerintahTugasDocument",
            "App\Models\Doc\LaporanHasilGelarPerkaraDocument\LaporanHasilGelarPerkaraDocument",
            "App\Models\Doc\SuratKetetapanTentangPenetapanTersangkaDocument\SuratKetetapanTentangPenetapanTersangkaDocument",
            "App\Models\Doc\SuratPemberitahuanDimulainyaPenyidikanDocument\SuratPemberitahuanDimulainyaPenyidikanDocument",
            "App\Models\Doc\SuratPemberitahuanDimulainyaPenyidikanPusiknasDocument\SuratPemberitahuanDimulainyaPenyidikanPusiknasDocument",
        ];

        $documentsCollection = Collection::make();

        foreach ($documentTypes as $documentType) {
            $documents = $documentType::with(['accident', 'documentCategory', 'status'])
                ->whereHas('accident', function($q) {
                    $q->where(function($q2) {
                        $q2->whereHas('suratPemberitahuanDimulainyaPenyidikanDocuments', function($query){
                            $query->whereIn('status_id', ['9', '12', '11', '10']);
                        })->orWhereHas('suratPemberitahuanDimulainyaPenyidikanPusiknasDocuments', function($query){
                            $query->whereIn('status_id', ['9', '12', '11', '10']);
                        });
                    });
                })
                ->whereIn('status_id', ['9', '12', '11', '10'])
                ->where('accident_id', $accidentId)
                ->get();

            if (!$documents->isEmpty()) {
                $documentsCollection = $documentsCollection->merge($documents);
            }
        }

        return $documentsCollection;
    }

    private function getDocumentsByStatus($statusIds) {
        $documentTypes = [
            "App\Models\Doc\SuratPerintahPenyelidikanDocument\SuratPerintahPenyelidikanDocument",
            "App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument",
            "App\Models\Doc\SuratPerintahTugasDocument\SuratPerintahTugasDocument",
            "App\Models\Doc\LaporanHasilGelarPerkaraDocument\LaporanHasilGelarPerkaraDocument",
            "App\Models\Doc\SuratKetetapanTentangPenetapanTersangkaDocument\SuratKetetapanTentangPenetapanTersangkaDocument",
            "App\Models\Doc\SuratPemberitahuanDimulainyaPenyidikanDocument\SuratPemberitahuanDimulainyaPenyidikanDocument",
            "App\Models\Doc\SuratPemberitahuanDimulainyaPenyidikanPusiknasDocument\SuratPemberitahuanDimulainyaPenyidikanPusiknasDocument",
        ];

        $documentsCollection = Collection::make();

        foreach ($documentTypes as $documentType) {
            $documents = $documentType::with(['accident', 'documentCategory', 'status'])
                ->whereHas('accident', function($q) {
                    $q->where(function($q2) {
                        $q2->whereHas('suratPemberitahuanDimulainyaPenyidikanDocuments', function($query){
                            $query->whereIn('status_id', ['9', '12', '11', '10']);
                        })->orWhereHas('suratPemberitahuanDimulainyaPenyidikanPusiknasDocuments', function($query){
                            $query->whereIn('status_id', ['9', '12', '11', '10']);
                        });
                    });
                })
                ->whereIn('status_id', $statusIds)
                ->get();

            if (!$documents->isEmpty()) {
                $documentsCollection = $documentsCollection->merge($documents);
            }
        }

        return $documentsCollection;
    }
}
