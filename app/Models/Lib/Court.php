<?php

namespace App\Models\Lib;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Court extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'lib.courts';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;
    
    protected $guarded = [];

    public static function getEnumOption($columnKey = null, $enumPropKey = null)
    {
        $enumOptions = [
            'class' => [
                'AGN' => 'AGUNG',
                'TGG' => 'TINGGI',
                'NGR' => 'NEGERI',
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

    protected $casts = [
        'id' => 'string',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    public function parent()
    {
        return $this->belongsTo(Court::class, 'parent_id')->with('parent');
    }

    public function children()
    {
        return $this->hasMany(Court::class, 'parent_id')->with('children');
    }
}
