<?php

namespace App\Http\Controllers\CMS\ReturnedDocuments;

use App\Http\Controllers\Controller;
use App\Models\Accident;
use App\Models\Polda;
use App\Models\ReturnDocuments;
use App\Traits\AccidentQueryTraits;
use App\Traits\DocumentCategoryTraits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;



class DocumentReturnController extends Controller
{
    use DocumentCategoryTraits;
    use AccidentQueryTraits;

    public function __construct()
    {
        $this->initializeDocumentTrait();
    }

    public function index()
    {
        return view('cms.return-documents.index');
    }

    public function searchAccident(Request $request)
    {
        $validate = $request->validate([
            'no_lp' => 'required|string|min:3',
        ]);

        $search = $validate['no_lp'];

        $query = Accident::from('accidents as a')
            ->leftJoin('polres', 'a.polres_id', '=', 'polres.id')     // leftJoin lebih aman kalau polres/polres nullable
            ->leftJoin('polda', 'polres.polda_id', '=', 'polda.id')
            ->leftJoin('ref', 'a.selra_flag', '=', 'ref.id');

        $query->where('a.no_lp', 'ILIKE', '%' . $search . '%');

        $accidents = $query->select([
            'a.id',
            'polda.name as polda_name',
            'polres.name as polres_name',
            'ref.id as selra_id',
            'a.rank_id',
            'a.no_lp',
            'a.md',
            'a.lb',
            'a.lr',
            'a.road_name',
            DB::raw("to_char(a.accident_date, 'DD-MM-YYYY') as accident_date"),
            'ref.name as selra',
            DB::raw("to_char(a.last_update, 'DD-MM-YYYY HH24:MI:SS') as accident_last_update"),
            DB::raw("CONCAT(officer_first_name, ' ', officer_last_name) as officer_name"),
        ])
            ->orderBy('a.accident_date', 'desc')
            ->paginate(10)
            ->withQueryString();

        return response()->json([
            'success' => true,
            'data'    => $accidents->items(),
            'pagination' => [
                'current_page' => $accidents->currentPage(),
                'last_page'    => $accidents->lastPage(),
                'total'        => $accidents->total(),
                'per_page'     => $accidents->perPage(),
            ],
            'message' => $accidents->isEmpty() ? 'Tidak ditemukan data dengan No LP tersebut' : null,
        ]);
    }

    public function getDocumentsByAccident($accidentId)
    {
        $documents = $this->getAllDocumentsByAccident($accidentId);

        return response()->json([
            'success' => true,
            'data'    => $documents,
            'count'   => $documents->count(),
        ]);
    }

    public function getCascadeInfo(Request $request, $accidentId, $documentId)
    {
        $request->validate([
            'document_type' => 'required|string',
        ]);

        $type = $request->document_type;

        Log::info('getCascadeInfo dipanggil', [
            'accident_id'   => $accidentId,
            'document_id'   => $documentId,
            'type'          => $type,
        ]);

        try {
            $categoryId = $this->getCategoryIdByType($type);
            if (!$categoryId) {
                Log::warning('Category ID tidak ditemukan untuk type', ['type' => $type]);
                return response()->json(['success' => false, 'message' => 'Tipe dokumen tidak valid']);
            }

            $mainDoc = $this->getDocumentById($documentId, $categoryId);
            if (!$mainDoc) {
                Log::warning('Dokumen utama tidak ditemukan', ['id' => $documentId, 'category_id' => $categoryId]);
                return response()->json(['success' => false, 'message' => 'Dokumen utama tidak ditemukan']);
            }

            $cascadeDocs = $this->getCascadeDocument($type, $accidentId, $documentId);

            $names = $cascadeDocs->map(function ($doc) {
                $cat = collect($this->documentCategories)->firstWhere('model', get_class($doc));
                return $cat ? $cat['name'] : class_basename($doc);
            })->unique()->values();

            return response()->json([
                'success' => true,
                'main'    => $names[0] ?? 'Dokumen utama',
                'cascade' => $names->slice(1)->values()->all(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error getCascadeInfo', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage() ?: 'Terjadi kesalahan server'
            ], 422);
        }
    }

    public function storeReturn(Request $request)
    {
        $request->validate([
            'accident_id'   => 'required|uuid|exists:accidents,id',
            'document_id'   => 'required|uuid',
            'document_type' => 'required|string',
            'reason'        => 'required|string|min:5|max:1000',
        ]);

        $accidentId = $request->accident_id;
        $docId      = $request->document_id;
        $docType    = $request->document_type;
        $reason = htmlspecialchars($request->reason);

        $messagesData = [
            'reason_approval_rejected' => $reason
        ];

        try {
            $mainDoc = $this->getDocumentById($docId, $this->getCategoryIdByType($docType));
            if (!$mainDoc) {
                throw new \Exception('Dokumen utama tidak ditemukan');
            }

            $documents = $this->getCascadeDocument($docType, $accidentId, $docId);

            [$allowed, $message] = $this->canReturnDocument($mainDoc, $accidentId);
            if (!$allowed) {
                throw new \Exception($message);
            }

            DB::beginTransaction();

            foreach ($documents as $doc) {
                if ($doc->status_id === 4) {
                    continue;
                }

                ReturnDocuments::create([
                    'accident_id'         => $accidentId,
                    'documentable_type'   => get_class($doc),
                    'documentable_id'     => $doc->id,
                    'document_category_id' => $this->getCategoryIdByType($this->getDocumentTypeByModel(get_class($doc))),
                    'returned_by_id'      => Auth::id(),
                    'returned_by_name'    => Auth::user()?->first_name ?? 'Helpdesk',
                    'returned_reason'     => $reason,
                    'returned_at'         => now(),
                ]);

                $doc->update([
                    'status_id' => 4,
                    'messages'  => $messagesData,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pengembalian dokumen berhasil dilakukan.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error storeReturn', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage() ?: 'Gagal melakukan pengembalian'
            ], 422);
        }
    }

}
