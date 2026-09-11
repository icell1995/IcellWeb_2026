<?php

namespace App\Models\Doc\SuratKesepakatanDiversiDocument;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratKesepakatanDiversiDocumentAttachment extends Model
{
    use HasFactory;

    protected $table = 'doc.surat_kesepakatan_diversi_document_attachments';

    protected $primaryKey = 'id';

    protected $guarded = [
        'id',
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

    public function suratKesepakatanDiversiDocument()
    {
        return $this->belongsTo('App\Models\Doc\SuratKesepakatanDiversiDocument\SuratKesepakatanDiversiDocument', 'surat_kesepakatan_diversi_document_id', 'id');
    }
}
