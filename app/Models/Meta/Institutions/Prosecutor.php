<?php

namespace App\Models\Meta\Institutions;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prosecutor extends Model
{
    use HasFactory;

    protected $table = 'prosecutors';
    protected $primaryKey = 'id';
    public $incrementing = false;

    protected $guarded = [];

    public function polres()
    {
        return $this->belongsToMany('App\Models\Polres', 'polres_prosecutor', 'prosecutor_id', 'polres_id');
    }
}
