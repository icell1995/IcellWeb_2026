<?php

namespace App\Models\Lib;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CaseKeyword extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'lib.case_keywords';
    protected $primaryKey = 'id';
    public $keyType = 'string';

    protected $guarded = [];

    protected $casts = [
        'id' => 'string',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
