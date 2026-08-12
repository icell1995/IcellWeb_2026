<?php

namespace App\Models\Stg;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DorsWitness extends Model
{
    use HasFactory;

    protected $table = 'stg_dors_witnesses';
    protected $primaryKey = 'id';

    public $keyType = 'uuid';

    protected $guarded = [];

    protected $casts = [
        'id' => 'string',
    ];
}
