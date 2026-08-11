<?php

namespace App\Models\Meta\Institutions;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Court extends Model
{
    use HasFactory;

    protected $table = 'courts';
    protected $primaryKey = 'id';
    public $incrementing = false;

    protected $guarded = [];

    public function polres()
    {
        return $this->belongsToMany('App\Models\Polres', 'polres_court', 'court_id', 'polres_id');
    }
}
