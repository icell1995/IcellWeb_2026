<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Uuid;


class SuratKetetapanPenetapanTersangka extends Model
{
    protected $table = 'suspect_determination_decision_letters';

    protected $guarded=[];

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->id = (string) Uuid::generate();
        });
    }

    use HasFactory;
}
