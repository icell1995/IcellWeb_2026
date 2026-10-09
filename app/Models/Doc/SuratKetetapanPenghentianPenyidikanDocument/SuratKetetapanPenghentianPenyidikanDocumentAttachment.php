<?php

namespace App\Models\Doc\SuratKetetapanPenghentianPenyidikanDocument;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;

class SuratKetetapanPenghentianPenyidikanDocumentAttachment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'doc.surat_ketetapan_penghentian_penyidikan_document_attachments';

    protected $primaryKey = 'id';
    protected $keyType = 'uuid';

    protected $guarded = [];

    protected $casts = [
        'id'        => 'string',
        'is_active' => 'boolean',
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
        return $this->belongsTo(SuratKetetapanPenghentianPenyidikanDocument::class, 'surat_ketetapan_penghentian_penyidikan_document_id', 'id');
    }
}
