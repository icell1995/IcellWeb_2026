<?php

namespace App\Models\Doc\SuratPerintahPencabutanPenangguhanPenahananDocument;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;

class SuratPerintahPencabutanPenangguhanPenahananDocumentAttachment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'doc.surat_perintah_pencabutan_penangguhan_penahanan_document_attachments';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $guarded = [
        'id',
    ];

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Uuid::generate();
            }
        });
    }

    public function suratPerintahPencabutanPenangguhanPenahananDocument()
    {
        return $this->belongsTo('App\Models\Doc\SuratPerintahPencabutanPenangguhanPenahananDocument\SuratPerintahPencabutanPenangguhanPenahananDocument', 'doc_id', 'id');
    }
}
