<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Uuid;

class OfficerSpringas extends Model
{
    use HasFactory;

    protected $table='legacy.officer_springas';

    protected $fillable = [
        'sprint_gas_id',
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
