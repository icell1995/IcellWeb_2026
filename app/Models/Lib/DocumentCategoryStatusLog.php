<?php

namespace App\Models\Lib;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class DocumentCategoryStatusLog extends Model
{
    use HasFactory;

    protected $table = 'lib.document_category_status_logs';

    protected $fillable = [
        'document_category_id',
        'previous_status',
        'new_status',
        'reason',
        'changed_by',
    ];

    protected $casts = [
        'previous_status' => 'boolean',
        'new_status' => 'boolean',
    ];

    public function documentCategory()
    {
        return $this->belongsTo(DocumentCategory::class, 'document_category_id');
    }

    public function changedByUser()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
