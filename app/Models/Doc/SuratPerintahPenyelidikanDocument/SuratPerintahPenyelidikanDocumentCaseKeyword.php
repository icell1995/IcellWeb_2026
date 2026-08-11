<?php

namespace App\Models\Doc\SuratPerintahPenyelidikanDocument;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratPerintahPenyelidikanDocumentCaseKeyword extends Model
{
    use HasFactory;

    protected $table = 'doc.surat_perintah_penyelidikan_document_case_keywords';
    protected $primaryKey = 'id';

    protected $guarded = [
        'id',
    ];

    public function suratPerintahPenyelidikanDocument()
    {
        return $this->belongsTo('App\Models\Doc\SuratPerintahPenyelidikanDocument\SuratPerintahPenyelidikanDocument', 'surat_perintah_penyelidikan_document_id', 'id');
    }
}
