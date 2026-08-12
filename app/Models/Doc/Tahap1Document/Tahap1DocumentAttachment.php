<?php

namespace App\Models\Doc\Tahap1Document;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Tahap1DocumentAttachment extends Model
{
    use HasFactory;

    protected $table = 'doc.tahap_1_document_attachments';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'tahap_1_document_id',
        'path',
        'name',
        'original_name',
        'extension',
        'mimetype',
        'size',
        'type',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function document()
    {
        return $this->belongsTo(Tahap1Document::class, 'tahap_1_document_id');
    }
}
