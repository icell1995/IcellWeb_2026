<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ref extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $table = 'ref';
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    // cast
    protected $casts = [
        'id' => 'string',
        'grp_id' => 'string',
    ];

    public function group() {

        return $this->belongsTo('App\Models\RefGroup', 'grp_id', 'id');
    }

}
