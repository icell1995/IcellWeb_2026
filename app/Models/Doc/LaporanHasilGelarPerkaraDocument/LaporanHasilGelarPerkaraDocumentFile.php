<?php

namespace App\Models\Doc\LaporanHasilGelarPerkaraDocument;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanHasilGelarPerkaraDocumentFile extends Model
{
    use HasFactory;

    protected $table="doc.laporan_hasil_gelar_perkara_document_files";

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

    public function laporanHasilGelarPerkaraDocument()
    {
        return $this->belongsTo(LaporanHasilGelarPerkaraDocument::class, 'laporan_hasil_gelar_perkara_document_id', 'id');
    }
}
