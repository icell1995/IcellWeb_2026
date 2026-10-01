<?php

namespace App\Models\Doc\Tahap2Document;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;

class Tahap2DocumentAttachment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'doc.tahap_2_document_attachments';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'tahap_2_document_id',
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
        return $this->belongsTo(Tahap2Document::class, 'tahap_2_document_id');
    }
}
