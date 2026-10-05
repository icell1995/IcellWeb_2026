<?php

namespace App\Models\Pivot;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class SuratPerintahPenangguhanPenahananDocumentSuspect extends Pivot
{
    use HasFactory;

    protected $table = 'pivot.surat_perintah_penangguhan_penahanan_document_suspect';
    protected $primaryKey = 'id';

    protected $fillable = [
        'surat_perintah_penangguhan_penahanan_document_id',
        'suspect_id'
    ];

    public function suratPerintahPenangguhanPenahananDocument()
    {
        return $this->belongsTo('App\Models\Doc\SuratPerintahPenangguhanPenahananDocument\SuratPerintahPenangguhanPenahananDocument', 'surat_perintah_penangguhan_penahanan_document_id', 'id');
    }

    public function suspect()
    {
        return $this->belongsTo('App\Models\Suspect', 'suspect_id', 'id');
    }
}
