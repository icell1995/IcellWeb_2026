<?php
namespace App\Services\DocValidation;

use App\Models\Log\CaseDocumentValidation;
use App\Models\LaporanPolisi;
use App\Models\Doc\SuratPerintahPenyelidikanDocument\SuratPerintahPenyelidikanDocument;
use App\Models\Doc\SuratPerintahTugasDocument\SuratPerintahTugasDocument;
use App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument;
use App\Models\Doc\LaporanHasilGelarPerkaraDocument\LaporanHasilGelarPerkaraDocument;
use App\Models\Doc\SuratKetetapanTentangPenetapanTersangkaDocument\SuratKetetapanTentangPenetapanTersangkaDocument;
use App\Models\Doc\SuratPemberitahuanDimulainyaPenyidikanDocument\SuratPemberitahuanDimulainyaPenyidikanDocument;

class DocValidationService
{
    public function logging(array $data)
    {
        CaseDocumentValidation::updateOrCreate([
            'document_id' => $data['document_id']
        ], 
            $data
        );
        
        return true;
    }

    /**
     * Check sequential prerequisite for validating a document in Case Document Validation.
     * Order:
     * 1. Laporan Polisi (LP)
     * 2. Surat Perintah Penyelidikan
     * 3. Surat Perintah Tugas
     * 4. Surat Perintah Penyidikan
     * 5. Laporan Hasil Gelar Perkara
     * 6. Surat Ketetapan Tentang Penetapan Tersangka
     * 7. Surat Pemberitahuan Dimulainya Penyidikan (SPDP)
     *
     * @param string $accidentId
     * @param int|string $step
     * @return array ['can_validate' => bool, 'reason' => ?string]
     */
    public function checkSequentialValidation($accidentId, $step)
    {
        switch ($step) {
            case 2:
            case 'surat-perintah-penyelidikan-document':
                $hasLp = LaporanPolisi::where('accident_id', $accidentId)->exists();
                return [
                    'can_validate' => $hasLp,
                    'reason' => $hasLp ? null : 'Laporan Polisi (LP) belum diunggah.'
                ];

            case 3:
            case 'surat-perintah-tugas-document':
                $sprinLidikApproved = SuratPerintahPenyelidikanDocument::where('accident_id', $accidentId)
                    ->whereIn('status_id', ['86', '85'])->exists();
                return [
                    'can_validate' => $sprinLidikApproved,
                    'reason' => $sprinLidikApproved ? null : 'Surat Perintah Penyelidikan belum divalidasi/disetujui.'
                ];

            case 4:
            case 'surat-perintah-penyidikan-document':
                $sprinGasApproved = SuratPerintahTugasDocument::where('accident_id', $accidentId)
                    ->whereIn('status_id', ['86', '85'])->exists();
                return [
                    'can_validate' => $sprinGasApproved,
                    'reason' => $sprinGasApproved ? null : 'Surat Perintah Tugas belum divalidasi/disetujui.'
                ];

            case 5:
            case 'laporan-hasil-gelar-perkara-document':
                $sprinDikApproved = SuratPerintahPenyidikanDocument::where('accident_id', $accidentId)
                    ->whereIn('status_id', ['86', '85'])->exists();
                return [
                    'can_validate' => $sprinDikApproved,
                    'reason' => $sprinDikApproved ? null : 'Surat Perintah Penyidikan belum divalidasi/disetujui.'
                ];

            case 6:
            case 'surat-ketetapan-tentang-penetapan-tersangka-document':
                $gelarApproved = LaporanHasilGelarPerkaraDocument::where('accident_id', $accidentId)
                    ->whereIn('status_id', ['86', '85'])->exists();
                return [
                    'can_validate' => $gelarApproved,
                    'reason' => $gelarApproved ? null : 'Laporan Hasil Gelar Perkara belum divalidasi/disetujui.'
                ];

            case 7:
            case 'surat-pemberitahuan-dimulainya-penyidikan-document':
                $tersangkaApproved = SuratKetetapanTentangPenetapanTersangkaDocument::where('accident_id', $accidentId)
                    ->whereIn('status_id', ['86', '85'])->exists();
                return [
                    'can_validate' => $tersangkaApproved,
                    'reason' => $tersangkaApproved ? null : 'Surat Ketetapan Penetapan Tersangka belum divalidasi/disetujui.'
                ];

            default:
                return ['can_validate' => true, 'reason' => null];
        }
    }
}