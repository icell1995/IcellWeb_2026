<?php

namespace App\Models\Doc\SuratPermintaanPerpanjanganPenahananLanjutanKeduaDocument;

use App\Observers\UserActionObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SuratPermintaanPerpanjanganPenahananLanjutanKeduaDocumentAttachment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'doc.surat_perpanjangan_penahanan_kedua_attachments';

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
        return $this->belongsTo(SuratPermintaanPerpanjanganPenahananLanjutanKeduaDocument::class, 'document_id');
    }
}
