<?php

namespace App\Models\Stg;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DorsVictim extends Model
{
    use HasFactory;

    protected $table = 'stg_dors_victims';
    protected $primaryKey = 'id';

    public $keyType = 'uuid';

    protected $guarded = [];

    protected $casts = [
        'id' => 'string',
    ];
}
