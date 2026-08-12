<?php

namespace App\Models\Pivot;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratKetetapanPenghentianPenyidikanDocumentSuspect extends Model
{
    use HasFactory;

    protected $table = 'pivot.surat_ketetapan_penghentian_penyidikan_document_suspect';
    public $timestamps = false;

    protected $fillable = [
        'surat_ketetapan_penghentian_penyidikan_document_id',
        'suspect_id'
    ];

    public function suratKetetapanPenghentianPenyidikanDocument()
    {
        return $this->belongsTo('App\Models\Doc\SuratKetetapanPenghentianPenyidikanDocument\SuratKetetapanPenghentianPenyidikanDocument', 'surat_ketetapan_penghentian_penyidikan_document_id', 'id');
    }

    public function suspect()
    {
        return $this->belongsTo('App\Models\Suspect', 'suspect_id', 'id');
    }
}
