<?php

namespace App\Models\Lib;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
use App\Models\User;

class DocumentCategory extends Model
{
    use HasFactory;

    public const CACHE_KEY = 'active_document_categories_map';

    protected $table = 'lib.document_categories';
    public $incrementing = false;
    protected $primaryKey = 'id';
    public $keyType = 'string';

    protected $guarded = [];

    protected $casts = [
        'id'                   => 'string',
        'is_active'            => 'boolean',
        'is_required'          => 'boolean',
        'is_digital_signature' => 'boolean',
        'is_case_finish'       => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(DocumentCategory::class, 'parent_id')->with('parent');
    }

    public function children(): HasMany
    {
        return $this->hasMany(DocumentCategory::class, 'parent_id')->with('children');
    }

    public function updatedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(DocumentCategoryStatusLog::class, 'document_category_id')->orderBy('created_at', 'desc');
    }

    /**
     * Get associative map of document status for fast route checking.
     * Incorporates both Stage (category = STAGE) and Type (category = TYPE).
     * If a parent Stage is inactive, all its child documents are automatically inactive.
     *
     * @return array<string, bool>
     */
    public static function getActiveMap(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            // 1. Fetch Stage statuses
            $stages = self::where('category', 'STAGE')
                ->select(['id', 'is_active'])
                ->get();

            $stageMap = [];
            $map = [];

            foreach ($stages as $stage) {
                $isStageActive = (bool) $stage->is_active;
                $stageMap[$stage->id] = $isStageActive;
                $map['stage:' . $stage->id] = $isStageActive;
            }

            // 2. Fetch Document Types
            $categories = self::where('category', 'TYPE')
                ->select(['id', 'parent_id', 'is_active', 'base_route'])
                ->get();

            foreach ($categories as $cat) {
                $isParentActive = $stageMap[$cat->parent_id] ?? true;
                $status = (bool) $cat->is_active && $isParentActive;

                $map['id:' . $cat->id] = $status;

                if (!empty($cat->base_route)) {
                    $map['route:' . $cat->base_route] = $status;
                }
            }

            // Alias for SP2HP
            if (isset($map['id:0709'])) {
                $map['route:doc.sp2hp-document'] = $map['id:0709'];
            }

            return $map;
        });
    }

    /**
     * Invalidate and re-warm the document categories cache.
     */
    public static function invalidateCache(): void
    {
        Cache::forget(self::CACHE_KEY);
        self::getActiveMap();
    }
}
