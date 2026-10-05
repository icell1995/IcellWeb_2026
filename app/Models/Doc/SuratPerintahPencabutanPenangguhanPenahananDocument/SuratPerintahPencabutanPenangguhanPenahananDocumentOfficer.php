<?php

namespace App\Models\Doc\SuratPerintahPencabutanPenangguhanPenahananDocument;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Webpatser\Uuid\Uuid;

class SuratPerintahPencabutanPenangguhanPenahananDocumentOfficer extends Model
{
    use HasFactory;
    
    protected $table = 'doc.surat_perintah_pencabutan_penangguhan_penahanan_document_officers';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $guarded = [
        'id',
    ];

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Uuid::generate();
            }
        });
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

    public function scopeWithRelated($query)
    {
        return $query->with([
            'suratPerintahPencabutanPenangguhanPenahananDocument', 
            'police',
            'position',
            'rank',
        ]);
    }

    public function suratPerintahPencabutanPenangguhanPenahananDocument()
    {
        return $this->belongsTo('App\Models\Doc\SuratPerintahPencabutanPenangguhanPenahananDocument\SuratPerintahPencabutanPenangguhanPenahananDocument', 'doc_id', 'id');
    }

    public function police()
    {
        return $this->belongsTo('App\Models\Lib\Police', 'police_id', 'id');
    }

    public function position()
    {
        return $this->belongsTo('App\Models\Lib\Position', 'position_id', 'id');
    }

    public function rank()
    {
        return $this->belongsTo('App\Models\Lib\Rank', 'rank_id', 'id');
    }

    public function officer()
    {
        return $this->belongsTo('App\Models\Officer', 'officer_id', 'id');
    }
}
