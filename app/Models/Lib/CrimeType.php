<?php

namespace App\Models\Lib;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CrimeType extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'lib.crime_types';
    protected $primaryKey = 'id';
    public $keyType = 'string';

    protected $guarded = [];

    protected $casts = [
        'id' => 'string',
    ];

    public function scopeWithRelated($query)
    {
        return $query->with([
            'crimeClass',
            'crimeConstitution'
        ]);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function crimeClass()
    {
        return $this->belongsTo(CrimeClass::class, 'crime_class_id', 'id');
    }

    public function crimeConstitution()
    {
        return $this->hasOne(CrimeConstitution::class, 'crime_type_id', 'id');
    }
}
