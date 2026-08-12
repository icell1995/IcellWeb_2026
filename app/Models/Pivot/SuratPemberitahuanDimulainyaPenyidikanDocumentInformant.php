<?php

namespace App\Models\Pivot;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class SuratPemberitahuanDimulainyaPenyidikanDocumentInformant extends Pivot
{
    use HasFactory;

    protected $table = 'pivot.surat_pemberitahuan_dimulainya_penyidikan_document_informant';
    protected $primaryKey = 'id';

    protected $fillable = [
        'surat_pemberitahuan_dimulainya_penyidikan_document_id',
        'informant_id'
    ];

    public function suratPemberitahuanDimulainyaPenyidikanDocument()
    {
        return $this->belongsTo('App\Models\Doc\SuratPemberitahuanDimulainyaPenyidikanDocument', 'surat_pemberitahuan_dimulainya_penyidikan_document_id', 'id');
    }

    public function informant()
    {
        return $this->belongsTo('App\Models\Informant', 'informant_id', 'id');
    }
}
