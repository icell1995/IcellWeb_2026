<?php

namespace App\Models\Lib;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DrivingLicenseType extends Model
{
    use HasFactory;

    protected $table = 'lib.driving_license_types';
    protected $primaryKey = 'id';
    
    protected $guarded = ['id'];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

