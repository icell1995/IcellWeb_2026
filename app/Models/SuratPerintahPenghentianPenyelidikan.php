<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Uuid;

class SuratPerintahPenghentianPenyelidikan extends Model
{
    protected $table = 'surat_perintah_penyelidikan';

    protected $fillable = [
        'id',
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
    use HasFactory;
}
