<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lib\DocumentCategory;

class DocumentController extends Controller
{
    public function createDocumentRouter(Request $request){
        $classDocumentId = $request->classDocument;
        $typeDocumentId = $request->typeDocument;
        $accidentId = $request->accidentId;

        $typeDocument = DocumentCategory::where('id', $typeDocumentId)
                            ->where('is_active', true)
                            ->first();

        if(!empty($typeDocument) && $typeDocument->route == null){
            return redirect()->back()->with('error', 'Form Berkas Tidak Tersedia');
        }

        if ($typeDocumentId == '0405') {
            $hasPermintaan = \App\Models\Doc\SuratPermintaanPenggeledahanDocument\SuratPermintaanPenggeledahanDocument::where('accident_id', $accidentId)->exists();
            if (!$hasPermintaan) {
                return redirect()->back()->with('error', 'Dokumen Permintaan Penggeledahan belum dibuat, mohon untuk buat Surat Permintaan Penggeledahan terlebih dahulu.');
            }
        }

        if ($typeDocumentId == '0206') {
            $hasSprintHenti = \App\Models\Doc\SuratPerintahPenghentianPenyidikanDocument\SuratPerintahPenghentianPenyidikanDocument::where('accident_id', $accidentId)->exists();
            if (!$hasSprintHenti) {
                return redirect()->back()->with('error', 'Dokumen Surat Perintah Penghentian Penyidikan belum dibuat, mohon untuk buat Surat Perintah Penghentian Penyidikan terlebih dahulu.');
            }
        }

        if ($typeDocumentId == '0216') {
            $hasSprintHenti = \App\Models\Doc\SuratPerintahPenghentianPenyidikanDocument\SuratPerintahPenghentianPenyidikanDocument::where('accident_id', $accidentId)->exists();
            if (!$hasSprintHenti) {
                return redirect()->back()->with('error', 'Dokumen Surat Perintah Penghentian Penyidikan belum dibuat, mohon untuk buat Surat Perintah Penghentian Penyidikan terlebih dahulu.');
            }
            $hasSketHenti = \App\Models\Doc\SuratKetetapanPenghentianPenyidikanDocument\SuratKetetapanPenghentianPenyidikanDocument::where('accident_id', $accidentId)->exists();
            if (!$hasSketHenti) {
                return redirect()->back()->with('error', 'Dokumen Surat Ketetapan Penghentian Penyidikan belum dibuat, mohon untuk buat Surat Ketetapan Penghentian Penyidikan terlebih dahulu.');
            }
        }
        
        if ($typeDocumentId == '0204') {
            return redirect()->route('doc.surat-pemberitahuan-dimulainya-penyidikan-pusiknas-document.create', ['accident_id' => $accidentId]);
        }

        return redirect()->route($typeDocument->route, ['accident_id' => $accidentId]);
    }

    public function getTypeDocument($id){
        $documentCategory = DocumentCategory::where('parent_id', $id)
                                ->where('category', 'TYPE')
                                ->where('route', '!=', NULL)
                                ->where('is_active', true)
                                ->orderBy('sort', 'asc')
                                ->orderBy('id', 'asc')
                                ->get();
        
        // Filter SP2HP documents - only show for role_id 1
        if (auth()->check() && auth()->user()->role_id != 1) {
            $documentCategory = $documentCategory->filter(function($doc) {
                // SP2HP document ID is 0709 and its variants (0710, 0711, 0712)
                return !in_array($doc->id, ['0709', '0710', '0711', '0712']);
            })->values();
        }
            
        return response()->json($documentCategory);
    }
}
