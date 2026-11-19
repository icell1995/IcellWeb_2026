<?php

namespace App\Models\Doc\SuratPerintahPembantaranPenahananDocument;

use App\Observers\UserActionObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SuratPerintahPembantaranPenahananDocumentLaw extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'doc.surat_perintah_pembantaran_penahanan_document_laws';

    protected $primaryKey = 'id';

    protected $guarded = [];

    protected $casts = [
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

    public function crimeType()
    {
        return $this->belongsTo('App\Models\Lib\CrimeType', 'crime_type_id', 'id');
    }

    public function crimeClass()
    {
        return $this->belongsTo('App\Models\Lib\CrimeClass', 'crime_class_id', 'id');
    }

    public function crimeConstitution()
    {
        return $this->belongsTo('App\Models\Lib\CrimeConstitution', 'crime_constitution_id', 'id');
    }
}
