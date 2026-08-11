<?php

namespace App\Models\Stg;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DorsEvidence extends Model
{
    use HasFactory;

    protected $table = 'stg_dors_evidences';
    protected $primaryKey = 'id';

    public $keyType = 'uuid';

    protected $guarded = [];

    protected $casts = [
        'id' => 'string',
    ];
}
