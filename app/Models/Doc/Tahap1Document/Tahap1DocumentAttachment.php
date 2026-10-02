<?php

namespace App\Models\Doc\Tahap1Document;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;

class Tahap1DocumentAttachment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'doc.tahap_1_document_attachments';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'tahap_1_document_id',
        'original_name',
        'name',
        'extension',
        'size',
        'mimetype',
        'type',
        'description',
        'created_by_user_id',
    ];

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->id = (string) Uuid::generate();
        });
    }

    public function document()
    {
        return $this->belongsTo(Tahap1Document::class, 'tahap_1_document_id');
    }
}
