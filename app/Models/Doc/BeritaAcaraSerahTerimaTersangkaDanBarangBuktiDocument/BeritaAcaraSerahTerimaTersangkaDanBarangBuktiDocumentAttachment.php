<?php

namespace App\Models\Doc\BeritaAcaraSerahTerimaTersangkaDanBarangBuktiDocument;

use App\Observers\UserActionObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;

class BeritaAcaraSerahTerimaTersangkaDanBarangBuktiDocumentAttachment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'doc.berita_acara_serah_terima_tersangka_attachments';

    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $guarded = [];

    protected $casts = [
        'id' => 'string',
        'doc_id' => 'string',
        'name' => 'string',
        'path' => 'string',
        'original_name' => 'string',
        'extension' => 'string',
        'size' => 'integer',
        'mime_type' => 'string',
    ];

    public static function boot()
    {
        parent::boot();

        self::observe(UserActionObserver::class);

        self::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Uuid::generate();
            }
        });
    }

    public function document()
    {
        return $this->belongsTo('App\Models\Doc\BeritaAcaraSerahTerimaTersangkaDanBarangBuktiDocument\BeritaAcaraSerahTerimaTersangkaDanBarangBuktiDocument', 'doc_id', 'id');
    }
}
