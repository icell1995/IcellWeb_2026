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
        $accident = Accident::with('police')->find($accidentId);
        $laporanPolisi = \App\Models\LaporanPolisi::where('accident_id', $accidentId)->first();
       
        $documents = $this->getDocumentsByAccident($accidentId);

        $viewData = [
            'accident' => $accident,
            'laporanPolisi' => $laporanPolisi,
            'documents' => $documents
        ];

        return view('cms.case-document-validation.components.documents-table-tbody', $viewData);
    }



    //===================================================================================================

    private function getAccidentsByStatus() {
        $accidents = Accident::with([
            'laporanPolisi',
            'suratPemberitahuanDimulainyaPenyidikanDocuments' => function($query) {
                $query->orderBy('updated_at', 'asc');
            },
            'suratKetetapanTentangPenetapanTersangkaDocuments',
            'laporanHasilGelarPerkaraDocuments',
            'suratPerintahTugasDocuments',
            'suratPerintahPenyelidikanDocuments',
            'suratPerintahPenyidikanDocuments',
            'police'
            ])
            ->whereHas('suratPemberitahuanDimulainyaPenyidikanDocuments', function($query){
                $query->whereIn('status_id', ['12']);
            })
            ->get();

        return $accidents;
    }

    private function getDocumentsByAccident($accidentId) {
        $docValidationService = app(\App\Services\DocValidation\DocValidationService::class);

        // Sequence:
        // 1. Laporan Polisi (LP) - Ditangani di row pertama view
        // 2. Surat Perintah Penyelidikan
        // 3. Surat Perintah Tugas
        // 4. Surat Perintah Penyidikan
        // 5. Laporan Hasil Gelar Perkara
        // 6. Surat Ketetapan Tentang Penetapan Tersangka
        // 7. Surat Pemberitahuan Dimulainya Penyidikan (SPDP)
        $steps = [
            [
                'step' => 2,
                'name' => 'Surat Perintah Penyelidikan',
                'class' => \App\Models\Doc\SuratPerintahPenyelidikanDocument\SuratPerintahPenyelidikanDocument::class,
            ],
            [
                'step' => 3,
                'name' => 'Surat Perintah Tugas',
                'class' => \App\Models\Doc\SuratPerintahTugasDocument\SuratPerintahTugasDocument::class,
            ],
            [
                'step' => 4,
                'name' => 'Surat Perintah Penyidikan',
                'class' => \App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument::class,
            ],
            [
                'step' => 5,
                'name' => 'Laporan Hasil Gelar Perkara',
                'class' => \App\Models\Doc\LaporanHasilGelarPerkaraDocument\LaporanHasilGelarPerkaraDocument::class,
            ],
            [
                'step' => 6,
                'name' => 'Surat Ketetapan Tentang Penetapan Tersangka',
                'class' => \App\Models\Doc\SuratKetetapanTentangPenetapanTersangkaDocument\SuratKetetapanTentangPenetapanTersangkaDocument::class,
            ],
            [
                'step' => 7,
                'name' => 'Surat Pemberitahuan Dimulainya Penyidikan',
                'class' => \App\Models\Doc\SuratPemberitahuanDimulainyaPenyidikanDocument\SuratPemberitahuanDimulainyaPenyidikanDocument::class,
            ],
        ];

        $documentsCollection = Collection::make();

        foreach ($steps as $stepConfig) {
            $step = $stepConfig['step'];
            $documentClass = $stepConfig['class'];
            $check = $docValidationService->checkSequentialValidation($accidentId, $step);

            $documents = $documentClass::with(['accident', 'documentCategory', 'status'])
                ->where('accident_id', $accidentId)
                ->whereIn('status_id', ['12', '86', '85', '4', '9', '10', '11'])
                ->get();

            foreach ($documents as $doc) {
                $doc->workflow_step = $step;
                $doc->workflow_step_name = $stepConfig['name'];
                $doc->can_validate = $check['can_validate'];
                $doc->disabled_reason = $check['reason'];
            }

            if (!$documents->isEmpty()) {
                $documentsCollection = $documentsCollection->concat($documents);
            }
        }

        return $documentsCollection->sortBy('workflow_step');
    }

    private function getDocumentsByStatus($statusIds) {
        $documentTypes = [
            "App\Models\Doc\SuratPerintahPenyelidikanDocument\SuratPerintahPenyelidikanDocument",
            "App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument",
            "App\Models\Doc\SuratPerintahTugasDocument\SuratPerintahTugasDocument",
            "App\Models\Doc\LaporanHasilGelarPerkaraDocument\LaporanHasilGelarPerkaraDocument",
            "App\Models\Doc\SuratKetetapanTentangPenetapanTersangkaDocument\SuratKetetapanTentangPenetapanTersangkaDocument",
            "App\Models\Doc\SuratPemberitahuanDimulainyaPenyidikanDocument\SuratPemberitahuanDimulainyaPenyidikanDocument",
            // "App\Models\Doc\SuratPemberitahuanDimulainyaPenyidikanPusiknasDocument\SuratPemberitahuanDimulainyaPenyidikanPusiknasDocument",
        ];

        $documentsCollection = Collection::make();

        foreach ($documentTypes as $documentType) {
            $documents = $documentType::with(['accident', 'documentCategory', 'status'])
                ->whereHas('accident.suratPemberitahuanDimulainyaPenyidikanDocuments', function($query){
                    $query->whereIn('status_id', ['9', '12', '11', '10']);
                })
                // ->whereHas('accident', function($q) {
                //     $q->where(function($q2) {
                //         $q2->whereHas('suratPemberitahuanDimulainyaPenyidikanDocuments', function($query){
                //             $query->whereIn('status_id', ['9', '12', '11', '10']);
                //         })->orWhereHas('suratPemberitahuanDimulainyaPenyidikanPusiknasDocuments', function($query){
                //             $query->whereIn('status_id', ['9', '12', '11', '10']);
                //         });
                //     });
                // })
                ->whereIn('status_id', $statusIds)
                ->get();

            if (!$documents->isEmpty()) {
                $documentsCollection = $documentsCollection->merge($documents);
            }
        }

        return $documentsCollection;
    }
}
