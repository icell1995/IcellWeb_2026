<?php

namespace App\Models\Doc\SuratPermintaanPerpanjanganPenahananLanjutanKeduaDocument;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SuratPermintaanPerpanjanganPenahananLanjutanKeduaDocumentLaw extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'doc.surat_perpanjangan_penahanan_kedua_laws';
    protected $primaryKey = 'id';

    protected $guarded = [
        'id',
    ];

    public static function getEnumOption($columnKey = null, $enumPropKey = null)
    {
        $enumOptions = [
            'flag' => [
                'MAIN' => 'MAIN',
                'ADDT' => 'ADDITIONAL',
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
            'crimeType',
            'crimeClass',
            'crimeConstitution'
        ]);
    }

    public function document()
    {
        return $this->belongsTo(SuratPermintaanPerpanjanganPenahananLanjutanKeduaDocument::class, 'document_id', 'id');
    }

    public function crimeType()
    {
        return $this->belongsTo('App\Models\Lib\CrimeType', 'crime_type_id', 'id');
    }

    public function crimeClass()
    {
        return $this->belongsTo('App\Models\Lib\CrimeClass', 'crime_class_id', 'id');
    }

    public function crimeConstitution()
    {
        return $this->belongsTo('App\Models\Lib\CrimeConstitution', 'crime_constitution_id', 'id');
    }
}
