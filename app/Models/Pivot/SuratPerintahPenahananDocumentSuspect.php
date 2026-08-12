<?php

namespace App\Models\Pivot;

use App\Models\Doc\SuratPerintahPenahananDocument\SuratPerintahPenahananDocument;
use App\Models\Suspect;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratPerintahPenahananDocumentSuspect extends Model
{
    use HasFactory;

    protected $table = 'pivot.surat_perintah_penahanan_document_suspect';
    protected $primaryKey = 'id';

    protected $fillable = [
        'surat_perintah_penahanan_document_id',
        'suspect_id'
    ];

    public function suratPerintahPenahananDocument()
    {
        return $this->belongsTo(SuratPerintahPenahananDocument::class, 'surat_perintah_penahanan_document_id');
    }

    public function suspect()
    {
        return $this->belongsTo(Suspect::class, 'suspect_id');
    }
}
