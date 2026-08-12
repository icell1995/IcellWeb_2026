<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Uuid;

class Dpo extends Model
{
    use HasFactory;

    protected $table = 'dpo';
    protected $fillable = [
        'id',
        'accident_id',
        'name',
        'gender',
        'deskripsi_dpo',
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
