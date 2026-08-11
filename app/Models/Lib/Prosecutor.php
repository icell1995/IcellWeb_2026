<?php

namespace App\Models\Lib;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Prosecutor extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'lib.prosecutors';
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

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function parent()
    {
        return $this->belongsTo(Prosecutor::class, 'parent_id')->with('parent');
    }

    public function children()
    {
        return $this->hasMany(Prosecutor::class, 'parent_id')->with('children');
    }

    public function polices()
    {
        return $this->belongsToMany('App\Models\Lib\Police', 'pivot.police_prosecutor', 'prosecutor_id', 'police_id');
    }

    public function regency(){
        return $this->belongsTo('App\Models\Lib\Location', 'regency_id', 'id');
    }

    public function district(){
        return $this->belongsTo('App\Models\Lib\Location', 'district_id', 'id');
    }
}
