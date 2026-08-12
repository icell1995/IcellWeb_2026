<?php

namespace App\Models\Doc\SuratPerintahPenyelidikanDocument;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class SuratPerintahPenyelidikanDocumentOfficer extends Model
{
    use HasFactory;

    protected $table = 'doc.surat_perintah_penyelidikan_document_officers';
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
            'suratPerintahPenyelidikanDocument', 
            'police',
            'position',
            'rank',
        ]);
    }

    public function suratPerintahPenyelidikanDocument()
    {
        return $this->belongsTo('App\Models\Doc\SuratPerintahPenyelidikanDocument\SuratPerintahPenyelidikanDocument', 'surat_perintah_penyelidikan_document_id', 'id');
    }

    public function police()
    {
        return $this->belongsTo('App\Models\Lib\Police', 'police_id', 'id')->with('parent');
    }

    public function rank()
    {
        return $this->belongsTo('App\Models\Lib\Rank', 'rank_id', 'id');
    }

    public function position()
    {
        return $this->belongsTo('App\Models\Lib\Position', 'position_id', 'id');
    }
}
