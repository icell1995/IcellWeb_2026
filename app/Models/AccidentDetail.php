<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Uuid;

class AccidentDetail extends Model
{
    use HasFactory;

    protected $table = 'accident_dtl';
    protected $fillable = [
        'id',
        'accident_id',
        'name',
        'category_id',
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
