<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Uuid;

class BeritaPenahananLanjutan extends Model
{
    use HasFactory;

    protected $table = 'berita_penahanan_lanjutan';

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
