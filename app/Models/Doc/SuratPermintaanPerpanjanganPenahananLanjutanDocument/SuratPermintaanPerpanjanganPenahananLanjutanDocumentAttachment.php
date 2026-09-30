<?php

namespace App\Models\Doc\SuratPermintaanPerpanjanganPenahananLanjutanDocument;

use App\Observers\UserActionObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;

class SuratPermintaanPerpanjanganPenahananLanjutanDocumentAttachment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'doc.surat_permintaan_perpanjangan_penahanan_lanjutan_document_attachments';

    protected $primaryKey = 'id';

    protected $guarded = [
        'id',
    ];

    public static function boot()
    {
        parent::boot();
    }

    public function document()
    {
        return $this->belongsTo(SuratPermintaanPerpanjanganPenahananLanjutanDocument::class, 'document_id');
    }
}
