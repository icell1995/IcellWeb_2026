<?php

namespace App\Models\Pivot;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class LaporanHasilGelarPerkaraDocumentSuspect extends Pivot
{
    use HasFactory;

    protected $table = 'pivot.laporan_hasil_gelar_perkara_document_suspect';
    protected $primaryKey = 'id';

    protected $fillable = [
        'laporan_hasil_gelar_perkara_document_id',
        'suspect_id',
    ];

    public function laporanHasilGelarPerkaraDocument()
    {
        return $this->belongsTo('App\Models\Doc\LaporanHasilGelarPerkaraDocument', 'laporan_hasil_gelar_perkara_document_id', 'id');
    }

    public function suspect()
    {
        return $this->belongsTo('App\Models\Suspect', 'suspect_id', 'id');
    }
}
