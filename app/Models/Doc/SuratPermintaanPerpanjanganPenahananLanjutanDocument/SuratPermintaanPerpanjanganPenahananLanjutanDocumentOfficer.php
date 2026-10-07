<?php

namespace App\Models\Doc\SuratPermintaanPerpanjanganPenahananLanjutanDocument;

use App\Observers\UserActionObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;

class SuratPermintaanPerpanjanganPenahananLanjutanDocumentOfficer extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'doc.surat_permintaan_perpanjangan_penahanan_lanjutan_document_officers';

    protected $guarded = [];

    protected $casts = [
        'status' => 'json',
        'class' => 'json',
        'flag' => 'json',
        'rank' => 'json',
        'position' => 'json',
        'role' => 'json',
        'ip_addresses' => 'json',
        'timestamps' => 'json',
    ];

    public static function boot()
    {
        parent::boot();
        self::observe(UserActionObserver::class);
    }

    public function document()
    {
        return $this->belongsTo(SuratPermintaanPerpanjanganPenahananLanjutanDocument::class, 'document_id');
    }

    public function officer()
    {
        return $this->belongsTo('App\Models\Officer', 'officer_id', 'id');
    }
}
