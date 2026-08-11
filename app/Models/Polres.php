<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Polda;

class Polres extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    protected $table = 'polres';
    protected $keyType = 'string';
    
    protected $guarded = [];

    /// cast
    protected $casts = [
        'id' => 'string',
        'polda_id' => 'string',
    ];

    // Relation
    public function polda()
    {
        return $this->belongsTo('App\Models\Polda', 'polda_id', 'id');
    }
    
    public function prosecutor() {
        return $this->belongsToMany('App\Models\Meta\Institutions\Prosecutor', 'polres_prosecutor', 'polres_id', 'prosecutor_id');
    }

    public function court() {
        return $this->belongsToMany('App\Models\Meta\Institutions\Court', 'polres_court', 'polres_id', 'court_id');
    }

    public function authorized_signatory() {
        return $this->hasMany('App\Models\Peoples\AuthorizedSignatory', 'polres_id', 'id');
    }
}
