<?php

namespace App\Models\Doc\SuratKetetapanTentangPenetapanTersangkaDocument;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratKetetapanTentangPenetapanTersangkaDocumentOfficer extends Model
{
    use HasFactory;

    protected $table = 'doc.surat_ketetapan_tentang_penetapan_tersangka_document_officers';
    protected $primaryKey = 'id';

    protected $guarded = [
        'id',
    ];

    protected $casts = [
 
    ];

    public static function getEnumOption($columnKey = null, $enumPropKey = null)
    {
        $enumOptions = [
            'status' => [
                'PRESENT' => 'PRESENT',
                'PAST' => 'PAST',
                'EXTERNAL' => 'EXTERNAL',
            ],
            'class' => [
                'MEMBER' => 'MEMBER',
                'LEADER' => 'LEADER',
                'SIGNATORY' => 'SIGNATORY',
            ],
            'flag' => [
                'INTERNAL' => 'INTERNAL',
                'MOVED' => 'MOVED',
                'EXTERNAL' => 'EXTERNAL',
            ],
            'insert_method' => [
                'MANUAL' => 'MANUAL',
                'IMPORT' => 'IMPORT',
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

    public function scopeWithRelated($query)
    {
        return $query->with([
            'suratKetetapanTentangPenetapanTersangkaDocument', 
            'police',
            'position',
            'rank',
        ]);
    }

    public function suratKetetapanTentangPenetapanTersangkaDocument(){
        return $this->belongsTo('App\Models\Doc\SuratKetetapanTentangPenetapanTersangkaDocument\SuratKetetapanTentangPenetapanTersangkaDocument', 'surat_ketetapan_tentang_penetapan_tersangka_document_id', 'id');
    }

    public function position(){
        return $this->belongsTo('App\Models\Lib\Position', 'position_id', 'id');
    }

    public function rank(){
        return $this->belongsTo('App\Models\Lib\Rank', 'rank_id', 'id');
    }

    public function police(){
        return $this->belongsTo('App\Models\Actor\Police', 'police_id', 'id');
    }
}
