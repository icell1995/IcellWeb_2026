<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lib\DocumentCategory;
use App\Models\Doc\SuratKetetapanTentangPenetapanTersangkaDocument\SuratKetetapanTentangPenetapanTersangkaDocument;
use App\Models\Doc\SuratPerintahPenangkapanDocument\SuratPerintahPenangkapanDocument;
use App\Models\Doc\SuratPerintahPenahananDocument\SuratPerintahPenahananDocument;
use App\Models\Doc\PermintaanPerpanjanganPenahananDocument\PermintaanPerpanjanganPenahananDocument;

class DocumentController extends Controller
{
    /**
     * Prasyarat dokumen: dokumen induk harus selesai alur persetujuan unggahan PDF (status 86).
     *
     * Rantai prasyarat:
     *   SPP (0301) approved → SPH (0601) muncul
     *   SPH (0601) approved → SPPP (0603) muncul
     *   SPH (0601) + SPPP (0603) approved → S22 (0604) muncul
     */
    private const STATUS_IDS_ELIGIBLE_PREREQUISITE_PENAHANAN = ['86'];

    public function createDocumentRouter(Request $request){
        $classDocumentId = $request->classDocument;
        $typeDocumentId  = $request->typeDocument;
        $accidentId      = $request->accidentId;

        $typeDocument = DocumentCategory::where('id', $typeDocumentId)
                            ->where('is_active', true)
                            ->first();

        if(!empty($typeDocument) && $typeDocument->route == null){
            return redirect()->back()->with('error', 'Form Berkas Tidak Tersedia');
        }

        // Prasyarat SPP (0301): SKET harus sudah berstatus 86
        if ((string) $typeDocumentId === '0301') {
            $hasEligibleSket = SuratKetetapanTentangPenetapanTersangkaDocument::query()
                ->where('accident_id', $accidentId)
                ->whereNull('deleted_at')
                ->whereIn('status_id', self::STATUS_IDS_ELIGIBLE_PREREQUISITE_PENAHANAN)
                ->exists();
            if (! $hasEligibleSket) {
                return redirect()->back()->with(
                    'error',
                    'Surat Ketetapan tentang Penetapan Tersangka harus sudah berstatus 86 (unggahan PDF telah disetujui admin). Selesaikan alur persetujuan unggahan terlebih dahulu.'
                );
            }
        }

        // Prasyarat SPH (0601): SPP harus sudah berstatus 86
        if ((string) $typeDocumentId === '0601') {
            $hasEligibleSpp = SuratPerintahPenangkapanDocument::query()
                ->where('accident_id', $accidentId)
                ->whereNull('deleted_at')
                ->whereIn('status_id', self::STATUS_IDS_ELIGIBLE_PREREQUISITE_PENAHANAN)
                ->exists();
            if (! $hasEligibleSpp) {
                return redirect()->back()->with(
                    'error',
                    'Surat Perintah Penangkapan harus sudah berstatus 86 (unggahan PDF telah disetujui admin). Selesaikan alur persetujuan unggahan terlebih dahulu.'
                );
            }
        }

        // Prasyarat SPPP (0603): SPH (0601) harus sudah berstatus 86
        if ((string) $typeDocumentId === '0603') {
            $hasEligibleSph = SuratPerintahPenahananDocument::query()
                ->where('accident_id', $accidentId)
                ->whereNull('deleted_at')
                ->whereIn('status_id', self::STATUS_IDS_ELIGIBLE_PREREQUISITE_PENAHANAN)
                ->exists();
            if (! $hasEligibleSph) {
                return redirect()->back()->with(
                    'error',
                    'Surat Perintah Penahanan harus sudah berstatus 86 (unggahan PDF telah disetujui admin). Selesaikan alur persetujuan unggahan terlebih dahulu.'
                );
            }
        }

        // Prasyarat S22 (0604): SPH (0601) DAN SPPP (0603) harus sudah berstatus 86
        if ((string) $typeDocumentId === '0604') {
            $hasEligibleSph = SuratPerintahPenahananDocument::query()
                ->where('accident_id', $accidentId)
                ->whereNull('deleted_at')
                ->whereIn('status_id', self::STATUS_IDS_ELIGIBLE_PREREQUISITE_PENAHANAN)
                ->exists();
            $hasEligibleS21 = PermintaanPerpanjanganPenahananDocument::query()
                ->where('accident_id', $accidentId)
                ->whereNull('deleted_at')
                ->whereIn('status_id', self::STATUS_IDS_ELIGIBLE_PREREQUISITE_PENAHANAN)
                ->exists();
            if (! $hasEligibleSph || ! $hasEligibleS21) {
                return redirect()->back()->with(
                    'error',
                    'Surat Perintah Penahanan dan Surat Permintaan Perpanjangan Penahanan harus sudah berstatus 86 (unggahan PDF telah disetujui admin). Selesaikan alur persetujuan unggahan terlebih dahulu.'
                );
            }
        }

        return redirect()->route($typeDocument->route, ['accident_id' => $accidentId]);
    }

