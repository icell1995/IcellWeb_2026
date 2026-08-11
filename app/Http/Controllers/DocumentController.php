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
        
        return redirect()->route($typeDocument->route, ['accident_id' => $accidentId]);
    }

    public function getTypeDocument($id){
        $documentCategory = DocumentCategory::where('parent_id', $id)
                                ->where('category', 'TYPE')
                                ->where('route', '!=', NULL)
                                ->where('is_active', true)
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
