<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Uuid;

class SuratPenyidikan extends Model
{
    use HasFactory;

    protected $table = 'surat_penyidikan';

    protected $fillable = [
        'accident_id',
        'officer_id'
    ];

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->id = (string) Uuid::generate();
        });
    }
}
