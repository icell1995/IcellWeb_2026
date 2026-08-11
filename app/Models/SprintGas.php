<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Uuid;

class SprintGas extends Model
{
    use HasFactory;

    protected $table='legacy.springas';

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'accident_id',
        'no_sprindik',
        'no_surat',
        'no_lp',
        'tanggal_springas',
        'lokasi',
        'tanggal_dimulai',
        'tanggal_berakhir',
        'pejabat_penandatangan',
        'ketua_tim',
        'officer',
        'created_by',
        'updated_by'
    ];

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->id = (string) Uuid::generate();
        });
    }

    public function officer_springas()
    {
        return $this->belongsToMany(Officer::class,'legacy.officer_springas')->withTimestamps();
    }

    public function officer(){
        return $this->belongsToMany('App\Models\Officer','legacy.officer_springas','sprint_gas_id','officer_id');
    }
}
