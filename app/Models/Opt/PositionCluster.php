<?php

namespace App\Models\Opt;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PositionCluster extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'opt.position_clusters';
    protected $primaryKey = 'id';
    public $keyType = 'string';

    protected $guarded = [];

    protected $casts = [
        'id' => 'string',
    ];

    public function scopeWithRelated($query)
    {
        return $query->with([
            'positions'
        ]);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function positions()
    {
        return $this->hasMany('App\Models\Lib\Position', 'position_cluster_id', 'id');
    }
}
