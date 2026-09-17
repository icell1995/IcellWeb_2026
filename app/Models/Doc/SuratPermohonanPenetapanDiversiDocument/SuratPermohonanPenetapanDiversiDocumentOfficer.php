<?php

namespace App\Models\Doc\SuratPermohonanPenetapanDiversiDocument;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratPermohonanPenetapanDiversiDocumentOfficer extends Model
{
    use HasFactory;

    protected $table = 'doc.surat_permohonan_penetapan_diversi_document_officers';

    protected $guarded = [
        'id',
    ];

    public static function getEnumOption($columnKey = null, $enumPropKey = null)
    {
        $enumOptions = [
            'status' => [
                'PRESENT' => 'PRESENT',
                'EXCUSED' => 'EXCUSED',
                'UPPER_UNIT_LEVEL' => 'UPPER_UNIT_LEVEL',
            ],
            'class' => [
                'SIGNATORY' => 'SIGNATORY',
                'INVESTIGATOR' => 'INVESTIGATOR',
                'FACILITATOR' => 'FACILITATOR',
            ],
            'flag' => [
                'INTERNAL' => 'INTERNAL',
                'EXTERNAL' => 'EXTERNAL',
                'UPPER_UNIT_LEVEL' => 'UPPER_UNIT_LEVEL',
            ],
            'insert_method' => [
                'IMPORT' => 'IMPORT',
                'MANUAL' => 'MANUAL',
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

    public function suratPermohonanPenetapanDiversiDocument()
    {
        return $this->belongsTo('App\Models\Doc\SuratPermohonanPenetapanDiversiDocument\SuratPermohonanPenetapanDiversiDocument', 'surat_permohonan_penetapan_diversi_document_id', 'id');
    }

    public function rank()
    {
        return $this->belongsTo('App\Models\Lib\Rank', 'rank_id', 'id');
    }

    public function position()
    {
        return $this->belongsTo('App\Models\Lib\Position', 'position_id', 'id');
    }

    public function police()
    {
        return $this->belongsTo('App\Models\Lib\Police', 'police_id', 'id');
    }
}
