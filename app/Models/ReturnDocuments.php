<?php

namespace App\Models;

use App\Models\Lib\DocumentCategory;
use App\Models\Accident;
use App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

use Illuminate\Support\Carbon;

class ReturnDocuments extends Model
{
    use HasFactory;

    protected $table="return_documents";

    protected $primaryKey = 'id';

    protected $fillable = [
        'accident_id',
        'documentable_type',
        'documentable_id',
        'document_category_id',
        'returned_by_id',
        'returned_by_name',
        'returned_reason',
        'returned_at'
    ];

    protected $casts = [
        'returned_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function accident(): BelongsTo
    {
        return $this->belongsTo(Accident::class, 'accident_id', 'id');
    }

    public function returnedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'returned_by_id', 'id');
    }

    public function documentCategory(): BelongsTo
    {
        return $this->belongsTo(DocumentCategory::class, 'document_category_id', 'id');
    }

    public function scopeForDocument($query, $document)
    {
        return $query->where('documentable_type', get_class($document))
                     ->where('documentable_id', $document->getKey());
    }

    public function scopeToday($query)
    {
        return $query->whereDate('returned_at', today());
    }
}
