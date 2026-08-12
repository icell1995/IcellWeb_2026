<?php

namespace App\Models\Lib;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentCategory extends Model
{
    use HasFactory;

    protected $table = 'lib.document_categories';
    public $incrementing = false;
    protected $primaryKey = 'id';
    public $keyType = 'string';

    protected $guarded = [];

    protected $casts = [
        'id' => 'string',
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
}
