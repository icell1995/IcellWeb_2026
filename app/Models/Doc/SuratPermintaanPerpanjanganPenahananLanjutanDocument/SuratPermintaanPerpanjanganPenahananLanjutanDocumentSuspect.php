<?php

namespace App\Models\Doc\SuratPermintaanPerpanjanganPenahananLanjutanDocument;

use App\Observers\UserActionObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;

class SuratPermintaanPerpanjanganPenahananLanjutanDocumentSuspect extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'doc.surat_permintaan_perpanjangan_penahanan_lanjutan_document_suspects';

    protected $primaryKey = 'id';

    protected $keyType = 'uuid';

    protected $guarded = [];

    protected $casts = [
        'id' => 'string',
    ];

    public static function boot()
    {
        parent::boot();
        self::observe(UserActionObserver::class);
        self::creating(function ($model) {
            $model->id = (string) Uuid::generate();
        });
    }
}
