<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Webpatser\Uuid\Uuid;

class Suspect extends Model
{
    use HasFactory;

    protected $table = 'public.suspects';
    protected $primaryKey = 'id';
    public $keyType = 'uuid';

    protected $guarded = [];

    protected $casts = [
        'id' => 'string',
        'properties' => 'json'
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
                'IMPORT' => 'IMPORT',
                'WITH_IDENTITY' => 'WITH_IDENTITY',
                'WITHOUT_IDENTITY' => 'WITHOUT_IDENTITY',
            ],
            'class' => [
                'DETERMINATION' => 'DETERMINATION',
                'ARREST' => 'ARREST',
                'REVOCATION' => 'REVOCATION',
            ],
            'flag' => [
                'TERLAPOR' => 'TERLAPOR',
                'TERDUGA' => 'TERDUGA',
                'TERSANGKA' => 'TERSANGKA',
            ],
            'group' => [
                'LAPORAN_HASIL_GELAR_PERKARA' => 'LAPORAN_HASIL_GELAR_PERKARA',
                'SURAT_KETETAPAN_TENTANG_PENETAPAN_TERSANGKA' => 'SURAT_KETETAPAN_TENTANG_PENETAPAN_TERSANGKA',
            ],
            'insert_method' => [
                'MANUAL' => 'MANUAL',
                'IMPORT' => 'IMPORT'
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

    public function scopeWithRelated($query){
        return $query->with([
            'accident',
            'suspectSource',
            'identityType',
            'gender',
            'ethnic',
            'job',
            'religion',
            'education',
            'maritalStatus',
            'location',
            'country',
            'province',
            'regency',
            'district',
            'village',
            'suratKetetapanTentangPenetapanTersangkaDocument',
        ]);
    } 

    public function laporanHasilGelarPerkaraDocuments()
    {
        return $this->belongsToMany('App\Models\Doc\LaporanHasilGelarPerkaraDocument\LaporanHasilGelarPerkaraDocument', 'pivot.laporan_hasil_gelar_perkara_document_suspect', 'suspect_id', 'laporan_hasil_gelar_perkara_document_id')->with(['caseDegreeType']);
    }

    public function suratKetetapanTentangPenetapanTersangkaDocument()
    {
        return $this->belongsToMany('App\Models\Doc\SuratKetetapanTentangPenetapanTersangkaDocument\SuratKetetapanTentangPenetapanTersangkaDocument', 'pivot.surat_ketetapan_tentang_penetapan_tersangka_document_suspect', 'suspect_id', 'surat_ketetapan_tentang_penetapan_tersangka_document_id');
    }

    public function vehicleAssociatedSuspect()
    {
        return $this->hasOne('App\Models\VehicleAssociatedSuspect', 'suspect_id', 'id');
    }

    public function accident()
    {
        return $this->belongsTo(Accident::class, 'accident_id', 'id');
    }

    public function suspectSource()
    {
        return $this->belongsTo('App\Models\Lib\SuspectSource', 'suspect_source_id', 'id');
    }

    public function identityType()
    {
        return $this->belongsTo('App\Models\Lib\IdentityType', 'identity_type_id', 'id');
    }

    public function gender(){
        return $this->belongsTo('App\Models\Lib\Gender', 'gender_id', 'id');
    }

    public function ethnic(){
        return $this->belongsTo('App\Models\Lib\Ethnic', 'ethnic_id', 'id');
    }
   
    public function job(){
        return $this->belongsTo('App\Models\Lib\Job', 'job_id', 'id');
    }
    
    public function religion(){
        return $this->belongsTo('App\Models\Lib\Religion', 'religion_id', 'id');
    }
 
    public function education(){
        return $this->belongsTo('App\Models\Lib\Education', 'education_id', 'id');
    }

    public function maritalStatus(){
        return $this->belongsTo('App\Models\Lib\MaritalStatus', 'marital_status_id', 'id');
    }

    public function location(){
        return $this->belongsTo('App\Models\Lib\Location', 'location_id', 'id');
    }

    public function country(){
        return $this->belongsTo('App\Models\Lib\Location', 'country_id', 'id');
    }

    public function province(){
        return $this->belongsTo('App\Models\Lib\Location', 'province_id', 'id');
    }

    public function regency(){
        return $this->belongsTo('App\Models\Lib\Location', 'regency_id', 'id');
    }

    public function district(){
        return $this->belongsTo('App\Models\Lib\Location', 'district_id', 'id');
    }

    public function village(){
        return $this->belongsTo('App\Models\Lib\Location', 'village_id', 'id');
    }
}
