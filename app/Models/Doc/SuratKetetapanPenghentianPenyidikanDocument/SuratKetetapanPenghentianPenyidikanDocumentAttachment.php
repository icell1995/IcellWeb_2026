<?php

namespace App\Models\Doc\SuratKetetapanPenghentianPenyidikanDocument;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratKetetapanPenghentianPenyidikanDocumentAttachment extends Model
{
    use HasFactory;

    protected $table = 'doc.surat_ketetapan_penghentian_penyidikan_document_attachments';

    protected $primaryKey = 'id';

    protected $guarded = [
        'id',
    ];

    protected $casts = [];

    public static function getEnumOption($columnKey = null, $enumPropKey = null)
    {
        $enumOptions = [
            'type' => [
                'DOCUMENT' => 'DOCUMENT',
                'IMAGE'    => 'IMAGE',
                'VIDEO'    => 'VIDEO',
                'AUDIO'    => 'AUDIO',
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

    /**
     * Relations
     */
    public function suratKetetapanPenghentianPenyidikanDocument()
    {
        return $this->belongsTo(
            'App\Models\Doc\SuratKetetapanPenghentianPenyidikanDocument\SuratKetetapanPenghentianPenyidikanDocument',
            'surat_ketetapan_penghentian_penyidikan_document_id',
            'id'
        );
    }
}
