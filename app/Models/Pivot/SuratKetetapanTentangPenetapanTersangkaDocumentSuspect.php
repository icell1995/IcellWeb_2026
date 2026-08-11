<?php

namespace App\Models\Pivot;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class SuratKetetapanTentangPenetapanTersangkaDocumentSuspect extends Pivot
{
    use HasFactory;

    protected $table = 'pivot.surat_ketetapan_tentang_penetapan_tersangka_document_suspect';
    protected $primaryKey = 'id';

    protected $fillable = [
        'surat_ketetapan_tentang_penetapan_tersangka_document_id',
        'suspect_id'
    ];

    public function suratKetetapanTentangPenetapanTersangkaDocument()
    {
        return $this->belongsTo('App\Models\Doc\SuratKetetapanTentangPenetapanTersangkaDocument', 'surat_ketetapan_tentang_penetapan_tersangka_document_id', 'id');
    }

    public function suspect()
    {
        return $this->belongsTo('App\Models\Suspect', 'suspect_id', 'id');
    }
}
