<?php

namespace App\Models\Doc\SuratPerintahPenyidikanDocument;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratPerintahPenyidikanDocumentCaseKeyword extends Model
{
    use HasFactory;

    protected $table = 'doc.surat_perintah_penyidikan_document_case_keywords';
    protected $primaryKey = 'id';

    protected $guarded = [
        'id',
    ];

    public function suratPerintahPenyidikanDocument()
    {
        return $this->belongsTo('App\Models\Doc\SuratPerintahPenyidikanDocument\SuratPerintahPenyidikanDocument', 'surat_perintah_penyidikan_document_id', 'id');
    }
}
