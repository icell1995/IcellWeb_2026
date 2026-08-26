<?php

namespace App\Models\Docs\Sp3PusiknasDocument;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sp3PusiknasDocumentAttachment extends Model
{
    use HasFactory;

    protected $table = 'doc.sp3_pusiknas_document_attachments';

    protected $fillable = [
        'sp3_pusiknas_document_id',
        'name',
        'original_name',
        'extension',
        'mimetype',
        'size',
        'path',
        'type',
    ];

    public function sp3PusiknasDocument()
    {
        return $this->belongsTo(Sp3PusiknasDocument::class, 'sp3_pusiknas_document_id');
    }
}
