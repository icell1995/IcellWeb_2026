<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CaseVehicle extends Model
{
    use HasFactory;

    protected $table = 'case_vehicles';

    protected $guarded = ['id'];

    public function accident()
    {
        return $this->belongsTo('App\Models\Accident', 'accident_id', 'id');
    }
}
