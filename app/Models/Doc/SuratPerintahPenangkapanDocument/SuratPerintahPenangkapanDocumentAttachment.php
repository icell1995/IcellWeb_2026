<?php

namespace App\Models\Doc\SuratPerintahPenangkapanDocument;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratPerintahPenangkapanDocumentAttachment extends Model
{
    use HasFactory;

    protected $table = 'doc.surat_perintah_penangkapan_document_attachments';

    protected $guarded = [];

    public function suratPerintahPenangkapanDocument()
    {
        return $this->belongsTo(
            SuratPerintahPenangkapanDocument::class,
            'surat_perintah_penangkapan_document_id',
            'id'
        );
    }
}
