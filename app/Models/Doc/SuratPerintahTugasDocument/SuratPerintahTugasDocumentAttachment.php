<?php

namespace App\Models\Doc\SuratPerintahTugasDocument;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratPerintahTugasDocumentAttachment extends Model
{
    use HasFactory;

    protected $table = 'doc.surat_perintah_tugas_document_attachments';

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

    public function suratPerintahTugasDocument()
    {
        return $this->belongsTo('App\Models\Doc\SuratPerintahTugasDocument\SuratPerintahTugasDocument', 'surat_perintah_tugas_document_id', 'id');
    }
}
