<?php

namespace App\Models\Doc\SuratPermohonanPerpanjanganPenahananKejaksaanDocument;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;

class SuratPermohonanPerpanjanganPenahananKejaksaanDocumentAttachment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'doc.surat_permohonan_perpanjangan_penahanan_kejaksaan_document_attachments';
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

    public function suratPermohonanPerpanjanganPenahananKejaksaanDocument()
    {
        return $this->belongsTo('App\Models\Doc\SuratPermohonanPerpanjanganPenahananKejaksaanDocument\SuratPermohonanPerpanjanganPenahananKejaksaanDocument', 'doc_id', 'id');
    }
}
