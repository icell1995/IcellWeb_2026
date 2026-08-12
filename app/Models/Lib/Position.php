<?php

namespace App\Models\Lib;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Position extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'lib.positions';
    protected $primaryKey = 'id';
    public $keyType = 'string';

    protected $guarded = [];

    protected $casts = [
        'id' => 'string',
        'properties' => 'json',
    ];

    public function scopeWithRelated($query)
    {
        return $query->with([
            'employmentType',
            'positionCluster',
            'police'
        ]);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
   
    public function scopeWherePolri($query)
    {
        return $query->where('employment_type_id', '1');
    }
    
    public function scopeWherePNS($query)
    {
        return $query->where('employment_type_id', '2');
    }

    public function officer(){
        return $this->hasMany('App\Models\Officer', 'position_id');
    }

    public function police(){
        return $this->belongsTo('App\Models\Lib\Police', 'police_id', 'id');
    }

    public function employmentType()
    {
        return $this->belongsTo('App\Models\Lib\EmploymentType', 'employment_type_id', 'id');
    }

    public function positionCluster()
    {
        return $this->belongsTo('App\Models\Opt\PositionCluster', 'position_cluster_id', 'id');
    }
}
