<?php

namespace App\Models\Doc\SuratPerintahPembantaranPenahananDocument;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratPerintahPembantaranPenahananDocumentSuspect extends Model
{
    use HasFactory;

    protected $table = 'doc.surat_perintah_pembantaran_penahanan_document_suspects';

    protected $primaryKey = 'id';

    protected $guarded = [];

    public function document()
    {
        return $this->belongsTo(SuratPerintahPembantaranPenahananDocument::class, 'document_id', 'id');
    }

    public function suspect()
    {
        return $this->belongsTo('App\Models\Suspect', 'suspect_id', 'id');
    }
}
