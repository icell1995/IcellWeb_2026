<?php

namespace App\Models\Lib;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Prison extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'lib.prisons';

    protected $fillable = [
        'province',
        'name',
        'branch',
        'puskarda_id',
        'spptti_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
