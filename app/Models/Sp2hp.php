<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Uuid;

class Sp2hp extends Model
{
    use HasFactory;

    protected $table = 'sp2hp';

    protected $fillable = [
        'accident_id',
        'tipe',
        'tingkat',
        'kota',
        'tanggal_terbit',
        'nomor_surat_1',
        'nomor_surat_2',
        'nomor_surat_3',
        'nomor_surat_4',
        'nomor_surat_5',
        'name',
        'address',
        'deskripsi',
        'created_by'
    ];

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->id = (string) Uuid::generate();
        });
    }
}
