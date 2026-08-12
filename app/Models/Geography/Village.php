<?php

namespace App\Models\Geography;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Village extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $table = 'village';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'name',
        'district_id',
        'state',
        'sort',
        'timezone',
    ];
}
