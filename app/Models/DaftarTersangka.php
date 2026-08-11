<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Uuid;

class DaftarTersangka extends Model
{
    use HasFactory;

    protected $table = 'daftar_tersangka';

    protected $fillable = [
       'id',
       'accident_id',
       'name',
       'gender',
       'city',
       'birth_date',
       'religion',
       'job',
       'education',
       'phone',
       'citizen',
       'address',
       'identity_no',
       'identity_type',
    ];

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->id = (string) Uuid::generate();
        });
    }
}
