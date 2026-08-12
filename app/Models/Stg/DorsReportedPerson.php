<?php

namespace App\Models\Stg;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DorsReportedPerson extends Model
{
    use HasFactory;

    protected $table = 'stg_dors_reported_persons';
    protected $primaryKey = 'id';

    public $keyType = 'uuid';

    protected $guarded = [];

    protected $casts = [
        'id' => 'string',
    ];
}
