<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Polda extends Model
{
    use HasFactory;
    public $incrementing = false;

    protected $table = 'polda';
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    protected $guarded = [];

    // cast
    protected $casts = [
        'id' => 'string',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'polda_id', 'id');
    }

    public function polres() {
        return $this->hasMany('App\Models\Polres', 'polda_id')->where('state','<>','0')->orderBy('sort');
    }
}
