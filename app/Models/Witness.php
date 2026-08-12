<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Webpatser\Uuid\Uuid;

class Witness extends Model
{
    use HasFactory;

    protected $table = 'public.witnesses';
    protected $primaryKey = 'id';
    public $keyType = 'uuid';

    protected $guarded = [];

    protected $casts = [
        'id' => 'string',
    ];

    // public static function boot()
    // {
    //     parent::boot();
    //     self::creating(function ($model) {
    //         $model->id = (string) Uuid::generate();
    //     });
    // }

    public static function getEnumOption($columnKey = null, $enumPropKey = null)
    {
        $enumOptions = [
            'status' => [
                'IMP' => 'IMPORT',
                'WIDNT' => 'WITH_IDENTITY',
                'WOIDNT' => 'WITHOUT_IDENTITY',
            ],
            'class' => [
                'PED' => 'PEDESTRIAN',
                'DRV' => 'DRIVER',
                'PSGR' => 'PASSENGER',
                'CTZN' => 'CITIZEN'
            ],
            'flag' => [
                'SKS' => 'SAKSI',
            ],
            'group' => [
                'LHGP' => 'LAPORAN_HASIL_GELAR_PERKARA',
                'STAPTPENTSK' => 'SURAT_KETETAPAN_TENTANG_PENETAPAN_TERSANGKA',
            ],
            'insert_method' => [
                'MNL' => 'MANUAL',
                'IMP' => 'IMPORT'
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

    public function accident()
    {
        return $this->belongsTo(Accident::class, 'accident_id', 'id');
    }

    public function identityType()
    {
        return $this->belongsTo('App\Models\Lib\IdentityType', 'identity_type_id', 'id');
    }
}
