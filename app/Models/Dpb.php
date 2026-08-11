<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Uuid;

class Dpb extends Model
{
    use HasFactory;

    protected $table = 'dpb';
    protected $fillable = [
        'id',
        'accident_id',
        'jenis',
        'no_tnkb',
        'deskripsi_dpb',
        'state'
    ];

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->id = (string) Uuid::generate();
        });
    }
}
