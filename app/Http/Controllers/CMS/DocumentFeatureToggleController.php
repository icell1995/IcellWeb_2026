<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use App\Services\Doc\DocumentFeatureFlagService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DocumentFeatureToggleController extends Controller
{
    public function __construct(
        protected DocumentFeatureFlagService $service
    ) {}

    /**
     * Display listing of document categories and their feature flag status.
     */
    public function index(Request $request): View
    {
        $data = $this->service->getDashboardData(
            $request->only(['stage', 'status', 'search', 'audit_page', 'audit_per_page'])
        );

        return view('cms.document-management.index', $data);
    }

    /**
     * Toggle the active status of a single document category.
     */
    public function toggle(Request $request, string $id): JsonResponse
    {
        $validated = $request->validate([
            'is_active' => 'required|boolean',
            'reason'    => 'nullable|string|max:500',
        ]);

        try {
            $result = $this->service->toggleDocument(
                $id,
                (bool) $validated['is_active'],
                $validated['reason'] ?? null,
                auth()->id()
            );

            $category = $result['category'];
            $newStatus = $result['is_active'];

            return response()->json([
                'success'    => true,
                'message'    => 'Status surat "' . $category->name . '" berhasil diubah menjadi ' . ($newStatus ? 'AKTIF' : 'NONAKTIF') . '.',
                'is_active'  => $newStatus,
                'updated_at' => now()->format('d/m/Y H:i'),
                'updated_by' => auth()->user() ? auth()->user()->first_name . ' ' . auth()->user()->last_name : 'Admin',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status surat: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Toggle the active status of an entire Stage / Kategori.
     * When deactivated, all letters within the category are also deactivated.
     */
    public function toggleStage(Request $request, string $id): JsonResponse
    {
        $validated = $request->validate([
            'is_active' => 'required|boolean',
            'cascade'   => 'nullable|boolean',
            'reason'    => 'nullable|string|max:500',
        ]);

        try {
            $result = $this->service->toggleStage(
                $id,
                (bool) $validated['is_active'],
                $request->has('cascade') ? (bool) $validated['cascade'] : true,
                $validated['reason'] ?? null,
                auth()->id()
            );

            return response()->json($result);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status kategori: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Batch toggle all documents within a specific stage.
     */
    public function batchToggle(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'stage_id'  => 'required|string',
            'is_active' => 'required|boolean',
            'reason'    => 'nullable|string|max:500',
        ]);

        try {
            $result = $this->service->batchToggleStage(
                $validated['stage_id'],
                (bool) $validated['is_active'],
                $validated['reason'] ?? null,
                auth()->id()
            );

            return response()->json($result, $result['success'] ? 200 : 404);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui status dokumen massal: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Trigger synchronization from DocumentTypeEnum.
     */
    public function sync(): JsonResponse
    {
        try {
            $stats = $this->service->syncFromEnum();

            return response()->json([
                'success' => true,
                'message' => "Sinkronisasi berhasil! Dokumen baru: {$stats['created']}, diperbarui: {$stats['updated']}.",
                'output'  => "Created: {$stats['created']}, Updated: {$stats['updated']}. Cache refreshed.",
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan sinkronisasi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get status audit logs for a specific document category.
     */
    public function history(string $id): JsonResponse
    {
        try {
            $history = $this->service->getDocumentHistory($id);

            return response()->json([
                'success'       => true,
                'category_name' => $history['category_name'],
                'category_id'   => $history['category_id'],
                'logs'          => $history['logs'],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat riwayat dokumen: ' . $e->getMessage(),
            ], 404);
        }
    }
}
