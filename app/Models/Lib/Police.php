<?php

namespace App\Models\Lib;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Police extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'lib.polices';
    public $incrementing = false;
    protected $primaryKey = 'id';
    public $keyType = 'string';

    protected $guarded = [];

    protected $casts = [
        'id' => 'string',
    ];

    public static function getEnumOption($columnKey = null, $enumPropKey = null)
    {
        $enumOptions = [
            'class' => [
                'PST' => 'PUSAT',
                'DRH' => 'DAERAH',
                'RES' => 'RESOR',
                'SEK' => 'SEKTOR',
                'SUB' => 'SUBSEKTOR',
            ],
        ];
    
        if ($columnKey !== null && $enumPropKey !== null) {
            if (isset($enumOptions[$columnKey]) && isset($enumOptions[$columnKey][$enumPropKey])) {
                return $enumOptions[$columnKey][$enumPropKey];
            }
            return null;
        }
    
        return null;
    }

    public function scopeWithRelated($query)
    {
        return $query->with([
            'parent',
            'children',
        ]);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function parent()
    {
        return $this->belongsTo(Police::class, 'parent_id')->with('parent');
    }

    public function children()
    {
        return $this->hasMany(Police::class, 'parent_id')->with('children');
    }

    public function prosecutors()
    {
        return $this->belongsToMany(Prosecutor::class, 'pivot.police_prosecutor', 'police_id', 'prosecutor_id')->withTimestamps();
    }
}
