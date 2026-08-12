<?php

namespace App\Models\Doc\SuratPerintahPenahananDocument;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratPerintahPenahananDocumentAttachment extends Model
{
    use HasFactory;

    protected $table = 'doc.surat_perintah_penahanan_document_attachments';

    protected $primaryKey = 'id';

    protected $guarded = [
        'id',
    ];

    protected $casts = [

    ];

    public static function getEnumOption($columnKey = null, $enumPropKey = null)
    {
        $enumOptions = [
            'type' => [
                'DOCUMENT' => 'DOCUMENT',
                'IMAGE' => 'IMAGE',
                'VIDEO' => 'VIDEO',
                'AUDIO' => 'AUDIO',
            ],
        ];

        if ($columnKey !== null && $enumPropKey !== null) {
            if (isset($enumOptions[$columnKey]) && isset($enumOptions[$columnKey][$enumPropKey])) {
                return $enumOptions[$columnKey][$enumPropKey];
            }
            return null;
        }

        return null;
    }

    public function suratPerintahPenahananDocument()
    {
        return $this->belongsTo('App\Models\Doc\SuratPerintahPenahananDocument\SuratPerintahPenahananDocument', 'surat_perintah_penahanan_document_id', 'id');
    }
}
