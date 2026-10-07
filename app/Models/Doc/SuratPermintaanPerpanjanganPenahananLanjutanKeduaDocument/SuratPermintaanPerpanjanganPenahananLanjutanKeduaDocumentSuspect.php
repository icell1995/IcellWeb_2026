<?php

namespace App\Models\Doc\SuratPermintaanPerpanjanganPenahananLanjutanKeduaDocument;

use App\Observers\UserActionObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratPermintaanPerpanjanganPenahananLanjutanKeduaDocumentSuspect extends Model
{
    use HasFactory;

    protected $table = 'doc.surat_perpanjangan_penahanan_kedua_suspects';

    protected $guarded = [];

    public static function boot()
    {
        parent::boot();
        self::observe(UserActionObserver::class);
    }
}
