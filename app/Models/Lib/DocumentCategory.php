<?php

namespace App\Models\Lib;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
        'id' => 'string',
        'is_active' => 'boolean',
        'is_required' => 'boolean',
        'is_digital_signature' => 'boolean',
        'is_case_finish' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function parent()
    {
        return $this->belongsTo(DocumentCategory::class, 'parent_id')->with('parent');
    }

    public function children()
    {
        return $this->hasMany(DocumentCategory::class, 'parent_id')->with('children');
    }

    public function updatedByUser()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function statusLogs()
    {
        return $this->hasMany(DocumentCategoryStatusLog::class, 'document_category_id')->orderBy('created_at', 'desc');
    }

    /**
     * Get associative map of document status for lightning-fast route checking.
     * Maps:
     * - id => is_active (e.g. '0101' => true)
     * - base_route => is_active (e.g. 'doc.surat-perintah-penyelidikan-document' => true)
     * - custom alias => is_active (e.g. 'doc.sp2hp-document' => is_active of 0709)
     */
    public static function getActiveMap(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            $categories = self::where('category', 'TYPE')->get();
            $map = [];

            foreach ($categories as $cat) {
                $status = (bool) $cat->is_active;
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
