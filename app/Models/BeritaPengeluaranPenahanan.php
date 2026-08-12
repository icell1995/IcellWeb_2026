<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Uuid;

class BeritaPengeluaranPenahanan extends Model
{
    use HasFactory;

    protected $table = 'berita_pengeluaran_penahanan';

    protected $fillable = [
        'accident_id',
        'name',
        'category',
        'initial',
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
