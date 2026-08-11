<?php

namespace App\Models\Lib;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PoliceDivision extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'lib.police_divisions';
    protected $primaryKey = 'id';

    protected $keyType = 'string';

    protected $guard = [];
    
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
