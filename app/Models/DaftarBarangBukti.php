<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Uuid;
class DaftarBarangBukti extends Model
{
    use HasFactory;
    protected $table = 'daftar_barang_bukti';

    protected $fillable = [
       'id',
       'accident_id',
       'nama_barang',
       'jumlah_barang',
    ];

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->id = (string) Uuid::generate();
        });
    }

}
