<?php

namespace App\Models\Pivot;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Webpatser\Uuid\Uuid;

class SuratPerintahPencabutanPenangguhanPenahananDocumentSuspect extends Pivot
{
    protected $table = 'public.pivot_surat_perintah_pencabutan_penangguhan_penahanan_document_suspect';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Uuid::generate();
            }
        });
    }

    public function suspect()
    {
        return $this->belongsTo('App\Models\Suspect', 'suspect_id', 'id');
    }

    public function document()
    {
        return $this->belongsTo('App\Models\Doc\SuratPerintahPencabutanPenangguhanPenahananDocument\SuratPerintahPencabutanPenangguhanPenahananDocument', 'document_id', 'id');
    }
}
