<?php

namespace App\Models\Doc\PermintaanPerpanjanganPenahananDocument;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermintaanPerpanjanganPenahananDocumentAttachment extends Model
{
    use HasFactory;

    protected $table = 'doc.permintaan_perpanjangan_penahanan_document_attachments';

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

    public function permintaanPerpanjanganPenahananDocument()
    {
        return $this->belongsTo(
            'App\Models\Doc\PermintaanPerpanjanganPenahananDocument\PermintaanPerpanjanganPenahananDocument',
            'permintaan_perpanjangan_penahanan_document_id',
            'id'
        );
    }
}

