<?php

namespace App\Models\Doc\SuratKetetapanPenghentianPenyidikanDocument;

use App\Models\Lib\Police;
use App\Models\Lib\Rank;
use App\Models\Lib\Position;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratKetetapanPenghentianPenyidikanDocumentOfficer extends Model
{
    use HasFactory;

    protected $table = 'doc.surat_ketetapan_penghentian_penyidikan_document_officers';

    protected $fillable = [
        'surat_ketetapan_penghentian_penyidikan_document_id',
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
        'insert_method'
    ];

    public function document()
    {
        return $this->belongsTo(SuratKetetapanPenghentianPenyidikanDocument::class, 'surat_ketetapan_penghentian_penyidikan_document_id');
    }

    public function police()
    {
        return $this->belongsTo(Police::class, 'police_id');
    }

    public function rank()
    {
        return $this->belongsTo(Rank::class, 'rank_id');
    }

    public function position()
    {
        return $this->belongsTo(Position::class, 'position_id');
    }

    public static function getEnumOption($columnKey = null, $enumPropKey = null)
    {
        $enumOptions = [
            'status' => [
                'PRESENT'  => 'PRESENT',
                'PAST'     => 'PAST',
                'EXTERNAL' => 'EXTERNAL',
            ],
            'class' => [
                'MEMBER'    => 'MEMBER',
                'LEADER'    => 'LEADER',
                'SIGNATORY' => 'SIGNATORY',
            ],
            'flag' => [
                'INTERNAL' => 'INTERNAL',
                'MOVED'    => 'MOVED',
                'EXTERNAL' => 'EXTERNAL',
            ],
            'insert_method' => [
                'MANUAL' => 'MANUAL',
                'IMPORT' => 'IMPORT',
            ],
        ];

        if ($columnKey !== null && $enumPropKey !== null) {
            return $enumOptions[$columnKey][$enumPropKey] ?? null;
        }

        if ($columnKey !== null) {
            return $enumOptions[$columnKey] ?? [];
        }

        return $enumOptions;
    }
}
