<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Uuid;

class LHGP extends Model
{
    use HasFactory;

    protected $table='lhgp';

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'accident_id',
        'no_lp',
        'no_sprindik',
        'jenis_lhgp',
        'jenis_gelar_perkara',
        'surat_undangan',
        'tanggal_pelaksanaan',
        'waktu_pelaksanaan',
        'zona_waktu',
        'tempat_pelaksanaan',
        'pimpinan_gelar_perkara',
        'pemapar',
        'Pembahasan',
        'Kesimpulan',
        'Penutup',
        'pejabat_penandatangan'
    ];

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->id = (string) Uuid::generate();
        });
    }

    public function officer_lhgp(){
        return $this->belongsToMany('App\Models\Officer','sign_officer_lhgp','lhgp_id','officer_id');
    }
}
