<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kejaksaan extends Model
{
    use HasFactory;

    protected $table = 'kejaksaan';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'alias',
        'top_agency',
        'class',
        'address',
        'village',
        'district',
        'regency',
        'province',
        'state',
        'sort',
    ];
}
