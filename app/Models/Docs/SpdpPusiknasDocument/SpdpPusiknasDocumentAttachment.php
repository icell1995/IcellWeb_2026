<?php

namespace App\Models\Docs\SpdpPusiknasDocument;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Lib\EnumBaseModel;

class SpdpPusiknasDocumentAttachment extends EnumBaseModel
{
    use HasFactory;

    protected $table = 'doc.spdp_pusiknas_document_attachments';

    protected $fillable = [
        'spdp_pusiknas_document_id',
        'name',
        'original_name',
        'extension',
        'mimetype',
        'size',
        'path',
        'type',
    ];

    public function spdpPusiknasDocument()
    {
        return $this->belongsTo(SpdpPusiknasDocument::class, 'spdp_pusiknas_document_id');
    }
}
