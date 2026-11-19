<?php

namespace App\Models\Doc\SuratPerintahPembantaranPenahananDocument;

use App\Observers\UserActionObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SuratPerintahPembantaranPenahananDocumentOfficer extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'doc.surat_perintah_pembantaran_penahanan_document_officers';

    protected $primaryKey = 'id';

    protected $guarded = [];

    protected $casts = [
        'rank' => 'json',
        'position' => 'json',
        'role' => 'json',
        'status' => 'json',
        'class' => 'json',
        'flag' => 'json',
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
        return $this->belongsTo(SuratPerintahPembantaranPenahananDocument::class, 'document_id', 'id');
    }

    public function officer()
    {
        return $this->belongsTo('App\Models\Officer', 'officer_id', 'id');
    }
}
