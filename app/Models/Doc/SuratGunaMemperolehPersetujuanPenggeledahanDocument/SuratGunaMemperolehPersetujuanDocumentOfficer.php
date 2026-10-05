<?php

namespace App\Models\Doc\SuratGunaMemperolehPersetujuanPenggeledahanDocument;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratGunaMemperolehPersetujuanDocumentOfficer extends Model
{
    use HasFactory;

    protected $table = 'doc.surat_memperoleh_persetujuan_penggeledahan_document_officers';

    protected $primaryKey = 'id';

    protected $guarded = [
        'id',
    ];

    protected $casts = [];

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
            'document',
            'police',
            'position',
            'rank',
        ]);
    }

    public function document()
    {
        return $this->belongsTo('App\Models\Doc\SuratGunaMemperolehPersetujuanPenggeledahanDocument\SuratGunaMemperolehPersetujuanPenggeledahanDocument', 'surat_memperoleh_persetujuan_penggeledahan_document_id', 'id');
    }

    public function police()
    {
        return $this->belongsTo('App\Models\Lib\Police', 'police_id', 'id')->with('parent');
    }

    public function position()
    {
        return $this->belongsTo('App\Models\Lib\Position', 'position_id', 'id');
    }

    public function rank()
    {
        return $this->belongsTo('App\Models\Lib\Rank', 'rank_id', 'id');
    }
}
