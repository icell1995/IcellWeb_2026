<?php

namespace App\Models\Doc\SuratPermintaanIzinPenyitaanDocument;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratPermintaanIzinPenyitaanDocumentAttachment extends Model
{
    use HasFactory;

    protected $table = 'doc.surat_permintaan_izin_penyitaan_document_attachments';

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
            if (
                isset($enumOptions[$columnKey]) &&
                isset($enumOptions[$columnKey][$enumPropKey])
            ) {
                return $enumOptions[$columnKey][$enumPropKey];
            }

            return null;
        }

        return null;
    }

    public function suratPermintaanIzinPenyitaanDocument()
    {
        return $this->belongsTo(
            'App\Models\Doc\SuratPermintaanIzinPenyitaanDocument\SuratPermintaanIzinPenyitaanDocument',
            'surat_permintaan_izin_penyitaan_document_id',
            'id'
        );
    }
}