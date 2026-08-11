<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Uuid;

class SP3 extends Model
{
    use HasFactory;

    protected $table = 'sp3';

    protected $fillable = [
        'accident_id',
        'no_lp',
        'no_spdp',
        'no_sp3',
        'no_surat_perintah_penyidikan',
        'tanggal_sp_dik',
        'penerima_surat',
        'klasifikasi',
        'tanggal_berlaku',
        'alasan',
        'lampiran'
    ];

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->id = (string) Uuid::generate();
        });
    }
}
