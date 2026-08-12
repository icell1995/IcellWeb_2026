<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengadilan extends Model
{
    use HasFactory;

    protected $table = 'pengadilan';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'top_agency',
        'class',
        'address',
        'province',
        'regency',
        'district',
        'village',
        'state',
        'sort',
    ];
}
