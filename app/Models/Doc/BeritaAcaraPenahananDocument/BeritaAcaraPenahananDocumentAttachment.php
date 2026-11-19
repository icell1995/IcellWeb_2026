<?php

namespace App\Models\Doc\BeritaAcaraPenahananDocument;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BeritaAcaraPenahananDocumentAttachment extends Model
{
    use HasFactory;

    protected $table = 'doc.berita_acara_penahanan_document_attachments';

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

    public function beritaAcaraPenahananDocument()
    {
        return $this->belongsTo('App\Models\BeritaAcaraPenahanan', 'berita_acara_penahanan_document_id', 'id');
    }
}
