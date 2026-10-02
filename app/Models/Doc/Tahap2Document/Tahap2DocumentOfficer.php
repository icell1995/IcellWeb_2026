<?php

namespace App\Models\Doc\Tahap2Document;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tahap2DocumentOfficer extends Model
{
    use HasFactory;

    protected $table = 'doc.tahap_2_document_officers';

    protected $fillable = [
        'tahap_2_document_id',
        'sort',
        'register_number',
        'first_title',
        'first_name',
        'last_name',
        'last_title',
        'rank_id',
        'position_id',
        'phone_number',
        'email',
        'information',
        'police_id',
        'status',
        'class',
        'flag',
        'insert_method',
    ];

    public function document()
    {
        return $this->belongsTo(Tahap2Document::class, 'tahap_2_document_id');
    }

    public function rank()
    {
        return $this->belongsTo(\App\Models\Lib\Rank::class, 'rank_id');
    }

    public function position()
    {
        return $this->belongsTo(\App\Models\Lib\Position::class, 'position_id');
    }

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
}
