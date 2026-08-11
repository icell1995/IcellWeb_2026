<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Uuid;

class DaftarSaksi extends Model
{
    use HasFactory;

    protected $table = 'daftar_saksi';

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
    ];

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->id = (string) Uuid::generate();
        });
    }

}
