<?php

namespace App\Models\Lib;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CrimeClass extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'lib.crime_classes';
    protected $primaryKey = 'id';
    public $keyType = 'string';

    protected $guarded = [];

    protected $casts = [
        'id' => 'string',
    ];

    public function scopeWithRelated($query)
    {
        return $query->with([
            'crimeType'
        ]);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function crimeType()
    {
        return $this->hasMany(CrimeType::class, 'crime_class_id', 'id');
    }
}
