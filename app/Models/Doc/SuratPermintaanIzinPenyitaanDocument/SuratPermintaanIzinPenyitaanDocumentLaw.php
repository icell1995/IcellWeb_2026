<?php

namespace App\Models\Doc\SuratPermintaanIzinPenyitaanDocument;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratPermintaanIzinPenyitaanDocumentLaw extends Model
{
    use HasFactory;

    protected $table = 'doc.surat_permintaan_izin_penyitaan_document_laws';

    protected $primaryKey = 'id';

    protected $guarded = [
        'id',
    ];

    protected $casts = [
        'id' => 'integer',
    ];

    public static function getEnumOption($columnKey = null, $enumPropKey = null)
    {
        $enumOptions = [
            'flag' => [
                'MAIN' => 'MAIN',
                'ADDT' => 'ADDITIONAL',
                'ADDITIONAL' => 'ADDITIONAL',
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
            'suratPermintaanIzinPenyitaanDocument',
            'crimeType',
            'crimeClass',
            'crimeConstitution',
        ]);
    }

    public function suratPermintaanIzinPenyitaanDocument()
    {
        return $this->belongsTo(
            'App\Models\Doc\SuratPermintaanIzinPenyitaanDocument\SuratPermintaanIzinPenyitaanDocument',
            'surat_permintaan_izin_penyitaan_document_id',
            'id'
        );
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
