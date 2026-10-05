<?php

namespace App\Models\Doc\SuratPemberitahuanDimulainyaPenyidikanPusiknasDocument;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class SuratPemberitahuanDimulainyaPenyidikanPusiknasDocumentAttachment extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $table = 'doc.surat_pemberitahuan_dimulainya_penyidikan_pusiknas_document_attachments';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'surat_pemberitahuan_dimulainya_penyidikan_pusiknas_document_id',
        'original_name',
        'name',
        'extension',
        'size',
        'mimetype',
        'type',
    ];

    public function document()
    {
        return $this->belongsTo(SuratPemberitahuanDimulainyaPenyidikanPusiknasDocument::class, 'surat_pemberitahuan_dimulainya_penyidikan_pusiknas_document_id');
    }
}