    public function getTypeDocument($id){
        $documentCategory = DocumentCategory::where('parent_id', $id)
                                ->where('category', 'TYPE')
                                ->where('route', '!=', NULL)
                                ->where('is_active', true)
                                ->get();

        // Filter prasyarat pada dropdown "Tambah Dokumen"
        $accidentId = request()->query('accident_id');
        if (! empty($accidentId)) {
            $hasEligibleSket = SuratKetetapanTentangPenetapanTersangkaDocument::query()
                ->where('accident_id', $accidentId)
                ->whereNull('deleted_at')
                ->whereIn('status_id', self::STATUS_IDS_ELIGIBLE_PREREQUISITE_PENAHANAN)
                ->exists();
            $hasEligibleSpp = SuratPerintahPenangkapanDocument::query()
                ->where('accident_id', $accidentId)
                ->whereNull('deleted_at')
                ->whereIn('status_id', self::STATUS_IDS_ELIGIBLE_PREREQUISITE_PENAHANAN)
                ->exists();
            $hasEligibleSph = SuratPerintahPenahananDocument::query()
                ->where('accident_id', $accidentId)
                ->whereNull('deleted_at')
                ->whereIn('status_id', self::STATUS_IDS_ELIGIBLE_PREREQUISITE_PENAHANAN)
                ->exists();
            $hasEligibleS21 = PermintaanPerpanjanganPenahananDocument::query()
                ->where('accident_id', $accidentId)
                ->whereNull('deleted_at')
                ->whereIn('status_id', self::STATUS_IDS_ELIGIBLE_PREREQUISITE_PENAHANAN)
                ->exists();

            $documentCategory = $documentCategory->filter(function ($doc) use ($hasEligibleSket, $hasEligibleSpp, $hasEligibleSph, $hasEligibleS21) {
                if ((string) $doc->id === '0301') {
                    return $hasEligibleSket; // SPP: SKET harus approved
                }
                if ((string) $doc->id === '0601') {
                    return $hasEligibleSpp; // SPH: SPP harus approved
                }
                if ((string) $doc->id === '0603') {
                    return $hasEligibleSph; // SPPP: SPH harus approved
                }
                if ((string) $doc->id === '0604') {
                    return $hasEligibleSph && $hasEligibleS21; // S22: SPH + SPPP harus approved
                }
                return true;
            })->values();
        } else {
            $documentCategory = $this->filterDocsWithoutAccidentContext($documentCategory);
        }

        // Filter SP2HP documents - only show for role_id 1 and 2
        if (auth()->check() && !in_array(auth()->user()->role_id, [1, 2])) {
            $documentCategory = $documentCategory->filter(function($doc) {
                return !in_array($doc->id, ['0709', '0710', '0711', '0712']);
            })->values();
        }

        return response()->json($documentCategory);
    }

    /**
     * Tanpa accident_id tidak bisa validasi prasyarat — sembunyikan 0601, 0603, 0604.
     */
    private function filterDocsWithoutAccidentContext($documentCategory)
    {
        // Tanpa accident_id tidak bisa validasi prasyarat — sembunyikan 0301, 0601, 0603, 0604
        return $documentCategory->filter(function ($doc) {
            return ! in_array((string) $doc->id, ['0301', '0601', '0603', '0604'], true);
        })->values();
    }
}
