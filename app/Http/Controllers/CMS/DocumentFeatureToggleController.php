<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lib\DocumentCategory;
use App\Models\Lib\DocumentCategoryStatusLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class DocumentFeatureToggleController extends Controller
{
    /**
     * Display listing of document categories and their feature flag status.
     */
    public function index(Request $request)
    {
        $stages = DocumentCategory::where('category', 'STAGE')
            ->orderBy('id', 'asc')
            ->get();

        $selectedStage = $request->query('stage');
        $selectedStatus = $request->query('status');
        $search = $request->query('search');

        $query = DocumentCategory::with(['parent', 'updatedByUser'])
            ->where('category', 'TYPE');

        if (!empty($selectedStage)) {
            $query->where('parent_id', $selectedStage);
        }

        if ($selectedStatus !== null && $selectedStatus !== '') {
            $query->where('is_active', $selectedStatus === '1');
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%")
                  ->orWhere('code', 'ilike', "%{$search}%");
            });
        }

        $documents = $query->orderBy('parent_id', 'asc')
            ->orderBy('sort', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $logs = DocumentCategoryStatusLog::with(['documentCategory', 'changedByUser'])
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get();

        $totalActive = DocumentCategory::where('category', 'TYPE')->where('is_active', true)->count();
        $totalInactive = DocumentCategory::where('category', 'TYPE')->where('is_active', false)->count();

        return view('cms.document-management.index', compact(
            'documents',
            'stages',
            'logs',
            'selectedStage',
            'selectedStatus',
            'search',
            'totalActive',
            'totalInactive'
        ));
    }

    /**
     * Toggle the is_active status of a document category.
     */
    public function toggle(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'is_active' => 'required|boolean',
            'reason' => 'nullable|string|max:500',
        ]);

        $category = DocumentCategory::where('id', $id)
            ->where('category', 'TYPE')
            ->firstOrFail();

        $previousStatus = (bool) $category->is_active;
        $newStatus = (bool) $request->input('is_active');

        if ($previousStatus === $newStatus) {
            return response()->json([
                'success' => true,
                'message' => 'Status surat tidak berubah.',
                'is_active' => $newStatus,
            ]);
        }

        DB::beginTransaction();
        try {
            $category->is_active = $newStatus;
            $category->updated_by = auth()->id();
            $category->save();

            DocumentCategoryStatusLog::create([
                'document_category_id' => $category->id,
                'previous_status' => $previousStatus,
                'new_status' => $newStatus,
                'reason' => $request->input('reason') ?: ($newStatus ? 'Diaktifkan oleh Administrator via CMS' : 'Dinonaktifkan oleh Administrator via CMS'),
                'changed_by' => auth()->id(),
            ]);

            DB::commit();

            // Invalidate cache immediately
            DocumentCategory::invalidateCache();

            return response()->json([
                'success' => true,
                'message' => 'Status surat "' . $category->name . '" berhasil diubah menjadi ' . ($newStatus ? 'AKTIF' : 'NONAKTIF') . '.',
                'is_active' => $newStatus,
                'updated_at' => now()->translatedFormat('d M Y, H:i:s'),
                'updated_by' => auth()->user() ? auth()->user()->first_name . ' ' . auth()->user()->last_name : 'Admin',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status surat: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get status audit logs for a specific document category.
     */
    public function history(string $id): JsonResponse
    {
        $category = DocumentCategory::where('id', $id)->firstOrFail();

        $logs = DocumentCategoryStatusLog::with('changedByUser')
            ->where('document_category_id', $id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'previous_status' => $log->previous_status,
                    'new_status' => $log->new_status,
                    'reason' => $log->reason,
                    'changed_by' => $log->changedByUser ? $log->changedByUser->first_name . ' ' . $log->changedByUser->last_name : 'System',
                    'created_at' => $log->created_at ? $log->created_at->format('d/m/Y H:i') : '-',
                ];
            });

        return response()->json([
            'success' => true,
            'category_name' => $category->name,
            'logs' => $logs,
        ]);
    }
}
