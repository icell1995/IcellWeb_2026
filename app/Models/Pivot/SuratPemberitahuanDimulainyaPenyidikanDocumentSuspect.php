<?php

namespace App\Models\Pivot;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class SuratPemberitahuanDimulainyaPenyidikanDocumentSuspect extends Pivot
{
    use HasFactory;

    protected $table = 'pivot.surat_pemberitahuan_dimulainya_penyidikan_document_suspect';
    protected $primaryKey = 'id';

    protected $fillable = [
        'surat_pemberitahuan_dimulainya_penyidikan_document_id',
        'suspect_id'
    ];

    public function suratPemberitahuanDimulainyaPenyidikanDocument()
    {
        return $this->belongsTo('App\Models\Doc\SuratPemberitahuanDimulainyaPenyidikanDocument', 'surat_pemberitahuan_dimulainya_penyidikan_document_id', 'id');
    }

    public function suspect()
    {
        return $this->belongsTo('App\Models\Suspect', 'suspect_id', 'id');
    }
}
