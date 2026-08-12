<?php

namespace App\Models\Lib;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccidentType extends Model
{
    use HasFactory;

    protected $table = 'lib.accident_types';
    protected $primaryKey = 'id';
    
    protected $guarded = ['id'];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
