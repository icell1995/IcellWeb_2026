<?php

namespace App\Services\Doc;

use App\Enums\DocumentTypeEnum;
use App\Models\Lib\DocumentCategory;
use App\Models\Lib\DocumentCategoryStatusLog;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class DocumentFeatureFlagService
{
    /**
     * Retrieve all data required for the CMS Document Management dashboard.
     * Guaranteed zero N+1 queries through precise eager loading and in-memory aggregation.
     *
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function getDashboardData(array $filters = []): array
    {
        $selectedStage = $filters['stage'] ?? null;
        $selectedStatus = $filters['status'] ?? null;
        $search = $filters['search'] ?? null;

        // 1. Fetch Stages with audit metadata and eager loaded user
        $stages = DocumentCategory::where('category', 'STAGE')
            ->select(['id', 'name', 'is_active', 'updated_by', 'updated_at'])
            ->with('updatedByUser:id,first_name,last_name')
            ->orderBy('id', 'asc')
            ->get();

        // 2. Fetch all document types for KPI stats and stage aggregates (single fast query)
        $allTypes = DocumentCategory::where('category', 'TYPE')
            ->select(['id', 'parent_id', 'is_active', 'route', 'base_route'])
            ->get();

        $totalAll = $allTypes->count();
        $totalActive = $allTypes->where('is_active', true)->count();
        $totalInactive = $allTypes->where('is_active', false)->count();
        $totalAvailableRoutes = $allTypes->filter(fn($d) => !empty($d->route) || !empty($d->base_route))->count();

        // 3. Compute stage statistics in memory (0 extra queries)
        $stageStats = [];
        foreach ($stages as $stage) {
            $stageDocs = $allTypes->where('parent_id', $stage->id);
            $stageStats[$stage->id] = [
                'total' => $stageDocs->count(),
                'active' => $stageDocs->where('is_active', true)->count(),
                'inactive' => $stageDocs->where('is_active', false)->count(),
            ];
        }

        // 4. Query documents list with precise eager loading to eliminate N+1
        $query = DocumentCategory::query()
            ->where('category', 'TYPE')
            ->select([
                'id',
                'parent_id',
                'code',
                'name',
                'category',
                'route',
                'base_route',
                'is_active',
                'sort',
                'updated_by',
                'updated_at',
            ])
            ->with([
                'parent:id,name,is_active',
                'updatedByUser:id,first_name,last_name',
            ]);

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

        // 5. Fetch audit logs with pagination & eager-loaded relations (eliminates N+1)
        $auditPageSize = (int) ($filters['audit_per_page'] ?? 10);
        $logs = DocumentCategoryStatusLog::query()
            ->select(['id', 'document_category_id', 'previous_status', 'new_status', 'reason', 'changed_by', 'created_at'])
            ->with([
                'documentCategory:id,name',
                'changedByUser:id,first_name,last_name',
            ])
            ->orderBy('created_at', 'desc')
            ->paginate($auditPageSize, ['*'], 'audit_page');

        return compact(
            'documents',
            'stages',
            'stageStats',
            'logs',
            'selectedStage',
            'selectedStatus',
            'search',
            'totalAll',
            'totalActive',
            'totalInactive',
            'totalAvailableRoutes'
        );
    }

    /**
     * Toggle the active status of a single document category (TYPE).
     *
     * @return array<string, mixed>
     */
    public function toggleDocument(string $id, bool $newStatus, ?string $reason = null, ?int $userId = null): array
    {
        $category = DocumentCategory::where('id', $id)
            ->where('category', 'TYPE')
            ->firstOrFail();

        $previousStatus = (bool) $category->is_active;

        if ($previousStatus === $newStatus) {
            return [
                'changed' => false,
                'category' => $category,
                'is_active' => $newStatus,
            ];
        }

        return DB::transaction(function () use ($category, $previousStatus, $newStatus, $reason, $userId) {
            $category->is_active = $newStatus;
            $category->updated_by = $userId;
            $category->save();

            DocumentCategoryStatusLog::create([
                'document_category_id' => $category->id,
                'previous_status' => $previousStatus,
                'new_status' => $newStatus,
                'reason' => $reason ?: ($newStatus ? 'Diaktifkan oleh Administrator via CMS' : 'Dinonaktifkan oleh Administrator via CMS'),
                'changed_by' => $userId,
            ]);

            DocumentCategory::invalidateCache();

            return [
                'changed' => true,
                'category' => $category,
                'is_active' => $newStatus,
            ];
        });
    }

    /**
     * Toggle the active status of an entire Stage / Kategori.
     * When deactivated, automatically cascades to all documents within the stage.
     *
     * @return array<string, mixed>
     */
    public function toggleStage(string $stageId, bool $newStatus, bool $cascadeToChildren = true, ?string $reason = null, ?int $userId = null): array
    {
        $stage = DocumentCategory::where('id', $stageId)
            ->where('category', 'STAGE')
            ->firstOrFail();

        $previousStatus = (bool) $stage->is_active;

        return DB::transaction(function () use ($stage, $stageId, $previousStatus, $newStatus, $cascadeToChildren, $reason, $userId) {
            $stage->is_active = $newStatus;
            $stage->updated_by = $userId;
            $stage->save();

            $defaultReason = $reason ?: ($newStatus 
                ? "Kategori/Tahapan {$stage->name} diaktifkan oleh Administrator via CMS" 
                : "Kategori/Tahapan {$stage->name} dinonaktifkan oleh Administrator via CMS");

            DocumentCategoryStatusLog::create([
                'document_category_id' => $stage->id,
                'previous_status' => $previousStatus,
                'new_status' => $newStatus,
                'reason' => $defaultReason,
                'changed_by' => $userId,
            ]);

            $updatedChildrenCount = 0;

            // Cascade to child documents when deactivating or when explicitly requested on activate
            if (!$newStatus || $cascadeToChildren) {
                $children = DocumentCategory::where('parent_id', $stageId)
                    ->where('category', 'TYPE')
                    ->get();

                foreach ($children as $child) {
                    if ((bool) $child->is_active !== $newStatus) {
                        $prevChildStatus = (bool) $child->is_active;
                        $child->is_active = $newStatus;
                        $child->updated_by = $userId;
                        $child->save();

                        DocumentCategoryStatusLog::create([
                            'document_category_id' => $child->id,
                            'previous_status' => $prevChildStatus,
                            'new_status' => $newStatus,
                            'reason' => "Otomatis diubah mengikuti status Kategori {$stage->name}",
                            'changed_by' => $userId,
                        ]);

                        $updatedChildrenCount++;
                    }
                }
            }

            DocumentCategory::invalidateCache();

            return [
                'success' => true,
                'stage' => $stage,
                'is_active' => $newStatus,
                'updated_children_count' => $updatedChildrenCount,
                'message' => $newStatus 
                    ? "Kategori {$stage->name} berhasil diaktifkan." . ($updatedChildrenCount > 0 ? " ({$updatedChildrenCount} surat di dalamnya ikut diaktifkan)" : "")
                    : "Kategori {$stage->name} berhasil dinonaktifkan beserta seluruh surat di dalamnya ({$updatedChildrenCount} surat).",
            ];
        });
    }

    /**
     * Batch toggle all documents within a given stage.
     *
     * @return array<string, mixed>
     */
    public function batchToggleStage(string $stageId, bool $newStatus, ?string $reason = null, ?int $userId = null): array
    {
        $categories = DocumentCategory::where('parent_id', $stageId)
            ->where('category', 'TYPE')
            ->get();

        if ($categories->isEmpty()) {
            return [
                'success' => false,
                'message' => 'Tidak ada dokumen yang terdaftar pada tahapan ini.',
                'updated_count' => 0,
            ];
        }

        return DB::transaction(function () use ($categories, $stageId, $newStatus, $reason, $userId) {
            $updatedCount = 0;
            $defaultReason = $reason ?: ($newStatus ? "Aktivasi massal seluruh surat tahapan {$stageId}" : "Deaktivasi massal seluruh surat tahapan {$stageId}");

            foreach ($categories as $cat) {
                if ((bool) $cat->is_active !== $newStatus) {
                    $previousStatus = (bool) $cat->is_active;
                    $cat->is_active = $newStatus;
                    $cat->updated_by = $userId;
                    $cat->save();

                    DocumentCategoryStatusLog::create([
                        'document_category_id' => $cat->id,
                        'previous_status' => $previousStatus,
                        'new_status' => $newStatus,
                        'reason' => $defaultReason,
                        'changed_by' => $userId,
                    ]);

                    $updatedCount++;
                }
            }

            if ($updatedCount > 0) {
                DocumentCategory::invalidateCache();
            }

            return [
                'success' => true,
                'message' => "Berhasil memperbarui status {$updatedCount} jenis dokumen pada tahapan {$stageId}.",
                'updated_count' => $updatedCount,
            ];
        });
    }

    /**
     * Synchronize document categories from DocumentTypeEnum into lib.document_categories table.
     * Preserves existing is_active status to prevent overwriting admin configurations.
     *
     * @return array<string, int> ['created' => int, 'updated' => int]
     */
    public function syncFromEnum(bool $forceActivate = false): array
    {
        return DB::transaction(function () use ($forceActivate) {
            $created = 0;
            $updated = 0;

            foreach (DocumentTypeEnum::cases() as $type) {
                $existing = DocumentCategory::where('id', $type->value)->first();

                if (!$existing) {
                    DocumentCategory::create([
                        'id' => $type->value,
                        'code' => 'DCT-' . $type->value,
                        'parent_id' => $type->parentId(),
                        'name' => $type->label(),
                        'category' => 'TYPE',
                        'base_route' => $type->baseRoute(),
                        'route' => $type->baseRoute() . '.create',
                        'is_active' => true,
                    ]);
                    $created++;
                } else {
                    $updateData = [
                        'name' => $type->label(),
                        'base_route' => $type->baseRoute(),
                        'route' => $type->baseRoute() . '.create',
                    ];

                    if ($forceActivate) {
                        $updateData['is_active'] = true;
                    }

                    $existing->update($updateData);
                    $updated++;
                }
            }

            DocumentCategory::invalidateCache();

            return [
                'created' => $created,
                'updated' => $updated,
            ];
        });
    }

    /**
     * Get audit trail logs for a specific document category.
     *
     * @return array<string, mixed>
     */
    public function getDocumentHistory(string $id): array
    {
        $category = DocumentCategory::where('id', $id)->firstOrFail();

        $logs = DocumentCategoryStatusLog::query()
            ->select(['id', 'document_category_id', 'previous_status', 'new_status', 'reason', 'changed_by', 'created_at'])
            ->with('changedByUser:id,first_name,last_name')
            ->where('document_category_id', $id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($log) => [
                'id' => $log->id,
                'previous_status' => (bool) $log->previous_status,
                'new_status' => (bool) $log->new_status,
                'reason' => $log->reason,
                'changed_by' => $log->changedByUser ? $log->changedByUser->first_name . ' ' . $log->changedByUser->last_name : 'System',
                'created_at' => $log->created_at ? $log->created_at->format('d/m/Y H:i:s') : '-',
            ]);

        return [
            'category_id' => $category->id,
            'category_name' => $category->name,
            'logs' => $logs,
        ];
    }
}
